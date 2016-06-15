<?php

namespace App\Services;

use App\Event;
use DateTime;

class EventService
{
    /**
     * Event model
     *
     * @var Event
     */
    protected $event;

    /**
     * EventService constructor.
     *
     * @param Event    $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get an event
     *
     * @param $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->event->findOrFail($id);
    }

    /**
     * Get a list of events
     *
     * @return static
     */
    public function getList()
    {
        return $this->event->with('sport', 'user', 'comments', 'comments.user', 'likes')->get();
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
            'description' => ['required'],
            'date' => ['required', 'date'],
            'sport_id' => ['required', 'exists:sports,id'],
        ];

        $validator = app('validator')->make($input, $rules);

        if ($validator->fails()) {
            throw new \Exception;
        }
    }

    /**
     * Creates a new event
     *
     * @param $input
     *
     * @return array
     */
    public function create($input)
    {
        //dd($input->date);
        $event = $this->event->create([
            'description' => $input->description,
            'date' => new DateTime($input->date),
            'sport_id' => $input->sport_id,
            'user_id' => $input->user['sub']
        ]);

        return ["id" => $event->id];
    }

    /**
     * Updates a event
     *
     * @param $input
     * @param $id
     *
     * @return array
     */
    public function update($input, $id)
    {
        $event = $this->event->findOrFail($id);

        $event->update([
            'description' => $input->description,
            'date' => $input->date,
            'sport_id' => $input->sport_id,
        ]);

        return ["id" => $event->id];
    }

    /**
     * Likes an event
     *
     * @param $input
     * @param $id
     *
     * @return array
     */
    public function like($id, $user_id)
    {
        $event = $this->event->with('likes')->where('id', $id)->first();
        
        if ($event->likes->contains($user_id)) {
            $event->likes()->detach($user_id);
        } else {
            $event->likes()->attach($user_id);
        }

        return $event->likes()->get();
    }
}