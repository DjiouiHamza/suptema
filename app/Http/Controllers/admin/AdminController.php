<?php

namespace App\Http\Controllers\admin;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\student;
use App\Models\absence;
use App\Models\section;
use App\Models\user;
use App\Models\timeslot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;




class AdminController extends Controller
{
    //

    public function index(){
        
        return view('admin.AdminHome');
    
    }

    public function updatePassword(request $request){
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);
        $user = Auth::user();

        if (!Hash::check($request->oldPassword, Auth::user()->password)) {
            return back()->withErrors(['oldPassword' => 'Current password is incorrect']);
        }
        Auth::user()->update([
            'password' => Hash::make($request->newPassword),
        ]);

        

        return redirect()->back()->with('status', 'Password successfully changed!');
    }


    public function teachers_list(){
        $users = user::where('isadmin',0)->get();
        return view('admin.teachersList', ['users'=>$users]);
    }

    public function storeTeachers(Request $request)
    {
                $users = $request->all();
            unset($users['_token']);
            
            
            $request->validate([
                'teacherName' => 'required',
                'teacherEmail' => 'required|email|unique:users,email',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,email',
            ]);
            
        user::create([
            'name' => $request->get('teacherName'),
            'email' => $request->get('teacherEmail'),
            'phone_number' => $request->get('phone'),
            'password' => Hash::make($request->get('password')),

            'active'  => $request->get('active' , 1)
        ]);
        
        return redirect()->back()->with('added' , 'Teacher added succesfully');
    }



    public function students_list(){
        $students = student::paginate(10);
        $studentsCount = student::all();
        $sections = section::all();
        return view('admin.studentsList',[
            'students'=>$students,
            'sections'=>$sections,
            'studentsCount' => $studentsCount
          
        ]);
    
    }

    

    public function class_absence_display(Request $request)
    {
        // Retrieve filters from request
        $sectionId = $request->input('section');
        $date = $request->input('date');

        // Initialize query
        $query = Absence::query();

        // Apply filters
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        if ($date) {
            $formattedDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            $query->whereDate('day', $formattedDate);
        }

        // Fetch absences with related models
        $absences = $query->with(['student', 'section', 'timeslot'])
        ->orderBy('day', 'desc')
        ->orderBy('timeslot_id', 'asc') // Sort by time slot in ascending order
        ->select('absences.*')
        ->get();

        // Fetch data for filters
        $sections = Section::all();
        $timeslots = Timeslot::all();

        // Pass data to the view
        return view('admin.class_absence_display', compact('absences', 'sections', 'timeslots'));
    }


    

    public function classes_list(){
        $sections = section::all();

        return view('admin.classesList',[
            'sections'=>$sections,
        ]);
    
    }


    public function class_show($id)
{
    $sections = Section::findOrFail($id);

    // Paginate students
    $students = Student::where('section_id', $id)->paginate(20); // Ensure you use paginate here

    return view('class', [
        'students' => $students,
        'sections' => $sections,
    ]);
}




    public function storeClasses(Request $request)
{
        $request->validate([
            'sectionName' => 'required|unique:sections,name',
        ]);
        section::create([
        'name' => $request->get('sectionName'),
        'active'  => $request->get('active' , 1)
    ]);
    
    return redirect()->back()->with('added' , 'Class added succesfully');
}

public function class_edit_form($id){
    $classes = section::findOrFail($id);
    return view('updateClass',['classes'=>$classes]);
}

public function class_update(Request $request, $id) {
    // Log all request data to debug
    \Log::info($request->all());

    $classes = section::findOrFail($id);

    $inputs = $request->validate([
        'class_Name' => 'min:2|unique:sections,name',
    ]);
    // Update student record
    $classes->update([
        'name' => $inputs['class_Name'],
    ]);

     return redirect()->back()->with('updated' , 'class updated succesfully');
}


public function class_delete($id){

    $classes = section::findOrFail($id);
    $classes->delete();

    return redirect()->back()->with('deleted' , 'Class deleted!!');

}
    



    public function storeStudents(Request $request)
{
            $student = $request->all();
        unset($student['_token']);
        
        $request->validate([
            'studentName' => 'required|min:2',
            'Age' => 'required|integer',
            'studentEmail' => 'required|email|unique:students,email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:students,phone_number',
            'studentSection' => 'required',
        ]);

    student::create([
        'section_id' => $request->get('studentSection'),
        'full_name' => $request->get('studentName'),
        'email' => $request->get('studentEmail'),
        'phone_number' => $request->get('phone'),
        'age' => $request->get('Age'),
        

        'active'  => $request->get('active' , 1)
    ]);
    
    return redirect()->back();
}



public function deleteStudent($id){

    $students = student::findOrFail($id);
    $students->delete();

    return redirect()->back();

}


public function studentUpdateForm($id){
    $sections=section::all();
    $students = student::findOrFail($id);
        
    return view('updateStudent',['sections'=>$sections, 'students'=>$students]);

}



public function updateStudent(Request $request, $id) {
    // Log all request data to debug
    \Log::info($request->all());

    $student = Student::findOrFail($id);

    $inputs = $request->validate([
        'studentName' => 'min:2',
        'Age' => 'integer',
        'studentEmail' => 'email|unique:students,email,' . $id,
        'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:students,phone_number,' . $id,
        'studentSection' => '',
    ]);
    // Update student record
    $student->update([
        'full_name' => $inputs['studentName'],
        'age' => $inputs['Age'],
        'email' => $inputs['studentEmail'],
        'phone_number' => $inputs['phone_number'],
        'section_id' => $inputs['studentSection'],
    ]);

     return redirect()->back()->with('udated' , 'student updated succesfully');
}




public function teacherUpdateForm($id){
    $sections=section::all();
    $users = user::findOrFail($id);
        
    return view('updateTeacher',['sections'=>$sections, 'users'=>$users]);

}


public function updateTeacher(Request $request, $id) {
    // Log all request data to debug
    \Log::info($request->all());

    $users = user::findOrFail($id);

    $inputs = $request->validate([
        'teachertName' => 'min:2',
        'teacherEmail' => 'email|unique:students,email,' . $id,
        'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:students,phone_number,' . $id,        
    ]);

    // Update student record
    $users->update([
        'name' => $inputs['teachertName'],
        'email' => $inputs['teacherEmail'],
        'phone_number' => $inputs['phone_number'],
    ]);

     return redirect()->back()->with('updated' , 'Teacher updated succesfully');
}

public function deleteTeacher($id){

    $users = user::findOrFail($id);
    $users->delete();

    return redirect()->back()->with('deleted' , 'Teacher deleted!');

}


}
