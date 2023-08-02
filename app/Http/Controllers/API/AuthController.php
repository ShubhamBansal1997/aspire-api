<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

# Validation Requests
use App\Http\Requests\Auth\CreateUserRequest;
use App\Http\Requests\Auth\LoginRequest;

# Interface
use App\Repositories\AuthRepository\AuthInterface;


class AuthController extends Controller
{
    protected $authRepo;
    public $successStatus = 200;

    public function __construct(AuthInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }
    /**
     * Create User
     * 
     * @param str name
     * @param str email
     * @param str password
     * @return token (JSON)
     */
    public function createUser(CreateUserRequest $request)
    {
        $user = $this->authRepo->createUser($request->all());
        $success['token'] =  $this->authRepo->getApiToken($user->id);
        return response()->json(['success'=>$success], $this->successStatus);
    }

    /**
     * Login User
     * 
     * @param str $email
     * @param str $password
     * 
     * @return token (JSON)
     */
    public function login(LoginRequest $request){

        $success['token'] = $this->authRepo->accessLogin($request->email, $request->password);

        return $success['token'] 
            ?  response()->json(['success' => $success], $this->successStatus)
            :  response()->json(['error'=>'Unauthorized'], 401);
    }


    /**
     * get user details
     * 
     * @return user (Json)
     */
    public function details()
    { 
        $user = $this->authRepo->getDetails(Auth::id());

        return response()->json(['success' => $user], $this->successStatus);
    }


}
