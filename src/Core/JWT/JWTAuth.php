<?php
namespace Core\JWT;

use Core\Http\Request;
use Core\JWT\JWT;
use Core\Database\Ligerbase\Model\Model;
use Core\JWT\Exceptions\AuthCredentialsNotSetException;
use Core\Validators\EmailValidator;
use Core\JWT\Http\Parser\Parser;
use Core\JWT\Http\Parser\Headers as headerParser;

class JWTAuth extends JWT{

    /**
     * Message set
     */
    public $msg;
    /**
     * New Instance of JWTAuth 
     * @param \Core\Http\Request required
     * @return void
     */
    public function __construct(Request $request)
    {
     $this->request = $request;   
     $this->model =  new Model();
     $this->parser =  new Parser($request,[new headerParser]);
     parent::__construct($this->parser);
     $this->auth = app('auth');
    }

    /**
     * Attempt to authenticate the user and return the token.
     *
     * @param  array  $credentials
     *
     * @return false|string
     */
    public function attempt(array $credentials)
    {
        if (! $this->auth->byCredentials($credentials)) {
            return false;
        }

        return $this->fromUser($this->user());
    }

    
    /**
     * Authenticate a user via a token.
     *
     * @return \Tymon\JWTAuth\Contracts\JWTSubject|false
     */
    public function authenticate()
    {
        $id = $this->getPayload()->get('sub');

        if (! $this->auth->byId($id)) {
            return false;
        }

        return $this->user();
    }

    /**
     * Alias for authenticate().
     *
     * @return \Corre\JWT\Contracts\JWTSubject|false
     */
    public function toUser()
    {
        return $this->authenticate();
    }

    /**
     * Get the authenticated user.
     *
     * @return \Core\JWTAuth\Contracts\JWTSubject
     */
    public function user()
    {
        return $this->auth->user();
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
        if(!$user){
            $this->msg = 'No user found with email';
        }
        if (isset($user->password) && password_verify($this->password, $user->password)) {
            
           return $this->makeWithCustomClaims(['id' => $user->id,'email' => $user->email,'fname' => $user->fname]);
        } else {
            $this->msg = 'Email and Password do not match';
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