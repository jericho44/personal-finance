<?php

namespace App\Interfaces;

interface CategoryInterface
{
    public function getAll(int $userId);
    public function findByIdHash($idHash, $withRelations = [], int $userId = null);
    public function create($data, $userId = null);
    public function update($idHash, $data, $userId = null);
    public function delete($idHash, $userId = null);
}
