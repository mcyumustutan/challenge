<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\HttpClient;
use App\Models\Subscription;
use Illuminate\Auth\Middleware\Authorize;

class DeviceController extends Controller
{

    use HttpClient;

    public function register(Request $request)
    {

        $attr = $request->validate([
            'uid' => 'required|string|max:255',
            'appId' => 'required|string|max:255',
            'language' => 'required|string|max:255',
            'os' => 'required|string|max:255',

        ]);


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
        $token = $device->createToken($attr['uid'] . '_' . $attr['appId'] . '-token')->plainTextToken;

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


        $purhase_result = $this->get("/" . $authorized_device->os, ['receipt' => $request->receipt]);

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
