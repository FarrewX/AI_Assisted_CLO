<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\EmailForSend;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void 
    {
        if (Schema::hasTable('email_for_send')) {
            $emailConfig = EmailForSend::latest()->first();

            if ($emailConfig && $emailConfig->mail_password) {
                try {
                    $mailPassword = Crypt::decryptString(trim($emailConfig->mail_password));

                    Config::set('mail.mailers.smtp.username', $emailConfig->mail_username);
                    Config::set('mail.mailers.smtp.password', $mailPassword);
                    Config::set('mail.from.address', $emailConfig->mail_username);

                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    // รหัสผ่านไม่สามารถถอดรหัสได้
                    Log::error('Failed to decrypt email password: ' . $e->getMessage());
                }
            }
        }
    }
}
