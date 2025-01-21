<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('admin');
    // }

    public function index()
    {
        return view('admin.dashboard');
    }

    public function users()
    {
        $format = 'F j, Y - g:i A';
        $usersCollection = DB::table('users')->where('is_admin', 0)->orderBy('id')->get();
        $angularAppUrl = env('APP_URL') . '/#/login';
        foreach ($usersCollection as $user) {
            $user->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->format($format);
            $user->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $user->updated_at)->format($format);
            if ($user->last_login_at) {
                $user->last_login_at = Carbon::createFromFormat('Y-m-d H:i:s', $user->last_login_at)->diffForHumans();
            } else {
                $user->last_login_at = 'Date not available';
            }
            $user->role = $user->is_admin == 1 ? 'Admin' : 'User';
            $user->enabled = $user->is_enabled == 1 ? 'Yes' : 'No';
        }
    
        $users = json_decode(json_encode($usersCollection), true);
        return view('admin.users', compact('users', 'angularAppUrl'));
    }
    


    public function projects()
    {
        $projectsColletion = DB::table('projects')
            ->join('users', 'projects.user_id', '=', 'users.id')
            ->select('projects.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('projects.id')
            ->get();

        $projects = json_decode(json_encode($projectsColletion), true);
        return view('admin.projects', compact('projects'));
    }
    public function simulations()
    {
        $simulationsColletion = DB::table('simulations')
            ->join('projects', 'simulations.project_id', '=', 'projects.id')
            ->select('simulations.*', 'projects.name as project_name')
            ->orderBy('simulations.id')
            ->get();

        $simulations = json_decode(json_encode($simulationsColletion), true);
        return view('admin.simulations', compact('simulations'));
    }

    public function user($id)
    {
        $format = 'F j, Y - g:i A';
        $userCollection = DB::table('users')->where('id', $id)->first();
        $userCollection->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->created_at)->format($format);
        $userCollection->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->updated_at)->format($format);
        if ($userCollection->last_login_at) {
            $userCollection->last_login_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->last_login_at)->format($format);
        } else {
            $userCollection->last_login_at = 'Date not available';
        }
        $userCollection->role = $userCollection->is_admin == 1 ? 'Admin' : 'User';
        $userCollection->enabled = $userCollection->is_enabled == 1 ? 'Yes' : 'No';

        $user = json_decode(json_encode($userCollection), true);
        return view('admin.user', compact('user'));
    }
    public function deleteUser($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function editUser($id)
    {
        $format = 'F j, Y - g:i A';
        $userCollection = DB::table('users')->where('id', $id)->first();
        $userCollection->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->created_at)->format($format);
        $userCollection->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->updated_at)->format($format);
        $userCollection->role = $userCollection->is_admin == 1 ? 'Admin' : 'User';

        $user = json_decode(json_encode($userCollection), true);
        return view('admin.update-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {

        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'User not found.']);
        }


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'is_admin' => 'nullable|in:0,1',
            'is_enabled' => 'nullable|in:0,1',
        ]);


        DB::table('users')->where('id', $id)->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $validated['is_admin'] ?? $user->is_admin,
            'is_enabled' => $validated['is_enabled'] ?? $user->is_enabled,
        ]);


        return redirect()->back()->with('success', 'User information updated successfully.');
    }


    public function project($id)
    {
        $format = 'F j, Y - g:i A';


        $projectCollection = DB::table('projects')->where('id', $id)->first();


        $carbonCreatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->created_at);
        $projectCollection->created_at = $carbonCreatedDate->format($format);

        $carbonUpdatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->updated_at);
        $projectCollection->updated_at = $carbonUpdatedDate->format($format);


        $project = json_decode(json_encode($projectCollection), true);


        $userCollection = DB::table('users')
            ->where('id', $projectCollection->user_id)
            ->select('id', 'name', 'email', 'created_at', 'updated_at')
            ->first();


        $userCollection->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->created_at)->format($format);
        $userCollection->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->updated_at)->format($format);


        $user = json_decode(json_encode($userCollection), true);


        return view('admin.project', compact('project', 'user'));
    }

    public function simulation($id)
    {
        $format = 'F j, Y - g:i A';


        $simulationCollection = DB::table('simulations')->where('id', $id)->first();


        $carbonCreatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $simulationCollection->created_at);
        $simulationCollection->created_at = $carbonCreatedDate->format($format);

        $carbonUpdatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $simulationCollection->updated_at);
        $simulationCollection->updated_at = $carbonUpdatedDate->format($format);


        $simulation = json_decode(json_encode($simulationCollection), true);


        $projectCollection = DB::table('projects')
            ->where('id', $simulationCollection->project_id)
            ->select('id', 'name', 'created_at', 'updated_at')
            ->first();


        $carbonCreatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->created_at);
        $projectCollection->created_at = $carbonCreatedDate->format($format);

        $carbonUpdatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->updated_at);
        $projectCollection->updated_at = $carbonUpdatedDate->format($format);


        $project = json_decode(json_encode($projectCollection), true);


        return view('admin.simulation', compact('simulation', 'project'));
    }
}
