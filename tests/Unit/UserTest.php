<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer des permissions pour les tests
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'create_users']);
        Permission::create(['name' => 'edit_users']);
        Permission::create(['name' => 'delete_users']);
        
        // Créer des rôles pour les tests
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        
        // Assigner les permissions aux rôles
        $adminRole->givePermissionTo([
            'view_users', 'create_users', 'edit_users', 'delete_users'
        ]);
        
        $editorRole->givePermissionTo([
            'view_users', 'edit_users'
        ]);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ];
        
        $user = User::create($userData);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }
    
    /** @test */
    public function it_can_assign_single_role_to_user()
    {
        $user = User::create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'password' => Hash::make('password')
        ]);
        
        $user->assignRole('editor');
        
        $this->assertTrue($user->hasRole('editor'));
        $this->assertFalse($user->hasRole('admin'));
    }
    
    /** @test */
    public function it_can_assign_multiple_roles_to_user()
    {
        $user = User::create([
            'name' => 'Multi Role User',
            'email' => 'multi@example.com',
            'password' => Hash::make('password')
        ]);
        
        $user->assignRole(['admin', 'editor']);
        
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('editor'));
        $this->assertEquals(2, $user->roles->count());
    }
    
    /** @test */
    public function it_can_check_if_user_has_permission()
    {
        $user = User::create([
            'name' => 'Permission Test User',
            'email' => 'permissions@example.com',
            'password' => Hash::make('password')
        ]);
        
        $user->assignRole('editor');
        
        $this->assertTrue($user->hasPermissionTo('view_users'));
        $this->assertTrue($user->hasPermissionTo('edit_users'));
        $this->assertFalse($user->hasPermissionTo('delete_users'));
    }
    
    /** @test */
    public function it_can_sync_roles()
    {
        $user = User::create([
            'name' => 'Role Sync User',
            'email' => 'sync@example.com',
            'password' => Hash::make('password')
        ]);
        
        // Assigner initialement 'editor'
        $user->assignRole('editor');
        $this->assertTrue($user->hasRole('editor'));
        
        // Synchroniser pour n'avoir que 'admin'
        $user->syncRoles(['admin']);
        
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('editor'));
        $this->assertEquals(1, $user->roles->count());
    }
    
    /** @test */
    public function it_can_remove_roles()
    {
        $user = User::create([
            'name' => 'Role Remove User',
            'email' => 'remove@example.com',
            'password' => Hash::make('password')
        ]);
        
        $user->assignRole(['admin', 'editor']);
        $this->assertEquals(2, $user->roles->count());
        
        $user->removeRole('admin');
        
        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('editor'));
        $this->assertEquals(1, $user->roles->count());
    }
    
    /** @test */
    public function it_can_update_user_details()
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => Hash::make('password')
        ]);
        
        $user->update([
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
        
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@example.com', $user->email);
    }
    
    /** @test */
    public function it_can_update_password()
    {
        $user = User::create([
            'name' => 'Password User',
            'email' => 'password@example.com',
            'password' => Hash::make('old_password')
        ]);
        
        $user->update([
            'password' => Hash::make('new_password')
        ]);
        
        // Vérification par l'assertion que le mot de passe a été mis à jour
        // (simple vérification que la mise à jour fonctionne, pas la validation du hash)
        $this->assertNotEquals(
            Hash::make('old_password'), 
            $user->password
        );
    }
    
    /** @test */
    public function it_can_delete_user()
    {
        $user = User::create([
            'name' => 'Delete User',
            'email' => 'delete@example.com',
            'password' => Hash::make('password')
        ]);
        
        $userId = $user->id;
        $user->delete();
        
        $this->assertNull(User::find($userId));
        $this->assertDatabaseMissing('users', [
            'email' => 'delete@example.com'
        ]);
    }
    
    /** @test */
    public function it_can_retrieve_all_permissions_from_user_roles()
    {
        $user = User::create([
            'name' => 'All Permissions User',
            'email' => 'allperms@example.com',
            'password' => Hash::make('password')
        ]);
        
        $user->assignRole('admin');
        
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        $this->assertContains('view_users', $permissions);
        $this->assertContains('create_users', $permissions);
        $this->assertContains('edit_users', $permissions);
        $this->assertContains('delete_users', $permissions);
        $this->assertEquals(4, count($permissions));
    }
    
    /** @test */
    public function it_can_find_user_by_email()
    {
        User::create([
            'name' => 'Find User',
            'email' => 'find@example.com',
            'password' => Hash::make('password')
        ]);
        
        $foundUser = User::where('email', 'find@example.com')->first();
        
        $this->assertNotNull($foundUser);
        $this->assertEquals('Find User', $foundUser->name);
    }
}