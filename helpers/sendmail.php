<?php
/**
 * PHP 7++
 *
 * LightPHPFrame
 * Copyright (c) Mo Ses
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package       email helper
 * @version       LightPHPFrame v1.1.10
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

App::load()->vendor('autoload');

class SendMail
{
    static $logo;
    static $banner1;
    static $banner2;
    static $login;
    static $options;

    public static function init()
    {
        self::$logo = View::asset('images/se-menu-logo.png', false);
        self::$banner1 = View::asset('images/social-enhancer-banner.png', false);
        self::$banner2 = View::asset('images/social-enhancer-banner2.png', false);
        self::$login   = View::url('login/', false);

        self::$options = Options::get();
    }

    /**
     * Loop clients
     *
     * @access public | static
     * @param (array) $users : (required) list of users
     * @return na
     */
    public static function send($to = '', $name = '', $subject = '', $content = '', $attachment = false, $cc = false, $bcc = false, $from = false, $fromname = false)
    {
        try {
            //PHPMailer Object
            $mail = new PHPMailer(true);

            $frm = ($from != false) ? $from : self::$options['email_from'];
            $frn = ($fromname != false) ? $fromname : self::$options['email_from_name'];

            $mail->isSMTP(); // tell to use smtp
            //$mail->SMTPDebug  = 2;
            $mail->CharSet    = "utf-8"; // set charset to utf8
            $mail->SMTPAuth   = true;  // use smpt auth
            $mail->SMTPSecure = Config::get('MAIL_ENCRYPTION');
            $mail->Host       = Config::get('MAIL_HOST');
            $mail->Port       = Config::get('MAIL_PORT');
            $mail->Username   = Config::get('MAIL_USERNAME');
            $mail->Password   = Config::get('MAIL_PASSWORD');
            $mail->setFrom($frm, $frn);
            $mail->addAddress($to, $name);

            //$mail->addCC(self::$options['email_cc']);
            if($cc) {
                 foreach($cc as $ccc) {
                     $mail->addCC($ccc['email'],$ccc['name']);
                 }
            }
            
            if (self::$options['email_bcc']) {
                $mail->addBCC(self::$options['email_bcc']);
            }

            if ($bcc) {
                foreach ($bcc as $bccc) {
                    $mail->addBCC($bccc['email'], $bccc['name']);
                }
            }

            $mail->Subject = $subject;
            $mail->MsgHTML($content);


            if ($attachment) {
                foreach ($attachment as $att) {
                    $mail->AddAttachment($att['path'], $att['name']);
                }
            }

            //if(Config::get('ENVIRONMENT') === 'production') {
            if (!$mail->send()) {
                return false;
            } else {
                return true;
            }
            //}
        } catch (phpmailerException $e) {
        }
    }

    public static function bugReport($info)
    {
        // Start Email sending
        $subject = "New Bug Report from User: ".$info['Subject'];

        $shortcode = array(
            '[Title]',
            '[Logo]',
            '[Banner1]',
            '[Banner2]',
            '[ClientName]',
            '[ClientEmail]',
            '[Subject]',
            '[Details]',
            '[Content]',
            '[Images]',
            '[LoginLink]',
        );

        $scvalues = array(
            $subject,
            self::$logo,
            self::$banner1,
            self::$banner2,
            User::info('FullName'),
            User::info('Email'),
            $info['Subject'],
            $info['Description'],
            $info['Title'],
            $info['Attachments'],
            self::$login
        );

        $to  = self::$options['email_to'];
        $frm = self::$options['email_from'];
        $frn = self::$options['email_from_name'];
        $bcc = self::$options['email_bcc'];

        $emailFormat = 'bugreport.html';
        $content     = str_replace($shortcode, $scvalues, App::getFileContents($emailFormat));

        self::send($to, $frm, $subject, $content);
    }

    public static function welcomeEmail($userInfo)
    {
        // Start Email sending
        $subject = "Welcome to Social Enhancer!";

        $shortcode = array(
            '[Title]',
            '[Logo]',
            '[Banner1]',
            '[Banner2]',
            '[FirstName]',
            '[LastName]',
            '[Email]',
            '[Password]',
            '[LoginLink]',
        );

        $scvalues = array(
            $subject,
            self::$logo,
            self::$banner1,
            self::$banner2,
            $userInfo->FirstName,
            $userInfo->LastName,
            $userInfo->Email,
            App::decryptHash($userInfo->HashKey),
            self::$login
        );

        $to  = self::$options['email_to'];
        $frm = self::$options['email_from'];
        $frn = self::$options['email_from_name'];
        $bcc = self::$options['email_bcc'];

        $emailFormat = 'welcome.html';
        $content     = str_replace($shortcode, $scvalues, App::getFileContents($emailFormat));

        self::send($to, $frm, $subject, $content);
    }

    /* 

    public static function sendLostPasswordEmail($uinfo)
    {
        $userInfo  = User::info(false, $uinfo->UserID);
        $subject   = "Request Password Reset";
        $shortcode = array(
            '[Title]',
            '[Logo]',
            '[Banner1]',
            '[Banner2]',
            '[FirstName]',
            '[LastName]',
            '[ResetLink]'
        );

        $scvalues = array(
            $subject,
            self::$logo,
            self::$banner1,
            self::$banner2,
            $userInfo->FirstName,
            $userInfo->LastName,
            View::url('resetpassword/' . $userInfo->ResetKey, false)
        );

        $to          = $userInfo->Email;
        $name        = $userInfo->FirstName . ' ' . $userInfo->LastName;
        $subject    .= " to Bee All Design!";
        $emailFormat = 'requestresetpw.html';
        $content     = str_replace($shortcode, $scvalues, App::getFileContents('emails' . DS . $emailFormat));

        self::sendSMTP($to, $name, $subject, $content);
    }

    public static function sendContactMessageEmail($msg)
    {
        $subject   = "New Contact Message from Client";
        $shortcode = array(
            '[Title]',
            '[Logo]',
            '[Banner1]',
            '[Banner2]',
            '[FirstName]',
            '[LastName]',
            '[Email]',
            '[LoginLink]',
            '[MessageSubject]',
            '[MessageContent]'
        );

        $scvalues = array(
            $subject,
            self::$logo,
            self::$banner1,
            self::$banner2,
            $msg['FirstName'],
            $msg['LastName'],
            $msg['Email'],
            self::$login,
            $msg['Subject'],
            $msg['Message']
        );

        $to          = 'moises.goloyugo@gmail.com'; //self::$options['email_to'];
        $name        = "Admin";
        $emailFormat = 'contactus.html';
        $content     = str_replace($shortcode, $scvalues, App::getFileContents($emailFormat));

        self::sendSMTP($to, $name, $subject, $content);
    } */ 
}

SendMail::init();