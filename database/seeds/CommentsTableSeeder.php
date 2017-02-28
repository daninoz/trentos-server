<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Event;
use App\User;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_AR');
        $users = User::lists('id')->all();
        $events = Event::lists('id')->all();

        DB::table('comments')->delete();

        for ($i = 0; $i < 200; $i++) {
            DB::table('comments')->insert([
                'comment' => $faker->realText($maxNbChars = 100, $indexSize = 1),
                'user_id' => $faker->randomElement($users),
                'event_id' => $faker->randomElement($events),
                'created_at' => $faker->dateTimeBetween($startDate = '-5 days',
                    $endDate = '-4 days', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeBetween($startDate = '-3 days',
                    $endDate = '-2 days', $timezone = date_default_timezone_get()),
            ]);
        }
    }
}