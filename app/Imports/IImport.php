<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class IImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        //
    }

    protected function transform_data(array $data): array
    {
        $kh = split_full_name($data['khmer_name'] ?? null);
        $en = split_full_name($data['english_name'] ?? null);

        return array_merge($data, [
            'first_name_kh' => $kh['first_name'],
            'last_name_kh'  => $kh['last_name'],
            'first_name'    => $en['first_name'],
            'last_name'     => $en['last_name'],
        ]);
    }
}
