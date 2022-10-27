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
        $data = ['corporates' => Corporate::active()->get()];

        return view('admin.corporate.index', $data);
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

        // upload logo if exists
        $logoUrl = '';
        if ($request->logo) {
            $logoUrl = Storage::disk('public')->put('corporate_logos', $request->logo);
        }

        // persists to database
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_phone' => $request->mobile_phone,
            'address' => $request->address,
            'country' => 'Indonesia',
            'regency_id' => $request->regency_id,
            'lat' => $request->lat,
            'long' => $request->long,
            'logo' => $logoUrl,
            'is_active' => true,
        ];

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
}
