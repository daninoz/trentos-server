<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SportsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_AR');
        //delete users table records
        DB::table('sports')->delete();
        //insert some dummy records
        DB::table('sports')->insert([
            [
                'name' => 'Running',
                'created_at' => $faker->dateTimeBetween($startDate = '-60 days',
                    $endDate = '-30 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-29 days',
                    $endDate = '-15 days', $timezone = date_default_timezone_get()),
            ],
            [
                'name' => 'Mountain Bike',
                'created_at' => $faker->dateTimeBetween($startDate = '-60 days',
                    $endDate = '-30 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-29 days',
                    $endDate = '-15 days', $timezone = date_default_timezone_get()),
            ],
            [
                'name' => 'Trekking',
                'created_at' => $faker->dateTimeBetween($startDate = '-60 days',
                    $endDate = '-30 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-29 days',
                    $endDate = '-15 days', $timezone = date_default_timezone_get()),
            ],
            [
                'name' => 'Ciclismo de Ruta',
                'created_at' => $faker->dateTimeBetween($startDate = '-60 days',
                    $endDate = '-30 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-29 days',
                    $endDate = '-15 days', $timezone = date_default_timezone_get()),
            ],
        ]);
    }
}