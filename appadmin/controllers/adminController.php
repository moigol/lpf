<?php
class AdminController extends Controller  
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('auth');
        $this->load->helper('asset');
        $this->load->helper('appt');
    }   

    function index() 
    {
        Auth::userSession(); // Continue if user has session
        $controller = isset($this->segment[1]) ? $this->segment[1] : false;
        $method     = isset($this->segment[2]) ? $this->segment[2] : 'index';
        $issub   = $this->loadController( $controller, $method, get_defined_vars() );

        if ($issub === false) {
            $this->dashboard();
        }        
    }

    public function dashboard()
    {
        Auth::userSession();
        //load('model', 'users');
        view('dashboard', get_defined_vars());
    }

    public function logout()
    {
        Auth::clearSession();
        Auth::removeUserSession();
        View::redirect();
    }

    public function config()
    {
        if (isset(App::$post['action']) && App::$post['action'] == 'doUpdateConfiguration') {
            if (isset(App::$post['mpf'])) {
                Config::update(App::$post['mpf']);
            }
            App::setSession('message', "Config file has been updated!");
            View::redirect('admin/config/');
        }

        View::page('config', get_defined_vars());
    }
}