<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Assignment extends Model
{
    public function member()
    {    	
        return $this->belongsTo('App\Member', 'member_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }
}
