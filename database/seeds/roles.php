<?php

use Illuminate\Database\Seeder;

class roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles_users')->insert([
            'description' => 'Administrador',
            'idEstado'  => 4
        ]); 
        DB::table('roles_users')->insert([
            'description' => 'Usuario',
            'idEstado'  => 4
        ]); 
        DB::table('roles_users')->insert([
            'description' => 'Recepción',
            'idEstado'  => 4
        ]); 
        DB::table('roles_users')->insert([
            'description' => 'Secretaria',
            'idEstado'  => 4
        ]); 
        DB::table('roles_users')->insert([
            'description' => 'Despacho',
            'idEstado'  => 4
        ]); 
    }
    
}
