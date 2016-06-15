<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['name'];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
    
    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
