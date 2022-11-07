<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Models\Corporate;
use Illuminate\Support\Facades\DB;

class CorporateBranchController extends Controller
{
    public function index($corporateId)
    {
        $corporate = Corporate::find($corporateId);
        $branches = Branch::where('corporate_id', $corporateId)
            ->orderBy('name')
            ->get();

        return view('admin.corporate.branch', [
            'corporate' => $corporate,
            'branches' => $branches,
        ]);
    }

    public function create(Request $request, $corporateId)
    {
        $corporate = Corporate::find($corporateId);

        return view('admin.corporate.chooseBranch', [
            'corporate' => $corporate
        ]);
    }

    public function findByName(Request $request)
    {
        if (!$request->name) {
            return response()->json([]);
        }

        $name = strtolower($request->name);
        $branches = Branch::where(DB::raw('LOWER(name)'), 'like', "%{$name}%")
            ->whereNull('corporate_id')
            ->orderBy('name')
            ->get();
        
        $data = $branches->map(function ($branch) {
            $item = [
                'id' => $branch->id,
                'name' => $branch->name,
                'regency' => $branch->Regency
            ];

            if ($branch->logo) {
                $item['logo'] = asset("storage/{$branch->logo}");
            }

            return $item;
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer',
            'corporate_id' => 'required|integer'
        ]);

        $branch = Branch::find($request->branch_id);

        if (!$branch) {
            return response()->json([
                'message' => 'Cannot find branch'
            ], 400);
        }

        if ($branch->corporate_id) {
            return response()->json([
                'message' => 'Branch already have parent'
            ]);
        }

        Branch::where('id', $request->branch_id)->update([
            'corporate_id' => $request->corporate_id
        ]);

        return;
    }
}
