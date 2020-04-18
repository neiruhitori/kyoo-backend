<?php

namespace App\Http\Controllers\Admin;

use App\Branch;
use App\IndustryCategory;
use App\ScheduleTemplate;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranch;
use App\Http\Requests\Admin\UpdateBranch;
use Illuminate\Http\Request;
use Countries;
use App\Models\Province;

use Storage;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branch::where('status', 'verified')->get();
        return view('admin.branch.index')->withBranches($branches);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Countries::getList('en_US');
        $categories = IndustryCategory::all();
        $templates = ScheduleTemplate::all();
        $provinces = Province::all();
        return view('admin.branch.create', [
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBranch $request)
    {
        // create branch
        $input = $request->all();
        $input['logo'] = Storage::disk('public')->put('branch_logos', $request->logo);
        $input['photo'] = Storage::disk('public')->put('branch_photos', $request->photo);
        $input['status'] = 'verified';
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
        $request->session()->flash('success', 'Branch '.$request->name.' has been inserted!');
        return redirect(route('admin.branch.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return view('admin.branch.show')->withBranch($branch);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        $countries = Countries::getList('en_US');
        $categories = IndustryCategory::all();
        $templates = ScheduleTemplate::all();
        $provinces = Province::all();
        return view('admin.branch.edit', [
            'branch' => $branch,
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranch $request, Branch $branch)
    {
        $input = $request->all();
        if (isset($request->logo)) {
            Storage::disk('public')->delete($branch->logo);
            $input['logo'] = Storage::disk('public')->put('branch_logos', $request->logo);
        }

        if (isset($request->photo)) {
            Storage::disk('public')->delete($branch->logo);
            $input['photo'] = Storage::disk('public')->put('branch_photos', $request->photo);
        }
        $branch->update($input);

        $admin = $branch->Admin[0];
        $admin->update([
            'name' => $input['admin_name'],
            'email' => $input['admin_email'],
            'password' => $input['admin_password'] ?: '',
            'phone' => $input['admin_phone'],
        ]);
        $request->session()->flash('warning', 'Branch '.$request->name.' has been updated!');
        return redirect(route('admin.branch.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Branch $branch)
    {
        $branch->delete();
        $request->session()->flash('error', 'Branch '.$branch->name.' has been suspended!');
        return redirect(route('admin.branch.index'));
    }

    /**
     * Display a listing of Verifying Branch
     */

     public function verifyList()
     {
         $branches = Branch::where('status', '!=', 'verified')->get();
         return view('admin.branch.verify')->withBranches($branches);
     }

     /**
      * Change status branch to be verified or rejected
      */
      public function doVerify(Request $request, Branch $branch)
      {
          $branch->status = $request->status;
          $branch->save();
          if ($request->status == 'verified') {
              $request->session()->flash('success', 'Branch '.$branch->name.' has been verified!');
          } else {
              $request->session()->flash('error', 'Branch '.$branch->name.' has been rejected!');
          }
          
          return redirect(route('admin.branch.verify.index'));
      }
}
