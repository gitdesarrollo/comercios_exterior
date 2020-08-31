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
        //     UserSeeder::class,
        //     EntidadSeeder::class,
        //     UnidadSeeder::class,
        //     grupos::class,
        //     categorias::class,
        //     secciones::class,
        //     tipos::class,
        //     bienes::class,
        //     estadoBien::class,
        //     ependencia::class,
        //     PersonDataSeeder::class,
        //     cuentas::class,
        //     documento_respaldos::class,
        //     secuencia_fac::class
        // );
        $this->call(UserSeeder::class);
        $this->call(EntidadSeeder::class);
        $this->call(UnidadSeeder::class);
        $this->call(grupos::class);
        $this->call(documento_respaldos::class);
        $this->call(secuencia_fac::class);
        // $this->call(cuentas::class);
        $this->call(dependencia::class);
        $this->call(PersonDataSeeder::class);

        // $this->call(bienes::class);

        // $this->call(categorias::class);
        // $this->call(secciones::class);
        // $this->call(tipos::class);
        // $this->call(estadoBien::class);
    }
}
