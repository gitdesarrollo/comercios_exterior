<?php

use Illuminate\Database\Seeder;
use App\Model\profesiones;

class profesion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $p = new profesiones;
        $p->descripcion = 'Ingeniero';
        $p->save();
    }
}
