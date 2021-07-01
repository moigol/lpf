<?php
class CapabilitiesController extends Controller  
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
        // Capabilities page ./admin/capabilities/
        Auth::userIs("Administrator");
        
        // Load models 
        $this->load->model('capabilitygroups');

        // Get all capabilities
        $capabilities = Capabilities::all();   
        
        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);

        // Load the view page
        View::page('capabilities/list', get_defined_vars());
    }

    public function add()
    {
        // Capabilities add page ./admin/capabilities/add/
        Auth::userIs("Administrator");

        if( App::$post ) {  

            // Parse post data
            $capa = isset(App::$post['capa']) ? App::$post['capa'] : array();

            $errorText = '';
            $hasError  = false;

            // Check name
            if( $capa['Name'] == "" ) {
                $errorText .= '* Role name is required! please enter the capability name.<br>';
                $hasError = true;
            }

            // Check name
            if( $capa['CapabilityGroupID'] == "" ) {
                $errorText .= '* Role link is required! please select the capability group.<br>';
                $hasError = true;
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {                

                // Add capability information
                $CapaID = Capabilities::add( $capa );

                if($CapaID) {
                    // Output a message
                    App::setSession( 'message', "New capability has been added with ID: ".$CapaID.'.' );
                }

                // Redrect page to
                View::redirect('admin/capabilities/');
            }
        }

        // Load models 
        $this->load->model('capabilitygroups');
        
        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);

        // Load the view page
        View::page('capabilities/add', get_defined_vars());        
    }

    public function update()
    {
        // Capabilities add page ./admin/capabilities/add/
        Auth::userIs("Administrator");

        // Get segment 3 to get passed user ID
        $CapaID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to capabilities list when false
        if(!$CapaID) { 
            View::redirect('admin/capabilities/');
        }

        if( App::$post ) {  

            // Parse post data
            $capa = isset(App::$post['capa']) ? App::$post['capa'] : array();

            $errorText = '';
            $hasError  = false;

            // Check name
            if( $capa['Name'] == "" ) {
                $errorText .= '* Role name is required! please enter the capability name.<br>';
                $hasError = true;
            }

            // Check name
            if( $capa['CapabilityGroupID'] == "" ) {
                $errorText .= '* Role link is required! please select the capability group.<br>';
                $hasError = true;
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {                

                // Add capability information
                Capabilities::update( $capa, $CapaID );
                App::setSession( 'message', "Capability has been updated!" );
            }
        }

        // Load models 
        $this->load->model('capabilitygroups');
        
        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);

        // Get all capabilities from user_levels table
        $c = Capabilities::one($CapaID);        
        $updateSession = [
            'CapabilityID' => $CapaID
        ];        

        App::setSession('updateSession'.$CapaID, $updateSession);

        // Load the view page
        View::page('capabilities/update', get_defined_vars());
    }

    public function delete()
    {
        // Capabilities add page ./admin/capabilities/delete/
        Auth::userIs("Administrator");

        // Get segment 3 to get passed user ID
        $CapaID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to capabilities list when false
        if(!$CapaID) { 
            View::redirect('admin/capabilities/');
        }
        
        // Delete
        Capabilities::delete( $CapaID );

        // Output message
        App::setSession( 'message', "Capability has been removed!" );

        // Redirect to capabilities list
        View::redirect('admin/capabilities/');
    }
}