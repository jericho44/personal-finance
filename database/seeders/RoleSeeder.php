<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $data = [
                [
                    'id' => 1,
                    'name' => 'Developer',
                    'slug' => 'developer',
                    'is_active' => true,
                ],
                [
                    'id' => 2,
                    'name' => 'Administrator',
                    'slug' => 'admin',
                    'is_active' => true,
                ],
                [
                    'id' => 3,
                    'name' => 'User',
                    'slug' => 'user',
                    'is_active' => true,
                ],
            ];

            $now = Carbon::now()->toDateTimeString();

            foreach ($data as $key => $value) {
                Role::updateOrInsert(['id' => $value['id']], [
                    'id' => $value['id'],
                    'id_hash' => Str::orderedUuid()->toString(),
                    'name' => $value['name'],
                    'slug' => $value['slug'],
                    'is_active' => $value['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $lastId = Role::orderBy('id', 'desc')->first();
            if (! empty($lastId)) {
                $newLastId = $lastId->id + 1;
                DB::statement("ALTER SEQUENCE roles_id_seq RESTART WITH {$newLastId}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
