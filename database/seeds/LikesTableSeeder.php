<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Event;
use App\User;

class LikesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_AR');
        $users = User::lists('id')->all();
        $events = Event::lists('id')->all();

        DB::table('likes')->delete();

        for ($i = 0; $i < 500; $i++) {
            DB::table('likes')->insert([
                'user_id' => $faker->randomElement($users),
                'event_id' => $faker->randomElement($events),
            ]);
        }
    }
}