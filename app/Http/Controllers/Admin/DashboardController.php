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
}
