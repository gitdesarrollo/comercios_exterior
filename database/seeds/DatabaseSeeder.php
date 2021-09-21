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
        $this->call(PadresSeeder::class);
        //$this->call(estadoDocumento::class);
         //$this->call(dependencia::class);
         //$this->call(roles::class);
         //$this->call(secuencia_fac::class);
         //$this->call(UnidadSeeder::class);
         
         
         //$this->call(UserSeeder::class);
         //$this->call(usersHasRoles::class);
         //$this->call(correlativoSeeder::class);
        
        //$this->call(TypeSeeder::class);
    }
}
