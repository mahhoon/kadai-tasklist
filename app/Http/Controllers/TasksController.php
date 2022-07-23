<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{

    public function index()
    {
        $tasks = [];
        
        if(\Auth::check()) {
            $user = \Auth::User();
            
            $tasks = $user->tasks()->orderBy('created_at')->get();
        }
        
        return view('welcome', [
            'tasks' => $tasks,
        ]);
    }


    public function create()
    {
        $task = new Task;
        
        return view('tasks.create', [
            'task' => $task,
        ]);
    }


    public function store(Request $request)
    {
        
        //バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);
        
        $request->user()->tasks()->create([
           'content' => $request->content,
           'status' => $request->status,
        ]);
        
        return redirect('/');
    }


    public function show($id)
    {
        $task = Task::findOrFail($id);
        
        //認証済みユーザ（閲覧者）がそのタスクの所有者である場合は、タスクを表示
        if(\Auth::id() === $task->user_id) {
            $user = \Auth::User();
            
            return view('tasks.show', [
                'task' => $task,
            ]);
        }
        else {
            return redirect('/');
        }
    }


    public function edit($id)
    {
        $task = Task::findOrFail($id);
        
        if(\Auth::id() === $task->user_id) {
            return view('tasks.edit', [
                'task' => $task,
            ]);
        }
        else {
            return redirect('/');
        }

    }


    public function update(Request $request, $id)
    {
        //バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);
        
        $task = Task::findOrFail($id);
        $task->content = $request->content;
        $task->status = $request->status;
        $task->save();
        
        return redirect('/');
    }


    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        if(\Auth::id() === $task->user_id) {
            $task->delete();
        }

        return redirect('/');
    }
}
