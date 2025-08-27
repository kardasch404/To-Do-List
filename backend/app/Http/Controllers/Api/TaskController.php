<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    //

    protected $taskrepository;
    protected $userRepository;

    public function __construct(TaskRepository $taskrepository, UserRepository $userRepository  )
    {
        $this->taskrepository = $taskrepository;
        $this->userRepository = $userRepository;
    }

    public function store(TaskRequest $r)
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'error' => 'No token provided'
                ], 401);
            }
            
            $payload = JWTAuth::getPayload($token);
            $userId = $payload->get('sub');
            $user = $this->userRepository->findUserById($userId);

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'
                ], 401);
            }
            
            $data = $r->validated();
            $task = $this->taskrepository->createTask($data, $user);

            return response()->json([
                'success' => true,
                'message' => 'Task created success',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function index()
    {
        try {
            $payload = JWTAuth::getPayload(JWTAuth::getToken());
            $user = $this->userRepository->findUserById($payload->get('sub'));

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'
                ], 401);
            }

            $tasks = $this->taskrepository->getTasksByUserId($user->id);

            return response()->json([

                'success' => true,
                 'user' => [
                    'full_name' => $user->full_name,
                ],
                'data' => $tasks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(TaskUpdateRequest $r, Task $task)
    {
        try {
            $payload = JWTAuth::getPayload(JWTAuth::getToken());
            $user = $this->userRepository->findUserById($payload->get('sub'));

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            if ($task->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $data = $r->validated();
            $updatedTask = $this->taskrepository->updateTask($data, $task->id);

            return response()->json([
                'success' => true,
                'message' => 'Task updated success',
                'data' => $updatedTask
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $payload = JWTAuth::getPayload(JWTAuth::getToken());
            $user = $this->userRepository->findUserById($payload->get('sub'));
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            if ($task->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $this->taskrepository->deleteTask($task);

            return response()->json([
                'success' => true,
                'message' => 'Task deleted success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
