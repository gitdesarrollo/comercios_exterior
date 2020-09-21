<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        // $this->call(
        //     estadoDocumento::class,
        //     dependencia::class,
        //     roles::class,
        //     UserSeeder::class
        // );
        $this->call(estadoDocumento::class);
        $this->call(dependencia::class);
        $this->call(roles::class);
        $this->call(UserSeeder::class);
        $this->call(usersHasRoles::class);
    }
}
