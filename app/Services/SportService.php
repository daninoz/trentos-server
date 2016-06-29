<?php

namespace App\Services;

use App\Sport;

class SportService
{
    /**
     * Sport model
     *
     * @var Sport
     */
    protected $sport;

    /**
     * SportService constructor.
     *
     * @param Sport    $sport
     */
    public function __construct(Sport $sport)
    {
        $this->sport = $sport;
    }

    /**
     * Get an sport
     *
     * @param $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->sport->findOrFail($id);
    }

    /**
     * Get a list of sports
     *
     * @return static
     */
    public function getList()
    {
        $sports = $this->sport->all();

        $response = $sports->map(function ($item) {
            return [
                "id" => $item->id,
                "name" => $item->name,
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
            'name' => ['required', 'max:100', 'unique:sports,name'],
        ];

        if ($update) {
            $rules['name'][2] .= "," . $id;
        }

        $validator = app('validator')->make($input, $rules);

        if ($validator->fails()) {
            throw new \Exception;
        }
    }

    /**
     * Creates a new sport
     *
     * @param $input
     *
     * @return array
     */
    public function create($input)
    {
        $sport = $this->sport->create([
            'name' => $input->name
        ]);

        return ["id" => $sport->id];
    }

    /**
     * Updates a sport
     *
     * @param $input
     * @param $id
     *
     * @return array
     */
    public function update($input, $id)
    {
        $sport = $this->sport->findOrFail($id);

        $sport->update([
            'name' => $input->name
        ]);

        return ["id" => $sport->id];
    }

    public function getEvents($id)
    {
        return $this->sport->findOrFail($id)->events()
            ->with('sport', 'user', 'comments', 'comments.user', 'likes')
            ->orderBy('highlight', 'desc')->orderBy('date', 'asc')->get();
    }
}