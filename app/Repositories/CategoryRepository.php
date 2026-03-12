<?php

namespace App\Repositories;

use App\Models\Category;
use App\Interfaces\CategoryInterface;

class CategoryRepository extends BaseRepository implements CategoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $userId)
    {
        return $this->model->where('user_id', $userId)->latest()->get();
    }

    public function findByIdHash($idHash, $withRelations = [], int $userId = null)
    {
        $query = $this->model->where('id_hash', $idHash);
        if ($withRelations) {
            $query->with($withRelations);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->firstOrFail();
    }

    public function create($data, $userId = null)
    {
        if ($userId) {
            $data['user_id'] = $userId;
        }
        return parent::create($data);
    }

    public function update($idHash, $data, $userId = null)
    {
        $category = $this->findByIdHash($idHash, [], $userId);
        return parent::update($category, $data);
    }

    public function delete($idHash, $userId = null)
    {
        $category = $this->findByIdHash($idHash, [], $userId);
        return parent::delete($category);
    }
}
