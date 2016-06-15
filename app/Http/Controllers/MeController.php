<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

//use App\Services\UserService;
use App\User;

class MeController extends Controller
{
    /**
     * User Service
     *
     * @var UserService
     */
    protected $userService;

    /**
     * MeController constructor.
     *
     * @param UserService $accountService
     */
    /*public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }*/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function subscriptionsEvents()
    {
        $response = $this->userService->getLoggedUser()->getSubscriptionEvents();

        return response()->json($response);
    }*/

    public function get(Request $request)
    {
        $user = User::find($request['user']['sub']);

        return $user;
    }
}
