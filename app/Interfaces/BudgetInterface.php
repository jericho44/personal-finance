<?php

namespace App\Interfaces;

interface BudgetInterface
{
    public function getAll(int $userId, array $filters = []);
    public function findByIdHash($idHash, $withRelations = [], int $userId = null);
    public function create($data, $userId = null);
    public function update($idHash, $data, $userId = null);
    public function delete($idHash, $userId = null);
    public function getBudgetProgress(int $userId, string $period = 'current_month');
}
