<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    public function index(Request $request) {
        $query = Task::query();
    
        // Check if the 'status' query parameter exists and is valid
        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $query->where('status', $request->status);
        }
    
        $tasks = $query->orderBy('created_at', 'desc')->paginate(5);
        // Return the paginated tasks data in JSON format with metadata and pagination links
        return response()->json([
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
            'links' => [
                'first' => $tasks->url(1),
                'last' => $tasks->url($tasks->lastPage()),
                'prev' => $tasks->previousPageUrl(),
                'next' => $tasks->nextPageUrl(),
            ]
        ]);
    }
    
    
    public function store(Request $request) {

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,completed',
        ]);

        $task = Task::create($validatedData);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }
    
    public function update(Request $request, $id) {

        $validatedData = $request->validate([
            'title' => 'nullable|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,completed',
        ]);

        $task = Task::findOrFail($id);

        $task->update($validatedData);
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }
    
    public function destroy($id) {

        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
