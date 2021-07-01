<?php 
View::$bodydata         = App::getFilePathBodyClass(__FILE__);
View::$title            = 'Account Dashboard - Light PHP Framework';
View::$bodyclass        = 'account-dashboard';;
?>

<?php View::header(); ?>
    <h1><?php echo View::$title; ?></h1>
    <p>This is your default account page, you can find it in the path below:</p>
    <p>Front Page: <code>app/views/pages/account/dashboard.php</code></p>
    <p>Header template: <code>app/views/pages/header.php</code></p>
    <p>Footer template: <code>app/views/pages/footer.php</code></p>	   
    <a href="<?php echo View::url('logout/'); ?>">Logout</a>
<?php View::footer(); ?>