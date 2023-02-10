<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Promotion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PromotionsController extends Controller
{
    public function index()
    {
        $promotions = Promotion::limit(10)
            ->where('branch_id', Auth::user()->branch_id)
            ->orderBy('created_at')
            ->get();
        $promotionTypes = [
            'image' => Promotion::IMAGE_TYPE,
            'text' => Promotion::TEXT_TYPE
        ];

        return view('adminBranch.branchConfiguration.promotions.index', [
            'promotions' => $promotions,
            'promotion_types' => $promotionTypes
        ]);
    }

    public function createImage()
    {
        return view('adminBranch.branchConfiguration.promotions.createImage');
    }

    public function createText()
    {
        return view('adminBranch.branchConfiguration.promotions.createText');
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'promotion_img' => 'required|image|max:512',
            'caption' => 'nullable|max:512'
        ]);

        $imagePath = Storage::disk('public')->put('promotions', $request->file('promotion_img'));

        if (!$imagePath) {
            return redirect()
                ->route('admin-branch.branch-configuration.promotions.index')
                ->with('error', 'Gambar gagal disimpan');
        }

        Promotion::create([
            'type' => Promotion::IMAGE_TYPE,
            'title' => $request->title,
            'branch_id' => Auth::user()->branch_id,
            'image_url' => $imagePath,
            'caption' => $request->caption ?? ''
        ]);

        return redirect()
            ->route('admin-branch.branch-configuration.promotions.index')
            ->with('success', 'Promosi ditambahkan');
    }

    public function storeText(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string|max:700',
            'color' => 'required|string',
            'font_size' => 'required|string'
        ]);
        
        Promotion::create([
            'type' => Promotion::TEXT_TYPE,
            'title' => $request->title,
            'branch_id' => Auth::user()->branch_id,
            'text' => $request->text,
            'color' => $request->color,
            'font_size' => $request->font_size
        ]);

        return redirect()
            ->route('admin-branch.branch-configuration.promotions.index')
            ->with('success', 'Promosi ditambahkan');
    }

    public function destroyText($id)
    {
        Promotion::destroy($id);

        return redirect()
            ->route('admin-branch.branch-configuration.promotions.index')
            ->with('success', 'Promosi dihapus');
    }

    public function destroyImage($id)
    {
        $promotion = Promotion::findOrFail($id);

        if (Storage::disk('public')->delete($promotion->image_url)) {
            $promotion->delete();
        }

        return redirect()
            ->route('admin-branch.branch-configuration.promotions.index')
            ->with('success', 'Promosi dihapus');
    }
}
