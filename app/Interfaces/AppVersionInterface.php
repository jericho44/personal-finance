<?php

namespace App\Interfaces;

interface AppVersionInterface
{
    /**
     * Get all records.
     *
     * @param  array  $select
     * @param  array  $withRelations
     * @param  array  $join
     * @param  array  $filter
     * @param  \Closure|null  $where
     * @param  string|null  $search
     * @param  array  $sortOption
     * @param  array  $paginateOption
     * @param  \Closure|null  $reformat
     * @return \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        $select = [],
        $withRelations = [],
        $join = [],
        $filter = [],
        $where = null,
        $search = null,
        $sortOption = [],
        $paginateOption = [],
        $reformat = null
    );

    /**
     * Find record by ID.
     *
     * @param  int  $id
     * @param  array  $withRelations
     * @return \App\Models\AppVersion|null
     */
    public function findById($id, $withRelations = []);

    /**
     * Find record by hashed ID.
     *
     * @param  string  $id
     * @param  array  $withRelations
     * @return \App\Models\AppVersion|null
     */
    public function findByIdHash($id, $withRelations = []);

    /**
     * Create new record.
     *
     * @param  array  $data
     * @return \App\Models\AppVersion
     */
    public function create($data);

    /**
     * Update record.
     *
     * @param  \App\Models\AppVersion|string  $id
     * @param  array  $data
     * @return \App\Models\AppVersion
     */
    public function update($id, $data);

    /**
     * Delete record.
     *
     * @param  \App\Models\AppVersion|string  $id
     * @return \App\Models\AppVersion
     */
    public function delete($id);

    /**
     * Check record code.
     *
     * @param  string  $code
     * @param  string  $appType
     * @param  int|null  $excepId
     * @return \App\Models\AppVersion
     */
    public function checkCode($code, $appType, $exceptId = null);
}
