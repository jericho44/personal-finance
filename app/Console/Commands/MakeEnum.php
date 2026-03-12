<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Enum class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Enums/{$name}.php");

        if (file_exists($path)) {
            $this->error("Enum {$name} already exists!");

            return Command::FAILURE;
        }

        (new Filesystem)->ensureDirectoryExists(app_path('Enums'));

        $stub = <<<PHP
        <?php

        namespace App\Enums;

        /**
         * @method static ?string getLabel(\$value) Mendapatkan label value
         * @method static array getValues() Mendapatkan semua nilai konstanta
         * @method static array getList(\$request = null) Mendapatkan list nilai dan label konstanta
         * @method static bool isFoo(\$value) Mengecek apakah value adalah FOO
         * @method static bool isBar(\$value) Mengecek apakah value adalah BAR
         * @method static bool isBaz(\$value) Mengecek apakah value adalah BAZ
         */
        final class {$name} extends BaseEnum
        {
            /** @Label('Foo') */
            public const FOO = 0;

            /** @Label('Bar') */
            public const BAR = 1;

            /** @Label('Baz') */
            public const BAZ = 2;
        }
        PHP;

        file_put_contents($path, $stub);

        $this->info("Enum {$name} created successfully!");

        return Command::SUCCESS;
    }
}
