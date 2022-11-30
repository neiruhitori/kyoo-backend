<?php

namespace App\Services;

use App\Branch;
use App\User;
use App\Models\Corporate;
use App\Events\CorporateBranchAddedEvent;

class CorporateBranchService {
    public function getCorporateBranches($corporateId)
    {
        return Branch::onsite()->where('corporate_id', $corporateId)->get();
    }

    public function addFromBranch($branchId, $corporateId)
    {
        $branch = Branch::find($branchId);
        $corporate = Corporate::find($corporateId);

        if (!$branch) {
            throw new \Exception('Cannot find branch');
        }

        if (!$corporate) {
            throw new \Exception('Cannot find corporate');
        }

        $branch->corporate_id = $corporateId;
        $branch->save();

        $user = User::where([
            'branch_id' => $branchId,
            'role' => 'admin_branch',
        ])->first();

        CorporateBranchAddedEvent::dispatch(
            $branch, $user, $corporate
        );

        return;
    }
}