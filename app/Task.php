<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

	protected $appends = ['status_name'];
	private $statusNames = [
				0 => 'In Progress',
				1 => 'Done',
				2 => 'Complete',
			];
	public $timestamps = false;		

	public function getParent()
    {
        return $this->belongsTo('App\Task', 'parent_id');
    }

    public function getChildren()
    {
        return $this->hasMany('App\Task', 'parent_id');
    }	
	
	public function getStatusNameAttribute()
	{	
		return $this->statusNames[$this->status];
	}

}
