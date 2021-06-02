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
            'latitude'=>'-8.1651576',
            'longitude'=>'113.7142243',
            'password'=>bcrypt('a')
        ]);

        \App\User::create([
            'name'=>'asif',
            'email'=>'b@b',
            'latitude'=>'-8.1605965',
            'longitude'=>'113.7178461',
            'password'=>bcrypt('b')
        ]);

    }
}
