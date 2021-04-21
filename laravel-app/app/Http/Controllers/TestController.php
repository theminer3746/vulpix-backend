<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function addResult(Request $request)
    {
        $test = $this->test->where('application_id', $request->input('appInfo.identifier'))
            ->orderBy('created_at', 'desc')
            ->firstOrFail();

        if ($request->input('status') === 'success') {
            $test->status = 'done';
        } else {
            $test->status = 'error';
        }

        $test->result = $request->input('result');
        $test->save();

        $response = Http::post('https://vulpix-backend.herokuapp.com/api/result', $request->input('result'));

        Log::debug("Add result reponse : $response");

        return response()->json();
    }
}
