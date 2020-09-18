<?php

namespace App\Http\Controllers\Admin;
use App\User;
use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:administrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = Admin::find($id);
        return view('admin.profileDetails')->with(['admin'=>$admin]);
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
    public function update(Request $data, $id)
    {   
        $data->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'dob'  => ['required','date'],
            'gender' => ['required'],
            'address'  => ['required', 'string', 'max:255'],
            'pno'  => ['required'],
        ]);

        $dataSet = User::find($id);

       $dataSet->update(
            [
                'name' => $data['name'],   
            ]
            );
        
        $dataSet->admin()->update([
            'name' => $data['name'], 
            'address' => $data['address'], 
            'phone_number' => $data['pno'],
            'dob' => $data['dob'],  
        ]);

        // $id->update(
        //     [
        //         'name' => $data['name'],   
        //         'email' => $data['email'],
        //     ]
        // );

        return redirect(route('admin.index'));
        
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function profileUpload(Request $request){
        if($request->hasFile('image')){
            if(auth()->user()->admin->profile_image){
                Storage::delete('/public/profileImages/'.auth()->user()->admin->profile_image);
            }

            $filename = $request->image->getClientOriginalname();
            $request->image->storeAs('profileImages',$filename,'public');
            auth()->user()->admin()->update(['profile_image'=> $filename]);
            return redirect()->back();
        }
    }
}
