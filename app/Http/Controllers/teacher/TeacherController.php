<?php

namespace App\Http\Controllers\Teacher;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\section;
use App\Models\User;
use App\Models\absence;
use App\Models\timetable;
use App\Models\timeslots;
use App\Models\student;
use App\Models\timeslot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;





class TeacherController extends Controller
{
    //
    public function index(){
        $user = User::all();
        return view('teacher.TeacherHome', ['user'=>$user]);
    
    }

    public function timetable($id){

    $timeSlots = [
        '09:00 - 10:30',
        '10:45 - 12:15',
        '12:30 - 14:00',
        '14:00 - 15:30',
        '15:45 - 16:15', 
    ];
    $teacherId = Auth::user()->id;

    // Fetch all timetables with related section and teacher data
    $timetables = Timetable::with('section', 'teacher')->where('teacher_id', $teacherId)->get();
    $class_name = Timetable::get('class_name');

    // Organize data into a nested array with days as X-axis and time as Y-axis
    $schedule = [];

    foreach ($timeSlots as $time) {
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day) {
            // Find the timetable that matches both the day and time
            $schedule[$time][$day] = $timetables->first(function ($timetable) use ($day, $time) {
                return $timetable->day === $day && $timetable->timing === $time;
            });
            
        }
    }
    return view('teacher.timetable', compact('schedule', 'timeSlots', 'class_name'));
}

    
    public function classes(){
        $sections = section::all();
        return view('teacher.classesList',[
            'sections' => $sections
        ]);
    }


    public function class_show($id)
{
    $sections = Section::findOrFail($id);

    // Fetch students with pagination
    $students = Student::where('section_id', $id)->paginate(20); // Paginate results

    return view('class', [
        'students' => $students,
        'sections' => $sections,
    ]);
}



public function class_absence($id, $day, $timing)
{
    $sections = Section::findOrFail($id);
    $timeslots = timeslot::all();


    // Fetch students
    $students = Student::where('section_id', $id)->get(); // show results
    $time_id = timeslot::where('time', $timing)->first()->id;
    
    $data = Absence::with(['student', 'section', 'timeslot'])
    ->where('section_id', $id)
    ->whereDate('day', $day)
    ->where('timeslot_id', $timing)
    ->orderBy('day', 'desc')
    ->get();
    return view('class_absence', [
        'students' => $students,
        'sections' => $sections,
        'timeslots' => $timeslots,
        'data' => $data, // Pass absences data
        'section_id' => $id,
        'day' => $day,
        'timing' => $timing,
        'time_id' => $time_id,

    ]);
}


public function class_absence_sent(request $request)
    {
        // $studentIdSent = $request->get('student_ids');
        // $sectionid = student::where('id' , $studentIdSent)->get('section_id');
        
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'timing' => 'required|exists:timeslots,id', // Ensure the timing exists in the timeslots table
            'student_ids' => 'required|array|min:1', // At least one student must be selected
            'student_ids.*' => 'exists:students,id' // Ensure each selected student exists in the students table
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput(); // Redirect back with input data and errors
        }
        $day = $request->input('day');
        $timing = $request->input('timing');
        $studentIds = $request->input('student_ids');
        $sectionId = $request->input('section_id'); 

        

        // Convert day to a date format if necessary (example: using current week for simplicity)
        $date = Carbon::parse($day . ' this week')->format('Y-m-d'); // Adjust this if needed
        


        foreach($studentIds as $studentId){
            Absence::create([
                'student_id' => $studentId,
                'section_id' => $sectionId, // Assuming you pass section_id as part of the request
                'day' => $date,
                'timeslot_id' => $timing,
            ]);

        }

    
    return redirect()->back()->with('sent' , 'absence list sent succesfuly');
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


    
    


}



