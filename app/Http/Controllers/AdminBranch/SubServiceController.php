<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use App\Models\SubService;

class SubServiceController extends Controller
{
    public function index($parentServiceId)
    {
        $parentService = Service::find($parentServiceId);
        $services = SubService::where('parent_id', $parentServiceId)->get();

        return view('adminBranch.subService.index', [
            'parentService' => $parentService,
            'services' => $services
        ]);
    }

    public function create($parentServiceId)
    {
        $parentService = Service::find($parentServiceId);

        return view('adminBranch.subService.create', [
            'parentService' => $parentService
        ]);
    }

    public function store($parentServiceId, Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        SubService::create([
            'parent_id' => $parentServiceId,
            'name' => $request->name
        ]);

        $request->session()->flash('success', 'Sub layanan ditambahkan');

        return redirect()->route('admin-branch.branch-configuration.service.sub-service.create', $parentServiceId);
    }

    public function destroy($parentId, $subServiceId)
    {
        SubService::destroy($subServiceId);

        return redirect()
            ->route('admin-branch.branch-configuration.service.sub-service.index', $parentId)
            ->with('success', 'Sub layanan dihapus');
    }
}
