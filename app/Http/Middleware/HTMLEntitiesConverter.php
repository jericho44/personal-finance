<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HTMLEntitiesConverter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $filteredData = $this->htmlFilter($request->all());
        $request->replace($filteredData);

        return $next($request);
    }

    public function htmlFilter($request)
    {

        $result = [];
        if (is_array($request)) {
            foreach ($request as $key => $value) {
                $filtered_value = $this->htmlFilter($value);
                $result[$key] = $filtered_value;
            }
        } elseif (is_string($request)) {
            $result = e($request);
        } else {
            $result = $request;
        }

        return $result;
    }
}
