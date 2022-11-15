<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate;
use App\Branch;
use App\Events\CorporateCreatedEvent;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Services\UserService;
use App\Services\CorporateBranchService;
use Illuminate\Support\Facades\Log;

class CorporateController extends Controller
{
    protected $userService, $corporateBranchService;

    public function __construct(UserService $userService, CorporateBranchService $corporateBranchService)
    {
        $this->userService = $userService;
        $this->corporateBranchService = $corporateBranchService;
    }

    public function index()
    {
        $corporate = Corporate::active()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.corporate.index', [
            'corporates' => $corporate
        ]);
    }

    public function createOptions()
    {
        return view('admin.corporate.createOptions');
    }

    public function create()
    {
        return view('admin.corporate.create');
    }

    public function store(Request $request)
    {
        // validate request
        $request->validate([
            'name' => 'string|required',
            'email' => 'email|required|unique:corporates',
            'mobile_phone' => 'string|required|unique:corporates',
            'address' => 'string',
            'regency_id' => 'integer|required',
            'lat' => 'numeric',
            'long' => 'numeric',
            'logo' => 'image|max:2048',
            'users.name' => 'string|required',
            'users.email' => 'email|required|unique:users',
            'users.phone' => 'string|required|unique:users',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_phone' => $request->mobile_phone,
            'address' => $request->address,
            'country' => 'Indonesia',
            'regency_id' => $request->regency_id,
            'lat' => $request->lat,
            'long' => $request->long,
            'is_active' => true,
        ];

        // upload logo if exists
        if ($request->logo) {
            $data['logo'] = Storage::disk('public')->put('corporate_logos', $request->logo);
        }

        // persists to database
        $corporate = Corporate::create($data);

        $user = (object) [
            'corporate_id' => $corporate->id,
            'name' => $request->users['name'],
            'email' => $request->users['email'],
            'phone' => $request->users['phone']
        ];

        // dispatch event
        CorporateCreatedEvent::dispatch($corporate, $user);

        return response()->noContent(Response::HTTP_CREATED);
    }

    public function edit($id)
    {
        $corporate = Corporate::find($id);

        return view('admin.corporate.edit', [
            'corporate' => $corporate
        ]);
    }

    public function show($id)
    {
        $corporate = Corporate::find($id);

        return response()->json([
            'id' => $corporate->id,
            'name' => $corporate->name,
            'mobile_phone' => $corporate->mobile_phone,
            'email' => $corporate->email,
            'address' => $corporate->address,
            'country' => $corporate->country,
            'province' => $corporate->Regency->province,
            'regency' => $corporate->Regency,
            'lat' => $corporate->lat,
            'long' => $corporate->long,
            'logo' => $corporate->logo
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string',
            'email' => 'email',
            'mobile_phone' => 'string',
            'address' => 'string',
            'regency_id' => 'integer',
            'lat' => 'numeric',
            'long' => 'numeric',
            'logo' => 'image|max:2048'
        ]);

        $branch = Corporate::find($id);

        $data = [
            'name' => $request->name,
            'mobile_phone' => $request->mobile_phone,
            'email' => $request->email,
            'address' => $request->address,
            'regency_id' => $request->regency_id,
            'lat' => $request->lat,
            'long' => $request->long,
        ];

        if ($request->logo) {
            Storage::disk('public')->delete($branch->logo);
            $data['logo'] = Storage::disk('public')->put('corporate_logos', $request->logo);
        }

        Corporate::where('id', $id)->update($data);
        return;
    }

    public function copyFromBranch()
    {
        return view('admin.corporate.copy');
    }

    public function findBranchByName(Request $request)
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
                'email' => $branch->email,
                'address' => $branch->address,
                'mobile_phone' => $branch->mobile_phone,
                'province' => $branch->Regency->province,
                'regency' => $branch->Regency,
                'lat' => $branch->lat,
                'long' => $branch->long
            ];

            if ($branch->logo) {
                $item['logo'] = asset("storage/{$branch->logo}");
            }

            return $item;
        });

        return response()->json($data);
    }

    public function storeCopiedBranch(Request $request)
    {
        $request->validate([
            'corporates.name' => 'string|required',
            'corporates.email' => 'email|required|unique:corporates',
            'corporates.mobile_phone' => 'string|required|unique:corporates',
            'corporates.address' => 'string',
            'corporates.regency_id' => 'integer|required',
            'corporates.lat' => 'numeric',
            'corporates.long' => 'numeric',
            'corporates.logo' => 'image|max:2048',
            'users.name' => 'string|required',
            'users.email' => 'email|required|unique:users',
            'users.phone' => 'string|required|unique:users',
            'branch_id' => 'numeric|required'
        ]);

        $branch = Branch::find($request->branch_id);

        if ($branch->corporate_id) {
            return response()->json([
                'message' => 'Cabang sudah memiliki corporate'
            ], 400);
        }

        $corporateData = $request->corporates;
        $corporateData['country'] = 'Indonesia';
        $corporateData['is_active'] = true;

        if (!isset($request->corporates['logo'])) {
            $corporateData['logo'] = $branch->logo;
        }

        DB::beginTransaction();
        
        try {
            $corporate = Corporate::create($corporateData);

            $this->userService->createCorporate([
                'corporate_id' => $corporate->id,
                'name' => $request->users['name'],
                'email' => $request->users['email'],
                'phone' => $request->users['phone'],
            ]);

            $this->corporateBranchService->addFromBranch(
                $branch->id,
                $corporate->id
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e);

            return response()->json([
                'message' => 'Internal server error'
            ], 500);
        }

        return response()->noContent(Response::HTTP_CREATED);
    }
}
