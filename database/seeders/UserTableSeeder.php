<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
               // clear out existing seed & create 10 seeds 
               DB::table('users')->truncate();         
               User::factory(10)->create(); 
    }
}
