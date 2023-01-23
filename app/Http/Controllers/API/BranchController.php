<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Appointment;
use App\DirectQueue;
use App\Models\TermCondition;
use App\Schedule;
use App\Models\Promotion;

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
        $branches = Branch::with('IndustryCategory')->where('name', 'ilike', "%$keyword%")->get();
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
        $branch->Schedule;
        $branch->IndustryCategory;
        $branch->BranchType;

        $branch->likes = Appointment::whereHas('Slot.Service', function($query) use ($branch){
            $query->where('branch_id', $branch->id);
        })->where('is_liked', true)->count();

        $appointmentLikes = Appointment::whereHas('Slot.Service', function($query) use ($branch){
            $query->where('branch_id', $branch->id);
        })->where('is_liked', true)->count();

        $directQueueLikes = DirectQueue::whereHas('Service', function($query) use ($branch){
            $query->where('branch_id', $branch->id);
        })->where('is_liked', true)->count();

        $branch->likes = $appointmentLikes + $directQueueLikes;
        
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

    public function getBranchType(Branch $branch)
    {
        return response()->json([
            'success' => true,
            'message' => 'get branch type by branch id',
            'data' => $branch->BranchType
        ]);
    }

    public function getSchedules(Branch $branch)
    {
        $schedules = Schedule::where('branch_id', $branch->id)->get();

        return response()->json($schedules);
    }

    public function getTermsConditions(Branch $branch)
    {
        $termCondition = TermCondition::where('branch_id', $branch->id)->first();

        return response()->json($termCondition);
    }

    public function getPromotions(Branch $branch)
    {
        $promotions = Promotion::where('branch_id', $branch->id)
            ->limit(3)
            ->orderBy('created_at')
            ->get();

        return response()->json($promotions);
    }
}
