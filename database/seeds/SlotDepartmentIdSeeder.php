<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Slot;

class SlotDepartmentIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slots = Slot::all();

        foreach ($slots as $slot) {
            Slot::where('id', $slot->id)->update([
                'department_id' => $slot->Service->department_id
            ]);
        }
    }
}
