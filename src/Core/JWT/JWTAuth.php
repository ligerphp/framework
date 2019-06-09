<?php
namespace Core\JWT;

use Core\Http\Request;
use Core\JWT\Factory;
use Core\Database\Ligerbase\Model\Model;
use Core\JWT\Exceptions\AuthCredentialsNotSetException;

class JWTAuth extends Factory{

    /**
     *  Authenticate and send back token from reques
     * @param  \Core\Http\Request $request
     */
    public function fromRequest(Request $request){

        
       return $this->authDB();
    }

    /**
     * Perfomr database query to find user and generate token;
     */
    public function authDB(){
        $user = $this->model->query("SELECT * FROM users WHERE email = ? ", [$this->email])->first();
        if ($user->password && password_verify($this->password, $user->password)) {
            
           return $this->makeWithCustomClaims(['id' => $user->id,'email' => $user->email,'fname' => $user->fname]);
        } else {
            return false;
        }

    }


    public function auth(){

        $this->email = $_REQUEST['email'];
        $this->password = $_REQUEST['password'];
        $this->model =  new Model();

        if(!isset($this->email) || !isset($this->password)){
         throw new AuthCredentialsNotSetException('Email and  password is required for authentication');
        }

        return $this;
    }

    public function isUser(){

    }


    public function setRequest($request){
        $this->request = $request;
    }



}