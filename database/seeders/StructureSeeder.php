<?php
namespace Database\Seeders;

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
    }
}
