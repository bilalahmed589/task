<?php

namespace App\Http\Controllers;

use App\Task;
use App\Http\Controllers\Controller;
use App\Http\Services\ITaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
	private $taskService;
	private $userService;

	/**
	 * Create a task controller instance.
	 * Inject TaskService
	 * @return void
	 */
	public function __construct(ITaskService $taskService)
	{
		$this->taskService = $taskService;
	}
	
    /**
     * Fetches all the task apply filter if required
     *
     * @param  int  $filter (optional)
     * @return Response
     */
    public function listTasks(Request $request)
    {
		$filter = $request->input('status');
		$tasks = $this->taskService->getAllTasks($filter);
		return view('task.list',['tasks' => $tasks,'taskForSelect'=> $this->taskService->getAll()]);
    }

    /**
     * Update task status roll up to the top
     *
     * @param  int  taskId, New Status
     * @return Response
     */	
    public function updateTaskStatus(Request $request)
    {
			$taskId = $request->input('taskId');
			$status = $request->input('status');
			$tasks = $this->taskService->updateTaskStatus($taskId,$status);
			return "$taskId Updated";
    }
	
    public function updateTask(Request $request)
    {
			$this->validate($request, [
				'title' => 'required'
			]);
			$data = (object) $request->json()->all();
			
			if($data->new_parent == $data->id)
			{
				//TODO Message should be movded to language file
				return $this->returnWithError("Task cannot be parent of itself");
			}
			
			if($this->taskService->isCircularDependent($data,$data->new_parent))
			{
				//TODO Message should be movded to language file
				return $this->returnWithError("Circular dependency found");
			}
			
			$task = new Task();
			// populate the model with the form data
			$task->title = $data->title;
			$task->id = $data->id;
			$task->newParent = $data->new_parent;
			$isUpdated = $this->taskService->updateTask($task);
			if($isUpdated){
				//Messages shoud be moved to language file
				return response()->json([
						'message' => "Task is updated success fully.",
						], 200);				
			}else
			{
				return response()->json([
						'message' => "Something went wrong.",
						], 400);
			}

    }	
	
    public function addTask(Request $request)
    {
			 $this->validate($request, [
				'title' => 'required'
			]);
			$taskName = $request->input('title');
			$parentId = $request->input('new_parent');
			
			$task = new Task();
			// populate the model with the form data
			$task->title = $taskName;
			$task->status = 0;
			$task->parent_id = isset($parentId)?$parentId:0;
			$isCreated = $this->taskService->addTask($task);
			if($isCreated)
			{
				return $task;
			}else
			{
				return null;
			}
			
    }	
	
	private function returnWithError($error)
	{
		return response()->json([
			'message' => $error,
		], 400);
	}
}