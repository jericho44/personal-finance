<?php

namespace App\Helpers;

class Query
{
    public static function getQueryWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            if (is_bool($binding)) {
                $convertBool = $binding ? 'true' : 'false';

                return "{$convertBool}::bool";
            } elseif (is_numeric($binding)) {
                return $binding;
            } else {
                return "'{$binding}'";
            }
        })->toArray());
    }
}
