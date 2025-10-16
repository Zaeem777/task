<?php

namespace App\Http\Controllers;
// use Illuminate\Support\Str;
use App\Http\Requests\EditUserRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use App\Notifications\CustomResetPassword;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{

    public function register(RegisterUserRequest $request){
       $field = $request->validated();

        $user = User::create([
            'name' =>$field['name'],
            'email' =>$field['email'],
            'password' =>bcrypt($field['password']),
            'phone' =>$field['phone'],
            'status' =>$field['status'],
            
        ]);
        $token = auth()->login($user);

        return new UserResource($user, $token, "User has been registered successfully");
}

  public function login(LoginUserRequest $request){
       $field = $request->validated();

       $user = User::where('email', $field['email'])->first();

    if(!$user || !Hash::check($field['password'], $user->password)){
        return response([
             "response" => [
            'message' => 'Incorrect Credentials',
            'status' => '401'
             ]
        ]);
    }

       $token = auth()->login($user);

        return new UserResource($user, $token, "User has Logged in successfully");
}

public function userInfo()
{
 try{
    $user = JWTAuth::parseToken()->authenticate();
    return response()->json(new UserResource($user), 200);
 }
 catch (\Exception $e) {
    return response()->json(
        ['message' => 'Could not retrieve user information', 
        'status' => 500]);
 }
}

public function editInfo(EditUserRequest $request){
    if($request->validated()){
    try{

        $user = JWTAuth::parseToken()->authenticate();
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->phone = $request->input('phone', $user->phone);
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->status = $request->input('status', $user->status);
        $user->save();
         return new UserResource($user, null, "User information updated successfully");
    }
    catch (\Exception $e) {
        return response()->json(
            [
                 "response" => [
                'message' => 'Could not update user information', 
            'status' => 500]]);
     }
    }
    else{
        return response()->json(
            [ "response" => [
                'message' => 'Validation failed', 
            'status' => 422]]);
    }
}

public function updateOtherUsersInfo(EditUserRequest $request , string $id){
    $validated = $request->validated();
    if($validated){
    try{
        $user = User::find($id);
        if(!$user){
            return response()->json([
                 "response" => [
                'message' => 'User Not Found',
                'status' => 404
                 ]
            ],404); 
        }
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->phone = $request->input('phone', $user->phone);
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->status = $request->input('status', $user->status);
        $user->save();
         return new UserResource($user, null, "User information updated successfully");
    }
    catch (\Exception $e) {
        return response()->json(
            [
                 "response" => [
                'message' => $e->getMessage(),
                // 'message' => 'Could not update user information', 
            'status' => 500]]);
     }
    }
    else{
        return response()->json(
            [ "response" => [
                'message' => 'Validation failed', 
            'status' => 422]]);
    }
}
public function userDelete(){
    try{
        $user = JWTAuth::parseToken()->authenticate();
        $user->delete();
        return response()->json(
            [ "response" => [
                'message' => 'User deleted successfully', 
            'status' => 200]]);
    }
    catch (\Exception $e) {
        return response()->json(
            [ "response" => [
                'message' => 'Could not delete user', 
            'status' => 500]]);
     }
}

public function forgotPassword(Request $request)
{
    try{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);
    }
    catch (\Exception $e) {
        return response()->json(
            [ "response" => [
                'message' => $e->errors(), 
            'status' => 422]]);
     }
    $status = Password::sendResetLink(
        $request->only('email')
    );
     if ($status === Password::RESET_LINK_SENT) {
        return response()->json([
            "response" => [
                'message' => 'Password reset link sent successfully',
                'status'  => 200
            ]
        ], 200);
    }
    if ($status !== Password::RESET_LINK_SENT) {
        return response()->json([
            "response" => [
                'message' => 'Failed to send password reset link',
                'status'  => 500
            ]
        ], 500);
    }
    // Here, you would typically send the token to the user's email.
    // For demonstration, we'll just return the token in the response.

    // return response()->json([
    //     "response" => [
    //         'message' => 'Password reset token generated successfully',
    //         'token' => $token,
    //         'status'  => 200
    //     ]
    // ]);
}

public function resetPassword(Request $request)
{
    try{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]); 
}
catch( \Exception $e) {
    return response()->json(
        [ "response" => [
            'message' => $e->errors(), 
        'status' => 422]]);
}
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
                // 'remember_token' => Str::random(60),
            ])->save();
        }
    );          
    if ($status == Password::PASSWORD_RESET) {
        return response()->json([
            "response" => [
                'message' => 'Password has been reset successfully',
                'status'  => 200
            ]
        ]);
    } else {
        return response()->json([
            "response" => [
                'message' => 'Failed to reset password',
                'status'  => 500
            ]
        ], 500);
    }
}

public function logout(Request $request)
{
     auth()->logout();

    return response()->json([
    "response" => [
        'message' => 'Successfully logged out',
        'status'  => 200
    ]
    ]);

}
}



