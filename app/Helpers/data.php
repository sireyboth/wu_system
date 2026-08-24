<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (! function_exists('make_fields')) {
    function make_fields(string $table_name,
        ? callable $fn = null,
        bool $is_common = true,
    ) : void {
        Schema::create($table_name, function (Blueprint $table) use ($fn, $is_common) {
            fields($table, fn() => $fn($table), $is_common);
        });
    }
}

if (! function_exists('fields')) {
    function fields(Blueprint $table,
        ? callable $fn = null,
        bool $is_common = false,
    ) : void {
        $table->id();
        if ($is_common) {
            $table->string('name_en', 100);
            $table->string('name_kh', 100);
            // $table->string('name', 255)->nullable();
        }

        $fn($table);
        $table->text('remark')->nullable();
        $table->timestamps();
        $table->softDeletes();

    }
}

if (! function_exists('to_list')) {
    function to_list(object $self, array $fields = [], bool $is_common = true, $is_extra = true): array
    {
        $data = array_merge(['id' => $self->id], $fields);
        if ($is_common) {
            $data['full_name'] = $self->full_name;
            $data['name_kh']   = $self->name_kh;
            $data['name_en']   = $self->name_en;
        }

        if ($is_extra) {
            $data['remark']     = $self->remark ?? null;
            $data['created_at'] = dated_format($self->created_at, 'D, d M Y H:i:s');
            $data['updated_at'] = dated_format($self->updated_at, 'D, d M Y H:i:s');
        }

        return $data;
    }
}

if (! function_exists('set_data')) {
    function set_data(string $filename, array $keys = [], bool $is_common = true): void
    {
        $records = get_data($filename);
        $now     = now();
        $data    = array_map(function ($row) use ($now, $keys, $is_common) {
            $item = $is_common
                ? [
                'id'      => $row['id'] ?? null,
                'name_en' => $row['name_en'] ?? null,
                'name_kh' => $row['name_kh'] ?? null,
                // 'name'    => trim(($row['name_kh'] ?? '') . ' (' . ($row['name_en'] ?? '') . ')', ' ()'),
            ]
                : [];

            foreach ($keys as $key) {
                $item[$key] = $row[$key] ?? null;
            }

            $item['created_at'] = $now;
            $item['updated_at'] = $now;

            return $item;
        }, $records);

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table($filename)->insertOrIgnore($chunk);
        }
    }
}

if (! function_exists('get_data')) {
    function get_data(string $filename): array
    {
        $path = database_path("data/{$filename}.json");

        if (! file_exists($path)) {
            throw new \RuntimeException("Seed file not found: {$filename}.json");
        }

        $rows = json_decode(file_get_contents($path), true);

        if (! is_array($rows)) {
            throw new \RuntimeException("Invalid JSON in seed file: {$filename}.json");
        }

        return $rows;
    }
}

if (! function_exists('set_records')) {
    function set_records(string $filename, callable $fn, bool $single = true): void
    {
        $records = get_data($filename);
        foreach ($records as $record) {
            $now = now();
            $single ? $fn($record, $now) : execute(fn() => $fn($record, $now));
        }
    }
}
