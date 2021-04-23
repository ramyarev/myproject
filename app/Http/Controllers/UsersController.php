<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use \Crypt;
use Auth;
use DB;


class UsersController extends Controller
{
    //Registeration
	public function register(Request $request)
	{
		$email=User::where('email', $request->email)->first();
		if($email){
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'The email address already exists'
            ]);
        }
		$user = new User;
        $user->name = $request->name;
		$user->email = $request->email;
        $user->password = bcrypt($request->password);
		if($user->save()){
			$data['token'] = $user->createToken('myproject')->accessToken;
			 return response()->json([
                'status' => true,
                'data' => $user,
                'token_data' => $data,
                'message' => 'Account has been created successfully.'
            ]);
		}
		else
		{
			return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'User Registration Failed'
            ]);
		}
	}
	
	//Login
	 public function login(Request $request)
	 {
        if(Auth::attempt([ 'email' => request('email'), 'password' => request('password') ]))
        {
				$user = Auth::user();            
                $data['token'] = $user->createToken('myproject')->accessToken; 
                return response()->json([
                    'status' => true,
                    'loggedin_user' => $user,
                    'token_data' => $data,
                    'message' => 'User Login Successfully'
                ]);
		}
		
        else{
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Email ID or Password is invalid'
            ]);
            
        }
    }
	
	// Logout
    public function logout()
    {
        $accessToken = Auth::user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $revoke = $accessToken->revoke();
        if($revoke){
            return response()->json([
                'status' => true,
                'message' => 'Successfully Logged Out'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Logout Failed'
            ]);
        }
    }
	//Get User List with authentication
	public function userlist(Request $request)
	{
		
		if (Auth::check()){
		
			$getlist=User::all();
			return response()->json([
				'status' => true,
				'message' => 'User List',
				'data'=>$getlist
			]);
		}
		else{
			return response()->json([
				'status' => false,
				'message' => 'Not Authenticated'
			]);
		}
	}
	
}
