<?php

namespace App\Services;

use App\Event;
use Carbon\Carbon;

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
        return $this->event->with('sport')->where('id', $id)->first();
    }

    /**
     * Get a list of events
     *
     * @return static
     */
    public function getList()
    {
        return $this->event->with('sport', 'user', 'comments', 'comments.user', 'likes')
            ->where('datetime', '>=', Carbon::now())
            ->orderBy('highlight', 'desc')->orderBy('datetime', 'asc')->paginate(10);
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
            'datetime' => ['required', 'date'],
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
        $event = [
            'description' => $input->description,
            'datetime' => strtotime($input->datetime),
            'sport_id' => $input->sport_id,
            'user_id' => $input->user['sub']
        ];

        if ($input->location) {
            $event['location'] = $input->location;
            $event['location_description'] = $input->location_description;
        }

        $event = $this->event->create($event);

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

        $eventData = [
            'datetime' => strtotime($input->datetime),
            'description' => $input->description,
            'sport_id' => $input->sport_id,
        ];

        if ($input->location) {
            $eventData['location'] = $input->location;
            $eventData['location_description'] = $input->location_description;
        }

        $event->update($eventData);

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

    public function delete($id)
    {
        $event = $this->event->findOrFail($id);

        $event->comments()->delete();
        $event->likes()->detach();

        $event->delete();
    }

    public function highlight($id)
    {
        $event = $this->event->findOrFail($id);
        if ($event->highlight == 1) {
            $event->highlight = 0;
        } else {
            $event->highlight = 1;
        }
        $event->save();
    }

    public function statistics()
    {
        $events = app('db')->select('select sport_id, count(*) as events, sum(comments) as comments,
            sum(likes) as likes from
            (select event_id, sport_id, likes, count(comments.event_id) as comments from (select
                events.id, events.sport_id,
                count(likes.event_id) as likes
                from events
                left join likes ON events.id = likes.event_id
                group by events.id) as events
                left join comments ON events.id = comments.event_id
                group by events.id) as events
            group by sport_id;');
        /*$events = $this->event->with('sport', 'comments', 'likes')->get();
        $total = $events->count();*/

        return $events;
    }

    public function getBySports($sports)
    {
        return $this->event->with('sport', 'user', 'comments', 'comments.user', 'likes')
            ->whereHas('sport', function ($q) use ($sports) {
                $q->whereIn('id', $sports);
            })
            ->where('datetime', '>=', Carbon::now())
            ->orderBy('highlight', 'desc')->orderBy('datetime', 'asc')->paginate(10);
    }

    public function getToday()
    {
        return $this->event->with('sport', 'user', 'comments', 'comments.user', 'likes')
            ->whereDate('datetime', '=', Carbon::today()->toDateString())
            ->orderBy('highlight', 'desc')->orderBy('datetime', 'asc')->paginate(10);
    }

    public function getPast()
    {
        return $this->event->with('sport', 'user', 'comments', 'comments.user', 'likes')
            ->where('datetime', '<', Carbon::now())
            ->orderBy('highlight', 'desc')->orderBy('datetime', 'desc')->paginate(10);
    }
}