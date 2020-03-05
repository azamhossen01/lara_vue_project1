<?php

namespace App\Http\Controllers;

use App\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Subject::paginate(20);
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
        // return $request;
        $this->validate($request,[
            'name' => 'required|unique:subjects',
            'number' => 'required|numeric',
            'description' => 'sometimes'
        ]);
        $subject = Subject::create([
            'name' => $request->name,
            'number' => $request->number,
            'description' => $request->description
        ]);
        return response()->json(['message'=>'Subject added successfully']);
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
        
        $subject = Subject::findOrFail($id);
        // return $subject;
        $this->validate($request,[
            // 'email' => 'required|string|email|unique:users,email,'.$user->id,
                    'name' => 'required|string|unique:subjects,name,'.$subject->id,
                    'number' => 'required|numeric',
                    'description' => 'sometimes'
                ]);
        // $this->validate($request,[
        //     'name' => 'required|unique:subjects',
        //     'number' => 'required|numeric',
        //     'description' => 'sometimes'
        // ]);
        $subject->update($request->all());
        return response()->json(['message'=>'Subject updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response()->json(['message' => 'Subject deleted successfully']);
    }

    public function search_subject(Request $request){
        $query = $request->get('query');
        // return $query;
        if($query !== null){
            $subjects = Subject::where(function($q)use($query){
                $q->where('name','LIKE',"%$query%")
                ->orWhere('number','LIKE',"%$query%");
            })->paginate(20);
            return $subjects;
        }
        
    }
}
