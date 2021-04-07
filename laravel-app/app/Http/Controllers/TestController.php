<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class TestController extends Controller
{
    public function __construct(public Test $test)
    {
    }

    public function createTest(Request $request)
    {
        $this->test->create([
            'applicationId' => $request->applicationId,
            'androidVersion' => $request->androidVersion,
            'forced' => $request->forced,
        ]);

        return response()->json();
    }

    public function getTest(Request $request)
    {
        $query = $this->test;

        if ($request->has('status')) {
            $query = $query->where('status', $request->status);
        }

        if ($request->has('limit')) {
            $query = $query->limit($request->limit);
        }

        return response()->json($query->get());
    }
}
