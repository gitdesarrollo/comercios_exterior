<?php

use Illuminate\Database\Seeder;
use App\Model\nombreCorrelativo;

class correlativoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $correlativo = new nombreCorrelativo;
        $correlativo->name = "MINECO-CIECA-";
        $correlativo->unidad_id = 2;
        $correlativo->save();
        
        $correlativo = new nombreCorrelativo;
        $correlativo->name = "MINECO-DPCE-";
        $correlativo->unidad_id = 3;
        $correlativo->save();
        
        $correlativo = new nombreCorrelativo;
        $correlativo->name = "MINECO-DACE-";
        $correlativo->unidad_id = 5;
        $correlativo->save();
        
        $correlativo = new nombreCorrelativo;
        $correlativo->name = "MINECO-DAE-";
        $correlativo->unidad_id = 6;
        $correlativo->save();
        
        $correlativo = new nombreCorrelativo;
        $correlativo->name = "MINECO-PCOMERCIAL-";
        $correlativo->unidad_id = 4;
        $correlativo->save();
        
        $correlativo = new nombreCorrelativo;
        $correlativo->name = "MINECO-DTI-";
        $correlativo->unidad_id = 7;
        $correlativo->save();
        
        $correlativo = new nombreCorrelativo;
        $correlativo->unidad_id = 1;
        $correlativo->name = "MINECO-DESPACHO-";
        $correlativo->save();

    }
}
