<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grade')->trucate();
        $grades = ['grade 1', 'grade 2', 'grade 3', 'grade 4', 'grade 5'];

        // Shuffle the grades to randomize the order
        shuffle($grades);

        foreach ($grades as $grade) {
            DB::table('grades')->insert([
                'grade' => $grade,
            ]);
        }
    }
}
