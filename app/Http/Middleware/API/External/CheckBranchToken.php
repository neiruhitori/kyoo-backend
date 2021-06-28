<?php

namespace App\Http\Middleware\API\External;

use Closure;
use Illuminate\Http\Request;
use App\BranchToken;

class CheckBranchToken
{

    private function failedResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'failed to validate branch token'
        ], 403);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->branch_token) {
            return $this->failedResponse();
        }
        
        $branchToken = BranchToken::whereToken($request->branch_token)->exists();
        if (!$branchToken) {
            return $this->failedResponse();
        }

        return $next($request);
    }
}
