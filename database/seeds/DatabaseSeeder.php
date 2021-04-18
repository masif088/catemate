<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \App\Race::create(['title'=>'angora']);
        \App\Race::create(['title'=>'persia']);
        \App\Race::create(['title'=>'scottish fold']);
        \App\Race::create(['title'=>'british shorthair']);

        \App\User::create([
            'name'=>'dita',
            'email'=>'a@a',
            'password'=>bcrypt('a')
        ]);

    }
}
