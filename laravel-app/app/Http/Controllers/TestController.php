<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function __construct(private Test $test)
    {
    }

    public function createTest(Request $request)
    {
        $request->validate([
            'application_id' => 'required|regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+[0-9a-z_]$/i',
            'requester_email' => 'nullable|email:rfc'
        ]);

        $this->test->create([
            'application_id' => $request->application_id,
            'android_version' => $request->android_version ?? '9.0',
            'forced' => $request->forced ?? false,
            'requester_email' => $request->requester_email,
            'uuid' => Str::uuid(),
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

        if ($query->count() === 0) {
            return response()->json([], 404);
        }

        if ((bool)$request->input('mark_as_running') === true) {
            foreach ($result as $item) {
                $item->status = 'running';
                $item->status_dynamic = 'running';
                $item->status_static = 'running';
                $item->assigned_to = $request->bearerToken();
                $item->assigned_at = now();

                $item->save();
            }
        }

        return response()->json($result);
    }

    public function addResult(Request $request)
    {
        Log::debug("Add result request : " . print_r($request->all(), true));

        $resultForAppVerExists = $this->test
            ->where('application_id', $request->input('appInfo.identifier'))
            ->where('application_version', $request->input('result.version'))
            ->exists();

        $test = null;
        $requesterEmail = null;
        if ($resultForAppVerExists) {
            // We know about this version of the app

            // Update the old result
            $test = $this->test
                ->where('application_id', $request->input('appInfo.identifier'))
                ->where('application_version', $request->input('result.version'))
                ->orderByDesc('created_at')
                ->firstOrFail();

            // Mark the new result as a duplicate
            $duplicateTest = $this->test->where('uuid', $request->uuid)->firstOrFail();
            $duplicateTest->status = 'duplicate';
            $duplicateTest->application_version = $request->input('result.version');
            $duplicateTest->save();

            $requesterEmail = $duplicateTest->requester_email;
        } else {
            // New version of the app
            $test = $this->test->where('application_id', $request->input('appInfo.identifier'))
                ->where('application_version', null)
                ->orderByDesc('created_at')
                ->firstOrFail();

            $test->application_version = $request->input('result.version');
            $requesterEmail = $test->requester_email;
        }

        if ($request->input('status') === 'success') {
            $test->status = 'done';
        } else {
            $test->status = 'error';
        }

        if ($request->input('result.testingMethod') == 'STATIC_ONLY') {
            $test->static_done_at = now();
            $test->status_static = ($request->input('status') === 'success') ? 'done' : (($request->has('error')) ? $request->input('error') : "UNSPECIFIED_ERROR");
            $test->result_static = $request->input('result');
        }

        if ($request->input('result.testingMethod') == 'DYNAMIC_ONLY') {
            $test->dynamic_done_at = now();
            $test->status_dynamic = ($request->input('status') === 'success') ? 'done' : (($request->has('error')) ? $request->input('error') : "UNSPECIFIED_ERROR");
            $test->result_dynamic = $request->input('result');
        }

        $test->save();

        $addAppResponse = Http::post('https://vulpix-backend.herokuapp.com/api/application', $request->input('appInfo'));
        Log::debug("Add app reponse : " . $addAppResponse->body());

        $result = array_merge([
            'applicationId' => $request->input('appInfo.identifier'),
            'version' => $request->input('appInfo.version'),
            'testingMethod' => $request->input('testingMethod'),
            'requesterEmail' => $requesterEmail,
            'error' => $request->input('error'),
        ], $request->input('result'));
        $addResultResponse = Http::post('https://vulpix-backend.herokuapp.com/api/result', $result);
        Log::debug("Add result reponse : " . $addResultResponse->body());

        if ($addResultResponse->failed()) {
            throw new Exception("Send failed");
        }

        return response()->json();
    }
}
