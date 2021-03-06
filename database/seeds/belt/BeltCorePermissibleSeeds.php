<?php

use Illuminate\Database\Seeder;

use Silber\Bouncer\BouncerFacade;
use Belt\Core\Ability;

class BeltCorePermissibleSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ability::unguard();
        $abilities = [
            '*' => '*',
            '' => [
                'admin-dashboard',
            ],
            'teams',
            'users',
        ];
        foreach ($abilities as $entity_type => $set) {
            if (is_numeric($entity_type)) {
                $entity_type = $set;
                $set = ['*', 'create', 'update', 'delete'];
            }
            $set = is_array($set) ? $set : [$set];
            foreach ($set as $ability) {
                Ability::firstOrCreate([
                    'entity_type' => $entity_type ?: null,
                    'name' => $ability,
                    'entity_id' => null,
                ]);
            }
        }

        BouncerFacade::allow('super')->everything();
        BouncerFacade::allow('editor')->to('admin-dashboard');
    }
}
