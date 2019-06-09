<?php
namespace Core\JWT;

use Core\Http\Request;
use Core\JWT\Factory;
use Core\Database\Ligerbase\Model\Model;
use Core\JWT\Exceptions\AuthCredentialsNotSetException;
use Core\Validators\EmailValidator;

class JWTAuth extends Factory{

    /**
     * New Instance of JWTAuth 
     * @param \Core\Http\Request required
     * @return void
     */
    public function __construct(Request $request)
    {
     $this->request = $request;   
     $this->model =  new Model();

    }
    /**
     * Alias to fromRequest
     * 
     * @return string
     * 
     */
    public function login(){
       return $this->authDB();
    }
    /**
     *  Authenticate and send back token from request
     * 
     * @param  \Core\Http\Request $request
     * 
     */
    public function fromRequest(){
       return $this->authDB();
    }

    /**
     * Perfrom database query to find user and generate token;
     */
    public function authDB(){

        $user = $this->model->query("SELECT * FROM users WHERE email = ? ", [$this->email])->first();
        if (isset($user->password) && password_verify($this->password, $user->password)) {
            
           return $this->makeWithCustomClaims(['id' => $user->id,'email' => $user->email,'fname' => $user->fname]);
        } else {
            return false;
        }

    }


    public function auth(){
        $this->email = $this->request->get('email');
        $this->password = $this->request->get('password');

        $this->email =  sanitize($this->email);
        $this->password = sanitize($this->password);

        $isValidEmail =  new EmailValidator($this->email);

        if(!isset($this->email) || !isset($this->password) || empty($this->email) || empty($this->password)){
         throw new AuthCredentialsNotSetException('Email and  password is required for authentication');
        }

        if(!$isValidEmail){
            return $isValidEmail->throw('Email format is not supported');
        }

        return $this;
    }

    public function isUser(){

    }


    public function setRequest($request){
        $this->request = $request;
    }



}