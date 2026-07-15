<?php
namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Major;
use App\Models\Nationality;
use App\Models\Shift;
use App\Models\Status;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        set_records('nationalities', fn($data) => Nationality::create($data));
        set_records('faculties', fn($data) => Faculty::create([ ...$data, 'remark' => fake()->sentence()]));
        set_records('majors', fn($data) => Major::create([ ...$data, 'remark' => fake()->sentence()]));
        set_records('subjects', fn($data) => Subject::create([ ...$data, 'remark' => fake()->sentence()]));
        set_records('statuses', fn($data) => Status::create([ ...$data, 'remark' => fake()->sentence()]));
        set_records('groups', fn($data) => Group::create([ ...$data, 'remark' => fake()->sentence()]));
        set_records('shifts', fn($data) => Shift::create([ ...$data, 'remark' => fake()->sentence()]));
        set_records('batches', fn($data) => Batch::create([ ...$data, 'remark' => fake()->sentence()]));
    }
}
