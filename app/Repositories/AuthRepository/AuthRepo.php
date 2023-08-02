<?php
namespace App\Repositories\AuthRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

# Model
use App\Models\User;

# Interface
use App\Repositories\AuthRepository\AuthInterface;

class AuthRepo implements AuthInterface
{
    

    public function __construct()
    {
        
    }
    
    public function createUser($data)
    {
        $user = User::create($data);
        return $user;
    }

    public function getDetails($id)
    {
        return User::find($id);
    }

    public function getApiToken($id)
    {    
        $user = $this->getDetails($id);
        return $user->createToken('API TOKEN')->plainTextToken;
    }

    public function accessLogin($email, $password)
    {
        if(Auth::attempt(['email' => $email, 'password' => $password])){

            $token =  $this->getApiToken(Auth::id());

            return $token;
        }
    }
}