<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\BranchConfiguration;
use Illuminate\Http\Request;
use App\Log;
use App\Http\Requests\AdminBranch\UpdateBranchConfiguration;
use Illuminate\Support\Facades\Auth;

class BranchConfigurationController extends Controller
{
  public function edit()
  {
    return view('adminBranch.branchConfiguration.edit');
  }

  public function checkInConfig(Request $request)
  {
    $branchConfig = Auth::user()->Branch->BranchConfiguration;
    if($branchConfig->layer != 2){
      $request->session()->flash('warning', __('Konfigurasi ini hanya untuk page portal Hybrid Onsite-Appointment'));
      return back();
    }
    $query = BranchConfiguration::where('id',$branchConfig->id)->update([
      'check_in_rule' => $request->check_in_rule
    ]);

    if($query){
      $request->session()->flash('warning', __('Konfigurasi Check-in telah diperbarui'));
      return back();
    }

  }

  public function update(UpdateBranchConfiguration $request)
  {
    $data = $request->all();

    $defaults = [
        'queue_voice' => 'off',
        'promotion' => 'off',
        'wa_notification' => 'off',
        'wa_notification_owner' => 'off',
        'serving_directly' => 'off'
    ];

    foreach($defaults as $key => $value){
      if(!isset($request->$key)){
        $data[$key] = $value;
      }
    }

    if(!isset($request->wa_notification_owner) && empty($request->phone_owner)){
      $request->validate([
        'phone_owner' => 'nullable',
    ]);
    }else{
      $request->validate([
        'phone_owner' => 'numeric|min:5', 
    ]);
    }

    $branchConfiguration = Auth::user()->Branch->BranchConfiguration;

    if ($branchConfiguration) {
      $branchConfiguration->update($data);
    } else {
      $data['branch_id'] = Auth::user()->branch_id;
      BranchConfiguration::create($data);
    }

    Log::create([
      'user_id' => Auth::id(),
      'description' => 'Update Branch Configuration'
    ]);

    $request->session()->flash('warning', __('Branch Configuration has been updated'));
    return back();
  }
}
