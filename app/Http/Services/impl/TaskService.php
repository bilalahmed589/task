<?php namespace App\Http\Services\impl;

use App\Http\Services\ITaskService;
use App\Task;
use App\Http\Repositories\TaskRepository;
use DB;

class TaskService implements ITaskService{

 /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $tasksRepo;
	
	
	public function __construct(TaskRepository $tasksRepo)
    {
        $this->tasksRepo = $tasksRepo;
    }
	

	/**
	 * This method first check the existence of parent after add task and start chain reaction to top
	 * Note : This method is transactional
	 * input : Task
	 * @return Task if success else return FALSE
	 */
	public function addTask(Task $task)
	{
	    try{
			DB::beginTransaction();
			$parentTask = null;
			if($task->parent_id > 0)
			{
				$parentTask = $this->tasksRepo->findTask($task->parent_id);
				if($parentTask == null)
				{
					return false;
				}
					
			}	
			$task =  $this->tasksRepo->addTask($task);

			//Set new Hierarchy
			if($task){
				if($parentTask != null && $parentTask->status == 2)
				{
					$this->updateTaskStatus($parentTask->id,1);
				}
			}
			DB::commit();
			return $task;
		}catch(Exception $e){
			DB::rollback();
			return FALSE;
		}
	}
	
	private function traverseHierarchy($tasks,&$data)
	{
		foreach($tasks as $task)
		{
			array_push($data,$task);
			if(count($task->getChildren->all()) > 0)
			{
				$this->traverseHierarchy($task->getChildren->all(),$data);
			}	
		}
	}
	
	/**
	 * Get all tasks
	 *
	 * @return Collection
	 */
	public function getAllTasks($filter)
	{
		$tasks = $this->tasksRepo->getAllTasks($filter);
		if(!isset($filter) OR ($filter== ""))
		{
			$data = array();
			$this->traverseHierarchy($tasks,$data);
			$tasks = $data;	
			
		}
	
		foreach($tasks as $task){
			$dependentTasks = $this->tasksRepo->findDependencies($task->id);
			$task->totalDependent = count($dependentTasks);
			$totalDone = 0;
			$totalComplete = 0;
			foreach($dependentTasks as $dependentTask)
			{
				if($dependentTask->status == 1){
					$totalDone++;
				}else if($dependentTask->status == 2){
					$totalComplete++;
				}
			}
			$task->totalDone = $totalDone;
			$task->totalComplete = $totalComplete;
		}
		return $tasks;
	}
	
	/**
	 * This method first check self reference , after it updates and update old parent and new parent hierarchies
	 * Note : This method is transactional
	 * input : Task
	 * @return Task if success else return FALSE
	 */	
	public function updateTask(Task $task)
	{
		$newParentId = $task->newParent;
		$newTitle = $task->title;
		try{
			DB::beginTransaction();
			$task = $this->tasksRepo->findTask($task->id);
			$oldParentTask = $this->tasksRepo->findTask($task->parent_id);
			$task->parent_id = $newParentId;
			$task->title = $newTitle;
			$this->tasksRepo->updateTask($task);		
			
			//Update old hierarchy
			if($oldParentTask !=null && $oldParentTask->status == 1){
				$this->updateTaskStatus($oldParentTask->id,1);
			}
			
			//Set new Hierarchy
			$parentTask = $this->tasksRepo->findTask($newParentId);
			if($task->status <= 1 && $parentTask!=null && $parentTask->status == 2)
			{
				$this->updateTaskStatus($parentTask->id,1);
			}
			DB::commit();
			return $task;
		}catch(Exception $e){
			DB::rollback();
			return FALSE;
		}	
	}
	
	public function isCircularDependent($task,$newParentId){
		//$task = $task->find($newTask->id);
		$dependentTasks = $this->tasksRepo->findDependencies($task->id);
		foreach($dependentTasks as $dependentTask)
		{
			if($dependentTask->id == $newParentId)
			{
					return true;
			}
			$this->isCircularDependent($dependentTask,$newParentId);
		}
		return false;
		
	}
	
	public function updateTaskStatus($taskId,$status)
	{
		try{
			DB::beginTransaction();
			$task = $this->tasksRepo->findTask($taskId);
			if(is_null($task) ){
				return null;
			}
			//Find all dependents
			if($status > 0)
			{		
				$dependentTasks = $this->tasksRepo->findDependencies($task->id);
				$newStatus = 2;
				foreach($dependentTasks as $dependentTask)
				{
					if($dependentTask->status !=2)
					{
						$newStatus = 1;
					}
				}
				$task->status=$newStatus;
				$this->tasksRepo->updateTask($task);
				if($task->getParent()->first()!=null && $task->getParent()->first()->status != 0)
				{
					$this->updateTaskStatus($task->parent_id,$status);
				}
			}else if($status == 0)
			{
				$task->status=$status;
				$this->tasksRepo->updateTask($task);
				while($task->parent_id >0){
					$task = $this->tasksRepo->findTask($task->parent_id);
					if($task->status == 2)
					{
						$task->status=1;
						$this->tasksRepo->updateTask($task);
					}	
				}
			}
			DB::commit();
		}catch(Exception $e){
			DB::rollback();
			return FALSE;
		}
	}
	
	public function findTask($id)
	{
		$task = $task->find($id);
		return $task;		
	}
	
	public function deleteTask($id)
	{
		$task = $task->find(1);
		$task->delete();
	}
	
	public function findTaskByUser($userId)
	{
		//TODO
	}
	
	public function getAll(){
		return $this->tasksRepo->getAll();
	}
	
}
