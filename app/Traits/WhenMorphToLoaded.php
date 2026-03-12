<?php

namespace App\Traits;

trait WhenMorphToLoaded
{
    public function whenMorphToLoaded($name, $map)
    {
        return $this->whenLoaded($name, function () use ($name, $map) {
            $morphType = $name.'_type';
            $morphAlias = $this->resource->$morphType;
            $morphResourceClass = $map[$morphAlias];

            return new $morphResourceClass($this->resource->$name);
        });
    }

    public function whenMorphManyToLoaded($name, $map)
    {
        return $this->whenLoaded($name, function () use ($name, $map) {
            $morphType = $name.'_type';
            $morphAlias = $this->resource->$morphType;
            $morphResourceClass = $map[$morphAlias];

            return $morphResourceClass::collection($this->resource->$name);
        });
    }
}
