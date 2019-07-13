<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{

    public function showLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request){

        $this->validadeLogin($request);
       
        if(Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'condicion' => 1])){
            return Redirect()->route('main');
        }

        return back()
        ->withErrors(['usuario' => trans('auth.failed')])
        ->withInput(request(['usuario']));
    }

    protected function validadeLogin(Request $request){
        $this->validate($request, [
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);

    }
   
}
