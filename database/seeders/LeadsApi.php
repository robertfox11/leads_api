<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadsApi extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrays = range(0, 20);
        $estados = ['abierto', 'rechazado', 'aceptado', 'cerrado'];
        foreach ($estados as $es) {
            foreach ($arrays as $valor) {
                if ($es == 'abierto') {
                    if ($valor <= 10) {
                        DB::table('leads')->insert([
                            'titulo' => "Title  $valor " . $es,
                            'estado_lead' => $es,
                            'fecha_creacion' => date("2022-m-d H:i:s"),
                            'fecha_cierre' => null
                        ]);
                    }
                } else {
                    DB::table('leads')->insert([
                        'titulo' => "Title  $valor " .$es,
                        'estado_lead' => $es,
                        'fecha_creacion' => date("Y-m-d H:i:s"),
                        'fecha_cierre' => null
                    ]);
                }
            }
        }
    }
}
