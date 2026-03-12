<?php

namespace App\Enums;

use Illuminate\Http\Request;
use ReflectionClass;

class BaseEnum
{
    /**
     * Handle dynamic static method calls for "isX()" methods.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return bool|null
     */
    public static function __callStatic($name, $arguments)
    {
        if (strpos($name, 'is') === 0) {
            $constantName = strtoupper(substr($name, 2)); // Ambil nama setelah "is"

            if (defined("static::$constantName")) {
                return $arguments[0] == constant("static::$constantName");
            }
        }

        throw new \BadMethodCallException("Method $name does not exist.");
    }

    /**
     * Mendapatkan label berdasarkan nilai konstanta
     *
     * @param  mixed  $value  Nilai enum
     * @return string|null Label yang sesuai atau null jika tidak ditemukan
     */
    public static function getLabel($value): ?string
    {
        $reflection = new ReflectionClass(static::class);

        foreach ($reflection->getReflectionConstants() as $constant) {
            if ($value == $constant->getValue()) {
                $docComment = $constant->getDocComment();

                if ($docComment && preg_match("/@Label\('(.+)'\)/", $docComment, $matches)) {
                    return $matches[1];
                }
            }
        }

        return null;
    }

    /**
     * Mendapatkan semua nilai konstanta
     *
     * @return array
     */
    public static function getValues()
    {
        $reflection = new ReflectionClass(static::class);

        return array_values($reflection->getConstants());
    }

    /**
     * Mendapatkan list nilai dan label konstanta
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     */
    public static function getList($request = null)
    {
        $excludes = [];

        if ($request instanceof Request) {
            $excludes = (array) $request->input('exclude', []);
        }

        $items = array_map(function ($value) {
            return ['value' => $value, 'text' => static::getLabel($value)];
        }, static::getValues());

        if (! empty($excludes)) {
            return array_values(
                array_filter($items, function ($item) use ($excludes) {
                    return ! in_array($item['value'], $excludes);
                })
            );
        }

        return $items;
    }
}
