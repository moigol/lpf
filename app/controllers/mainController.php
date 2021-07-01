<?php
class MainController extends Controller  
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('auth');
        $this->load->helper('asset');
        $this->load->helper('discord');
        $this->load->helper('stripeengine');
        $this->load->helper('remoteaddress');
        $this->load->helper('appt');
    }   

    function index() 
    {
        View::page('front', get_defined_vars());
    }

    public function login()
    {
        Auth::noUserSession();
        Auth::user();
        
        View::page('main/login', get_defined_vars());
    }

    public function logout()
    {
        Auth::clearSession();
        Auth::removeUserSession();
        View::redirect();
    }

    public function phpinfo()
    {
        phpinfo();
    }
}