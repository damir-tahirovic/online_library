<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function getPolicies()
    {
        $policies = Policy::all();

        return response()->json($policies);
    }

    public function editPolicyPeriod(Request $request, $id)
    {
        $request->validate([
            'period' => 'required|integer|min:1',
        ]);

        $policy = Policy::find($id);

        if (!$policy) {
            return response()->json(['error' => 'Policy not found'], 404);
        }

        $policy->period = $request->input('period');

        try {
            $policy->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update policy period'], 500);
        }

        return response()->json($policy);
    }
}
