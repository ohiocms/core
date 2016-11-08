<?php

use Illuminate\Database\Seeder;

use Ohio\Core\User\User;
use Ohio\Core\Role\Role;
use Ohio\Core\UserRole\UserRole;

class OhioCoreUserSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superUser = User::firstOrCreate([
            'first_name' => 'SUPER',
            'last_name' => 'ADMIN',
            'email' => 'super@ohiocms.org',
        ]);

        $superUser->update(['password' => bcrypt('secret')]);

        $adminRole = Role::whereName('SUPER')->first();

        UserRole::firstOrCreate([
            'user_id' => $superUser->id,
            'role_id' => $adminRole->id,
        ]);

        factory(User::class, 100)->create()->each(function ($user) {
            //$user->posts()->save(factory(App\Post::class)->make());
        });
    }
}