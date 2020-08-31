<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hashedPassIn = hash('sha256', '12345678', false);
        DB::table('users')->insert([
            'name' => 'Usuario Administrador',
            'username' => 'admin',
            'email' => 'admin@correo.com',
            'password' => $hashedPassIn
        ]); 
    }

}
