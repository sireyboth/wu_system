<?php
namespace Database\Seeders;

use App\Models\Person;
use Faker\Factory;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        set_records('students', function ($data) {
            $faker       = Factory::create('km_KH');
            $status_exam = ['none', 'passed', 'failed'];

            // 1. Remap JSON keys -> Person columns
            $person_data = [
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'first_name_kh'  => $data['first_name_kh'],
                'last_name_kh'   => $data['last_name_kh'],
                'nationality_id' => $faker->numberBetween(1, 20),
                'dob'            => $faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
                'sex'            => $faker->randomElement(['male', 'female']),
                'phones'         => $data['phones'] ?? [$faker->phoneNumber()],
                'email'          => $faker->unique()->safeEmail(),
                'remark'         => $faker->sentence(),
            ];
            $person = Person::firstOrCreate($person_data);

            // 2. Remap JSON keys -> Student columns
            $student_data = [
                'code'           => $data['code'],
                'major_id'       => $faker->numberBetween(1, 5),
                'batch_id'       => $faker->numberBetween(1, 5),
                'status_id'      => $faker->numberBetween(1, 10),
                'group_id'       => $faker->numberBetween(1, 6),
                'shift_id'       => $faker->numberBetween(1, 3),
                'admission_date' => $faker->date(),
                'bacc_2_code'    => $data['bacc_2_code'],
                'entrance_exam'  => $faker->randomElement($status_exam),
                'exit_exam'      => $faker->randomElement($status_exam),
                'degree_type'    => $faker->randomElement(['associate', 'bachelor', 'master', 'phd']),
                'from_school'    => $faker->company(),
                'intake'         => $faker->randomElement(['primary', 'secondary']),
                'scholarship'    => $faker->randomElement(['none', 'ministry', 'prince', 'school']),
                'remark'         => $faker->sentence(),
            ];
            $student = $person->student()->firstOrCreate($student_data);

            // 3. Remap nested addresses
            $address_data = array_map(function ($a) use ($faker) {
                return [
                    'province_id' => $a['province_id'],
                    'district_id' => $a['district_id'],
                    'commune_id'  => $a['commune_id'],
                    'village_id'  => $a['village_id'],
                    'type'        => $a['type'],
                    'street'      => $faker->streetAddress(),
                    'house_no'    => $faker->macAddress(),
                    'remark'      => $faker->sentence(),
                ];
            }, $data['addresses'] ?? []);
            $person->addresses()->createMany($address_data);

            // 4. Remap nested guardians
            $guardian_data = array_map(function ($g) use ($faker) {
                return [
                    'job'          => $faker->jobTitle(),
                    'relationship' => $faker->randomElement(['father', 'mother', 'other']),
                    'name_en'      => $g['name_en'],
                    'name_kh'      => $g['name_kh'],
                    'phones'       => $data['phones'] ?? [$faker->phoneNumber()],
                    'addresses'    => [$faker->address()],
                    'remark'       => $faker->sentence(),
                ];
            }, $data['guardians'] ?? []);
            $student->guardians()->createMany($guardian_data);

        }, false);
    }
}
