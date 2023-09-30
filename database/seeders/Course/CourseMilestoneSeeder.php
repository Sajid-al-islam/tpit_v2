<?php

namespace Database\Seeders\Course;

use App\Models\Course\CourseMilestone;
use Illuminate\Database\Seeder;

class CourseMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourseMilestone::truncate();
        CourseMilestone::create([
            'course_id' => 1,
            'title' => 'Build a Personal Portfolio & Deployment',
        ]);   

        CourseMilestone::create([
            'course_id' => 1,
            'title' => 'PSD TO HTML',
        ]);   

        CourseMilestone::create([
            'course_id' => 1,
            'title' => 'JavaScript Fundamentals',
        ]); 

        CourseMilestone::create([
            'course_id' => 1,
            'title' => 'Explore ES6 Features',
        ]); 
    }
}
