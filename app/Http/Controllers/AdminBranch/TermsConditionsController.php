<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;

use App\Models\TermCondition;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TermsConditionsController extends Controller
{
    public function index()
    {
        return view('adminBranch.branchConfiguration.termsConditions');
    }

    public function get()
    {
        $termsConditions = TermCondition::where('branch_id', Auth::user()->branch_id)->first();

        if (!$termsConditions) {
            return response()->json([
                'message' => 'Terms & conditions not found'
            ], 404);
        }

        return response()->json($termsConditions);
    }

    public function update(Request $request)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        TermCondition::updateOrCreate(
            ['branch_id' => Auth::user()->branch_id],
            ['body' => $request->body]
        );

        return response()->noContent();
    }
}
