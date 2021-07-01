<?php 
View::$bodydata         = App::getFilePathBodyClass(__FILE__);
View::$title            = 'Light PHP Framework';
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
    <h1><?php echo View::$title; ?></h1>
    <p>This is your default page, you can find it in the path below:</p>
    <p>Front Page: <code>app/views/pages/front.php</code></p>
    <p>Header template: <code>app/views/pages/header.php</code></p>
    <p>Footer template: <code>app/views/pages/footer.php</code></p>	   
    <a href="<?php echo View::url('login/'); ?>">Login</a>
<?php View::footer(); ?>