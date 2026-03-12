<?php

namespace App\Interfaces;

interface RoleInterface
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
     * @return \App\Models\Role|null
     */
    public function findById($id, $withRelations = []);

    /**
     * Find record by hashed ID.
     *
     * @param  string  $id
     * @param  array  $withRelations
     * @return \App\Models\Role|null
     */
    public function findByIdHash($id, $withRelations = []);

    /**
     * Create new record.
     *
     * @param  array  $data
     * @return \App\Models\Role
     */
    public function create($data);

    /**
     * Update record.
     *
     * @param  \App\Models\Role|string  $id
     * @param  array  $data
     * @return \App\Models\Role
     */
    public function update($id, $data);

    /**
     * Toggle record status.
     *
     * @param  \App\Models\Role|string  $id
     * @return \App\Models\Role
     */
    public function switchStatus($id);
}
