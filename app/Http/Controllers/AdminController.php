<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Office;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function dashboard(){

        $user = Auth::user();
        $users = User::leftJoin('offices', 'users.office_id', '=', 'offices.id')
                 ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                 ->select('users.*', 'offices.name as office_name', 'departments.name as department_name')
                 ->get();
        return view('system.dashboard', compact('users'));
    }
    public function create()
    {
        $offices = Office::all();
        $departments = Department::all();
        return view('system.create_user', compact('offices', 'departments'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'office_id' => 'required|exists:offices,id',
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt('password123');
        $user->office_id = $request->input('office_id');

        if ($user->office->name === 'Admin') {
            $user->department_id = null;
            $user->role = 'admin';
        } else {
            $validatedData = $request->validate([
                'department_id' => 'required|exists:departments,id',
            ]);
            $user->department_id = $request->input('department_id');
            switch ($user->office->name) {
                case 'College':
                    $user->role = 'college';
                    break;
                case 'Campus Records':
                    $user->role = 'campus_records';
                    break;
                case 'Campus Extension':
                    $user->role = 'campus_extensions';
                    break;
                case 'Chancellor':
                    $user->role = 'chancellor';
                    break;
                case 'Central Admin':
                    $user->role = 'central_admin';
                    break;
                case 'President':
                    $user->role = 'president';
                    break;
                case 'VP Research':
                    $user->role = 'vp_research';
                    break;
                case 'University Extension':
                    $user->role = 'university_extension';
                    break;
                case 'Extension':
                    $user->role = 'extension';
                    break;
                default:
                    $user->role = 'college';
                    break;
            }
        }

        $user->save();

        return redirect()->route('users.create')->with('success', 'User created successfully.');
    }

        public function edit($id)
        {
        $user = User::findOrFail($id);
        $offices = Office::all();
        $departments = Department::all();

        // Set default role to "admin" if the selected office is "Admin"
        if ($user->office->name === 'Admin') {
            $user->role = 'admin';
        }

        return view('system.edit_user', compact('user', 'offices', 'departments'));

        }
        public function update(Request $request, $id)
        {
            $user = User::findOrFail($id);
            
            // Validate user input
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'office_id' => 'required',
                'department_id' => 'nullable',
                'role' => 'required'
            ]);

            // Update user data
            $user->name = $request->name;
            $user->email = $request->email;
            $user->office_id = $request->office_id;
            $user->department_id = $request->department_id;
            $user->role = $request->role;
            $user->save();

            return redirect()->route('system.dashboard')->with('success', 'User has been updated.');
        }


        public function destroy($id)
        {
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('system.dashboard')->with('success', 'User has been deleted.');
        }


}
