<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBranch;
use App\Branch;
use App\IndustryCategory;
use App\ScheduleTemplate;
use App\User;
use Countries;
use App\Models\Province;
use Storage;

class BranchController extends Controller
{
    public function register()
    {
        $countries = Countries::getList('en_US');
        $categories = IndustryCategory::all();
        $templates = ScheduleTemplate::all();
        $provinces = Province::all();
        return view('register', [
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces
        ]);
    }

    public function store(StoreBranch $request)
    {
        // create branch
        $input = $request->all();
        $input['logo'] = Storage::disk('public')->put('branch_logos', $request->logo);
        $input['photo'] = Storage::disk('public')->put('branch_photos', $request->photo);
        $input['status'] = 'unverified';
        $branch = Branch::create($input);

        // create admin branch
        $user = User::create([
            'branch_id' => $branch->id,
            'name' => $input['admin_name'],
            'email' => $input['admin_email'],
            'password' => $input['admin_password'],
            'phone' => $input['admin_phone'],
            'role' => 'admin_branch'
        ]);
        
        return redirect(route('branch.afterRegister'));
    }

    public function afterRegister()
    {
        return view('afterRegister');
    }
}
