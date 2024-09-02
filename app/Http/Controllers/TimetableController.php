<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\section;
use App\Models\timetable;
use Illuminate\Support\Facades\Auth;



use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function create(){
        $sections = section::all();
        $teachers = User::where('isadmin',0)->get();
        return view('admin.creatTimeTable', [
            'teachers' => $teachers,
            'sections' => $sections,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'section_id' => 'required|exists:sections,id',
            'class_name' => 'required',
            'day' => 'required|string',
            'timing' => 'required',
        ]);
    
        // Check if a record with the same teacher, section, class name, day, and timing already exists
        $existingTimetable = Timetable::where([
            ['teacher_id', '=', $request->teacher_id],
            ['section_id', '=', $request->section_id],
            ['class_name', '=', $request->class_name],
            ['day', '=', $request->day],
            ['timing', '=', $request->timing],
        ])->first();
    
        if ($existingTimetable) {
            // If a duplicate is found, redirect back with an error message
            return redirect()->back()->withErrors(['duplicate' => 'This section with the same information already exists.']);
        }

        // Check if a record with the same teacher, timing, and day already exists but different class name/section
    $existingTimetable = Timetable::where([
        ['teacher_id', '=', $request->teacher_id],
        ['day', '=', $request->day],
        ['timing', '=', $request->timing],
    ])->first();

    if ($existingTimetable && ($existingTimetable->class_name != $request->class_name || $existingTimetable->section_id != $request->section_id)) {
        
        
        // Store data in session for confirmation
        session([
            'new_class_name' => $request->class_name,
            'new_section_id' => $request->section_id,
            'existing_class_name' => $existingTimetable->class_name,
            'existing_section_id' => $existingTimetable->section_id,
            'existing_timetable_id' => $existingTimetable->id,
        ]);

        // Redirect to confirmation page
        return redirect()->route('timetable.confirmation');
    }

    
        // If no duplicate is found, create or update the timetable
        Timetable::updateOrCreate(
            [
                'teacher_id' => $request->teacher_id,
                'section_id' => $request->section_id,
                'class_name' => $request->class_name,
                'day' => $request->day,
                'timing' => $request->timing,
            ]
        );
    
        return redirect()->back()->with('success', 'Timetable saved successfully!');
    }
    

    public function updateExisting(Request $request)
    {
        $timetable = Timetable::find(session('existing_timetable_id'));
    
        // Update the timetable with new class name and section
        $timetable->update([
            'class_name' => session('new_class_name'),
            'section_id' => session('new_section_id'),
        ]);
    
        // Clear session data
        session()->forget(['new_class_name', 'new_section_id', 'existing_class_name', 'existing_section_id', 'existing_timetable_id']);
    
        return redirect()->route('timetable.create')->with('success', 'Timetable updated successfully!');
    }
    
    
    

    



    public function edit(request $request, $teacherId){
        // Fetch the teacher by ID
    $teacher = User::findOrFail($teacherId);

    // Fetch all timetable entries for this teacher
    $timetables = Timetable::where('teacher_id', $teacherId)->get();

    // Define the time slots (you can customize this based on your schedule)
    $timeSlots = [
        '09:00 - 10:30',
        '10:45 - 12:15',
        '12:30 - 14:00',
        '14:00 - 15:30',
        '15:45 - 16:15',
    ];

    // Initialize an empty array to hold the schedule data
    $schedule = [];

    // Populate the schedule array with the timetable data
    foreach ($timetables as $timetable) {
        $schedule[$timetable->timing][$timetable->day] = $timetable;
    }
        return view('admin.editTimeTable', compact('schedule', 'timeSlots'));
    }


}
