<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuário de teste para acessar o painel em desenvolvimento.
 *
 * E-mail: igor@gmail.com · Senha: 12345
 *
 * A senha é fraca de propósito, então o seeder só roda nos ambientes
 * local e testing. Pode ser executado várias vezes: atualiza o usuário
 * em vez de duplicá-lo.
 */
class TestUserSeeder extends Seeder
{
    public const EMAIL = 'igor@gmail.com';

    public const PASSWORD = '12345';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('TestUserSeeder ignorado: só roda nos ambientes local e testing.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'Igor (teste)',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
            ],
        );
    }
}
