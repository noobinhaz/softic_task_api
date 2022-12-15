<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\Point;
use App\Models\User;
use App\Models\Affiliation;
use Illuminate\Http\Request;
use App\Models\AffiliateUser;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserControl extends Controller
{
    //
    public function register(Request $request){
        try {
            //code...'
            DB::beginTransaction();
            $formFields = $request->validate([
                'name' => ['required', 'string'],
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'password' => ['required', 'string', 'min:8'],
                'reference' => 'nullable | string'
            ]);
            
            $formFields['role'] = Role::General_user;
            $formFields['password'] = bcrypt($formFields['password']);

            $register = User::create($formFields);

            if(isset($formFields['reference']) && $register){
                $userId = Affiliation::where('ref_id', $formFields['reference'])->value('userId');
                
                if(!$userId){
                    throw new \Exception('Link Unavailable');
                }

                $affiliatedUser = AffiliateUser::create([
                    'userId' => $userId,
                    'taggedUserId' => $register['id']
                ]);

                $userInfo = User::where('id',$userId)->select('name', 'email', 'point', 'role')->first();
                $role = $userInfo['role'] > 2 ? 2: $userInfo['role'];
                $register['affiliatedUser'] = $userInfo;

                $point = (int)$userInfo['point'] + Point::getPoint;
                User::where('id', $userId)->update([
                    'point' => $point,
                    'role' => $role
                ]);

            }
            DB::commit();
            return response()->json([
                'isSuccess'=>true,
                'message'  => 'Registration successful',
                'data'     => $register
            ],200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'isSuccess' => false,
                'message'   => $th->getMessage(),
                'data'      => []
            ],403);
        }
    }

    public function authenticate(Request $request){
        try {
            //code...
             
            $formFields = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string']
            ]);
    
            if(auth()->attempt($formFields)){

                $user = Auth::user();

                $token = $user->createToken('access_token')->accessToken;

                $user = User::find($user->id);
                
                return response()->json([
                    'user' => $user,
                    'token' => $token
                ], 200);

            }else{
                throw new \Exception('Email/Password did not match');
            }

        } catch (\Throwable $th) {
            return response()->json([
                'isSuccess' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 401);
        }
    }


    public function index(){
        $role = auth()->user()->role;
        $search = request('search');

        if(Role::Super_admin == $role){
            $users = User::with(['affiliatedUsers'])
                    ->select(array('id', 'name', 'email', 'point','role'))
                    ->where('name', 'like', '%'.$search.'%')
                    ->get();

            return response()->json([
                'isSuccess' => true,
                'message'   => '', 
                'data'      => $users
            ],200);
        }
        return response()->json([
            'isSuccess'=>false,
            'message'  => 'This User is not authenticated to access this role!',
            'data'=> []
        ], 401);
    }
    
    public function show(Request $request, $id){
        $role = auth()->user()->role;
        

        if(Role::Super_admin == $role){
            $users = User::with(['affiliatedUsers'])
                    ->select(array('id', 'name', 'email', 'point','role'))
                    ->where('id', $id)
                    ->first();

            return response()->json([
                'isSuccess' => true,
                'message'   => '', 
                'data'      => $users
            ],200);
        }
        return response()->json([
            'isSuccess'=>false,
            'message'  => 'This User is not authenticated to access this role!',
            'data'=> []
        ], 401);
    }
    public function logout(Request $request){
        
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'isSuccess' => false,
            'message'   => 'Logout successful',
            'data'      => []
        ], 200);
    }
}
