<?php 
View::$bodydata         = App::getFilePathBodyClass(__FILE__);
View::$title            = 'Admin Dashboard - Light PHP Framework';
View::$bodyclass        = 'admin-dashboard';
?>
<?php View::header(); ?>
    <h1><?php echo View::$title; ?></h1>
    <p>This is your default admin dashboard page, you can find it in the path below:</p>
    <p>Front Page: <code>appadmin/views/pages/front.php</code></p>
    <p>Header template: <code>appadmin/views/pages/header.php</code></p>
    <p>Footer template: <code>appadmin/views/pages/footer.php</code></p>	   
    <a href="<?php echo View::url('logout/'); ?>">Logout</a>
<?php View::footer(); ?>