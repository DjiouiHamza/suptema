<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

     use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */




     public function redirectTo() {
        // User role
        $role = auth()->user()->isadmin;
        // Check user role
        switch ($role) {
            case 1:
                return '/adminhome';
                break;
            case 0:
                return '/teacherhome';
                break;
            default:
                return '/login';
                break;
        }
    }



    //  protected function authentication(Request $request, $user): Response
    //  {
        
    //      if(auth()->check() && !auth()->user()->isadmin){
    //         return redirect('/teacherhome');
    //         echo 'test';
            
    //      } elseif(auth()->check() && auth()->user()->isadmin){
    //         return redirect('/adminhome');
    //      }
    //  }

    //  protected $redirectTo = '/home';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
      }

}
