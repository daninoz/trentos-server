<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\UserService;
use App\User;

class MeController extends Controller
{
    /**
     * User Service
     *
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function getFeed(Request $request)
    {
        $response = $this->userService->getFeed($request['user']['sub']);

        return response()->json($response);
    }

    public function get(Request $request)
    {
        $user = $this->userService->get($request['user']['sub']);

        return $user;
    }
}
