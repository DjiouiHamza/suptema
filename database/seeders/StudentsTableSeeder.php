<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Section; // Ensure you have the Section model available
use Faker\Factory as Faker;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Find the Telecom S3 section
        $section = Section::where('name', 'telecom s3')->first();

        if ($section) {
            // Create 20 random students and assign them to the Telecom S3 section
            for ($i = 0; $i < 32; $i++) {
                Student::create([
                    'full_name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'age' => $faker->numberBetween(18, 25),
                    'phone_number' => $faker->phoneNumber,
                    'section_id' => $section->id, // Assign to Telecom S3
                ]);
            }
        } else {
            $this->command->info('Section Telecom S3 not found!');
        }
    }
}
