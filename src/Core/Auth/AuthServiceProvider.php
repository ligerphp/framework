<?php
namespace Core\Auth;

use Core\Http\Request;
use Core\Database\Model\Model;
use Core\Session\Session;

class AuthServiceProvider {
    /**
     * User token
     */
    private $token;

    /**
     * Current Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request->createFromGlobals();
    }

    public function attempt(){
           $session = new Session();
        if($session->exists('email') && $session->exists('loggedin')){
            $session->addMsg('success','Welcome back');
            dd($session);
         return true;
        }else{
            $email =  $this->request->get('email');
            $_password =  $this->request->get('password');
            $model = new Model();
            $user = $model->query("SELECT * FROM users WHERE email = ? ",[$email])->first();
                // dd($user->password);            
            if($user->password && password_verify($_password,$user->password)){
                $session->set('email',$email);
                $session->set('loggedin',true);
                $session->set('userToken',$user);
                $session->addMsg('warning','Authentication Successful');
                return true;
            }
            $session->set('loggedin',false);
            $session->addMsg('failed','Incorrect Username or password');
            return false;  
    
        }
    }
        
    public function user(){
        $session =  new Session('web');
        if (! is_null($this->user)) {
            return $this->user;
        }
        if($session->exists('userToken')){
            //user exists
        }
    }

    public function login(){

    }
}