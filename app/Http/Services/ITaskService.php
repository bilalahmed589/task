<?php namespace App\Http\Services;

use App\Task;

interface ITaskService{
	
	public function addTask(Task $task);
	
	public function updateTask(Task $task);
	
	public function isCircularDependent($task,$newParentId);
	
	public function findTask($id);
	
	public function deleteTask($id);
	
	public function getAllTasks($filter);
	
	public function findTaskByUser($userId);
}
