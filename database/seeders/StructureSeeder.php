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
        set_data('faculties', ['shortcut']);
        set_data('majors', ['shortcut', 'faculty_id']);
        set_data('subjects', [
            'code',
            'year_level',
            'major_id',
            'semester',
            'credit',
        ]);
        set_data('nationalities', ['code']);
        set_data('statuses', ['shortcut']);
        set_data('groups', ['shortcut']);
        set_data('shifts', ['shortcut']);
        set_data('batches', ['shortcut', 'academic_year']);
    }
}
