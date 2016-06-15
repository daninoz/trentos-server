<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\SportService;

class SportController extends Controller
{
    /**
     * Sport Service
     *
     * @var SportService
     */
    protected $sportService;

    /**
     * SportController constructor.
     *
     * @param SportService $sportService
     */
    public function __construct(SportService $sportService)
    {
        $this->sportService = $sportService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->sportService->getList();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->sportService->validateInput($request->all());
        } catch (\Exception $e) {
            abort(422);
        }

        $response = $this->sportService->create($request);

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->sportService->validateInput($request->all(), true, $id);
        } catch (\Exception $e) {
            abort(422);
        }

        $response = $this->sportService->update($request, $id);

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // TODO
    }

    /**
     * Get the events of the sport
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function events($id)
    {
        $response = $this->sportService->getEvents($id);

        return response()->json($response);
    }
}
