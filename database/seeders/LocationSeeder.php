<?php
namespace Database\Seeders;

use App\Models\Country;
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
        set_records('countries', function ($data) {
            $faker = fake('km_KH');
            Country::create([
                'name'        => $data['en_short_name'],
                'ranking'     => $data['num_code'],
                'alpha2'      => $data['alpha_2_code'],
                'alpha3'      => $data['alpha_3_code'],
                'nationality' => $data['nationality'],
                'remark'      => $faker->sentence(),
            ]);
        });
        set_data('provinces', increment: false);
        set_data('districts', ['province_id'], increment: false);
        set_data('communes', ['district_id'], increment: false);
        set_data('villages', ['commune_id'], increment: false);
    }
}
