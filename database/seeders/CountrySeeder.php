<?php

namespace Database\Seeders;

use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                'id' => 1,
                'code' => 'AF',
                'name' => 'Afghanistan',
            ],
            [
                'id' => 2,
                'code' => 'AM',
                'name' => 'Armenia',
            ],
            [
                'id' => 3,
                'code' => 'AZ',
                'name' => 'Azerbaijan',
            ],
            [
                'id' => 4,
                'code' => 'BH',
                'name' => 'Bahrain',
            ],
            [
                'id' => 5,
                'code' => 'BD',
                'name' => 'Bangladesh',
            ],
            [
                'id' => 6,
                'code' => 'BT',
                'name' => 'Bhutan',
            ],
            [
                'id' => 7,
                'code' => 'BN',
                'name' => 'Brunei',
            ],
            [
                'id' => 8,
                'code' => 'KH',
                'name' => 'Cambodia',
            ],
            [
                'id' => 9,
                'code' => 'CN',
                'name' => 'China',
            ],
            [
                'id' => 10,
                'code' => 'CY',
                'name' => 'Cyprus',
            ],
            [
                'id' => 11,
                'code' => 'GE',
                'name' => 'Georgia',
            ],
            [
                'id' => 12,
                'code' => 'IN',
                'name' => 'India',
            ],
            [
                'id' => 13,
                'code' => 'ID',
                'name' => 'Indonesia',
            ],
            [
                'id' => 14,
                'code' => 'IR',
                'name' => 'Iran',
            ],
            [
                'id' => 15,
                'code' => 'IQ',
                'name' => 'Iraq',
            ],
            [
                'id' => 16,
                'code' => 'IL',
                'name' => 'Israel',
            ],
            [
                'id' => 17,
                'code' => 'JP',
                'name' => 'Japan',
            ],
            [
                'id' => 18,
                'code' => 'JO',
                'name' => 'Jordan',
            ],
            [
                'id' => 19,
                'code' => 'KZ',
                'name' => 'Kazakhstan',
            ],
            [
                'id' => 20,
                'code' => 'KW',
                'name' => 'Kuwait',
            ],
            [
                'id' => 21,
                'code' => 'KG',
                'name' => 'Kyrgyzstan',
            ],
            [
                'id' => 22,
                'code' => 'LA',
                'name' => 'Laos',
            ],
            [
                'id' => 23,
                'code' => 'LB',
                'name' => 'Lebanon',
            ],
            [
                'id' => 24,
                'code' => 'MY',
                'name' => 'Malaysia',
            ],
            [
                'id' => 25,
                'code' => 'MV',
                'name' => 'Maldives',
            ],
            [
                'id' => 26,
                'code' => 'MN',
                'name' => 'Mongolia',
            ],
            [
                'id' => 27,
                'code' => 'MM',
                'name' => 'Myanmar',
            ],
            [
                'id' => 28,
                'code' => 'NP',
                'name' => 'Nepal',
            ],
            [
                'id' => 29,
                'code' => 'KP',
                'name' => 'North Korea',
            ],
            [
                'id' => 30,
                'code' => 'OM',
                'name' => 'Oman',
            ],
            [
                'id' => 31,
                'code' => 'PK',
                'name' => 'Pakistan',
            ],
            [
                'id' => 32,
                'code' => 'PS',
                'name' => 'Palestine',
            ],
            [
                'id' => 33,
                'code' => 'PH',
                'name' => 'Philippines',
            ],
            [
                'id' => 34,
                'code' => 'QA',
                'name' => 'Qatar',
            ],
            [
                'id' => 35,
                'code' => 'SA',
                'name' => 'Saudi Arabia',
            ],
            [
                'id' => 36,
                'code' => 'SG',
                'name' => 'Singapore',
            ],
            [
                'id' => 37,
                'code' => 'KR',
                'name' => 'South Korea',
            ],
            [
                'id' => 38,
                'code' => 'LK',
                'name' => 'Sri Lanka',
            ],
            [
                'id' => 39,
                'code' => 'SY',
                'name' => 'Syria',
            ],
            [
                'id' => 40,
                'code' => 'TW',
                'name' => 'Taiwan',
            ],
            [
                'id' => 41,
                'code' => 'TJ',
                'name' => 'Tajikistan',
            ],
            [
                'id' => 42,
                'code' => 'TH',
                'name' => 'Thailand',
            ],
            [
                'id' => 43,
                'code' => 'TL',
                'name' => 'Timor-Leste',
            ],
            [
                'id' => 44,
                'code' => 'TR',
                'name' => 'Turkey',
            ],
            [
                'id' => 45,
                'code' => 'TM',
                'name' => 'Turkmenistan',
            ],
            [
                'id' => 46,
                'code' => 'AE',
                'name' => 'United Arab Emirates',
            ],
            [
                'id' => 47,
                'code' => 'UZ',
                'name' => 'Uzbekistan',
            ],
            [
                'id' => 48,
                'code' => 'VN',
                'name' => 'Vietnam',
            ],
            [
                'id' => 49,
                'code' => 'YE',
                'name' => 'Yemen',
            ],
        ];

        try {
            DB::beginTransaction();

            $now = Carbon::now()->toDateTimeString();

            foreach ($countries as $key => $value) {
                Country::updateOrInsert(['id' => $value['id']], [
                    'code' => $value['code'],
                    'name' => $value['name'],
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $lastId = Country::orderBy('id', 'desc')->first();
            if (! empty($lastId)) {
                $newLastId = $lastId->id + 1;
                DB::statement("ALTER SEQUENCE countries_id_seq RESTART WITH {$newLastId}");
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            echo $th->getMessage();
        }
    }
}
