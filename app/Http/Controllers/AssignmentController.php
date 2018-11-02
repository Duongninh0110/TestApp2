<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Assignment;
use App\Member;
use App\Project;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Validator;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listMembers($id)
    {

        
        $assignments = Assignment::where(['project_id' => $id])->get();
        $members = [];
        foreach ($assignments as $assignment) {
           $members[]= $assignment->member;         
        }
        return response()->json(['data'=>$members], 200);
    }

    public function listProjects($id)
    {

        $assignments = Assignment::where(['member_id' => $id])->get();        
        $projects = [];
        foreach ($assignments as $assignment) {
           $projects[]= $assignment->project;           
        }
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
            'project_id' => 'required|exists:projects,id',
            'member_id' => 'required|exists:members,id',
            'role' =>[
            'required',
            Rule::in(['dev', 'pl', 'pm', 'po', 'sm'])],            
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // dd($validator->messages());
            $errors = $validator->errors();
            return response()->json(['data'=>$errors], 201);die;
        }

        $assignment = new Assignment;
        $assignment->project_id = $request->input('project_id');
        $assignment->member_id = $request->input('member_id');
        $assignment->role = $request->input('role');
        $assignment->save();

        return response()->json(['data'=>$assignment], 200); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id, $member_id)
    {
        $assignment = Assignment::where(['project_id' => $project_id, 'member_id' => $member_id]);        
        $assignment->delete();
        return response()->json(['data'=>$assignment], 200);
    }
}