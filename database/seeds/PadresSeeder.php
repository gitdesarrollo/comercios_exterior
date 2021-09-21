<?php

use Illuminate\Database\Seeder;
use App\Model\padres;
use App\Model\estadopadre;
class PadresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $estadop= new estadopadre;
        $estadop->descripcion='ACTIVO';
        $estadop->save();

        $estadop= new estadopadre;
        $estadop->descripcion='INACTIVO';
        $estadop->save();

        $padre = new padres;
        $padre->descripcion = 'NO CLASIFICADO';
        $padre->estatus= 1;
        $padre->idpadre=1;
        $padre->save();
    
    }
}
