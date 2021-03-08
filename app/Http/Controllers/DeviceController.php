<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRegisterRequest;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Traits\HttpClient;
use App\Models\Subscription;
use Illuminate\Support\Str;


class DeviceController extends Controller
{

    use HttpClient;

    public function register(DeviceRegisterRequest $request)
    {

        $attr = $request->validated();


        if (!in_array($attr['os'], ['ios', 'android'])) {
            return response()->json([
                'message' => "This OS ({$attr['os']}) not supported!"
            ], 200);
        }

        //if already exists return data, else create new record
        $device = Device::firstOrCreate([
            'uid' => $attr['uid'],
            'appId' => $attr['appId'],
        ], [
            'uid' => $attr['uid'],
            'appId' => $attr['appId'],
            'language' => $attr['language'],
            'os' => $attr['os'],
        ]);

        //delete old tokens
        $device->tokens()->delete();

        //create new token
        $token = $device->createToken(Str::of($attr['uid'])->append('_')->append($attr['appId'])->append('-token'))->plainTextToken;

        return response()->json([
            'register' => 'OK',
            'token' => $token
        ]);
    }

    public function purchase(Request $request)
    {

        $authorized_device = auth()->user();


        if (!in_array($authorized_device->os, ['ios', 'android'])) {
            return response()->json([
                'device' => $authorized_device,
                'message' => "This OS ({$authorized_device->os}) not supported!"
            ], 200);
        }


        $purhase_result = $this->get(Str::start($authorized_device->os, '/'), ['receipt' => $request->receipt]);

        if (!$purhase_result["status"]) {
            return response()->json($purhase_result, 200);
        }

        Subscription::firstOrCreate([
            'device_id' => $authorized_device->id,
            'receipt' => $request->receipt,
        ], [
            'device_id' => $authorized_device->id,
            'receipt' => $request->receipt,
            'expire_date' => $purhase_result['expire-date'],
            'status' => 'started'
        ]);

        return response()->json($purhase_result, 201);
    }

    function checkSubscription()
    {
        $authorized_device = auth()->user();

        $subscriptionDetail = Subscription::findOrFail($authorized_device->id);

        return response()->json($subscriptionDetail, 200);
    }
}
