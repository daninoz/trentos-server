<?php

namespace App\Services;

use App\User;
use App\Services\EventService;
use DateTime;
use Storage;

class UserService
{
    /**
     * Event model
     *
     * @var Event
     */
    protected $user;

    /**
     * EventService constructor.
     *
     * @param Event    $event
     */
    public function __construct(User $user, EventService $eventService)
    {
        $this->user = $user;
        $this->event = $eventService;
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
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'email_2' => ['required', 'same:email'],
            'password' => ['required'],
            'password_2' => ['required', 'same:password'],
            'sports.*' => ['exists:sports,id'],
            'avatar' => ['file'],
        ];

        $validator = app('validator')->make($input, $rules);

        if ($validator->fails()) {
            throw new \Exception;
        }
    }

    public function validateSportsInput($input)
    {
        $rules = [
            'sports.*' => ['exists:sports,id']
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
        $file = $input->file('avatar');

        $user = $this->user->create([
            'email' => $input->email,
            'name' => $input->name,
            'password' => app('hash')->make($input->password),
            'avatar' => $file->getClientOriginalName(),
        ]);

        $file->move('images/' . $user->id, $file->getClientOriginalName());

        $user->sports()->attach($input->sports);

        return ["id" => $user->id];
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
            'date' => new DateTime($input->date),
            'sport_id' => $input->sport_id,
        ]);

        return ["id" => $event->id];
    }

    public function get($id)
    {
        $user = $this->user->find($id);

        $user->load('sports');

        return $user;
    }

    public function getFeed($id)
    {
        $user = $this->get($id);

        if (!$user->sports) {
            return [];
        }

        $sports = $user->sports->pluck('id');

        return $this->event->getBySports($sports);
    }

    public function updateSports($id, $sports)
    {
        $user = $this->user->find($id);

        $user->sports()->sync($sports['sports']);

        $user->load('sports');

        return $user;
    }
}