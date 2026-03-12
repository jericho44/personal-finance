<?php

namespace App\Enums;

/**
 * @method static ?string getLabel($value) Mendapatkan label value
 * @method static array getValues() Mendapatkan semua nilai konstanta
 * @method static array getList($request = null) Mendapatkan list nilai dan label konstanta
 * @method static bool isFoo($value) Mengecek apakah value adalah FOO
 * @method static bool isBar($value) Mengecek apakah value adalah BAR
 * @method static bool isBaz($value) Mengecek apakah value adalah BAZ
 */
final class ExampleStatusEnum extends BaseEnum
{
    /** @Label('Foo') */
    public const FOO = 0;

    /** @Label('Bar') */
    public const BAR = 1;

    /** @Label('Baz') */
    public const BAZ = 2;
}
