<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Slot;
use App\Models\SlotService;

class SlotServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slots = Slot::all();

        $data = $slots->map(function ($slot) {
            return [
                'slot_id' => $slot->id,
                'service_id' => $slot->service_id,
                'max_slots' => $slot->max_slots
            ];
        })->toArray();

        SlotService::insert($data);
    }
}
