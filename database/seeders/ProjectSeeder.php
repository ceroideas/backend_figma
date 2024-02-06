<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('projects')->insert([
            'name' => "Proyecto1",
            'year_from' => 2020,
            'year_to' => 2024,
            'sceneries' => json_encode(['escenario1','escenario2','escenario3']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('nodes')->insert([
            'name' => 'nodo1',
            'project_id' => 1,
            'description' => 'nodo1',
            'type' => 1, // constante
            'distribution_shape' => 1,
            'formula' =>null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('nodes')->insert([
            'name' => 'nodo2',
            'project_id' => 1,
            'description' => 'nodo2',
            'type' => 1, // constante
            'distribution_shape' => 1,
            'formula' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('nodes')->insert([
            'name' => 'nodo3',
            'project_id' => 1,
            'description' => 'nodo3',
            'type' => 2, // variable
            'distribution_shape' => 1,
            'formula' => json_encode([1,'+',2,'(',1,'*',2,')']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('nodes')->insert([
            'name' => 'nodo4',
            'project_id' => 1,
            'description' => 'nodo4',
            'type' => 2, // variable
            'distribution_shape' => 1,
            'formula' => json_encode([3,'*',3]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        DB::table('sceneries')->insert([
            'node_id' => 1, 'name' => 'escenario1', 'years'=>json_encode(["2020"=>10,"2021"=>20,"2022"=>30,"2023"=>40,"2024"=>50]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sceneries')->insert([
            'node_id' => 1, 'name' => 'escenario2', 'years'=>json_encode(["2020"=>15,"2021"=>25,"2022"=>35,"2023"=>45,"2024"=>55]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sceneries')->insert([
            'node_id' => 1, 'name' => 'escenario3', 'years'=>json_encode(["2020"=>18,"2021"=>28,"2022"=>38,"2023"=>48,"2024"=>58]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sceneries')->insert([
            'node_id' => 2, 'name' => 'escenario1', 'years'=>json_encode(["2020"=>20,"2021"=>30,"2022"=>40,"2023"=>50,"2024"=>60]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sceneries')->insert([
            'node_id' => 2, 'name' => 'escenario2', 'years'=>json_encode(["2020"=>25,"2021"=>35,"2022"=>45,"2023"=>55,"2024"=>65]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sceneries')->insert([
            'node_id' => 2, 'name' => 'escenario3', 'years'=>json_encode(["2020"=>28,"2021"=>38,"2022"=>48,"2023"=>58,"2024"=>68]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
