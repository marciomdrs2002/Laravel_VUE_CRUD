<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::paginate(2);
        if (!$projects) {
            return response()->json(['error' => 'An error has occurred. Please Try Again'], 404);
        }
        return response()->json($projects, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'value' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $project = new Project([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'value' => $request->value,
            'status' => $request->status,
            'creator' => $user->name,
        ]);

        $project->user()->associate($user);
        $project->save();

        return response()->json(['success' => 'Project has been created'], 200);
    }
    public function destroy($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['error' => 'An error has occurred. Project not found'], 404);
        }
        $project->delete();
        return response()->json(['success' => 'Project has been deleted'], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'value' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $projects = Project::find($request->id);

        if (!$projects) {
            return response()->json(['error' => 'An error has occurred. Project not found'], 404);
        }

        $projects->update($request->all());

        return response()->json(['success' => 'Project has been updated'], 200);
    }
}
