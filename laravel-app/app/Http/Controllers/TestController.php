<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function __construct(private Test $test)
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

        $result = $query->get();

        if ($query->count() === 0)
        {
            return response()->json([], 404);
        }

        if ((bool)$request->input('mark_as_running') === true)
        {
            foreach ($result as $item)
            {
                $item->status = 'running';
                $item->save();
            }
        }

        return response()->json($result);
    }

    public function addResult(Request $request)
    {
        Log::debug("Add result request : " . print_r($request->all(), true));

        $test = $this->test->where('application_id', $request->input('appInfo.identifier'))
            ->orderBy('created_at', 'desc')
            ->firstOrFail();

        if ($request->input('status') === 'success') {
            $test->status = 'done';
        } else {
            $test->status = 'error';
        }

        if($request->input('testingMethods') == 'STATIC_ONLY')
        {
            $test->result_static = $request->input('result_static');
        }

        if($request->input('testingMethods') == 'DYNAMIC_ONLY')
        {
            $test->result_dynamic = $request->input('result_dynamic');
        }

        $test->save();

        $addAppResponse = Http::post('https://vulpix-backend.herokuapp.com/api/application', $request->input('appInfo'));
        Log::debug("Add app reponse : " . $addAppResponse->body());

        $addResultResponse = Http::post('https://vulpix-backend.herokuapp.com/api/result', $request->input('result'));
        Log::debug("Add result reponse : " . $addResultResponse->body());

        if ($addAppResponse->failed() || $addResultResponse->failed()) {
            throw new Exception("Send failed");
        }

        return response()->json();
    }
}
