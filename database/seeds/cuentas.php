<?php

use Illuminate\Database\Seeder;
use App\Model\sequences;
use App\Model\cuentas_activo;

class cuentas extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new sequences;
        $data->name = "cuenta_activos";
        $data->value = 1;
        $data->save();
        $id = $data->value;

        $unidad = new cuentas_activo;
        $unidad->id_cuenta = $id;
        $unidad->numero_cuenta = "1232.03";
        $unidad->descripcion = "INVENTARIO INICIAL";
        $unidad->save();
    }
}
