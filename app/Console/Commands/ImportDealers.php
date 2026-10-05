<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportDealers extends Command
{
    protected $signature = 'dealers:import
        {files* : Path(s) to the .xlsx file(s)}
        {--category=* : Category id for each file, in the same order as the files}
        {--dry-run : Parse and report without touching the database}';

    protected $description = 'Replace all dealers (users_group_id = 2) with the rows from one or more Excel files';

    const DEALER_GROUP = 2;

    public function handle()
    {
        $files = $this->argument('files');
        $categoryIds = $this->option('category');
        if (count($categoryIds) !== count($files)) {
            $this->error('Pass one --category=ID per file, in the same order as the files.');
            return 1;
        }
        try {
            $known = DB::table('categories')->pluck('name', 'id');
            if ($missing = array_diff($categoryIds, $known->keys()->map(fn ($i) => (string) $i)->all())) {
                $this->error('Unknown category id(s): ' . implode(', ', $missing));
                $this->line('Categories: ' . $known->map(fn ($n, $id) => "$id=$n")->implode(', '));
                return 1;
            }
        } catch (\Exception $e) {
            if (!$this->option('dry-run')) {
                throw $e;
            }
            $this->warn('DB unreachable: category ids not validated.');
        }

        // keyed by sympark id (or name|mobile when absent) so a dealer in two files gets both categories
        $dealers = [];
        $skipped = 0;
        foreach ($files as $n => $file) {
            if (!is_file($file)) {
                $this->error("File not found: $file");
                return 1;
            }
            $sheet = IOFactory::load($file)->getActiveSheet();
            $count = 0;
            foreach ($sheet->toArray(null, true, false, false) as $i => $r) {
                if ($i === 0) {
                    continue; // header
                }
                $name = trim((string) ($r[1] ?? ''));
                if ($name === '') {
                    $skipped++;
                    continue;
                }
                $mobile = preg_replace('/\D+/', '', (string) ($r[9] ?? ''));
                $sympark = trim((string) ($r[0] ?? ''));
                $key = $sympark !== '' ? $sympark : strtolower($name) . '|' . $mobile;
                if (!isset($dealers[$key])) {
                    $dealers[$key] = [
                        'sympark_id' => $sympark !== '' ? $sympark : null,
                        'name' => $name,
                        'address' => trim((string) ($r[2] ?? '')),
                        'town' => trim((string) ($r[3] ?? '')),
                        'district' => trim((string) ($r[4] ?? '')),
                        'state' => trim((string) ($r[5] ?? '')),
                        'zipcode' => trim((string) ($r[6] ?? '')),
                        'latitude' => is_numeric($r[7] ?? null) ? $r[7] : null,
                        'longitude' => is_numeric($r[8] ?? null) ? $r[8] : null,
                        'mobile' => $mobile,
                        'dealer_type' => trim((string) ($r[10] ?? '')) ?: null,
                        'categories' => [],
                    ];
                }
                $dealers[$key]['categories'][$categoryIds[$n]] = true;
                $count++;
            }
            $this->line(basename($file) . ": $count rows -> category {$categoryIds[$n]}");
        }
        $rows = array_values($dealers);

        try {
            $existing = DB::table('users')->where('users_group_id', self::DEALER_GROUP)->count();
        } catch (\Exception $e) {
            $existing = 'unknown (DB unreachable)';
        }
        $this->info(count($rows) . " unique dealers ($skipped blank rows skipped); $existing existing dealers will be removed.");

        if ($this->option('dry-run')) {
            $this->line('Dry run: no changes made.');
            return 0;
        }
        if (!$this->confirm('Delete all existing dealers and import these files?')) {
            return 1;
        }

        $country = Country::where('name', 'India')->first();
        $hasGeo = Schema::hasColumn('users', 'latitude') && Schema::hasColumn('users', 'longitude');
        $hasSympark = Schema::hasColumn('users', 'sympark_id');
        $hasType = Schema::hasColumn('users', 'dealer_type');
        $newStates = $newCities = 0;

        DB::transaction(function () use ($rows, $country, $hasGeo, $hasSympark, $hasType, &$newStates, &$newCities) {
            $ids = DB::table('users')->where('users_group_id', self::DEALER_GROUP)->pluck('id');
            DB::table('dealer_category')->whereIn('dealer_id', $ids)->delete();
            DB::table('users')->where('users_group_id', self::DEALER_GROUP)->delete();

            $states = State::pluck('id', 'name')->mapWithKeys(fn ($id, $n) => [strtolower($n) => $id])->all();
            $cities = [];
            foreach (City::get(['id', 'name', 'state_id']) as $c) {
                $cities[$c->state_id . '|' . strtolower($c->name)] = $c->id;
            }

            foreach ($rows as $row) {
                $sKey = strtolower($row['state']);
                if (!isset($states[$sKey])) {
                    $states[$sKey] = DB::table('states')->insertGetId(array_filter([
                        'name' => $row['state'],
                        'country_id' => Schema::hasColumn('states', 'country_id') ? ($country->id ?? null) : null,
                    ]));
                    $newStates++;
                }
                $stateId = $states[$sKey];

                $cityName = $row['town'] !== '' ? $row['town'] : $row['district'];
                $cKey = $stateId . '|' . strtolower($cityName);
                if (!isset($cities[$cKey])) {
                    $cities[$cKey] = City::create(['name' => $cityName, 'state_id' => $stateId])->id;
                    $newCities++;
                }

                $data = [
                    'name' => $row['name'],
                    'email' => '',
                    'mobile' => substr($row['mobile'], -15),
                    'users_group_id' => self::DEALER_GROUP,
                    'address' => $row['address'],
                    'country_id' => $country->id ?? null,
                    'city_id' => $cities[$cKey],
                    'state_id' => $stateId,
                    'zipcode' => $row['zipcode'],
                    'status' => 1,
                ];
                if ($hasGeo) {
                    $data['latitude'] = $row['latitude'];
                    $data['longitude'] = $row['longitude'];
                }
                if ($hasType) {
                    $data['dealer_type'] = $row['dealer_type'];
                }
                if ($hasSympark) {
                    $data['sympark_id'] = $row['sympark_id'];
                }
                User::create($data)->dealerCategory()->sync(array_keys($row['categories']));
            }
        });

        $this->info('Imported ' . count($rows) . " dealers. Created $newStates new states and $newCities new cities.");
        return 0;
    }
}
