<?php

namespace App\Http\Controllers\AdminBranch;

use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortalMenuController extends Controller
{
    public function edit()
    {
        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;
        $services = Auth::user()->Branch->Service;

        return view('adminBranch.branchConfiguration.portalMenu.index', compact('branchConfiguration','services'));
    }

    public function formServiceUpdate(Request $request)
    {
        $formTemplates = $request->template_form_service;

        foreach ($formTemplates as $serviceId => $formTemplate) {
            $form = $formTemplate == 'none' ? null : $formTemplate;
            Service::where('id', $serviceId)->update([
                'template_form_booking' => $form
            ]);
        }
        $request->session()->flash('warning', __('Portal Menu has been updated'));
        return back();
    }

    public function update(Request $request)
    {
        $data['layer'] = $request->layer;

        if($request->web_style && $request->ticket_style){
            $data['web_style'] = $request->web_style;
            $data['ticket_style'] = $request->ticket_style;
        }
        
        if($request->template_booking_form) {
            $data['template_booking_form'] = $request->template_booking_form;
        }

        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;

        $branchConfiguration->update($data);

        $request->session()->flash('warning', __('Portal Menu has been updated'));
        return back();
    }
}
