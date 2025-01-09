<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
        // $usuarios = [
        //     [
        //         'id' => 1,
        //         'nombre' => 'Juan Pérez',
        //         'email' => 'juan.perez@example.com',
        //     ],
        //     [
        //         'id' => 2,
        //         'nombre' => 'María López',
        //         'email' => 'maria.lopez@example.com',
        //     ],
        //     [
        //         'id' => 3,
        //         'nombre' => 'Carlos García',
        //         'email' => 'carlos.garcia@example.com',
        //     ],
        //     [
        //         'id' => 4,
        //         'nombre' => 'Ana Fernández',
        //         'email' => 'ana.fernandez@example.com',
        //     ],
        // ];
        $usersColletion = DB::table('users')->where('is_admin', 0)->orderBy('id')->get();
        $users = json_decode(json_encode($usersColletion), true);

        return view('admin.users',compact('users'));
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
        return view('admin.user',compact('user'));
    }
}
