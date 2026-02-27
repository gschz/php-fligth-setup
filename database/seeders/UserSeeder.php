<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class UserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->table('users')
            ->insert([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->saveData();
    }
}
