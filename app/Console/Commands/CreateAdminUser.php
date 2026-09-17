<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
 * Membuat akun pengelola panel.
 *
 * Kata sandi sengaja tidak diminta lewat prompt interaktif, karena perintah ini
 * sering dijalankan lewat SSH non-interaktif. Bila tidak diisi, sandi acak
 * dibuatkan dan ditampilkan sekali saja.
 */
class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {email : Alamat email untuk masuk}
                            {--name= : Nama yang ditampilkan}
                            {--password= : Kata sandi; kosongkan untuk dibuatkan acak}';

    protected $description = 'Buat atau perbarui akun pengelola panel admin';

    public function handle()
    {
        $email = strtolower(trim($this->argument('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Alamat email tidak valid.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: Str::password(16, true, true, false);

        if (strlen($password) < 12) {
            $this->error('Kata sandi minimal 12 karakter.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $this->option('name') ?: Str::before($email, '@'),
                'password' => Hash::make($password),
            ]
        );

        $this->info($user->wasRecentlyCreated ? 'Akun dibuat.' : 'Kata sandi akun diperbarui.');
        $this->newLine();
        $this->line('  email : '.$user->email);

        if (! $this->option('password')) {
            $this->line('  sandi : '.$password);
            $this->newLine();
            $this->warn('Sandi ini hanya ditampilkan sekali. Simpan sekarang.');
        }

        return self::SUCCESS;
    }
}
