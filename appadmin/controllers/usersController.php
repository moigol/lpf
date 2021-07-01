<?php
class UsersController extends Controller  
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
        // Users page ./admin/users/
        Auth::userIs("Administrator");
        
        $this->load->model('users');
        $this->load->model('avatars');
        $this->load->model('roles');

        // Get all roles
        $roles = Roles::all();

        // Get all users
        $users = Users::getAll();        

        // Load the view page
        View::page('users/list', get_defined_vars());
    }

    public function add()
    {
        // Users add page ./admin/users/add/
        Auth::userIs("Administrator");

        // Load models {*** Can be loaded in __construct but lets just load the needed model per page}
        $this->load->model('users');
        $this->load->model('usermeta');
        $this->load->model('roles');
        $this->load->model('capabilities');
        $this->load->model('capabilitygroups');

        if( App::$post ) {  

            // Parse post data
            $user = isset(App::$post['user']) ? App::$post['user'] : array();
            $meta = isset(App::$post['meta']) ? App::$post['meta'] : array();
            $capa = isset(App::$post['capa']) ? App::$post['capa'] : array();

            $errorText = '';
            $hasError  = false;

            // Check valid emails
            if( isset($user['Email']) && !filter_var( $user['Email'], FILTER_VALIDATE_EMAIL ) ) {
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
            if( User::infoByEmail( 'UserID',  $user['Email'] ) ) {
                $errorText .= '* The email you entered already existing.<br>';
                $hasError = true;
                $errorFields[] = 'Email';
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {                

                // Encrypt password
                $user['Password'] = App::encrypt( App::$post['Password'] );

                // Add hash key
                $user['HashKey']  = App::encryptHash( App::$post['Password'] );

                // Sanitize capabilities array
                $user['Capability'] = App::jsonEncode($capa);

                // Add user login information
                $UserID = Users::add( $user );

                if($UserID) {
                    // Add user meta data
                    $meta['UserID'] = $UserID;

                    if(App::$file && App::$file['Avatar']['name']) {
                        $meta['Avatar'] = Media::upload( App::$file, $UserID, 'Avatar', true );
                    }

                    UserMeta::add( $meta );
                }

                // Output a message
                App::setSession( 'message', "New user has been added with ID: ".$UserID.'.' );

                // Redrect page to
                View::redirect('admin/users/');
            }
        }
        
        // Get all capabilities
        $capabilities = Capabilities::all(['group' => 'CapabilityGroupID']);

        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);

        // Get all roles from user_levels table
        $roles = Roles::all();

        // Load the view page
        View::page('users/add', get_defined_vars());        
    }

    public function update()
    {
        // Users add page ./admin/users/add/
        Auth::userIs("Administrator");

        // Get segment 3 to get passed user ID
        $UserID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to users list when false
        if(!$UserID) { 
            View::redirect('admin/users/');
        }

        // Load models {*** Can be loaded in __construct but lets just load the needed model per page}
        $this->load->model('users');
        $this->load->model('usermeta');
        $this->load->model('roles');
        $this->load->model('capabilities');
        $this->load->model('capabilitygroups');

        if( App::$post ) {  

            // Parse post data
            $user = isset(App::$post['user']) ? App::$post['user'] : array();
            $meta = isset(App::$post['meta']) ? App::$post['meta'] : array();
            $capa = isset(App::$post['capa']) ? App::$post['capa'] : array();

            $errorText = '';
            $hasError  = false;

            // Check valid emails
            if( isset($user['Email']) && !filter_var( $user['Email'], FILTER_VALIDATE_EMAIL ) ) {
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
            if( User::infoByEmail( 'UserID',  $user['Email'] ) != $UserID && User::infoByEmail( 'UserID',  $user['Email'] ) ) {
                $errorText .= '* The email you entered already existing.<br>';
                $hasError = true;
                $errorFields[] = 'Email';
            }

            if( $hasError ) {
                App::setSession( 'error', $errorText );
            } else {         
                
                
                // Get session ID's
                $updateSession = App::getSession('updateSession'.$UserID);

                // Check if password is set
                if( strlen(App::$post['Password']) > 0  ) {
                    // Encrypt password
                    $user['Password'] = App::encrypt( App::$post['Password'] );

                    // Add hash key
                    $user['HashKey']  = App::encryptHash( App::$post['Password'] );
                }

                if(App::$file && App::$file['Avatar']['name']) {
                    $meta['Avatar'] = Media::upload( App::$file, $UserID, 'Avatar', true );
                }

                // Sanitize capabilities array
                $user['Capability'] = (string) App::jsonEncode($capa);

                // Update user security information 
                Users::update( $user, $UserID );

                // Update user  meta data
                UserMeta::update( $meta, $updateSession['UserMetaID'] );

                if($UserID == User::info('UserID')) {
                    Auth::updateUserSession();
                }

                // Output a message
                App::setSession( 'message', "User has been updated!" );
            }
        }

        // Get all capabilities
        $capabilities = Capabilities::all(['group' => 'CapabilityGroupID']);

        // Get all capabilitie groups as array
        $capagroup = CapabilityGroups::all(['index' => 'CapabilityGroupID']);
        
        // Get all roles from user_levels table
        $roles  = Roles::all();
        $u      = Users::getOne($UserID);
        $avatar = Media::getOne($u->Avatar);
        $capas  = App::jsonDecode($u->Capability);

        $updateSession = [
            'UserID'         => $UserID,
            'UserMetaID'     => $u->UserMetaID
        ];        

        App::setSession('updateSession'.$UserID, $updateSession);

        // Load the view page
        View::page('users/update', get_defined_vars());
    }

    public function delete()
    {
        // Users add page ./admin/users/delete/
        Auth::userIs("Administrator");

        // Get segment 3 to get passed user ID
        $UserID = isset(App::$segment[3]) ? App::$segment[3] : false;

        // Redirect to users list when false
        if(!$UserID) { 
            View::redirect('admin/users/');
        }     
        
        // Update date deleted field
        Users::update(
            ['DateDeleted' => date("Y-m-d H:i:s")],
            $UserID
        );

        // Output message
        App::setSession( 'message', "User has been removed!" );

        // Redirect to users list
        View::redirect('admin/users/');
    }
}