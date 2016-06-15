<?php

namespace App\Services;

use App\Comment;
use DateTime;

class CommentService
{
    /**
     * Comment model
     *
     * @var Comment
     */
    protected $comment;

    /**
     * CommentService constructor.
     *
     * @param Comment    $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get an comment
     *
     * @param $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->comment->findOrFail($id);
    }

    /**
     * Get a list of comments
     *
     * @return static
     */
    public function getList()
    {
        $comments = $this->comment->with('sport', 'user')->get();

        $response = $comments->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description,
                'date' => $item->date_formatted,
                'sport' => $item->sport,
                'user' => $item->user,

            ];
        });

        return $response;
    }

    /**
     * Validate the input
     *
     * @param      $input
     * @param bool $update
     * @param null $id
     *
     * @throws \Exception
     */
    public function validateInput($input, $update = false, $id = null)
    {
        $rules = [
            'comment' => ['required'],
            'event_id' => ['required', 'exists:events,id'],
        ];

        if ($update) {
            unset($rules['event_id']);
        }

        $validator = app('validator')->make($input, $rules);

        if ($validator->fails()) {
            throw new \Exception;
        }
    }

    /**
     * Creates a new comment
     *
     * @param $input
     *
     * @return array
     */
    public function create($input)
    {
        $comment = $this->comment->create([
            'comment' => $input->comment,
            'event_id' => $input->event_id,
            'user_id' => $input->user['sub']
        ]);

        return $comment->event->comments()->with('user')->get();
    }

    /**
     * Updates a comment
     *
     * @param $input
     * @param $id
     *
     * @return array
     */
    public function update($input, $id)
    {
        $comment = $this->comment->findOrFail($id);

        $comment->update([
            'comment' => $input->comment
        ]);

        return ["id" => $comment->id];
    }
}