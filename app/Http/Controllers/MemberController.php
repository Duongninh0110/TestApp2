<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Assignment;
use App\Member;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::orderBy('id', 'desc')->get();
        return response()->json(['Member'=>$members], 200);
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
        $startdate = date("Y-m-d", time() - (365*60 * 24*60*60));
        $rules = [
            'name' => 'required|alpha_spaces|max:50',
            'information' => 'max:300',
            'phone_number' => 'required|phone_number|max:20',
            'date_of_birth' => 'required|date|date_format:Y-m-d|before:tomorrow|after:'.$startdate,
            'avatar' => 'mimes:jpeg,png,gif|max:10240',
            'position' =>[
            'required',
            Rule::in(['intern', 'junior', 'senior', 'pm', 'ceo', 'cto', 'bo'])],
            'gender' =>[
            'required',
            Rule::in(['male', 'female'])],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['data'=>$errors], 201);
            die;
        }
        

        $member = new Member;
        $member->name = $request->input('name');
        $member->information = $request->input('information');
        $member->phone_number = $request->input('phone_number');
        $member->date_of_birth = $request->input('date_of_birth');
        $member->avatar = $request->input('avatar');
        $member->position = $request->input('position');
        $member->gender = $request->input('gender');
       
        if ($request->hasFile('avatar')) {
            $image_tmp = Input::file('avatar');
            if ($image_tmp->isvalid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $image_path = 'images/avatars/';
                $image_tmp->move($image_path, $filename);
                $member->avatar = $filename;
            }
        }
    
        $member->save();

        return response()->json([
            'Notice'=>'The member has been added',
            'Member'=>$member], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (preg_match('/^[0-9]+$/', $id) === 0 || !$member = Member::find($id)) {
            return response()->json(['error' =>
                'The Memeber you want to get does not exist', 'code' => 404], 404);
            die;
        }

        $member = Member::findOrFail($id);
        return response()->json(['Member'=>$member], 200);
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
        if (preg_match('/^[0-9]+$/', $id) === 0 || !$member = Member::find($id)) {
            return response()->json(['error' =>
                'The Memeber you want to update does not exist', 'code' => 404], 404);
            die;
        }

        $member = Member::findOrFail($id);

        $startdate = date("Y-m-d", time() - (365*60 * 24*60*60));

        $rules = [
            'name' => 'alpha_spaces|max:50',
            'information' => 'max:300',
            'phone_number' => 'phone_number|max:20',
            'date_of_birth' => 'date|date_format:Y-m-d|before:tomorrow|after:'.$startdate,
            'avatar' => 'mimes:jpeg,png,gif|max:10240',
            'position' =>
            Rule::in(['intern', 'junior', 'senior', 'pm', 'ceo', 'cto', 'bo']),
            'gender' =>
            Rule::in(['male', 'female']),
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // dd($validator->messages());
            $errors = $validator->errors();
            return response()->json(['Error'=>$errors], 201);
            die;
        }

        if ($request->has('name')) {
            $member->name = $request->input('name');
        }

        if ($request->has('information')) {
            $member->information = $request->input('information');
        }

        if ($request->has('date_of_birth')) {
            $member->date_of_birth = $request->input('date_of_birth');
        }

        if ($request->has('phone_number')) {
            $member->phone_number = $request->input('phone_number');
        }

        if ($request->hasFile('avatar')) {
            $image_tmp = Input::file('avatar');
            if ($image_tmp->isvalid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $image_path = 'images/avatars/';
                $image_tmp->move($image_path, $filename);
                $member->avatar = $filename;
            }
        }

        if ($request->has('position')) {
            $member->position = $request->input('position');
        }

        if ($request->has('gender')) {
            $member->gender = $request->input('gender');
        }

        if (!$member->isDirty()) {
                return response()->json([
                    'error' => 'you need to specify different value to update',
                    'code' => 422], 422);
        }

        $member->save();

        return response()->json([
            'Notice'=>'The Member has been updated successfully',
            'Member'=>$member], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = Assignment::where(['member_id' => $id])->get();
        if (preg_match('/^[0-9]+$/', $id) === 1 && !$assignment->isempty()) {
            return response()->json(['error'=>
                'There is assignment for this member, Please remove the assignment before delete the member'], 404);
            die;
        }

        if (preg_match('/^[0-9]+$/', $id) === 0 || !$member = Member::find($id)) {
            return response()->json(['error' =>
                'The Memeber you want to delete does not exist', 'code' => 404], 404);
            die;
        }

        $member = Member::findOrFail($id);
        $member->delete();
        return response()->json(['notice'=>'The member has been deleted','member'=>$member], 200);
    }
}
