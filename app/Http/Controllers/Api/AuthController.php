<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Interfaces\UserFirebaseInterface;
use App\Interfaces\UserInterface;
use App\Mail\OtpMail;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use App\Models\UserOtp;
use App\Rules\SecurePasswordRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AuthController extends Controller
{
    public function __construct(
        private UserFirebaseInterface $userFirebaseRepository,
        private UserInterface $userRepository
    ) {}

    private function respondWithToken($type, $token, $expired = null)
    {
        return [
            'access_token' => $token,
            'token_type' => $type,
            'expires_in' => $expired,
        ];
    }

    private function createAuthLogin(Request $request, User $user)
    {
        $this->userFirebaseRepository->login($user->id, $request->header('X-Firebase-Token'));

        $token = $user->createToken('auth_token', ['*'], Carbon::now()->addSeconds(config('sanctum.expiration') * 60))->plainTextToken;

        $user->load([
            'role',
        ]);

        return [
            'status' => null,
            'token' => $this->respondWithToken('bearer', $token, (int) config('sanctum.expiration')),
            'user' => new UserResource($user),
        ];
    }

    private function createAuth2FA(User $user, string $tokenIdPrefix = '')
    {
        $id = uniqid($tokenIdPrefix);

        $expiresTs = Carbon::now()
            ->addMinutes(config('env.user_2fa_token_expired'))
            ->timestamp;

        $token = encrypt("2fa:auth_token|{$id}|{$user->id}|$expiresTs");

        return [
            'status' => '2fa_required',
            'token' => $this->respondWithToken('api-key', $token, (int) config('env.user_2fa_token_expired')),
            'user' => null,
        ];
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/login",
     *   summary="User Authentication Login",
     *
     *   @OA\Parameter(
     *     name="x-firebase-token",
     *     in="header",
     *     description="Firebase ID token",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *       type="object",
     *       required={"username", "password"},
     *
     *       @OA\Property(property="username", type="string", example="administrator"),
     *       @OA\Property(property="password", type="string", example="123456"),
     *     )
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = $this->userRepository->findByUsername($request->username);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ResponseFormatter::error(400, 'Username atau password salah');
        }

        if (! $user->is_active) {
            return ResponseFormatter::error(400, 'Akun anda tidak aktif');
        }

        if (config('env.user_2fa_enabled')) {
            $twoFaCount = 0;

            if ($user->is_email_verified) {
                $twoFaCount++;
            }

            if ($user->is_google2fa_enabled) {
                $twoFaCount++;
            }

            if ($twoFaCount > 0) {
                return ResponseFormatter::success($this->createAuth2FA($user));
            }
        }

        return ResponseFormatter::success($this->createAuthLogin($request, $user), 'Login berhasil');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/logout",
     *   summary="Auth logout",
     *
     *   @OA\Parameter(
     *     name="x-firebase-token",
     *     in="header",
     *     description="Firebase ID token",
     *
     *     @OA\Schema(type="string")
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        $this->userFirebaseRepository->logout($user->id, $request->header('x-firebase-token'));
        $user->currentAccessToken()->delete();

        return ResponseFormatter::success(null, 'Logout berhasil');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Auth"},
     *   path="/api/auth/me",
     *   summary="Auth get me",
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user();

        $user->load('role');

        return ResponseFormatter::success(new UserResource($user), 'Data profile berhasil ditampilkan');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Auth"},
     *   path="/api/auth/change-password",
     *   summary="Auth change password",
     *
     *   @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *       type="object",
     *       required={"current_password", "new_password", "new_password_confirmation"},
     *
     *       @OA\Property(property="current_password", type="string"),
     *       @OA\Property(property="new_password", type="string"),
     *       @OA\Property(property="new_password_confirmation", type="string")
     *     )
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'different:current_password', 'confirmed', new SecurePasswordRule],
            'new_password_confirmation' => 'required',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return ResponseFormatter::error(400, 'Password sekarang yang Anda berikan salah');
        }

        // Tidak boleh menggunakan katasandi yang sama seperti sblmnya
        if (Hash::check($request->new_password, $user->password)) {
            return ResponseFormatter::error(400, 'Password tidak boleh sama dengan yang sebelumnya');
        }

        $this->userRepository->updatePassword($user, $request->new_password);

        return ResponseFormatter::success(null, 'Data password berhasil diperbarui');
    }

    /**
     * @OA\Put(
     *   tags={"Api|Auth"},
     *   path="/api/auth/me",
     *   summary="Auth update me",
     *
     *   @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *       type="object",
     *       required={"name"},
     *
     *       @OA\Property(property="name", type="string"),
     *     )
     *   ),
     *
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|max:255|regex:/^[a-zA-Z0-9.,\s\-]+$/',
        ]);

        $this->userRepository->update($user, [
            'name' => e($request->name),
        ]);

        return ResponseFormatter::success(new UserResource($user), 'Data berhasil diperbarui');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Auth"},
     *   path="/api/auth/account-security-level",
     *   summary="Get Account Security Level Information",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function accountSecurityLevel(Request $request)
    {
        $user = $request->user();

        $levels = [
            1 => [
                'level' => 1,
                'name' => 'Weak',
                'description' => 'Akun hanya menggunakan password, email belum diverifikasi, dan tidak ada proteksi tambahan.',
            ],
            2 => [
                'level' => 2,
                'name' => 'Medium',
                'description' => 'Email sudah diverifikasi, namun 2FA belum diaktifkan.',
            ],
            3 => [
                'level' => 3,
                'name' => 'Strong',
                'description' => 'Email sudah diverifikasi dan 2FA aktif (OTP via email atau Google Authenticator).',
            ],
        ];

        $index = 1; // Weak (default)

        // cek apakah email user diverifikasi
        if ($user->is_email_verified) {
            $index = 2; // Medium
        }

        // cek apakah 2fa diaktifkan (berdasarkan .env)
        if (config('env.user_2fa_enabled')) {
            // flag: otp email atau google authenticator
            if ($user->is_email_verified || $user->is_google2fa_enabled) {
                $index = 3; // Strong
            }
        }

        return ResponseFormatter::success(
            $levels[$index],
            'Data tingkat keamanan akun berhasil ditampilkan'
        );
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/add-email",
     *   summary="Add and Verify Email for 2FA",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function addEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($user->is_email_verified) {
            return ResponseFormatter::error(400, 'Email sudah diverifikasi');
        }

        try {
            DB::beginTransaction();

            $user->email = $request->input('email');
            $user->email_verified_at = null;
            $user->save();

            // generate signed URL untuk verifikasi (berlaku 60 menit)
            $link = URL::temporarySignedRoute(
                'api.auth.verify_email',
                Carbon::now()->addMinutes(60),
                ['id' => $user->id_hash, 'hash' => sha1($user->email)]
            );

            Mail::to($user->email)->send(new VerifyEmailMail($user->name, $link));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return ResponseFormatter::error(400, 'Gagal menambahkan email');
        }

        return ResponseFormatter::success(null, 'Email berhasil ditambahkan. Silakan cek email untuk verifikasi.');
    }

    /**
     * Email Verify
     *
     * Menambahkan dan Verifikasi Email
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailVerify(Request $request, $id, $hash)
    {
        $user = \App\Models\User::firstWhere('id_hash', $id);

        if (! $user) {
            abort(404, 'User tidak ditemukan');
        }

        if (! hash_equals($hash, sha1($user->email))) {
            abort(400, 'Link verifikasi tidak valid.');
        }

        if (! $user->is_email_verified) {
            $user->email_verified_at = now();
            $user->save();
        }

        return view('auth.verify_email_verified');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/enable-google2fa",
     *   summary="Enable Google Authenticator (Google 2FA)",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function enableGoogle2fa(Request $request)
    {
        $user = $request->user();

        if ($user->is_google2fa_enabled) {
            return ResponseFormatter::error(400, 'Akun sudah terhubung dengan Google Authenticator');
        }

        try {
            DB::beginTransaction();

            $google2fa = new Google2FA;
            $secret = $google2fa->generateSecretKey();

            $user->google2fa_secret = $secret;
            $user->save();

            $qrcodeUrl = $google2fa->getQRCodeUrl(
                $company = config('app.name'),
                $holder = $user->name,
                $secret
            );

            // for test purpose
            // $qrcodeImage = QrCode::size(200)->generate($qrcodeUrl);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return ResponseFormatter::error(400, 'Gagal menambahkan email');
        }

        // for test purpose
        // return response()->make("
        //     <div style='text-align:center;'>
        //         {$qrcodeImage}
        //         <p>Atau masukkan kode manual: <strong>{$secret}</strong></p>
        //     </div>
        // ", 200, ['Content-Type' => 'text/html']);

        return ResponseFormatter::success([
            'company' => $company,
            'holder' => $holder,
            'qr_code_url' => $qrcodeUrl,
            'secret' => $secret,
        ], 'Google Authenticator berhasil ditambahkan');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Auth"},
     *   path="/api/auth/qrcode-url-google2fa",
     *   summary="Get QR Code URL for Google 2FA",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function qrcodeUrlGoogle2fa(Request $request)
    {
        $user = $request->user();

        if (! $user->is_google2fa_enabled) {
            return ResponseFormatter::error(400, 'Akun tidak terhubung dengan Google Authenticator');
        }

        $google2fa = new Google2FA;

        $secret = $user->google2fa_secret;

        $qrcodeUrl = $google2fa->getQRCodeUrl(
            $company = config('app.name'),
            $holder = $user->name,
            $secret
        );

        // for test purpose
        // $qrcodeImage = QrCode::size(200)->generate($qrcodeUrl);

        // for test purpose
        // return response()->make("
        //     <div style='text-align:center;'>
        //         {$qrcodeImage}
        //         <p>Atau masukkan kode manual: <strong>{$secret}</strong></p>
        //     </div>
        // ", 200, ['Content-Type' => 'text/html']);

        return ResponseFormatter::success([
            'company' => $company,
            'holder' => $holder,
            'qr_code_url' => $qrcodeUrl,
            'secret' => $secret,
        ], 'Google Authenticator berhasil ditampilkan');
    }

    /**
     * @OA\Get(
     *   tags={"Api|Auth"},
     *   path="/api/auth/2fa",
     *   summary="Get available 2FA verification methods",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function method2fa(Request $request)
    {
        $user = $request->user();

        $data = [];

        if ($user->is_email_verified) {
            $data[] = [
                'type' => 'email',
                'name' => 'Email',
                'value' => mask_string($user->email, 2, 2, '*', 'email'),
            ];
        }

        if ($user->is_google2fa_enabled) {
            $data[] = [
                'type' => 'google',
                'name' => 'Google Authenticator',
                'value' => null,
            ];
        }

        return ResponseFormatter::success($data, '2FA metode verifikasi berhasil ditampilkan');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/2fa/challenge",
     *   summary="Send OTP challenge (Email)",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function challenge2fa(Request $request)
    {
        $user = $request->user();

        if (! $user->is_email_verified) {
            return ResponseFormatter::error(400, 'Email anda belum diverifikasi.');
        }

        try {
            DB::beginTransaction();

            UserOtp::query()
                ->where('user_id', $user->id)
                ->where('token_id', $user->two_fa_token_id)
                ->where('type', '2fa:challenge:email')
                ->whereNull('used_at')
                ->delete();

            $expiredAt = Carbon::now()
                ->addMinutes((int) config('env.user_2fa_otp_expired'))
                ->toDateTimeString();

            UserOtp::create([
                'user_id' => $user->id,
                'token_id' => $user->two_fa_token_id,
                'type' => '2fa:challenge:email',
                'code' => $otp = random_int(100000, 999999),
                'expired_at' => $expiredAt,
            ]);

            Mail::to($user->email)->send(new OtpMail($otp));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return ResponseFormatter::error(null, $th->getMessage());

            return ResponseFormatter::error(null, 'Gagal mengirim OTP');
        }

        return ResponseFormatter::success(null, 'Berhasil mengirim OTP');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/2fa/verify",
     *   summary="Verify OTP code (Email or Google)",
     *   security={{"authBearerToken":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"type", "otp"},
     *       @OA\Property(property="type", type="string", enum={"email", "google"}),
     *       @OA\Property(property="otp", type="string", example="123456")
     *     )
     *   ),
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function verify2fa(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,google',
            'otp' => 'required',
        ]);

        $user = $request->user();

        $now = Carbon::now()->toDateTimeString();

        switch ($request->input('type')) {
            case 'email':
                $userOtp = UserOtp::query()
                    ->where('user_id', $user->id)
                    ->where('token_id', $user->two_fa_token_id)
                    ->where('type', '2fa:challenge:email')
                    ->where('code', $request->input('otp'))
                    ->first();

                if (! $userOtp) {
                    return ResponseFormatter::error(400, 'Kode OTP tidak valid');
                }

                if (Carbon::parse($userOtp->expired_at)->isPast() || $userOtp->used_at) {
                    return ResponseFormatter::error(400, 'Kode OTP tidak valid');
                }

                $userOtp->used_at = $now;
                $userOtp->save();
                break;

            case 'google':
                try {
                    DB::beginTransaction();

                    UserOtp::create([
                        'user_id' => $user->id,
                        'token_id' => $user->two_fa_token_id,
                        'type' => '2fa:challenge:google',
                        'code' => $request->otp,
                        'used_at' => $now,
                        'expired_at' => $now,
                    ]);

                    $google2fa = new Google2FA;

                    $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('otp'));

                    if (! $valid) {
                        throw new \Error('Gagal verifikasi Google Authenticator');
                    }

                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return ResponseFormatter::error(400, 'Kode OTP tidak valid');
                }
                break;

            default:
                // code...
                break;
        }

        return ResponseFormatter::success($this->createAuthLogin($request, $user), 'Verifikasi OTP berhasil');
    }

    /**
     * @OA\Post(
     *   tags={"Api|Auth"},
     *   path="/api/auth/generate-telegram-code",
     *   summary="Generate linking code for Telegram bot",
     *   security={{"authBearerToken":{}}},
     *   @OA\Response(response="default", ref="#/components/responses/globalResponse")
     * )
     */
    public function generateTelegramLinkCode(Request $request)
    {
        $user = $request->user();
        
        if ($user->telegram_id) {
            return ResponseFormatter::error(400, 'Akun sudah terhubung dengan Telegram');
        }

        $code = \Illuminate\Support\Str::random(10);
        $user->update(['telegram_link_code' => $code]);

        return ResponseFormatter::success([
            'link_code' => $code,
            'bot_username' => config('services.telegram.bot_username', 'SmartFinanceBot'),
        ], 'Kode penghubung berhasil dibuat');
    }
}
