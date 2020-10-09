<?php

namespace App\Http\Controllers\Teacher;
use App\User;
use App\Grade;
use App\Student;
use App\Subject;
use App\Teacher;
use App\Resource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:teacher|administrator',['except' => [
            'showResources','viewResources'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->isA('teacher')){
            return view('teacher.index');
        }
        else{
            return redirect(route('login'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subject = Subject::all();

        return view('teacher.teacherRegister')->with(['subjects'=> $subject]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $data)
    {
        $data->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'dob'  => ['required', 'date'],
            'gender' => ['required'],
            'address'  => ['required', 'string', 'max:255'],
            'pno'  => ['required'],
            'subject'  => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->attachRole('teacher');

        $user->teacher()->create([
            'subject_id' => $data['subject'],
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'dob' => $data['dob'],
            'gender' => $data['gender'],
            'phone_number' => $data['pno'],
        ]);

        return view('admin.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacher = Teacher::find($id);
        return view('teacher.profileDetails')->with(['teacher'=>$teacher]);
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
            'dob'  => ['required', 'date'],
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
        
        $dataSet->teacher()->update([
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

        return redirect(route('teacher.index'));
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
            if(auth()->user()->teacher->profile_image){
                Storage::delete('/public/profileImages/'.auth()->user()->teacher->profile_image);
            }

            $filename = $request->image->getClientOriginalname();
            $request->image->storeAs('profileImages',$filename,'public');
            auth()->user()->teacher()->update(['profile_image'=> $filename]);
            return redirect()->back();
        }
    }

    public function getAllDetails(){
        $teachers = Teacher::all();
        return view('teacher.allTeacherDetails')->with(['teachers'=>$teachers]);
    }

    public function showClasses(){
        $teacher = Teacher::find(auth()->user()->teacher->id);
        $teacher_grades = $teacher->grade()->paginate(10);
        $grades = Grade::all();
        // return $grades;
        return view('teacher.classes')->with(['grades'=> [$teacher_grades,$grades]]);
    }

    // public function attendanceShowClasses(){
    //     $teacher = Teacher::find(auth()->user()->teacher->id);
    //     $teacher_grades = $teacher->grade;
    //     // $grades = Grade::all();
    //     // return $grades;
    //     return view('teacher.attendance')->with(['grades'=> $teacher_grades]);
    // }

    public function resourcesShowClasses(){
        $teacher = Teacher::find(auth()->user()->teacher->id);
        $teacher_grades = $teacher->grade()->paginate(10);
        // $grades = Grade::all();
        // return $grades;
        return view('teacher.resources')->with(['grades'=> $teacher_grades]);
    }

    public function resourcesUploadForm($grade){
        return view('teacher.upload-resources')->with(['grade_id'=> $grade]);
    }

    public function resourcesUpload(Request $request,$grade_id){
        $filename='';
        // $file_extension='';

        $request->validate([
            'capter' => ['required','max:255'],
            'title'  => ['required','max:255'],
            'file' => ['required']
        ]);



        if($request->file){
            $filename = auth()->user()->teacher->id.$grade_id.'-'.$request->file->getClientOriginalname();
            $request->file->storeAs('public/resources',$filename);
        }

        $newResource = Resource::create(['grade_id'=>$grade_id,'teacher_id'=>auth()->user()->teacher->id,'capter'=>$request->capter, 'title'=> $request->title, 'file'=> $filename]);
        $filename='';
        return redirect()->back()->with('success','New Resources Uploaded.');
    }

    public function editResourcesForm($id){
        $resources = Resource::find($id);
        return view('teacher.edit-resources')->with(['resources'=>$resources]);
    }

    public function editResources(Request $data,$id){
        $data->validate([
            'capter' => ['required','max:255'],
            'title'  => ['required','max:255']
        ]);
        Resource::where('id',$id)->update(['capter'=>$data->capter,'title'=>$data->title]);
        return redirect()->back()->with('success','Resources Updated.');
    }

    public function viewResources($grade_id,$teacher_id){

        // $teacher = Teacher::find($teacher_id)
        $resources = Resource::where([
            ['grade_id', '=',$grade_id],
            ['teacher_id', '=',$teacher_id]
        ])->paginate(10);
        // return $resources;
        return view('teacher.view-resources')->with(['resources'=>$resources]);
    }

    public function showResources($id){
        $file = Resource::find($id);
        return view('teacher.show-resources')->with(['file'=>$file]);
    }

    public function editResourcesFileForm($id){
        $resources = Resource::find($id);
        return view('teacher.edit-resources-file')->with(['resources'=>$resources]);
    }

    public function editResourcesFile(Request $request,$id){
        $filename='';
        // $file_extension='';

        $request->validate([
            'file' => ['required']
        ]);



        if($request->file){
            $oldfile = Resource::find($id);
            $filename = auth()->user()->teacher->id.$oldfile->grade_id.'-'.$request->file->getClientOriginalname();
            Storage::delete('/public/resources/'.$oldfile->file);

            
            $request->file->storeAs('public/resources',$filename);
            Resource::where('id',$id)->update(['file'=>$filename]);
        }
        $filename='';
        return redirect()->back()->with('success','Resources Updated.');
    }

    public function deleteResources($id){
        $resource = Resource::find($id);
        Storage::delete('/public/resources/'.$resource->file);
        Resource::where('id',$id)->delete();

        return redirect()->back()->with('success','Resource deleted successfully.');
    }


    public function addClasses(Request $request){
        $request->validate(['selected'=> 'required']);

        $grade = $request->selected;
        // $gradeid = Grade::where('grade',$grade)->get('id');
        $gradeid = $request->selected;
        $user = Teacher::find(auth()->user()->teacher->id);
        $user->grade()->attach($gradeid);
        return redirect(route('showClasses'));
    }

    public function showGradeStudentList($grade_id){
        $teacher = Teacher::find(auth()->user()->teacher->id);
        $student_teachers = $teacher->student;
        $student_id = [];
        foreach($student_teachers as $student_teacher){
            $student_id[] = $student_teacher->id;
        }
        
        $students = Student::where('grade_id',$grade_id)->whereIn('id',$student_id)->paginate(10);
        // return $student_grades;
        // $student = array();
        // foreach($student_teachers as $student_teacher){
        //     foreach($student_grades as $student_grade){
        //         if($student_teacher->id == $student_grade->id){
        //             $student[]=$student_grade;
        //             break;
        //         }
        //     }

        // }
        // $students = $student;

        return view('teacher.teacher-student')->with(['students'=>$students]);
    }

}
