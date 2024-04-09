<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

//public function login(){
//    echo "login endpoint";
//}


    public function assignRole(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|integer',
            'role_name' => 'required|string',
        ]);

        // Retrieve the user instance based on the provided ID
        $user = User::findOrFail($request->user_id);

        // Retrieve the role by name
        $role = Role::where('name', $request->role_name)->first();

        if (!$role) {
            // Role not found, return an error response
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Attach the role to the user
        $user->role()->associate($role)->save();

        // Return a success response
        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user,
//            'role' => $role
        ], 200);
    }


    //http://localhost:8000/api/auth/login
public function login(Request $request){
    $credentials = request(['email','password']);

    if(!Auth::attempt($credentials)){
        return response()->json([
            'message'=> 'Invalid email or password'
        ],401);
    }
    $user= $request->user();
    $token= $user->createToken('Access Token');
    $user->access_token = $token->accessToken;
    return response()->json([
        'message' => 'Login successful',
        'user'=>$user,
        'username'=>$user->name,
        'role'=>$user->role->name,
        'token'=> $user->access_token,
    ],200);

}

    //http://localhost:8000/api/auth/register
//public function register(){
//    echo "register endpoint";
//}

    public function register(Request $request) {

        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        // Retrieve the default role ("user")
        $defaultRole = Role::where('name', 'user')->first();

        // Ensure that the default role exists
        if (!$defaultRole) {
            return response()->json([
                "error" => "Default role 'user' not found"
            ], 404);
        }

        // Create the user with the default role attached
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Save the user
//        $user->save();

        // Assign the default role to the user
        $user->role()->associate($defaultRole);
        $user->save();

        return response()->json([
            "message" => "User registered successfully"
        ], 201);
    }



//http://localhost:8000/api/logout
    public function logout(Request $request){
    $request->user()->token()->revoke();
    return response()->json([
        'message'=> "User logged out successfully"
    ],200);

    }

//http://localhost:8000/api/helloworld
public function index(){
    echo "hello world";
}

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => 'failed', 'message' => 'User not found'], 404);
        }

        $token = Str::random(60);
        $existingToken = DB::table('password_resets')->where('email', $user->email)->first();

        if ($existingToken) {
            DB::table('password_resets')->where('email', $user->email)->update(['token' => $token]);
        } else {
            DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token]);
        }

        Mail::send('forgot_password', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Réinitialisation du mot de passe');
        });

        return response()->json(['status' => 'success', 'message' => 'Password reset link sent to your email']);
    }

    public function reset(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $validatedData['email'],
                'token' => $request->token
            ])->first();
        if (!$updatePassword) {
            return response()->json(['error' => 'invalid_token'], 400);
        }

        User::where('email', $validatedData['email'])
            ->update(['password' => Hash::make($validatedData['password'])]);

        DB::table('password_resets')->where('email', $validatedData['email'])->delete();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }




}
