<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/redis', function (Request $request) {
    try {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            ]);

            $randomId = Str::uuid();

            $res = Redis::hSet("user:$randomId", 'name', $data['name'], 'email', $data['email'], 'password', $data['password']);
            Redis::lPush('users:sync_queue', $randomId);

            return response()->json([$res]);
    } catch(Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }
});

Route::get('redis', function (Request $request) {
    $res = Redis::sMembers($request->input('key'));

    return response()->json([$res]);
});

Route::get('/redis/all', function () {
    $keys = (Object) Redis::keys('*');
    $result = [];

    foreach ($keys as $key) {
        $result[$key] = Redis::hGetAll($key);
    }

    return response()->json($result);
});

Route::delete('/redis', function (Request $request) {
    $res = Redis::flushAll();
    return response()->json([$res]);
});
