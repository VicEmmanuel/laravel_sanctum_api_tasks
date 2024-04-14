<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TasksResource;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use HttpResponses;
    // Get my tasks
    public function index()
    {

        $task  = TasksResource::collection(
            Task::where("user_id", Auth::user()->id)->get()
        );
        return $this->success(
            $task,'Fetched My Tasks',);
    }

    ///Get All Tasks

    public function getAllTasks()
    {
        $task = TasksResource::collection(
            Task::get()
        );
        return $this->success(
            $task,

         'All Task Fetched',);

    }

    ///Get Others Task
    public function getOthersTask()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return$this->error('', 'User is unauthorized', 401);
        }

        // Retrieve all tasks that do not belong to the authenticated user
        $otherTasks = Task::where('user_id', '!=', Auth::id())->get();

        if ($otherTasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found for other users'
            ]);
        }

        $task = TasksResource::collection($otherTasks);

        return $this->success([
            'task' => $task,

        ], 'Login Successful',);
        // Return a collection of TasksResource
        // return TasksResource::collection($otherTasks);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $storeTaskRequest)
    {

        $storeTaskRequest->validated($storeTaskRequest->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $storeTaskRequest->name,
            'description' => $storeTaskRequest->description,
            'priority' => $storeTaskRequest->priority,
        ]);

        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TasksResource($task);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('', 'You are not authorized', 403);
        }

        return new TasksResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {

        // return response(null, 204);
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();
    }

    private function isNotAuthorized(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('', 'You are not authorized', 403);
        }
    }
}
