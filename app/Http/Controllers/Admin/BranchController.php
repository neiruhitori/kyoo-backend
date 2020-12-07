<?php

namespace App\Http\Controllers\Admin;

use App\Branch;
use App\BranchType;
use App\BranchConfiguration;
use App\IndustryCategory;
use App\ScheduleTemplate;
use App\User;
use App\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranch;
use App\Http\Requests\Admin\UpdateBranch;
use Illuminate\Http\Request;
use Countries;
use App\Models\Province;
use App\Mail\RegisteredBranchMail;
use Illuminate\Support\Facades\Mail;

use Storage;
use Auth;

class BranchController extends Controller
{

    /**
     * @param integer $length
     * @param string $characters
     * 
     * @return void
     */
    private function generateRandomString($length = 3, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branch::all();
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
        $branchTypes = BranchType::all();
        return view('admin.branch.create', [
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces,
            'branchTypes' => $branchTypes
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

        // create branch configuration
        BranchConfiguration::create([
            'branch_id' => $branch->id
        ]);

        // create admin branch
        $password = $this->generateRandomString(3, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // random uppercase
        $password .= $this->generateRandomString(3, 'abcdefghijklmnopqrstuvwxyz'); // random lowercase
        $password .= $this->generateRandomString(3, '0123456789'); // random number

        $user = User::create([
            'branch_id' => $branch->id,
            'name' => $input['admin_name'],
            'email' => $input['admin_email'],
            'password' => $password,
            'is_password_changed' => false,
            'phone' => $input['admin_phone'],
            'role' => 'admin_branch'
        ]);
        
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Create Branch'
        ]);
        // sending email
        Mail::to($user->email)->send(new RegisteredBranchMail($branch, $password));

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
        $branchTypes = BranchType::all();
        return view('admin.branch.edit', [
            'branch' => $branch,
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces,
            'branchTypes' => $branchTypes
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
            'phone' => $input['admin_phone'],
        ]);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Branch'
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
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Branch'
        ]);
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
              Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Success Verify Branch'
                ]);
              $request->session()->flash('success', 'Branch '.$branch->name.' has been verified!');
          } else {
              Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Failed Verify Branch'
                ]);
              $request->session()->flash('error', 'Branch '.$branch->name.' has been rejected!');
          }
          
          return redirect(route('admin.branch.verify.index'));
      }
}
