<?php
namespace Core\Routing;

use App\Models\Users;
use Core\Foundation\Api\Framework;
use Core\Http\Request;
use Core\Session\Session;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Router
{

    public static function route($url, $app)
    {


            $request = Request::createFromGlobals();

            $context = new RequestContext();
            $context->fromRequest($request);

            $matcher = new UrlMatcher($app->routes, $context);

            $controllerResolver = new HttpKernel\Controller\ControllerResolver();
            $argumentResolver = new HttpKernel\Controller\ArgumentResolver();

            $framework = new Framework($matcher, $controllerResolver, $argumentResolver);
            $framework = new HttpKernel\HttpCache\HttpCache(
                $framework,
                new HttpKernel\HttpCache\Store(PROOT . 'public' . DS . 'cache')
            );
            $response = $framework->handle($request);
            
            $response->send();

    }

    public static function redirect($location)
    {
        if (!headers_sent()) {
            header('Location: ' . PROOT . $location);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . PROOT . $location . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
            echo '</noscript>';exit;
        }
    }

    public static function hasAccess($controller_name, $action_name = 'index')
    {
        $acl_file = file_get_contents(ROOT . DS . 'app' . DS . 'acl.json');
        $acl = json_decode($acl_file, true);
        $current_user_acls = ["Guest"];
        $grantAccess = false;

        if (Session::exists(CURRENT_USER_SESSION_NAME)) {
            $current_user_acls[] = "LoggedIn";
            foreach (Users::currentUser()->acls() as $a) {
                $current_user_acls[] = $a;
            }
        }

        foreach ($current_user_acls as $level) {
            if (array_key_exists($level, $acl) && array_key_exists($controller_name, $acl[$level])) {
                if (in_array($action_name, $acl[$level][$controller_name]) || in_array("*", $acl[$level][$controller_name])) {
                    $grantAccess = true;
                    break;
                }
            }
        }

        //check for denied
        foreach ($current_user_acls as $level) {
            $denied = $acl[$level]['denied'];
            if (!empty($denied) && array_key_exists($controller_name, $denied) && in_array($action_name, $denied[$controller_name])) {
                $grantAccess = false;
                break;
            }
        }
        return $grantAccess;
    }

    public static function getMenu($menu)
    {
        $menuAry = [];
        $menuFile = file_get_contents(ROOT . DS . 'app' . DS . $menu . '.json');
        $acl = json_decode($menuFile, true);
        foreach ($acl as $key => $val) {
            if (is_array($val)) {
                $sub = [];
                foreach ($val as $k => $v) {
                    if (substr($k, 0, 9) == 'separator' && !empty($sub)) {
                        $sub[$k] = '';
                        continue;
                    } else if ($finalVal = self::get_link($v)) {
                        $sub[$k] = $finalVal;
                    }
                }
                if (!empty($sub)) {
                    $menuAry[$key] = $sub;
                }
            } else {
                if ($finalVal = self::get_link($val)) {
                    $menuAry[$key] = $finalVal;
                }
            }
        }
        return $menuAry;
    }

    public static function get_link($val)
    {
        //check if external link
        if (preg_match('/https?:\/\//', $val) == 1) {
            return $val;
        } else {
            $uAry = explode('/', $val);
            $controller_name = ucwords($uAry[0]);
            $action_name = (isset($uAry[1])) ? $uAry[1] : '';
            if (self::hasAccess($controller_name, $action_name)) {
                return PROOT . $val;
            }
            return false;
        }
    }

}
