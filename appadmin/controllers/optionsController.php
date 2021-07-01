<?php
class OptionsController extends Controller  
{
    function __construct()
    {
        parent::__construct();

        // Load helpers
        $this->load->helper('auth');
        $this->load->helper('asset');
        $this->load->helper('discord');
        $this->load->helper('appt');
        $this->load->helper('contentfiles');
    }   

    function index()
    {
        // Options page ./admin/options/
        Auth::optionIs("Administrator");
        
        $alldata = get_defined_vars(); // used as carrier for all data*/
        $this->load->model('options');
        $this->load->model('avatars');

        // Get all options
        $options = Options::getAll();        

        // Load the view page
        View::page('options/list', get_defined_vars());
    }

    public function add()
    {
        // Options add page ./admin/options/add/
        Auth::optionIs("Administrator");

        // Load models {*** Can be loaded in __construct but lets just load the needed model per page}
        $this->load->model('options');
        $this->load->model('optionmeta');
        $this->load->model('roles');
        $this->load->model('capabilities');
        $this->load->model('capabilitygroups');

        if( App::$post ) {  

            // Parse post data
            $option = isset(App::$post['option']) ? App::$post['option'] : array();
            $meta = isset(App::$post['meta']) ? App::$post['meta'] : array();
            $capa = isset(App::$post['capa']) ? App::$post['capa'] : array();

            $errorText = '';
            $hasError  = false;

            // Check valid emails
            if( isset($option['Email']) && !filter_var( $option['Email'], FILTER_VALIDATE_EMAIL ) ) {
                $errorText .= '* The email you have entered is invalid! Please enter a valid email address.<br>';
                $hasError = true;
                $errorFields[] = 'Email';
            }

            // Check valid password length
            if( strlen( App::$post['Password'] ) < 4 ) {
                $errorText .= '* Password should be minimum of 4 alphanumeric characters! please enter the password again.<br>';
                $hasError = true;
                $errorFields[] = 'Password';
            }

            // Check if email already exists
            if( Option::infoByEmail( 'OptionID',  $option['Email'] ) ) {
                $errorText .= '* The email you entered already existing.<br>';
                $hasError = true;
                $errorFields[] = 'Email';
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {                

                // Encrypt password
                $option['Password'] = App::encrypt( App::$post['Password'] );

                // Add hash key
                $option['HashKey']  = App::encryptHash( App::$post['Password'] );

                // Sanitize capabilities array
                $option['Capability'] = App::jsonEncode($capa);

                // Add option login information
                $OptionID = Options::add( $option );

                if($OptionID) {
                    // Add option meta data
                    $meta['OptionID'] = $OptionID;

                    if(App::$file && App::$file['Avatar']['name']) {
                        $meta['Avatar'] = Media::upload( App::$file, $OptionID, 'Avatar' );
                    }

                    OptionMeta::add( $meta );
                }

                // Output a message
                App::setSession( 'message', "New option has been added with ID: ".$OptionID.'.' );

                // Redrect page to
                View::redirect('admin/options/');
            }
        }
        
        // Get all capabilities
        $capabilities = Capabilities::all(['group' => 'CapabilityGroupID']);

        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);

        // Get all roles from option_levels table
        $roles = Roles::all();

        // Load the view page
        View::page('options/add', get_defined_vars());        
    }

    public function update()
    {
        // Options add page ./admin/options/add/
        Auth::optionIs("Administrator");

        // Get segment 3 to get passed option ID
        $OptionID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to options list when false
        if(!$OptionID) { 
            View::redirect('admin/options/');
        }

        // Load models {*** Can be loaded in __construct but lets just load the needed model per page}
        $this->load->model('options');
        $this->load->model('optionmeta');
        $this->load->model('roles');
        $this->load->model('capabilities');
        $this->load->model('capabilitygroups');

        if( App::$post ) {  

            // Parse post data
            $option = isset(App::$post['option']) ? App::$post['option'] : array();
            $meta = isset(App::$post['meta']) ? App::$post['meta'] : array();
            $capa = isset(App::$post['capa']) ? App::$post['capa'] : array();

            $errorText = '';
            $hasError  = false;

            // Check valid emails
            if( isset($option['Email']) && !filter_var( $option['Email'], FILTER_VALIDATE_EMAIL ) ) {
                $errorText .= '* The email you have entered is invalid! Please enter a valid email address.<br>';
                $hasError = true;
                $errorFields[] = 'Email';
            }

            // Check valid password length
            if( strlen( App::$post['Password'] ) > 0 && strlen( App::$post['Password'] ) < 4 ) {
                $errorText .= '* Password should be minimum of 4 alphanumeric characters! please enter the password again.<br>';
                $hasError = true;
                $errorFields[] = 'Password';
            }

            // Check if email already exists
            if( Option::infoByEmail( 'OptionID',  $option['Email'] ) != $OptionID && Option::infoByEmail( 'OptionID',  $option['Email'] ) ) {
                $errorText .= '* The email you entered already existing.<br>';
                $hasError = true;
                $errorFields[] = 'Email';
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {         
                
                
                // Get session ID's
                $updateSession = App::getSession('updateSession'.$OptionID);

                // Check if password is set
                if( strlen(App::$post['Password']) > 0  ) {
                    // Encrypt password
                    $option['Password'] = App::encrypt( App::$post['Password'] );

                    // Add hash key
                    $option['HashKey']  = App::encryptHash( App::$post['Password'] );
                }

                if(App::$file && App::$file['Avatar']['name']) {
                    $meta['Avatar'] = Media::upload( App::$file, $OptionID, 'Avatar' );
                }

                // Sanitize capabilities array
                $option['Capability'] = (string) App::jsonEncode($capa);

                // Update option security information 
                Options::update( $option, $OptionID );

                // Update option  meta data
                OptionMeta::update( $meta, $updateSession['OptionMetaID'] );

                if($OptionID == Option::info('OptionID')) {
                    Auth::updateOptionSession();
                }

                // Output a message
                App::setSession( 'message', "Option has been updated!" );
            }
        }

        // Get all capabilities
        $capabilities = Capabilities::all(['group' => 'CapabilityGroupID']);

        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);
        
        // Get all roles from option_levels table
        $roles  = Roles::all();
        $u      = Options::getOne($OptionID);
        $avatar = Media::getOne($u->Avatar);
        $capas  = App::jsonDecode($u->Capability);

        $updateSession = [
            'OptionID'         => $OptionID,
            'OptionMetaID'     => $u->OptionMetaID
        ];        

        App::setSession('updateSession'.$OptionID, $updateSession);

        // Load the view page
        View::page('options/update', get_defined_vars());
    }

    public function delete()
    {
        // Options add page ./admin/options/delete/
        Auth::optionIs("Administrator");

        // Get segment 3 to get passed option ID
        $OptionID = isset($this->segment[3]) ? $this->segment[3] : false;

        // Redirect to options list when false
        if(!$OptionID) { 
            View::redirect('admin/options/');
        }

        // Load models {*** Can be loaded in __construct but lets just load the needed model per page}
        $this->load->model('options');        
        
        // Update date deleted field
        Options::update(
            ['DateDeleted' => date("Y-m-d H:i:s")],
            $OptionID
        );

        // Output message
        App::setSession( 'message', "Option has been removed!" );

        // Redirect to options list
        View::redirect('admin/options/');
    }
}