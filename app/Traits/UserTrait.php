<?php

namespace App\Traits;

trait UserTrait
{
    /**
     * Periksa apakah pengguna memiliki salah satu dari role yang diberikan.
     *
     * @param  string  $slug
     * @return bool
     */
    public function hasRole($slug)
    {
        if (! $this->role) {
            return false;
        }

        $slugs = is_array($slug) ? $slug : explode(',', preg_replace('/\s+/', '', $slug));

        foreach ($slugs as $slug) {
            if ($this->role->slug == $slug) {
                return true;
            }
        }

        return false;
    }
}
