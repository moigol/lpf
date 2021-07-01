<?php
class AccountController extends Controller  
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('auth');
        $this->load->helper('asset');
        $this->load->helper('discord');
        $this->load->helper('appt');
        
        $this->load->model('admin');
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
        View::page('account/dashboard', get_defined_vars());
    }
}