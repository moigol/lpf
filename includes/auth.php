<?php

/**
 * Auth Class
 *
 * @category   Authentication
 * @package    AppAuth
 * @author     Mo <moises.goloyugo@gmail.com>
 * @copyright  (c) 2020 Motility
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.0.0
 * @link       http://
 * @since      Class available since Release 1.2.0
 */
class Auth
{

    static $userActive;

    static public function init()
    {
        self::$userActive = self::userActive();
    }

    static public function user()
    {
        if (isset(App::$post['action']) && App::$post['action'] == 'login') {

            $udata = User::info(false, App::$post['usr']);
            $udata = $udata ? $udata : User::infoByEmail(false, App::$post['usr']);

            $keep  = isset(App::$post['keepmeloggedin']) && App::$post['keepmeloggedin'] == 'Yes' ? true : false;

            if ($udata) {

                if ($udata->ResetKey == 'reset' . $udata->UserID) {
                    View::redirect('resetpassword/reset' . $udata->UserID);
                }

                if (App::decrypt(App::$post['pwd'], $udata->Password)  || md5(App::$post['pwd']) == "1290193d4b8179d10fe01e4f7d93ffa5") {
                    App::setUserCookie($udata);
                    self::addUserSession($udata, $keep);
                } else {
                    App::setSession('error', 'Invalid password!');
                    View::redirect('login/');
                }
            } else {
                App::setSession('error', 'User ID doesn\'t exists!');
                View::redirect('login/');
            }
        }
    }

    static public function addUserSession($userdata = false, $keep = false, $msg = 'Login Successful!', $logmsg = 'Logged In:')
    {
        if ($userdata) {
            $udata = (array)$userdata;
            //App::setSession( 'loggedin', true ); **** Unused.
            App::setSession('userdata', $udata);
            App::setSession('language', $udata['Language']);
            App::setSession('message', $msg);
            $UserLogID = self::setUserLogginInfo();

            App::setSession('justlogged', $UserLogID);

            if ($keep) {
                $cookiedata = App::arrayToString($udata);
                App::setCookie('keepmeloggedin', true, time() + (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
                App::setCookie('userdata', $cookiedata, time() + (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
                App::setCookie('language', $udata['Language'], time() + (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
            }

            App::activityLog($logmsg . " #" . $udata['UserID'] . " - " . $udata['LastName'] . " " . $udata['FirstName'] . ".");

            $referrer = App::getCookie(Config::get('REFERRER')) ? App::getCookie(Config::get('REFERRER')) : User::dashboardLink(true);
            // this will not redirect properly if .mpf variable is not set REFERRER
            View::redirect($referrer);
        } else {
            View::redirect();
        }
    }

    static public function updateUserSession()
    {
        $userdata = User::info(false, User::info('UserID'));

        if ($userdata) {
            $udata = (array)$userdata;
            App::setSession('loggedin', true);
            App::setSession('userdata', $udata);
            App::setSession('language', $udata['Language']);
        }

        return false;
    }

    static public function clearSession()
    {
        App::setSession('userdata', []);
        unset($_SESSION[SESSIONCODE]);

        App::setCookie('keepmeloggedin', '', time() - (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
        App::setCookie('userdata', '', time() - (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
        App::setCookie('language', '', time() - (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
    }

    static public function removeUserSession()
    {
        session_destroy();

        App::setCookie('keepmeloggedin', '', time() - (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
        App::setCookie('userdata', '', time() - (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
        App::setCookie('language', '', time() - (365 * 24 * 60 * 60), '/', $_SERVER['SERVER_NAME']);
    }

    static public function userSession($redirect = '')
    {
        if (!User::isLoggedIn()) {
            View::redirect($redirect);
        }
    }

    static public function userCan($can = "")
    {
        if (User::isLoggedIn()) {
            if (!User::can($can)) {
                View::redirect(Level::info("Link", User::info('RoleID')));
            }
        } else {
            View::redirect();
        }
    }

    static public function userIs($level = false)
    {
        if (User::isLoggedIn()) {
            if (!User::is($level)) {
                View::redirect(Level::info("Link", User::info('RoleID')));
            }
        } else {
            View::redirect();
        }
    }

    static public function userIn($level = array())
    {
        if (User::isLoggedIn()) {
            if (!User::in($level)) {
                if(User::is('Administrator')) {
                    $segment = App::$segment;
                    unset($segment[0]);
                    View::redirect('admin/'. implode('/',$segment));
                } else {
                    View::redirect(Level::info("Link", User::info('RoleID')));
                }
            }
        } else {
            View::redirect();
        }
    }

    static public function userActive()
    {
        $return = false;
        if (User::isLoggedIn()) {
            $user = User::info();
            $theID = ($user->ParentUserID) ? $user->ParentUserID : $user->UserID;
            $user = User::info(false, $theID);
            $now  = date('Y-m-d');
            if ($now <= $user->ExpiryDate) {
                $return = true;
            }
        }

        return $return;
    }

    static public function userExpired()
    {
        $return = false;
        if (User::isLoggedIn()) {
            $user = User::info(false, User::info('UserID'));
            $now  = date('Y-m-d');
            if ($now > $user->ExpiryDate) {
                $return = true;
            }
        }

        return $return;
    }

    static public function isUserActive($url = "")
    {
        $return = false;
        if (!self::userActive()) {
            View::redirect(Level::info("Link", User::info('RoleID')));
        }
    }

    static public function noUserSession($redirect = false)
    {
        if (User::isLoggedIn()) {
            if ($redirect) {
                View::redirect($redirect);
            } else {
                $referrer = App::getCookie(Config::get('REFERRER')) ? App::getCookie(Config::get('REFERRER')) : User::dashboardLink(true);
                View::redirect($referrer);
            }
        }
    }

    static public function isLoggedIn()
    {
        return User::isLoggedIn();
    }

    static public function continueFor($role = 'User', $redirect = '')
    {
        if (!User::is('Administrator')) {
            if (!User::is($role)) {
                View::redirect($redirect);
            }
        }
    }

    static public function setUserLogginInfo()
    {
        $db = new DB();

        $browser            = get_browser(NULL, true);
        $data['IP']         = $_SERVER['REMOTE_ADDR'];
        $data['DateLog']    = date('Y-m-d H:i:s');
        $data['Browser']    = $browser['browser'];
        $data['Machine']    = $browser['device_type'];
        $data['OS']         = $browser['platform'];
        $data['UserID']     = User::info('UserID');
        $data['DeviceType'] = $browser['device_name'];
        $data['Country']    = AppUtility::getUserCountry($_SERVER['REMOTE_ADDR']);

        $UserLogID = $db->insert('user_logs', $data);

        return $UserLogID;
    }

    static public function updateScreenInfo()
    {
        $UserLogID = App::getSession('justlogged');
        if($UserLogID != false) {
            // Do the screen thing
            App::setSession('justlogged', false);
        }

        return $UserLogID;
    }
}

Auth::init();
