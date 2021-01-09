<?php

use Illuminate\Database\Seeder;
use App\Model\typeDocument;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Type = new typeDocument;
        $Type->descripcion = "Documento";
        $Type->save();

        $Type = new typeDocument;
        $Type->descripcion = "Memorandum";
        $Type->save();

        $Type = new typeDocument;
        $Type->descripcion = "Oficio";
        $Type->save();

        $Type = new typeDocument;
        $Type->descripcion = "Resolución";
        $Type->save();

        $Type = new typeDocument;
        $Type->descripcion = "Providencia";
        $Type->save();


    }
}
