<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
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
                    'role_id' => 1,
                    'name' => 'Developer',
                    'username' => 'developer',
                    'email' => 'developer@example.com',
                    'is_active' => true,
                ],
                [
                    'id' => 2,
                    'role_id' => 2,
                    'name' => 'Administrator',
                    'username' => 'administrator',
                    'email' => 'administrator@example.com',
                    'is_active' => true,
                ],
                [
                    'id' => 3,
                    'role_id' => 3,
                    'name' => 'User',
                    'username' => 'user',
                    'email' => 'user@example.com',
                    'is_active' => true,
                ],
            ];

            $now = Carbon::now()->toDateTimeString();
            $password = Hash::make(config('myconfig.default_password'));

            foreach ($data as $key => $value) {
                User::updateOrInsert(['id' => $value['id']], [
                    'id' => $value['id'],
                    'id_hash' => Str::orderedUuid()->toString(),
                    'role_id' => $value['role_id'],
                    'name' => $value['name'],
                    'username' => $value['username'],
                    'email' => $value['email'],
                    'password' => $password,
                    'is_active' => $value['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $lastId = User::orderBy('id', 'desc')->first();
            if (! empty($lastId)) {
                $newLastId = $lastId->id + 1;
                DB::statement("ALTER SEQUENCE users_id_seq RESTART WITH {$newLastId}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
