<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Assignment;
use App\Project;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Input;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::orderBy('id', 'desc')->get();
        return response()->json(['data'=>$projects], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|alpha_spaces|max:10',
            'information' => 'max:300',
            'deadline' => 'nullable|date|date_format:Y-m-d',
            'type' =>[
            'required',
            Rule::in(['lab', 'single', 'acceptance',])],
            'status' =>[
            'required',
            Rule::in(['planned', 'onhold', 'doing', 'done'])],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['error'=>$errors]);
            die;
        }

        $project = new Project;
        $project->name = $request->input('name');
        $project->information = $request->input('information');
        $project->deadline = $request->input('deadline');
        $project->type = $request->input('type');
        $project->status = $request->input('status');
        
        $project->save();

        return response()->json([
            'Notice'=>'the Project has been created',
            'Project'=>$project]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (preg_match('/^[0-9]+$/', $id) === 0 || !$project = Project::find($id)) {
            return response()->json(['error' =>
                'The Project you want to get does not exist']);
            die;
        }

        $project = Project::findOrFail($id);
        return response()->json(['Project'=>$project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (preg_match('/^[0-9]+$/', $id) === 0 || !$project = Project::find($id)) {
            return response()->json(['error' =>
                'The Project you want to update does not exist', 'code' => 404], 404);
            die;
        }

        $project = Project::findOrFail($id);

        $rules = [
            'name' => 'alpha_spaces|max:10',
            'information' => 'max:300',
            'deadline' => 'nullable|date|date_format:Y-m-d',
            'type' =>
            Rule::in(['lab', 'single', 'acceptance',]),
            'status' =>
            Rule::in(['planned', 'onhold', 'doing', 'done']),
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['error'=>$errors]);
            die;
        }

        
        if (!empty($request->input('name'))) {
            $project->name = $request->input('name');
        }

        if (!empty($request->input('information'))) {
            $project->information = $request->input('information');
        }

        if (!empty($request->input('deadline'))) {
            $project->deadline = $request->input('deadline');
        }

        if (!empty($request->input('type'))) {
            $project->type = $request->input('type');
        }

        if (!empty($request->input('status'))) {
            $project->status = $request->input('status');
        }

        // if (!$project->isDirty()) {
        //         return response()->json(['error' =>
        //             'you need to specify different value to update', 'code' => 422], 422);
        // }

        $project->save();

        return response()->json([
            'Notice'=>'The Project has been updated',
            'Project'=>$project]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = Assignment::where(['project_id' => $id])->get();        
        if (preg_match('/^[0-9]+$/', $id) === 1 && !$assignment->isempty()) {
            return response()->json(['error'=>
                'There is assignment for this project, Please remove the assignment before delete the project']);
            die;
        }

        if (preg_match('/^[0-9]+$/', $id) === 0 || !$project = Project::find($id)) {
            return response()->json(['error' =>
                'The Project you want to delete does not exist']);
            die;
        }

        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['notice'=>'The project has been deleted', 'project'=>$project]);
    }
}
