<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_AR');
        //delete users table records
        DB::table('users')->delete();
        //insert some dummy records
        for ($i = 0; $i < 50; $i++) {
            DB::table('users')->insert([
                'displayName' => $faker->name,
                'email' => $faker->unique()->email,
                'facebook' => $faker->isbn10(),
                'is_admin' => 0,
                'created_at' => $faker->dateTimeBetween($startDate = '-60 days',
                    $endDate = '-30 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-29 days',
                    $endDate = '-15 days', $timezone = date_default_timezone_get()),
            ]);
        }
    }
}