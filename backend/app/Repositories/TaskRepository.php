<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function createTask(array $data, $user)
    {
        return $user->tasks()->create($data);
    }

    public function getTasksByUserId($userId)
    {
        return Task::where('user_id', $userId)->get();
    }

    public function updateTask(array $data, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->update($data);
        return $task;
    }

    public function deleteTask(Task $task)
    {
        return $task->delete();
    }

    public function findTaskById($id)
    {
        return Task::find($id);
    }
}