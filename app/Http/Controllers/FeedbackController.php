<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Appointment;
use App\DirectQueue;
use Illuminate\Http\Request;
use App\Models\SurveyQuestions;
use App\Models\SurveyResponses;
use App\Models\SurveyConfiguration;

class FeedbackController extends Controller
{
    public function index($branchId, $queueType, $queueId)
    {
        $modelMap = [
            'appointment'  => ['model' => Appointment::class],
            'onsite'       => ['model' => DirectQueue::class],
        ];
        $responseMap = [
            'appointment'  => 'appointment_id',
            'onsite'       => 'direct_queue_id',
        ];

        $model = $modelMap[$queueType]['model'];
        $columnResp = $responseMap[$queueType];

        $check = $model::where('id', $queueId)
            ->where('branch_id', $branchId)
            ->whereNotNull('rating')
            ->whereNotNull('survey_type')
            ->where('survey_type', '<>', '')
            ->where('status', 'end served')
            ->first();
            
        $branchName = Branch::where('id',$branchId)->select('name')->first();
        $config = SurveyConfiguration::where('branch_id', $branchId)->first();
        $query = SurveyQuestions::where('survey_config_id', $config->id)
                                 ->orderBy('question_index', 'asc');
        $answers = null;


        if ($check) {
            $answers = SurveyResponses::where('branch_id', $branchId)
            ->where('survey_config_id', $config->id)
            ->where($columnResp, $queueId)
            ->where('survey_type', $check->survey_type)
            ->with('question')
            ->get();
        }

        if ($config->type == 'nps') {
            $query->limit(1);
        }

        $questions = $query->get();
        
        return view('feedback', [
            'questions'   => $questions,
            'queue_id'   => $queueId,
            'branch_name' => $branchName->name,
            'data'        => $check,
            'answers'     => $answers,
            'type'        => $config->type,
            'queue_type'  => $queueType,
        ]);
    }

}
