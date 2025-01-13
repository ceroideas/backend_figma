<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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

        $usersColletion = DB::table('users')->where('is_admin', 0)->orderBy('id')->get();
        $users = json_decode(json_encode($usersColletion), true);

        return view('admin.users', compact('users'));
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
        $userColletion = DB::table('users')->where('id', $id)->first();
        $user = json_decode(json_encode($userColletion), true);
        return view('admin.user', compact('user'));
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
    
}
