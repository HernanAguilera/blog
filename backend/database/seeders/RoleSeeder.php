<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\src\Domain\User\ValueObjects\UserRole;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = bcrypt('password123');

        // Super Admin (disabled by default as per requirements)
        User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@blogv2.local',
            'password' => $password,
            'role' => UserRole::SUPER_ADMIN->value,
            'is_active' => false, // Disabled by default
            'email_verified_at' => now(),
        ]);

        // Admin
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@blogv2.local',
            'password' => $password,
            'role' => UserRole::ADMIN->value,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Collaborator
        User::factory()->create([
            'name' => 'Colaborador Demo',
            'email' => 'colaborador@blogv2.local',
            'password' => $password,
            'role' => UserRole::COLLABORATOR->value,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Guest (regular user)
        User::factory()->create([
            'name' => 'Usuario Invitado',
            'email' => 'guest@blogv2.local',
            'password' => $password,
            'role' => UserRole::GUEST->value,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Usuarios con roles creados:');
        $this->command->info('   - Super Admin: superadmin@blogv2.local (DESACTIVADO)');
        $this->command->info('   - Admin: admin@blogv2.local');
        $this->command->info('   - Colaborador: colaborador@blogv2.local');
        $this->command->info('   - Guest: guest@blogv2.local');
        $this->command->warn('⚠️  Contraseña para todos: password123');
    }
}
