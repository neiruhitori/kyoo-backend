<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ScheduleTemplateDetail;

class HolidayController extends Controller
{
    public function index()
    {
        $scheduleTemplates = ScheduleTemplateDetail::all();

        return response()->json($scheduleTemplates);
    }
}
