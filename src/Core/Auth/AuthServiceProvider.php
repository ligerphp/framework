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

    /**
     * Attempt to authenticate a user
     * @return boolean
     */
    public function attempt(){
           $session = new Session();
        if($session->exists('email') && $session->exists('loggedin')){
            $session->addMsg('success','Welcome back');
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
                $session->addMsg('success','Authentication Successful');
                return true;
            }else{ 
            $session->addMsg('warning','Incorrect Username or password');
            return false;  
            }

    
        }
    }
        
    /**
     * Check if user session exists
     */
    public function session(){
        $session =  new Session('web');
        if($session->exists('userToken')){
            return true;
        }
        return false;
    }

    public function login(){

    }
}