<?php

namespace App\Interfaces;

interface UserInterface
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
     * @return \App\Models\User|null
     */
    public function findById($id, $withRelations = []);

    /**
     * Find record by hashed ID.
     *
     * @param  string  $id
     * @param  array  $withRelations
     * @return \App\Models\User|null
     */
    public function findByIdHash($id, $withRelations = []);

    /**
     * Find record by username.
     *
     * @param  string  $username
     * @param  array  $withRelations
     * @return \App\Models\User|null
     */
    public function findByUsername($username, $withRelations = []);

    /**
     * Find record by email.
     *
     * @param  string  $email
     * @param  array  $withRelations
     * @return \App\Models\User|null
     */
    public function findByEmail($email, $withRelations = []);

    /**
     * Create new record.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create($data);

    /**
     * Update record.
     *
     * @param  \App\Models\User|string  $id
     * @param  array  $data
     * @return \App\Models\User
     */
    public function update($id, $data);

    /**
     * Toggle record status.
     *
     * @param  \App\Models\User|string  $id
     * @return \App\Models\User
     */
    public function switchStatus($id);

    /**
     * Update record password.
     *
     * @param  \App\Models\User|string  $id
     * @return \App\Models\User
     */
    public function updatePassword($id, $password);

    /**
     * Reset record password.
     *
     * @param  \App\Models\User|string  $id
     * @return \App\Models\User
     */
    public function resetPassword($id);
}
