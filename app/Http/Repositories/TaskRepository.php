<?php

namespace App\Http\Repositories;

use App\Task;

class TaskRepository
{
    /**
     * Get all of the tasks
     * 
     * @return Collection
     */
    public function getAllTasks($status)
    {
		if(!isset($status) OR ($status == ""))
		{
			return Task::where('parent_id', 0)->get();
		}	
		else{
			return 	Task::where('status', $status)->get();
		}	   
		
    }
	
	public function findTask($id)
	{
		return Task::find($id);
	}
	
    public function updateTask(Task $task)
    {
        return $task->save();
    }	
	
    public function addTask(Task $task)
    {
        return $task->save();
    }
	
	public function findDependencies($parent_id)
	{
		return 	Task::where('parent_id', $parent_id)->get();
	}
	
	public function getAll(){
		return Task::lists('id', 'id');
	}
	
	
}