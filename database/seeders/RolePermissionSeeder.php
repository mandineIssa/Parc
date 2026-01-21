<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolePermissionSeeder extends Seeder
{

public function run()
{
    // Créer les rôles
    $adminRole = Role::create(['name' => 'admin']);
    $managerRole = Role::create(['name' => 'manager']);
    $userRole = Role::create(['name' => 'user']);
    
    // Créer les permissions
    $permissions = [
        'view_users', 'create_users', 'edit_users', 'delete_users',
        'view_parcs', 'create_parcs', 'edit_parcs', 'delete_parcs',
    ];
    
    foreach ($permissions as $perm) {
        Permission::create(['name' => $perm]);
    }
    
    // Assigner des permissions aux rôles
    $adminRole->givePermissionTo(Permission::all());
    $managerRole->givePermissionTo(['view_parcs', 'create_parcs', 'edit_parcs']);
    
    // Créer un admin par défaut
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@parc.com',
        'password' => bcrypt('password123'),
    ]);
    
    $admin->assignRole('admin');
}
}


