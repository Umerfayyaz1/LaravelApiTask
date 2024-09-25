<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function welcome(){
        echo "Welcome to Api Task Project"; die;
    }
    public function register(Request $request){
        try{
            $validatedUser=Validator::make($request->all(),
            [
                'name' => 'required',
                'email'=>'required|email|unique:users,email',
                'password'=>'required'
            ]);

            if($validatedUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'validation error',
                    'errors'=> $validatedUser->errors()
                ],401);
            }

            $user=User::create([
                'name' => $request->name,
                'email'=>$request->email,
                'password'=>$request->password,
            ]);

            return response()->json([
                'status'=>true,
                'message'=>'User Registered Successfully',
            ],200);

        }catch(Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage(),
            ],500);
        }
    }

    public function login(Request $request){
        try{

            $validatedUser=Validator::make($request->all(),
            [
                'email'=>'required|email',
                'password'=>'required'
            ]);

            if($validatedUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'validation error',
                    'errors'=> $validatedUser->errors()
                ],401);
            }

            if(! Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status'=>false,
                    'message'=>'invalid Credentials',
                ],401);
            }

            $user=User::where('email',$request->email)->first();

            return response()->json([
                'status'=>true,
                'message'=>'User Login Successful',
                'token'=> $user->createToken("API TOKEN")->plainTextToken
            ],200);

        } catch(Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage(),
            ],500);
        }
    }

    public function profile(){
        $userData= auth()->user();
        return response()->json([
            'status'=>true,
            'message'=>'Profile Information',
            'data'=> $userData,
            'id'=>auth()->user()->id
        ],200);

    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=>true,
            'message'=>'User Logout Successful',
            'data'=> []
        ],200);

    }




}
