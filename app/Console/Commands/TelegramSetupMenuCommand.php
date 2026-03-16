<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TelegramSetupMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Telegram bot command menu (the blue button menu)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = config('services.telegram.bot_token');
        if (!$token) {
            $this->error('TELEGRAM_BOT_TOKEN not found in .env');
            return;
        }

        $commands = [
            ['command' => 'start', 'description' => '⚠️ PENTING! Panduan BOT'],
            ['command' => 'manual', 'description' => '📝 Catat Transaksi Manual'],
            ['command' => 'insight', 'description' => '📊 AI Financial Insight'],
            ['command' => 'menu', 'description' => '🛠️ Menu Utama'],
            ['command' => 'help', 'description' => '❓ Bantuan & Cara Pakai'],
            ['command' => 'cancel', 'description' => '❌ Batalkan Input'],
        ];

        $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/setMyCommands", [
            'commands' => $commands
        ]);

        if ($response->successful()) {
            $this->info('✅ Telegram Bot Menu updated successfully!');
        } else {
            $this->error('❌ Failed to update menu: ' . $response->body());
        }
    }
}
