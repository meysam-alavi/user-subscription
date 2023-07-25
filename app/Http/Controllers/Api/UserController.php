<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use function request;
use function response;

/**
 * @mixin
 */
class UserController extends Controller
{
    /**
     * user registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $result = array(
            'data' => null,
            'messages' =>  [
                'Database level error, please try again later. !'
            ],
            'success' => false
        );

        $validator = Validator::make(request()->all(), [
            'name' => 'required|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $result['messages'] = $validator->messages()->toArray();
        } else {
            $user = User::query()->create($request->all());
            if ($user) {
                $agent = new Agent();
                if ($agent->isMobile()) {
                    if ($agent->isAndroidOS()) {
                        $subscribeExpiredAt = time() + 2592000; // 1 month ~ 30*86400
                        Redis::command('set', ["user-{$user->id}:subscribe-expired-at", $subscribeExpiredAt]);
                    } elseif($agent->isiOS()) {
                        $subscribeExpiredAt = time() + 5184000; // 2 month ~ 60*86400
                        Redis::command('set', ["user-{$user->id}:subscribe-expired-at", $subscribeExpiredAt]);
                    }
                }

                $result['data'] = $user;
                $result['messages'] = ['user register successfully'];
                $result['success'] = true;
            }
        }

        return response()->json($result);
    }

    /**
     *
     *
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getRemindSubscribe($id, Request $request): JsonResponse
    {
        $result = array(
            'data' => null,
            'messages' =>  [
                'Database level error, please try again later. !'
            ],
            'success' => false
        );

        $request->merge(['id' => $id]);
        $validator = Validator::make(request()->all(), [
            'id' => 'required|exists:users'
        ]);

        if ($validator->fails()) {
            $result['messages'] = $validator->messages()->toArray();
        } else {

            $subscribeExpiredAt = Redis::command('get', ["user-{$id}:subscribe-expired-at"]);
            $subscribeExpiredAt = ($subscribeExpiredAt - time())/86400;

            $result['data'] = ['subscribeRemindDays' => $subscribeExpiredAt];
            $result['messages'] = [];
            $result['success'] = true;
        }

        return response()->json($result);
    }
}

