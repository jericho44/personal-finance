<?php

namespace App\Interfaces;

interface UserFirebaseInterface
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
     * Find resource by ID.
     *
     * @param  int  $id
     * @param  array  $withRelations
     * @return \App\Models\UserFirebase|null
     */
    public function findById($id, $withRelations = []);

    /**
     * Find resource by hashed ID.
     *
     * @param  string  $id
     * @param  array  $withRelations
     * @return \App\Models\UserFirebase|null
     */
    public function findByIdHash($id, $withRelations = []);

    /**
     * Create new resouce.
     *
     * @param  array  $data
     * @return \App\Models\UserFirebase
     */
    public function create($data);

    /**
     * Handle token login.
     *
     * @param  int  $userId
     * @param  string|null  $token
     * @return void
     */
    public function login($userId, $token);

    /**
     * Handle token logout.
     *
     * @param  int  $userId
     * @param  string|null  $token
     * @return void
     */
    public function logout($userId, $token);
}
