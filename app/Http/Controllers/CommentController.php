<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\CommentService;

class CommentController extends Controller
{
    /**
     * Comment Service
     *
     * @var CommentService
     */
    protected $commentService;

    /**
     * CommentController constructor.
     *
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $event_id)
    {
        $request['event_id'] = $event_id;
        try {
            $this->commentService->validateInput($request->all());
        } catch (\Exception $e) {
            abort(422);
        }

        $response = $this->commentService->create($request);

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
            $this->commentService->validateInput($request->all(), true, $id);
        } catch (\Exception $e) {
            abort(422);
        }

        $response = $this->commentService->update($request, $id);

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
}
