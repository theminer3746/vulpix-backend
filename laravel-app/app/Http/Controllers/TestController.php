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
        // $request->validate([
        //     'application_id' => 'required|regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+[0-9a-z_]$/i',
        // ]);

        $this->test->create([
            'application_id' => $request->application_id,
            'android_version' => $request->android_version ?? '9.0',
            'forced' => $request->forced ?? false,
        ]);

        return response()->json([], 201);
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
