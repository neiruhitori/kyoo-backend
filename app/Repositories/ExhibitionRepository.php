<?php

namespace App\Repositories;

use App\Interfaces\ExhibitionRepositoryInterface;
use App\Models\Exhibition;
use App\Slot;

class ExhibitionRepository implements ExhibitionRepositoryInterface 
{
    public function getDailyReport($params)
    {
        return Exhibition::whereHas('Slot.Service', function ($query) use ($params) {
            $params['service_id']
                ? $query->where('id', $params['service_id'])
                : $query->where('branch_id', $params['branch_id']);
        })
            ->where('date', $params['date'])
            ->orderBy('queue_order')
            ->get();
    }

    public function getMonthlyReport($params)
    {
        return Exhibition::whereHas('Slot.Service', function ($query) use ($params) {
            $params['service_id']
                ? $query->where('id', $params['service_id'])
                : $query->where('branch_id', $params['branch_id']);
        })
            ->whereMonth('date', $params['month'])
            ->whereYear('date', $params['year'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnfinishedQueue($params)
    {
        return Exhibition::whereHas('Slot.Service', function ($query) use ($params) {
            $query->where('branch_id', $params['branch_id']);
        })
            ->where('date', $params['date'])
            ->where('status', 'book')
            ->get()
            ->sortBy(function ($query) {
                return $query->slot->start_time;
            });
    }

    public function getFinishedQueue($params)
    {
        return Exhibition::whereHas('Slot.Service', function ($query) use ($params) {
            $query->where('branch_id', $params['branch_id']);
        })
            ->where('date', $params['date'])
            ->whereIn('status', ['no show', 'end served'])
            ->get()
            ->sortBy(function ($query) {
                return $query->slot->start_time;
            });
    }

    public function isSameQueue($params)
    {
        return !Exhibition::where([
            'email' => $params['email'],
            'phone' => $params['phone'],
            'slot_id' => $params['slot_id'],
            'date' => $params['date']
        ])->first();
    }

    public function isSlotExceeded($params)
    {
        $slot = Slot::find($params['slot_id']);

        $usedSlot = Exhibition::where([
            'slot_id' => $params['slot_id'],
            'date' => $params['date']
        ])->count();

        return $usedSlot > $slot->max_slots;
    }
}