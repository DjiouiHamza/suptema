<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
        protected function index() {
            // User role
            $role = auth()->user()->isadmin;
            // Check user role
            switch ($role) {
                case 1:
                    return redirect('/adminhome') ;
                    break;
                case 0:
                    return redirect('/teacherhome');
                    break;
                default:
                    return redirect('/login');
                    break;
            }
        }
    }

