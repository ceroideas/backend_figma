<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

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
        return view('admin.users', compact('users'));
    }


    public function projects()
    {
        $projectsCollection = DB::table('projects')
            ->join('users', 'projects.user_id', '=', 'users.id')
            ->select('projects.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('projects.id')
            ->get()
            ->map(function ($project) {
                $sceneries = json_decode($project->sceneries, true);
                $project->sceneries_count = is_array($sceneries) ? count($sceneries) : 0;
                return $project;
            });

        $projects = json_decode(json_encode($projectsCollection), true);
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
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }


        $user->created_at2 = Carbon::parse($user->created_at)->format($format);
        $user->updated_at2 = Carbon::parse($user->updated_at)->format($format);


        $user->last_login_at2 = $user->last_login_at
            ? Carbon::parse($user->last_login_at)->format($format)
            : 'Date not available';

        $user->role = $user->is_admin == 1 ? 'Admin' : 'User';
        $user->enabled = $user->is_enabled == 1 ? 'Yes' : 'No';


        $lastCreatedDate = $user->projects()->max('created_at');
        $user->last_project_created_at = $lastCreatedDate
            ? Carbon::createFromFormat('Y-m-d H:i:s', $lastCreatedDate)->format($format)
            : 'Date not available';
        $lastUpdatedDate = $user->projects()->max('updated_at');
        $user->last_project_updated_at = $lastUpdatedDate
            ? Carbon::createFromFormat('Y-m-d H:i:s', $lastUpdatedDate)->format($format)
            : 'Date not available';

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

    public function updateProject(Request $request, $id)
    {
        $project = DB::table('projects')->where('id', $id)->first();

        if (!$project) {
            return redirect()->back()->withErrors(['error' => 'Project not found.']);
        }

        $validated = $request->validate([

            'user_id' => 'required|exists:users,id',

        ]);

        DB::table('projects')->where('id', $id)->update([

            'user_id' => $validated['user_id'],

        ]);

        return redirect()->back()->with('success', 'Project information updated successfully.');
    }


    public function project($id)
    {
        $format = 'F j, Y - g:i A';

        $projectCollection = DB::table('projects')->where('id', $id)->first();

        $carbonCreatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->created_at);
        $projectCollection->created_at = $carbonCreatedDate->format($format);

        $carbonUpdatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->updated_at);
        $projectCollection->updated_at = $carbonUpdatedDate->format($format);

        $userCollection = DB::table('users')
            ->where('id', $projectCollection->user_id)
            ->select('id', 'name', 'email', 'created_at', 'is_admin', 'updated_at')
            ->first();

        $userCollection->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->created_at)->format($format);
        $userCollection->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->updated_at)->format($format);
        $userCollection->role = $userCollection->is_admin == 1 ? 'Admin' : 'User';

        $project = json_decode(json_encode($projectCollection), true);
        $user = json_decode(json_encode($userCollection), true);


        $project['nodes_count'] = DB::table('nodes')
            ->where('project_id', $id)
            ->count();


        $project['constants_count'] = DB::table('nodes')
            ->where('project_id', $id)
            ->where('type', 1)
            ->count();

        $project['variables_count'] = DB::table('nodes')
            ->where('project_id', $id)
            ->where('type', 2)
            ->count();

        $project['simulation_count'] = DB::table('simulations')
            ->where('project_id', $id)
            ->count();


        return view('admin.project', compact('project', 'user'));
    }

    public function editProject($id)
    {
        $format = 'F j, Y - g:i A';

        $projectCollection = DB::table('projects')->where('id', $id)->first();

        $carbonCreatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->created_at);
        $projectCollection->created_at = $carbonCreatedDate->format($format);

        $carbonUpdatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $projectCollection->updated_at);
        $projectCollection->updated_at = $carbonUpdatedDate->format($format);

        $userCollection = DB::table('users')
            ->where('id', $projectCollection->user_id)
            ->select('id', 'name', 'email', 'created_at', 'is_admin', 'updated_at')
            ->first();

        $userCollection->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->created_at)->format($format);
        $userCollection->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $userCollection->updated_at)->format($format);
        $userCollection->role = $userCollection->is_admin == 1 ? 'Admin' : 'User';

        $project = json_decode(json_encode($projectCollection), true);
        $user = json_decode(json_encode($userCollection), true);
        $owners = User::all();
        $owners = json_decode(json_encode($owners), true);

        return view('admin.update-project', compact('project', 'user', 'owners'));
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
