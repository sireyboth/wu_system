<?php
namespace Database\Seeders;

use App\Models\ExamState;
use Illuminate\Database\Seeder;

class ExamStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        set_records('exam-states', function ($data) {
            $faker = fake('km_KH');
            ExamState::create([ ...$data, 'remark' => $data['remark'] ?? $faker->sentence()]);
        });
    }
}
