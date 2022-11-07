<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Events\CorporateCreatedEvent;

class CorporateController extends Controller
{
    public function index()
    {
        $corporate = Corporate::active()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.corporate.index', [
            'corporates' => $corporate
        ]);
    }

    public function create()
    {
        return view('admin.corporate.create');
    }

    public function store (Request $request)
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

        $corporate->user = [
            'corporate_id' => $corporate->id,
            'name' => $request->users['name'],
            'email' => $request->users['email'],
            'phone' => $request->users['phone']
        ];

        // dispatch event
        CorporateCreatedEvent::dispatch($corporate);

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
}
