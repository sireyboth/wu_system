<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Seeds Cambodia's administrative divisions in the correct
     * parent -> child order to satisfy foreign key constraints:
     * Provinces -> Districts -> Communes -> Villages.
     */
    public function run(): void
    {

        $path = database_path("/data/countries.json");
        $rows = json_decode(file_get_contents($path), true);

        $now  = Carbon::now();
        $data = array_map(function ($row) use ($now) {
            return [
                'name'        => $row['en_short_name'],
                'ranking'     => $row['num_code'],
                'alpha2'      => $row['alpha_2_code'],
                'alpha3'      => $row['alpha_3_code'],
                'nationality' => $row['nationality'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }, $rows);

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('countries')->insertOrIgnore($chunk);
        }

        set_data('provinces', increment: false);
        set_data('districts', ['province_id'], increment: false);
        set_data('communes', ['district_id'], increment: false);
        set_data('villages', ['commune_id'], increment: false);
    }
}
