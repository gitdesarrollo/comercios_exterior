<?php

use Illuminate\Database\Seeder;
use App\Model\userHasRoles;

class usersHasRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = new userHasRoles;
        $roles->idUser = 1;
        $roles->idRoles = 1;
        $roles->save();

    }
}
