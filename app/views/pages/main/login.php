<?php 
View::$bodydata         = App::getFilePathBodyClass(__FILE__);
View::$title            = 'Login';
View::$metadescription  = '';
View::$bodyclass        = 'login-page';
View::$robots           = 'index,follow';
View::$ogtype           = 'website';
View::$ogtitle          = 'Login';
View::$ogdescription    = '';
View::$ogimage          = View::asset('images/og.jpg', false);
View::$ogimagesecure    = View::asset('images/og.jpg', false);
View::$ogimagewidth     = '1200';
View::$ogimageheight    = '1200';
View::$ogsitename       = 'Site';
View::$ogurl            = View::url('login/',false);
?>

<?php View::header(); ?>

    <form method="post" action="<?php View::url('login/'); ?>">
        <input type="hidden" name="action" value="login" />
        <input type="hidden" id="remember" name="keepmeloggedin" value="">

        <?php echo View::getMessage(); ?>
            <h5>Login to your account <small>Enter your credentials below</small></h5>

            <div>
                <input type="text" autocomplete="off" class="form-control" placeholder="Email" name="usr" required>                
            </div>

            <div>
                <input type="password" autocomplete="off" class="form-control" placeholder="Password" name="pwd" required>
            </div>

            <div>
                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
            </div>

            <div>
                <a href="<?php View::url('forgotpassword/'); ?>">Forgot password?</a>
            </div>
        </div>
    </form>

<?php View::footer(); ?>