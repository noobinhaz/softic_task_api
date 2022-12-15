<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Models\Affiliation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DashBoardControl extends Controller
{
    //
    private function unique_str($column) {
        $uniqueStr = Str::random(10);

        while(Affiliation::where($column, $uniqueStr)->exists()) {
            
            $uniqueStr = Str::random(10);

        }

        return $uniqueStr;
    }

    public function updateCredentials(){
        $userId = auth()->user()->id;
        $role = auth()->user()->role;
        try {
            //code...
            if(Role::Super_admin != $role && Role::Affiliate != $role){
                throw new \Exception('This User is not allowed to access this role!');
            }
            $ref_id = self::unique_str('ref_id');
            $url = 'http://localhost:3000/register?ref='. $ref_id;
            $rand_code = self::unique_str('rand_code');

            $ifExists = Affiliation::where('userId', $userId)->first();
            
            $info = $ifExists;

            if($ifExists){
               
                $update = Affiliation::where('userId', $userId)->update([
                    'url' => $url,
                    'rand_code' => $rand_code,
                    'ref_id' => $ref_id,
                ]);
                if(!$update){
                    throw new \Exception('Could not Update Credentials');
                }

                $info = [
                    'url' => $url,
                    'rand_code' => $rand_code,
                    'ref_id' => $ref_id,
                ];
            }

            return response()->json([$info], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'isSuccess' => false,
                'message' => $th->getMessage(),
                'data' => []
            ],403);
        }
    }

    public function dashboard(){
        $userId = auth()->user()->id;
        $role = auth()->user()->role;
        try {
            //code...
            if(Role::Super_admin != $role && Role::Affiliate != $role){
                throw new \Exception('This User is not allowed to access this role!');
            }
            $ref_id = self::unique_str('ref_id');
            $url = 'http://localhost:3000/register?ref='. $ref_id;
            $rand_code = self::unique_str('rand_code');

            $ifExists = Affiliation::where('userId', $userId)->first();
            
            $info = $ifExists;

            if(!$ifExists){
               
                $created = Affiliation::create([
                    'url' => $url,
                    'rand_code' => $rand_code,
                    'ref_id' => $ref_id,
                    'userId' => $userId
                ]);
                $info = $created;
            }

            return response()->json([$info], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'isSuccess' => false,
                'message' => $th->getMessage(),
                'data' => []
            ],403);
        }
        
    }

    public function adminDashboard(Request $request){

        $userId = auth()->user()->id;
        $role = auth()->user()->role;

        try {
            //code...
            if(Role::Super_admin != $role){
                throw new \Exception('This User is not allowed to access this role!');
            }

            $form = $request->validate([
                'userId' => ['required', 'integer'], 
                'role' => ['required', 'integer'],
            ]);

            $update = User::where('id', $form['userId'])
                        ->update([
                            'role'=> $form['role']
                        ]);

            if(!$update){
                throw new \Exception('Could not update User role');
            }
            return response()->json([
                'isSuccess' => true,
                'message'   => 'User Role updated successfully',
                'data'      => []
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'isSuccess' => false,
                'message' => $th->getMessage(),
                'data' => []
            ],403);
        }
        
    }

    public function myAffiliateList(){
        $userId = auth()->user()->id;
        $role = auth()->user()->role;
        try {
            //code...
            if(Role::General_user == $role){
                throw new \Exception('User is not allowed to view this role');
            }

            $user = User::find($userId);
            $user->affiliatedUsers;
            
            return response()->json([
                'isSuccess' => true,
                'message'   => '',
                'data'      => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'isSuccess' => false,
                'message' => $th->getMessage(),
                'data' => []
            ],403);
        }
    }
}
