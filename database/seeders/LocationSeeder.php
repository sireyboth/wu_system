<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Seeds Cambodia's administrative divisions in the correct
     * parent -> child order to satisfy foreign key constraints:
     * Provinces -> Districts -> Communes -> Villages.
     */
    public function run(): void
    {
        set_data('provinces');
        set_data('districts', ['province_id']);
        set_data('communes', ['district_id']);
        set_data('villages', ['commune_id']);
    }
}
