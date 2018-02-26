<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Sport;
use App\User;

class EventsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_AR');
        $users = User::lists('id')->all();
        $sports = Sport::lists('id')->all();

        DB::table('events')->delete();

        for ($i = 0; $i < 50; $i++) {
            DB::table('events')->insert([
                'description' => $faker->realText($maxNbChars = 500, $indexSize = 1),
                'datetime' => $faker->dateTimeBetween('-2 days', '+2 days', date_default_timezone_get()),
                'highlight' => 0,
                'sport_id' => $faker->randomElement($sports),
                'user_id' => $faker->randomElement($users),
                'created_at' => $faker->dateTimeBetween($startDate = '-14 days',
                    $endDate = '-10 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-9 days',
                    $endDate = '-5 days', $timezone = date_default_timezone_get()),
            ]);
        }
    }
}