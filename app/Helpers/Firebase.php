<?php

namespace App\Helpers;

use Carbon\Carbon;
use Firebase\JWT\JWT;

class Firebase
{
    protected array $config = [];

    /**
     * @param  string|null  $path
     * @return void
     */
    public function __construct($path = null)
    {
        $this->loadConfig($path);
    }

    /**
     * @param  string|null  $path
     * @return void
     *
     * @throws \Exception
     */
    private function loadConfig($path)
    {
        $filepath = base_path($path ?: 'firebase.json');

        if (! is_readable($filepath)) {
            throw new \Exception("Config file missing or not readable at: {$filepath}");
        }

        $config = json_decode(file_get_contents($filepath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON in config file: '.json_last_error_msg());
        }

        if (empty($config)) {
            throw new \Exception("Config file is empty or malformed: {$filepath}");
        }

        $this->config = $config;
    }

    /**
     * @param  string  $jwt
     * @return string
     *
     * @throws \Exception
     */
    private function requestAccessToken($jwt)
    {
        $payload = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ];

        $ch = curl_init($this->config['token_uri']);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => http_build_query($payload),
        ]);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }

        $response = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: '.json_last_error_msg());
        }

        if ($httpCode !== 200 || empty($response['access_token'])) {
            throw new \Exception('Failed to get access token.');
        }

        return $response['access_token'];
    }

    /**
     * @param  string  $token
     * @param  array  $data
     * @return array
     */
    private function createPayload($token, $data)
    {
        $notification = $data['data'] ?? [];
        $data['data'] = json_encode(array_map('strval', $notification));

        return [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $notification['title'] ?? 'Untitled',
                    'body' => $notification['message'] ?? '...',
                ],
                'data' => array_map('strval', $data),
            ],
        ];
    }

    /**
     * @param  string  $accessToken
     * @param  array  $payload
     * @return void
     *
     * @throws \Exception
     */
    private function sendMessage($accessToken, $payload)
    {
        $headers = [
            "Authorization: Bearer {$accessToken}",
            'Content-Type: application/json',
        ];

        $ch = curl_init("https://fcm.googleapis.com/v1/projects/{$this->config['project_id']}/messages:send");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new \Exception("Curl error: {$error}");
        }

        $response = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: '.json_last_error_msg());
        }

        if ($httpCode !== 200) {
            throw new \Exception("FCM returned HTTP code {$httpCode}.");
        }
    }

    /**
     * @return string
     */
    private function getAccessToken()
    {
        $timestamp = Carbon::now()->timestamp;

        $jwt = JWT::encode([
            'iss' => $this->config['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $this->config['token_uri'],
            'iat' => $timestamp,
            'exp' => $timestamp + 3600,
        ], $this->config['private_key'], 'RS256');

        return $this->requestAccessToken($jwt);
    }

    /**
     * Kirim notifikasi ke Firebase.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>  $firebaseTokens
     * @param  array<string, mixed>  $data
     * @return void
     */
    public function send($firebaseTokens, $data)
    {
        $accessToken = $this->getAccessToken();

        if ($firebaseTokens instanceof \Illuminate\Database\Eloquent\Model) {
            $payload = $this->createPayload($firebaseTokens->token, $data);
            $this->sendMessage($accessToken, $payload);

            return;
        }

        foreach ($firebaseTokens as $firebase) {
            $payload = $this->createPayload($firebase->token, $data);
            $this->sendMessage($accessToken, $payload);
        }
    }
}
