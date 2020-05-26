<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\ScheduleTemplateDetail;

class BranchController extends Controller
{
    public function getAllByCityId($regency_id)
    {
        $branches = Branch::with('IndustryCategory')->whereRegencyId($regency_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'get all branches by city id',
            'data' => $branches
        ]);
    }

    public function getAllByKeyword($keyword)
    {
        $branches = Branch::with('IndustryCategory')->where('name', 'like', "%$keyword%")->get();
        return response()->json([
            'success' => true,
            'message' => 'get all branches by keyword',
            'data' => $branches
        ]);
    }

    public function getAllByIndustryCategory($industry_category_id)
    {
        $branches = Branch::whereIndustryCategoryId($industry_category_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'get all branches by industry category id',
            'data' => $branches
        ]);
    }

    public function show(Branch $branch)
    {
        $branch->regency_id = (int) $branch->regency_id;
        $branch->Schedule;
        $branch->IndustryCategory;

        $week = $this->getWeek();

        if ($branch->schedule_template_id) {
            $scheduleTemplate = ScheduleTemplateDetail::where('schedule_template_id', $branch->schedule_template_id)->whereBetween('date', [$week['week_start'],$week['week_end']])->get();
            foreach ($branch->Schedule as $key => $schedule) {
                foreach ($scheduleTemplate as $key2 => $template) {
                    if ($schedule->day == strtolower(date("l", strtotime($template->date)))) {
                        $branch->Schedule[$key]['status'] = 'close template';   
                        $branch->Schedule[$key]['close_description'] = $template->description;   
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'get detail branch with schedule and service',
            'data' => $branch
        ]);
    }

    public function getWeek()
    {
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
        $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));

        return ['week_start' => $week_start, 'week_end' => $week_end];
    }
}
