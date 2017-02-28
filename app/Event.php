<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['description', 'location', 'sport_id', 'user_id'];

    protected $hidden = ['updated_at', 'sport_id', 'user_id'];

    protected $casts = [
        'highlight' => 'int',
    ];

    public function getDates()
    {
        return ['date', 'created_at', 'updated_at'];
    }

    public function sport()
    {
        return $this->belongsTo('App\Sport');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
}
