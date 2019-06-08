<?php
namespace Core\Auth;

use Core\Database\Ligerbase\Model\Model;
use Core\Http\Request;
use Core\Session\Session;

class AuthServiceProvider
{
    /**
     * User token
     */
    private $token;

    /**
     * Current Request
     */
    private $request;
    /**
     * Authentication configuratons
     */
    private $authConfig;

    public function __construct(Request $request)
    {
        $this->request = $request->createFromGlobals();
        $this->session = new Session('web');
        $this->model =  new Model();


    }

    public function check_csrf()
    {
        //check csrf_token;
        $csrf_status = $_SESSION['csrf_token'] == $this->request->get('csrf_token') ? true : false;
        if (!$csrf_status) {
            $this->session->addMsg('warning', 'Invalid csrf_token,do not manipulate.');
            return false;
        }
        return true;
    }

    /**
     * Create a user based on request data sent
     *
     */
    public function fresh()
    {
        $payload = $this->request->toArray();
        $prepared_keys = '';
        $prepared_bindings = array();
        
        foreach ($payload as $key => $value) {
            if ($value === '') {
                $this->session->addMsg('warning', ucfirst($key) . " cannot be empty");
                return false;
            }

            trim($value);
            htmlentities($value);
            strip_tags($value);
            \htmlspecialchars($value);
            
            $user = $this->model->query("SELECT email FROM users WHERE email = ? ", [$value])->first();
            if(!empty($user)){
                $this->session->addMsg('warning', 'Email already exists');
                    return false;
            }
                
            if($key == 'password'){
                    if($payload['password'] === $payload['confirm']){
                    $value = \password_hash($key,PASSWORD_BCRYPT);
                    }else{
                        
                    $this->session->addMsg('warning', 'Passwords do not match');
                    return false;
                    }
                }

                if (lcfirst($key) == 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->session->addMsg('warning', ucfirst($key) . " should be a valid email address");
                        return false;
                    }
                }


                if($key !== 'csrf_token' && $key !== 'confirm'){
                         $prepared_keys .= "$key=?,";
                        $prepared_bindings[] = $value;
                }
                if($Key === 'website_url' && $url = explode('_url',$value)){
                    dd($url);
                }
               
        }


       $fixed_prepared_keys =  rtrim($prepared_keys,',');
        // get user table from config
        if(file_exists(ROOT . DS . 'config' . DS . 'auth.php')){
            $this->authConfig =  include ROOT .DS . 'config' . DS . 'auth.php';
           };
           //validate urls
           //valiate confirm passwords
           //validate password strength
           //validate first name
           
           $table = $this->authConfig['providers']['users']['table'];
           try {
        $user =  $this->model->query("INSERT INTO $table SET $fixed_prepared_keys",$prepared_bindings);
            return true;
           } catch (\Throwable $th) {
               throw $th;
           }
           
            $this->session->set('user_token', $user->user_id);
            $this->session->addMsg('success', 'Authentication Successful');
           return true;


    }
    /**
     *
     * Attempt to authenticate a user
     * @return boolean
     */
    public function attempt()
    {

        $csrf_status = $this->check_csrf() ? true : false;

        if (!$csrf_status) {
            return false;
        }

        //check input field
        $payload = $this->request->toArray();
        foreach ($payload as $key => $value) {
            if ($value === '') {
                $this->session->addMsg('warning', ucfirst($key) . " cannot be empty");
                return false;
            } else if (lcfirst($key) === 'email') {
                if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {

                    $this->session->addMsg('warning', ucfirst($key) . " should be a valid email address");
                    return false;
                }
            }
        }
        // dd($this->session->exists('user_token'));
        if ($this->session->exists('user_token')) {

            $this->session->addMsg('success', 'Already Logged in,Welcome back '.$_SESSION['fname']);
            return true;

        } else {

            $email = $this->request->get('email');
            $_password = $this->request->get('password');
            $user = $this->model->query("SELECT * FROM users WHERE email = ? ", [$email])->first();
            if ($user->password && password_verify($_password, $user->password)) {
                $this->session->set('user_token', $user->id);
                $this->session->set('fname', $user->fname);
                $this->session->addMsg('success', 'Authentication Successful');
                return true;
            } else {

                $this->session->addMsg('warning', 'Incorrect Username or password');
                return false;

            }

        }
    }

    /**
     * Check if user session exists
     */
    public function session()
    {
        if ($this->session->exists('user_token')) {
            return true;
        }
        return false;
    }

    public function check()
    {
        return session();
    }
}
