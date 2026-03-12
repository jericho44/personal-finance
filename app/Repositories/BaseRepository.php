<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function resolveIdentity($value)
    {
        return is_object($value) ? $value : $this->findByIdHash($value);
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  array  $array
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function input($array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            if (! is_null($array[$key]) && $array[$key] !== '') {
                return $array[$key];
            }
        }

        return $default;
    }

    /**
     * Determine if the given input key is an empty string for "filled".
     *
     * @param  array  $array
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyString($array, $key)
    {
        $value = $this->input($array, $key);

        return ! is_bool($value) && ! is_array($value) && trim((string) $value) === '';
    }

    /**
     * Retrieve input as a boolean value.
     *
     * Returns true when value is "1", "true", "on", and "yes". Otherwise, returns false.
     *
     * @param  array  $array
     * @param  string  $key
     * @param  bool  $default
     * @return bool
     */
    public function boolean($array, $key, $default = false)
    {
        return filter_var($this->input($array, $key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  array  $array
     * @param  string|array  $key
     * @return bool
     */
    public function filled($array, $key)
    {
        $keys = is_array($key) ? $key : [$key];

        foreach ($keys as $value) {
            if ($this->isEmptyString($array, $value)) {
                return false;
            }
        }

        return true;
    }

    public function findById($id, $withRelations = [])
    {
        return $this->model
            ->with($withRelations)
            ->find($id);
    }

    public function findByIdHash($id, $withRelations = [])
    {
        return $this->model
            ->with($withRelations)
            ->where('id_hash', $id)
            ->first();
    }

    public function create($data)
    {
        $model = $this->model->newInstance();

        foreach ($data as $key => $value) {
            $model->$key = $value;
        }

        $model->save();

        return $model;
    }

    public function update($id, $data)
    {
        $model = $this->resolveIdentity($id);

        foreach ($data as $key => $value) {
            $model->$key = $value;
        }

        $model->save();

        return $model;
    }

    public function delete($id)
    {
        $model = $this->resolveIdentity($id);
        $model->delete();

        return $model;
    }
}
