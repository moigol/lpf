<?php
class RolesController extends Controller  
{
    function __construct()
    {
        parent::__construct();

        // Load helpers
        $this->load->helper('auth');
        $this->load->helper('asset');
    }   

    function index()
    {
        // Roles page ./admin/roles/
        Auth::userIs("Administrator");

        // Get all roles
        $roles = Roles::all();        

        // Load the view page
        View::page('roles/list', get_defined_vars());
    }

    public function add()
    {
        // Roles add page ./admin/roles/add/
        Auth::userIs("Administrator");

        if( App::$post ) {  

            // Parse post data
            $role = isset(App::$post['role']) ? App::$post['role'] : array();

            $errorText = '';
            $hasError  = false;

            // Check code
            if( $role['Code'] == "" ) {
                $errorText .= '* Role code is required! please enter the role code.<br>';
                $hasError = true;
            }

            // Check name
            if( $role['Name'] == "" ) {
                $errorText .= '* Role name is required! please enter the role name.<br>';
                $hasError = true;
            }

            // Check name
            if( $role['Link'] == "" ) {
                $errorText .= '* Role link is required! please enter the role link.<br>';
                $hasError = true;
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {                

                // Add role information
                $RoleID = Roles::add( $role );

                if($RoleID) {
                    // Output a message
                    App::setSession( 'message', "New role has been added with ID: ".$RoleID.'.' );
                }

                // Redrect page to
                View::redirect('admin/roles/');
            }
        }

        // Load the view page
        View::page('roles/add', get_defined_vars());        
    }

    public function update()
    {
        // Roles add page ./admin/roles/add/
        Auth::userIs("Administrator");

        // Get segment 3 to get passed user ID
        $RoleID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to roles list when false
        if(!$RoleID) { 
            View::redirect('admin/roles/');
        }

        if( App::$post ) {  

            // Parse post data
            $role = isset(App::$post['role']) ? App::$post['role'] : array();

            $errorText = '';
            $hasError  = false;

            // Check code
            if( $role['Code'] == "" ) {
                $errorText .= '* Role code is required! please enter the role code.<br>';
                $hasError = true;
            }

            // Check name
            if( $role['Name'] == "" ) {
                $errorText .= '* Role name is required! please enter the role name.<br>';
                $hasError = true;
            }

            // Check name
            if( $role['Link'] == "" ) {
                $errorText .= '* Role link is required! please enter the role link.<br>';
                $hasError = true;
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {                

                // Update role information
                Roles::update( $role, $RoleID );
                App::setSession( 'message', "Role has been updated!" );
            }
        }

        // Get all roles from user_levels table
        $r = Roles::one($RoleID);        
        $updateSession = [
            'RoleID' => $RoleID
        ];        

        App::setSession('updateSession'.$RoleID, $updateSession);

        // Load the view page
        View::page('roles/update', get_defined_vars());
    }

    public function delete()
    {
        // Roles add page ./admin/roles/delete/
        Auth::userIs("Administrator");

        // Get segment 3 to get passed user ID
        $RoleID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to roles list when false
        if(!$RoleID) { 
            View::redirect('admin/roles/');
        }
        
        // Delete
        Roles::delete( $RoleID );

        // Output message
        App::setSession( 'message', "Role has been removed!" );

        // Redirect to roles list
        View::redirect('admin/roles/');
    }
}