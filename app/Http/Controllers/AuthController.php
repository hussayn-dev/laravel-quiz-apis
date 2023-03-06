<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Events\SendUserVerificationEvent;
use App\Providers\SendPasswordMailEvent;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\JsonResponseService;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{

    public $jsonResponseService;
    public function __construct(JsonResponseService $jsonResponseService)
    {
        $this->jsonResponseService = $jsonResponseService;
    }



 public function register(Request $request) {

    $validation = Validator::make($request->all(),[
        'email' => 'bail|required|email|unique:users|max:100',
        'username' => 'bail|required|alpha_dash|min:3|max:20',
        'password' => 'bail|required|confirmed|min:8|max:25',
        'password_confirmation' => 'bail|required|',
    ]);

    if ($validation->fails()) {
        return response()->json($validation->errors(), 400);
    }

    $user = User::create([
       'email' => $request->email,
       'username' => $request->username,
       'password' =>  bcrypt($request->password),
    //    'verification_token' => $this->generateToken(),
    ]);
    $user->save();

    // event(new SendUserVerificationEvent($user));
    // event(new Registered($user));
    // $user->sendEmailVerificationNotification();
    // return $this->jsonResponseService->success("Verification email sent successfully",[], 201);
    return $this->jsonResponseService->success("User created successfully",[], 201);

 }

//  public function verify(Request $request)
//  {
//     try {
//         $validation = Validator::make($request->all(),[
//             'email' => 'bail|required|email|max:100',
//             'token' => 'bail|required|string|max:100',
//         ]);

//         if ($validation->fails()) {
//             throw new Exception('Verification failed');
//         }

//         $token = $request->token;
//         $user = User::where('verification_token', $token)
//                       ->where('email', $request->email)->first();

//         if (!$user) {
//             throw new Exception('Invalid verification token');
//         }

//         $user->email_verified_at = now();
//         $user->verification_token = null;
//         $user->save();

//         return $this->jsonResponseService->success("Email verified successfully",[], 200);
//     } catch (\Exception $e ) {

//      return $this->jsonResponseService->error($e->getMessage(),[], 400);

//     }
//  }

//  public function resend(Request $request)
//  {
//     try {
//         $validation = Validator::make($request->all(),[
//             'email' => 'bail|required|email|max:100',
//         ]);

//         if ($validation->fails()) {
//             return response()->json($validation->errors(), 400);
//         }

//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//         throw new CustomException('User not found', 404);
//         }

//         if ($user->hasVerifiedEmail()) {
//            throw new CustomException('Email already verified',400);
//         }

//         $user->verification_token = $this->generateToken();
//         $user->save();

//         event(new SendUserVerificationEvent($user));

//         return $this->jsonResponseService->success("Verification email sent successfully",[], 200);
//     } catch(CustomException $e) {

//         return $this->jsonResponseService->error($e->getMessage(),[], $e->status);
//     }

//  }

public function login (Request $request)
{
    try {
    $validation = Validator::make($request->all(),[
        'email' => 'bail|required|email|max:100',
        'password' => 'bail|required|min:8|max:25',
    ]);

    if ($validation->fails()) {
        throw new CustomException('Invalid login credentials', 400);
    }

    $credentials = $request->only(['email', 'password']);

    // if (!Auth::attempt($credentials)) {
    //     throw new CustomException('Invalid login credentials', 401);
    // }
    $user = User::where(['email' => $credentials['email']])->select(["id", "email","username", "password"])->first();

    if(!$user) {
        throw new CustomException("A user with this email doesn't exit", 404);
    }

    if(!Hash::check($request->password, $user->password)) {
        throw new CustomException("Invalid login credentials", 400);
    }
    // $user = Auth::user();
    // if(isNull($user->verified_at)) {
    //    throw new CustomException('Your Email is not verified', 401);
    // }
    $expiration = now()->day(2);
    $token = $user->createToken('login', ['*'], [
        'expires_at' => $expiration
    ])->plainTextToken;

    return $this->jsonResponseService->success("Login successful",['user' => $user, 'token' => $token,  'token_expires_at' => Carbon::parse($expiration)->toDateTimeString()], 200);
    } catch (CustomException $e) {

    return $this->jsonResponseService->error($e->getMessage(),[], $e->status);
    }
}

public function logout(Request $request) {
     $request->user()->currentAccessToken()->delete();
    return $this->jsonResponseService->success("Successfully logged out",[], 200);

}
// public function sendPasswordResetRequest(Request $request) {
//     try {
//         $validation = Validator::make($request->all(),[
//             'email' => 'bail|required|email|max:100',
//         ]);

//         if ($validation->fails()) {
//             return response()->json($validation->errors(), 400);
//         }

//         $user = User::where('email', $request->email)->first();

//         if(!$user) {
//             throw new CustomException('An account with this email does not exist', 404);
//         }

//         // if($this->justCreatedToken($user)) {
//         //     throw new CustomException('Password throttled',400);
//         // }
//         $token = $this->passwordToken($user);

//         event(new SendPasswordMailEvent($user, $token));
//         return $this->jsonResponseService->success("Password-reset email sent successfully",[], 200);
//     } catch (CustomException $e) {
//         return $this->jsonResponseService->error($e->getMessage(),[], $e->status);
//     }
// }

// public function passwordReset(Request $request) {
//     try {
//         $validation = Validator::make($request->all(),[
//             'email' => 'bail|required|email|max:100',
//             'token' => 'bail|required|string|max:100',
//             'password' => 'bail|required|min:8|confirmed|max:25',
//             'password_confirmation' => 'bail|required|min:8|max:25'
//         ]);
//         if ($validation->fails()) {
//             return response()->json($validation->errors(), 400);
//         }
//         $user = User::where('email', $request->email)->first();

//         if(!$user) {
//             throw new CustomException('An account with this email does not exist', 404);
//         }

//         $token =  DB::table('password_resets')
//         ->where('email', $user->email)
//         ->where('token', $request->token);


//         if(!$token->first())
//         {
//             throw new CustomException('Wrong token provided', 400);
//         }
//         $user->password = bcrypt($request->password);
//         $user->save();
//         $token->delete();
//         return $this->jsonResponseService->success("Password reset successful",[], 200);

//     } catch (CustomException $e) {
//         return $this->jsonResponseService->error($e->getMessage(),[], $e->status);
//     }
// }
// private function passwordToken($user) {
//     // $passwordReset = DB::table('password_resets')
//     // ->where('email', $user->email);

//     // if ($passwordReset->first()) {

//     //     $passwordReset->delete();
//     // }

//     $token = $this->generateToken();
//     $payload = ['email' => $user->email,
//     'token' => $token,
//     'created_at' => now()];

//      DB::table('password_resets')->insert($payload);
//      return $token;
// }
// private function justCreatedToken ($user) {
//     $passwordReset = DB::table('password_resets')
//     ->where('email', $user->email)
//     ->first();

//     if (!$passwordReset) {
//         return false;
//     }

//     $difference = now()->diffInDays(Carbon::parse($passwordReset->created_at));
//     if($difference < 2)
// }

private function generateToken()
{
    return md5(rand(1, 10) . microtime());
}

}
