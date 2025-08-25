<?php

namespace App\Http\Controllers\AdminBranch;

use Illuminate\Http\Request;
use App\Models\SurveyQuestions;
use App\Models\SurveyConfiguration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerFeedbackController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->Branch->id;
        $config = SurveyConfiguration::where('branch_id',$branchId)->with('questions')->first();
        if ($config) {
            if ($config->type === 'nps') {
                $config->load(['questions' => fn($q) => $q->orderBy('question_index')->limit(1)]);
            } elseif ($config->type === 'csat') {
                $config->load(['questions' => fn($q) => $q->orderBy('question_index')]);
            } elseif ($config->type === 'default') {
                $config->setRelation('questions', collect());
            }
        }
        return view('adminBranch.customerFeedback.index', ['config' => $config]);
    }
    public function create()
    {
        $branchId = Auth::user()->Branch->id;
        $config = SurveyConfiguration::where('branch_id', $branchId)->first();
        $questionCount = $config->questions()->count();

        if ($config->type === 'default') {
            return redirect()->route('admin-branch.feedback.index')
                            ->with('error', 'Default type cannot add question');
        }
        if ($config->type === 'csat' && $questionCount >= 3) {
            return redirect()->route('admin-branch.feedback.index')
                            ->with('error', 'Maximum 3 questions allowed for CSAT.');
        }
        if ($config->type === 'nps' && $questionCount >= 1) {
            return redirect()->route('admin-branch.feedback.index')
                            ->with('error', 'Only 1 question allowed for NPS.');
        }

        return view('adminBranch.customerFeedback.create');
    }
    public function edit(Request $request, $id)
    {
        $question =  SurveyQuestions::find($id);
        return view('adminBranch.customerFeedback.edit',['question' => $question]);
    }
    public function update(Request $request, $id)
    {
       $request->validate([
            'question' => 'required|string|max:1000'
        ]);

        $question = SurveyQuestions::findOrFail($id);
        $question->question_text = $request->question;
        $question->save();
        return redirect()
            ->route('admin-branch.feedback.index')
            ->with('success', 'Question updated');
    }

    public function save(Request $request){
        $request->validate([
            'survey_type' => 'required|string',
        ]);

        $branchId = Auth::user()->Branch->id;

        $config = SurveyConfiguration::updateOrCreate(
            ['branch_id' => $branchId],
            ['type' => $request->survey_type]
        );
        return redirect()
            ->route('admin-branch.feedback.index')
            ->with('success', 'Survey Type updated');
    }


    public function addQuestion(Request $request){

        $request->validate([
            'question' => 'required|string|max:255',
        ]);

        $branchId = Auth::user()->Branch->id;
        $config = SurveyConfiguration::where('branch_id', $branchId)->first();

        $questionCount = $config->questions()->count();

        if ($config->type === 'csat' && $questionCount >= 3) {
            return back()->with('error', 'Maximum 3 questions allowed for CSAT.');
        }
        if ($config->type === 'nps' && $questionCount >= 1) {
            return back()->with('error', 'Only 1 question allowed for NPS.');
        }

        SurveyQuestions::create([
            'survey_config_id' => $config->id,
            'question_index' => $questionCount,
            'question_text' => $request->question,
        ]);

        return redirect()
            ->route('admin-branch.feedback.index')
            ->with('success', 'Question added');

    }

    public function fetch($branchId)
    {
        $config = SurveyConfiguration::where('branch_id',$branchId)->with('questions')->first();
        if ($config) {
            if ($config->type === 'nps') {
                $config->load(['questions' => fn($q) => $q->orderBy('question_index')->limit(1)]);
            } elseif ($config->type === 'csat') {
                $config->load(['questions' => fn($q) => $q->orderBy('question_index')]);
            } elseif ($config->type === 'default') {
                $config->setRelation('questions', collect());
            }
        }
       return response()->json([
            'success' => true,
            'message' => 'fetching survey',
            'data' => $config
        ]);
    }

    public function delete($id)
    {
        $config = SurveyConfiguration::where('branch_id', Auth::user()->Branch->id)->first();
        $question = SurveyQuestions::where('id',$id)->where('survey_config_id', $config->id)
                    ->firstOrFail();
        $question->delete();
        return redirect()
            ->route('admin-branch.feedback.index')
            ->with('success', 'Question added');
    }

    public function report()
    {
        # code...
    }

}
