<?php

namespace Database\Seeders;

use App\Models\Articles;
use App\Models\Rubros;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //Rubros::truncate();
        $faker = \Faker\Factory::create();


        for ($i = 0; $i < 50; $i++) {
            Rubros::create([
                'nombre'=> $faker->name,
                'created_at'=>$faker->dateTimeBetween(),
                'updated_at'=>$faker->dateTimeBetween(),
            ]);
        }

        //Articles::truncate();

        $faker = \Faker\Factory::create();

        // And now, let's create a few Articles in our database:
        for ($i = 0; $i < 50; $i++) {
            Articles::create([
                'rubro_id' => $faker->numberBetween(1,50),
                'nombre'=> $faker->name,
                'descripcion'=>$faker->text(200),
                'codigo'=>$faker->numberBetween(000000,5555555),
                'cantidad'=>$faker->numberBetween(1,200),
                'caracteristicas'=>$faker->text(200),
                'created_at'=>$faker->dateTimeBetween(),
                'updated_at'=>$faker->dateTimeBetween(),
            ]);
        }
    }
}
