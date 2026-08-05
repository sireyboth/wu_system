<?php
namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // 2025-2026
            [
                'year'       => 2025,
                'semester'   => 1,
                'code'       => 'S1-2025',
                'name'       => '2025-2026',
                'start_date' => '2025-11-01',
                'end_date'   => '2026-03-30',
            ],
            [
                'year'       => 2025,
                'semester'   => 2,
                'code'       => 'S2-2025',
                'name'       => '2025-2026',
                'start_date' => '2026-04-20',
                'end_date'   => '2026-07-31',
                'is_active'  => true,
            ],
        ];

        foreach ($items as $item) {
            Term::create($item);
        }
    }
}
