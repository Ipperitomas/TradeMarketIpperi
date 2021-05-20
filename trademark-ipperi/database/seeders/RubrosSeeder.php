<?php


namespace Database\Seeders;

use App\Models\Rubros;
use Illuminate\Database\Seeder;

class RubrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Rubros::truncate();
        $faker = \Faker\Factory::create();


        for ($i = 0; $i < 50; $i++) {
            Rubros::create([
                'nombre'=> $faker->name,
                'created_at'=>$faker->dateTimeBetween(),
                'updated_at'=>$faker->dateTimeBetween(),
            ]);
        }
    }
}
