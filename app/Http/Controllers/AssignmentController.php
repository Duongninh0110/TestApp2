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
    public function index()
    {
        $assignments = Assignment::orderBy('id', 'desc')->get();
        return response()->json(['Assignments'=>$assignments], 200);
    }

    public function listMembers($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['error'=>'The Project does not exist'], 404);
            die;
        }
        
        $assignments = Assignment::where(['project_id' => $id])->get();
        $members = [];
        foreach ($assignments as $assignment) {
            $members[]= $assignment->member;
        }
        return response()->json(['members'=>$members], 200);
    }

    public function listProjects($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['error'=>'The member does not exist'], 404);
            die;
        }

        $assignments = Assignment::where(['member_id' => $id])->get();
        $projects = [];
        foreach ($assignments as $assignment) {
            $projects[]= $assignment->project;
        }
        return response()->json(['projects'=>$projects], 200);
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
        $project_id = $request->input('project_id');
        $member_id = $request->input('member_id');
        $assignment = Assignment::where(['project_id' => $project_id, 'member_id' => $member_id])->get();
        
        if (!$assignment->isempty()) {
            return response()->json(['error'=>'The assignment already has been made'], 422);
        }

        $rules = [
            'project_id' => 'required|exists:projects,id',
            'member_id' => 'required|exists:members,id',
            'role' =>[
            'required',
            Rule::in(['dev', 'pl', 'pm', 'po', 'sm'])],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['Error'=>$errors], 201);
            die;
        }

        $assignment = new Assignment;
        $assignment->project_id = $request->input('project_id');
        $assignment->member_id = $request->input('member_id');
        $assignment->role = $request->input('role');
        $assignment->save();

        return response()->json([
            'Notice'=>'The assignment has been made',
            'Assignment'=>$assignment], 200);
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
    public function update(Request $request, $project_id, $member_id)
    {
        $assignment = Assignment::where(['project_id' => $project_id, 'member_id' => $member_id])->first();
        if (!$assignment) {
            return response()->json(['error'=>'The assignment does not exist'], 404);
        }

        $rules = [
            'role' =>[
            Rule::in(['dev', 'pl', 'pm', 'po', 'sm'])],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['Error'=>$errors], 201);
            die;
        }

        $assignment->role = $request->input('role');
        $assignment->save();

        return response()->json([
            'notice' => 'The assignment has been updated successfully',
            'Assignment' => $assignment], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id, $member_id)
    {
        $assignment = Assignment::where(['project_id' => $project_id, 'member_id' => $member_id])->first();
        if (!$assignment) {
            return response()->json(['error'=>'The assignment does not exist'], 404);
        }
        $assignment->delete();
        return response()->json([
            'notice' => 'The assignments has been deleted',
            'Assignment'=>$assignment], 200);
    }
}
