<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
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
        $projects = Project::All();
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
            // dd($validator->messages());
            $errors = $validator->errors();
            return response()->json(['data'=>$errors], 201);die;
        }

        $project = new Project;
        $project->name = $request->input('name');
        $project->information = $request->input('information');
        $project->deadline = $request->input('deadline');
        $project->type = $request->input('type');
        $project->status = $request->input('status');        
        
        $project->save();

        return response()->json(['data'=>$project], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json(['data'=>$project], 200);
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
            // dd($validator->messages());
            $errors = $validator->errors();
            return response()->json(['data'=>$errors], 201);die;
        }

        $project = Project::findOrFail($id);
        if ($request->has('name')){
            $project->name = $request->input('name');
        }

        if ($request->has('information')){
            $project->information = $request->input('information');
        }       

        if ($request->has('deadline')){
            $project->deadline = $request->input('deadline');
        }

        if ($request->has('type')){
            $project->type = $request->input('type');
        }

        if ($request->has('status')){
            $project->status = $request->input('status');
        }       

        if (!$project->isDirty()){
                return response()->json(['error' => 'you need to specify different value to update', 'code' => 422], 422);
        }

        $project->save();

        return response()->json(['data'=>$project], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['data'=>$project], 200);
    }
}
