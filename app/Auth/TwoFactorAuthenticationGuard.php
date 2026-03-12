<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class TwoFactorAuthenticationGuard implements Guard
{
    protected $user;

    public function __construct(
        protected UserProvider $provider,
        protected Request $request
    ) {}

    public function check()
    {
        return ! is_null($this->user());
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        $token = $this->request->header('X-2FA-Token');

        try {
            if (! $token) {
                throw new \Error('2FA Token tidak ditemukan');
            }

            $decoded = decrypt($token);
            $parts = explode('|', $decoded);

            if (count($parts) < 4 || $parts[0] !== '2fa:auth_token') {
                throw new \Error('Struktur token tidak valid');
            }

            [$name, $tokenId, $userId, $expiresTs] = $parts;

            $user = $this->provider->retrieveById($userId);
            if (! $user) {
                throw new \Error('User tidak ditemukan');
            }

            $user->two_fa_token_id = $tokenId;
            $user->two_fa_token_expires_ts = (int) $expiresTs;

            if (\Carbon\Carbon::createFromTimestamp($expiresTs)->isPast()) {
                throw new \Error('Token kadaluarsa');
            }

            $this->user = $user;
        } catch (\Throwable $th) {
            return null;
        }

        return $this->user;
    }

    public function id()
    {
        return $this->user ? $this->user->getAuthIdentifier() : null;
    }

    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

            return true;
        }

        return false;
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }

    public function hasUser()
    {
        return ! is_null($this->user);
    }
}
