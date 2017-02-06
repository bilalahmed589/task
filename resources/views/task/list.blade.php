@extends('welcome')

@section('content')

    <!-- Current Tasks -->
    @if (count($tasks) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Tasks
            </div>
			  <button id="btn-add" name="btn-add" type="button" class="btn btn-xs btn-success">Add Task
					<span class="glyphicon glyphicon-plus"></span>&nbsp;
				</button>
            <div class="panel-body">
			<div class="form-group">
					<label for="gender1" class="col-sm-2 control-label" >Filter Status:</label>
					<div class="col-sm-2">
					<select class="form-control" id="statusFilter" >
						<option value=""></option>
						<option value="">All</option>
						<option value="0">In Progress</option>
						<option value="1">Done</option>
						<option value="2">Complete</option>
					</select>          
					  
					</div>
			</div>
			
                <table class="table tree">

                    <!-- Table Headings -->
                    <thead>
                        <th>Id</th>
                        <th>Description</th>
						<th>Status</th>
						<th>Action (Done)</th>
						<th>Total Dependencies</th>
						<th>Total Done</th>
						<th>Total Complete</th>
						<th>Actions</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr class="treegrid-{{ $task->id }} {{ ($task->parent_id>0)?'treegrid-parent-'.$task->getParent()->first()->id:'' }}" >
                                <!-- Task Name -->
                                <td>{{ $task->id }}</td>
                                <td>
                                    <div id="{{ $task->id }}_title">{{ $task->title }}</div>
                                </td>
                                <td>
                                    <div id="{{ $task->id }}_status">{{ $task->status_name }}</div>
                                </td>
                                <td>
                                    <div>
										{{ Form::checkbox('task_status', $task->id , ($task->status > 0)?true:false , array('class' => 'test','onchange' => 'markStatusDone(this)')) }}
									</div>
                                </td>
                                <td>
                                    <div>{{ $task->totalDependent }}</div>
                                </td>
                                <td>
                                    <div>{{ $task->totalDone }}</div>
                                </td>
                                <td>
                                    <div>{{ $task->totalComplete }}</div>
                                </td>								
								<td>
									<button class="btn btn-warning btn-xs btn-detail open-modal"  parent="{{$task->parent_id}}"  value="{{$task->id}}">Edit</button>
								</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
		@else
			<br/><br/>
			<div class="alert alert-info">
			  <strong>Info!</strong> No task exists! Add first here 
			  <button id="btn-add" name="btn-add" type="button" class="btn btn-xs btn-success">Add Task
					<span class="glyphicon glyphicon-plus"></span>&nbsp;
				</button>
			</div>
		@endif
	
        <!-- Modal (Pop up when detail button clicked) -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLabel">Add / Edit Task</h4>
                        </div>
                        <div class="modal-body">
						<div id="errorDiv" class="alert alert-danger fade in hidden">
							<a href="#" class="close" data-dismiss="alert">&times;</a>
							<strong>Warning!</strong> There was a problem with your network connection.
							</div>
							<div class="alert alert-success" id="success-alert">
								<button type="button" class="close" data-dismiss="alert">x</button>
								<strong>Success! Reloading task list. Please wait....</strong>
							</div>	
                            <form id="frmTasks" name="frmTasks" class="form-horizontal" >

                                <div class="form-group" id="inputTitle">
                                    <label for="inputTask" class="col-sm-3 control-label">Title</label>
                                    <div class="col-sm-9 has-danger">
                                        <input required="true" type="text" class="form-control has-error" id="task" name="task" placeholder="" value="">
										<input type="hidden" class="form-control has-error" id="id" name="task" value="">
										<input type="hidden" class="form-control has-error" id="new_parent" name="task" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Parent Task</label>
                                    <div class="col-sm-9">
                                        <!--<input type="text" class="form-control" id="description" name="description" placeholder="Description" value="">-->
										{!! Form::select('parentId', $taskForSelect,null, array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="btn-save" value="add">Save</button>
                        </div>
                    </div>
                </div>
            </div>

	<script>

		 $('.open-modal').click(function(){
			$("#errorDiv").html("");
			$("#errorDiv").addClass("hidden");
			$("#inputTitle").removeClass("has-error");
			var taskId = $(this).val();
			$("#id").val(taskId);
			$('#task').val($("#"+ taskId + "_title").text());
			$("select[name=parentId]").val($(this).attr("parent"));
			$('#myModal').modal('show');
		});
		
		 $('#btn-add').click(function(){
			$("#errorDiv").html("");
			$("#errorDiv").addClass("hidden");
			$("#inputTitle").removeClass("has-error");
			var taskId = $(this).val();
			$("#id").val("");
			$('#task').val($("#"+ taskId + "_title").text());
			$("select[name=parentId]").val($(this).attr("parent"));
			$('#myModal').modal('show');
		});		
		
		 $('#btn-save').click(function(){
			 var task = {
					id : $("#id").val(),
					title : $("#task").val(),
					new_parent : $("select[name=parentId]").val()
			}
			var method = 'PUT';
			if($("#id").val() == "")
			{
				method = 'POST';
			}
			$.ajax({
               type:method,
			   dataType: 'json',
			   contentType: "application/json",
			   accepts: {
					text: "application/json"
				},
			   beforeSend: function(xhr){xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="_token"]').attr('content'))},
               url:'/task',
			   data: JSON.stringify(task),
               success:function(){
					$("#success-alert").alert();
					$("#success-alert").fadeTo(2000, 500).slideUp(300, function(){
						//$("#success-alert").slideUp(500);
						location.reload();
					});   
				},
				error: function(xhr) {
								if(xhr.status == 422)
								{
									var erroObj = JSON.parse(xhr.responseText);
									$("#errorDiv").html(erroObj["title"][0]);
									$("#errorDiv").removeClass('hidden');
									$("#inputTitle").addClass("has-error");
								}else if(xhr.status == 400)
								{
									var erroObj = JSON.parse(xhr.responseText);
									$("#errorDiv").html(erroObj["message"]);
									$("#errorDiv").removeClass('hidden');									
								}
				}				
			
            });
			
		});		
	
		function markStatusDone(obj){
			var taskId = obj.value;
			var status = 0;
			if(obj.checked){
				status = 1;
			}	
			$.ajax({
               type:'PUT',
			   beforeSend: function(xhr){xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="_token"]').attr('content'))},
               url:'/task/update',
			   data:'taskId='+ taskId + "&status="+ status,
               success:function(data){
				   location.reload();
				   /*var newStatus = "In progress";
				   if(status == 1){
					   newStatus = "Done";
				   }
                  $("#"+ taskId + "_status").html(newStatus);*/
               }
            });
		}
		
		$('#statusFilter').change(function() {
			window.location = "/task?status=" + $(this).val();
		});
		
		$(document).ready(function() {
				$("#success-alert").hide();
                $('.tree').treegrid();
            });
		
	</script>
@endsection	