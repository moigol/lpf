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
 * @package       Views router
 * @version       LightPHPFrame v1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class View
{
    public static $scripts = array();
    public static $styles = array();
    public static $footerscripts = array();
    public static $footerstyles = array();
    public static $iescripts = array();
    public static $iestyles = array();
    public static $title = '';
    public static $ptitle = '';
    public static $metatitle = '';
    public static $metadescription = '';
    public static $metakeywords = '';
    public static $segments = array();
    public static $bodyclass = '';
    public static $bodydata = '';
    public static $pagedata = '';
    public static $viewpageslug = 'pages';

    public static $robots = 'index,nofollow';
    public static $ogtype = 'website';
    public static $ogtitle = '';
    public static $ogdescription = '';
    public static $ogimage = '';
    public static $ogimagesecure = '';
    public static $ogimagewidth = '1200';
    public static $ogimageheight = '1200';
    public static $ogsitename = '';
    public static $ogurl = '';
    public static $segment = array();
    public static $theme = 'admin';
    public static $counter = 0;

    public static function url( $path = false, $echo = true )
    {
        $multiLanguage   = Level::info('MultiLanguage', User::info('RoleID'));

        $langURL = $path;
        if($multiLanguage == "Yes") {
            $currentlanguage = (App::getSession('language')) ? App::getSession('language') : 'en';
            $langURL          = $currentlanguage == 'en' ? $path : $currentlanguage.'/'.$path;
        }
        
        $outURL = Config::siteURL( $langURL );

        if($echo) {
            echo $outURL;
        } else {
            return $outURL; 
        }
    }

    public static function cleanURL( $path = false, $echo = false )
    {
        $outURL = Config::siteURL($path, false);

        if($echo) {
            echo $outURL;
        } else {
            return $outURL; 
        }
    }
    
    public static function image($thefile=false,$a=false,$c=false,$i=false,$s=false,$e=true)
    {
        // TODO: improve this method
        $file = ($thefile) ? $thefile : '';
        $fileUrl = self::cleanURL(IMAGES_URI.$file);
        $return = file_exists(ASSETS.$file) ? $fileUrl : false;

        if($return) {
            $alt   = ($a) ? 'alt="'.$a.'"' : '';
            $cls   = ($c) ? 'class="'.$c.'"' : '';
            $id    = ($i) ? 'id="'.$i.'"' : '';
            $style = ($s) ? 'style="'.$s.'"' : '';
            
            if($e) {
                echo '<img src="'.$return.'" '.$alt.' '.$cls.' '.$id.' '.$style.'>';
            } else {
                return '<img src="'.$return.'" '.$alt.' '.$cls.' '.$id.' '.$style.'>';
            }
        } else {
            return false;
        }
    }

    public static function asset($thefile = false, $echo = true)
    {
        $file = ($thefile) ? $thefile : '';
        $themed = file_exists(ASSETS.$file) ? ASSETS_URI.$file : '';
        $finurl = file_exists(RASSETS.$file) ? ASSETS_PURI.$file : $themed;

        $return = Config::siteURL( $finurl );

        if($echo) {
            echo $return;
        } else {
            return $return;
        }
    }
    
    public static function header($folder=false)
    {        
        $fold = ($folder) ? $folder : '';
        self::fetch(PAGES.$fold.DS.'header'.DOT.Config::get('FILE_EXT'), 'ro');
    }
    
    public static function footer($folder=false)
    {
        $fold = ($folder) ? $folder : '';
        self::fetch(PAGES.$fold.DS.'footer'.DOT.Config::get('FILE_EXT'), 'ro');
    }
    
    public static function sidebar($folder=false)
    {
        $fold = ($folder) ? $folder : '';
        self::fetch(PAGES.$fold.DS.'sidebar'.DOT.Config::get('FILE_EXT'), 'ro');
    }
    
    public static function page($filename = NULL, $data = array())
    {
        self::fetch(PAGES.$filename.DOT.Config::get('FILE_EXT'), 'ro', $data);
    }

    public static function block($filename = NULL, $data = array())
    {
        self::fetch(PAGES.$filename.DOT.Config::get('FILE_EXT'), 'i', $data);
    }
        
    public static function fetch($file = NULL, $load = 'ro', $data = array())
    {
        if(file_exists($file))
        {
            extract($data);
            
            switch(strtolower($load))
            {
                case 'require':
                case 'r':
                {
                    require($file);
                } break;
                case 'require_once':
                case 'ro':
                {
                    require_once($file);
                } break;
                case 'include':
                case 'i':
                {
                    include($file);
                }
                case 'include_once':
                case 'io':
                {
                    include_once($file);
                }
            }

            return true;
        }
        else 
        {
            return false;
        }
    }
    
    public static function reset( $t )
    {
        self::$$t = array();
    }
    
    public static function style( $path = false )
    {
        if($path) {
            $paths = is_array($path) ? $path : [$path];
            foreach($paths as $p) {
                $url = ($p) ? $p : '';        
                echo '<link href="'.self::cleanURL(CSS_URI.$url).'" rel="stylesheet" />';
            }
        }
    }
    
    public static function script( $path = false )
    {
        if($path) {
            $paths = is_array($path) ? $path : [$path];

            foreach($paths as $p) {
                $url = ($p) ? $p : '';        
                echo '<script src="'.self::cleanURL(JS_URI.$url).'" type="text/javascript"></script>';
            }
        }
    }
    
    public static function headers()
    {
        Assets::header();
    }
    
    public static function footers()
    {
        Assets::footer();
    }    

    public static function redirect( $path=false, $referrer = false, $absoluteurl = false, $previouspage = false )
    {
        $slug = '';

        if($path) {
            $environment = strtolower(Config::get('ENVIRONMENT'));
            $slug = ($path) ? $path : '';        
        }

        $url = self::url($slug,false);

        if($referrer) {
            $referrerurl = App::getSession('referrer');
            $url = ($referrerurl) ? $referrerurl : $url;
            App::setSession('referrer', false);
            header("location:".$url);
        }

        if($absoluteurl) {
            $url = $absoluteurl;
        }

        if($previouspage) {            
            header("location:".$_SERVER['HTTP_REFERER']); 
        } else {
            header("location:".$url);
        }   
        exit();
    }

    public static function referrer()
    {
        App::setSession( 'referrer', Config::siteURL( trim($_SERVER['REQUEST_URI'], "/") ) );
    }
    
    public static function getError()
    {
        return App::getSession( 'error' );
    }
    
    public static function getMessage($echo = false)
    {
        $e = App::getSession('error');
        $m = App::getSession('message');
        $n = App::getSession('notice');

        $r = '';
        if($e) {
            $r .= '<div class="alert alert-danger" role="alert">'.$e.'</div>';
            App::setSession('error',false);
        } 
        
        if($m) {
            $r .= '<div class="alert alert-info" role="alert">'.$m.'</div>';
            App::setSession('message',false);
        }

        if($n) {
            $r .= '<div class="alert alert-warning" role="alert">'.$n.'</div>';
            App::setSession('notice',false);
        }

        if($echo) {
            echo $r;
        } else {
            return $r;
        }
    }

    public static function getReferrer()
    {
        return App::getSession( 'referrer' );
    }

    public static function inlineCss( $identifier = '#LPF_general_rule', $rule = '' )
    {
        Assets::inline( $identifier, $rule );
    }

    public static function inlineJs( $jsscript )
    {
        JS::customrule($jsscript);
    }

    /**
     * Form field.
     * 
     * @since   revised from Mo's framework
     * @access  public
     */     
    public static function form($type = 'text', $args, $e = true) 
    {
        // TODO: Enhance this
        $label = isset($args['label']) ? '<label>'.$args['label'].'</label>' : '';  
        $name = isset($args['name']) ? ' name="'.$args['name'].'"' : '';    
        $value = isset($args['value']) ? ' value="'.$args['value'].'"' : '';    
        $class = isset($args['class']) ? ' class="'.$args['class'].'"' : '';    
        $id = isset($args['id']) ? ' id="'.$args['id'].'"' : ' id="'.$args['name'].'"'; 
        $placeholder = isset($args['placeholder']) ? ' placeholder="'.$args['placeholder'].'"' : '';    
        $options = isset($args['options']) ? $args['options'] : array();    
        $rel = isset($args['rel']) ? ' rel="'.$args['rel'].'"' : '';
        $multi = isset($args['multiple']) ? ' multiple="true"' : '';
        $style = isset($args['style']) ? ' style="'.$args['style'].'"' : '';
        $readonly = isset($args['readonly']) ? ' readonly' : '';
        $disabled = isset($args['disabled']) ? ' disabled' : '';
        $inarray = isset($args['inarray']) ? $args['inarray'] : false;
        $custom = isset($args['custom']) ? $args['custom'] : false;
        $required = isset($args['required']) ? $args['required'] : false;
        $rows = isset($args['rows']) ? ' rows="'.$args['rows'].'"' : '';
        $cols = isset($args['cols']) ? ' cols="'.$args['cols'].'"' : '';

        switch($type){  
            case 'hidden':  
                    $return = $label.'<input type="hidden"'.$name.$value.$class.$id.' '.$custom.' />';  
            break;

            case 'text':    
                    $return = $label.'<input type="text"'.$name.$value.$class.$id.$rel.$placeholder.$style.$readonly.$disabled.' '.$custom.' />';   
            break;  
            case 'number':    
                    $return = $label.'<input type="number"'.$name.$value.$class.$id.$rel.$placeholder.$style.$readonly.$disabled.' '.$custom.' />'; 
            break;
            case 'textarea':    
                    $thevalue = isset($args['value']) ? $args['value'] : '';
                    $return = $label.'<textarea'.$name.$class.$id.$placeholder.$style.$readonly.$disabled.$rows.$cols.' '.$custom.'>'.stripslashes($thevalue).'</textarea>';   
            break;  
            case 'select':  
                    $return = $label.'<select'.$name.$id.$class.$rel.$multi.$style.$readonly.$disabled.' '.$custom.' '.$required.' >';  
                    foreach($options as $option) {  
                        $val = explode(':', $option);   
                        $thevalue = isset($args['value']) ? $args['value'] : '';
                        $sel = $thevalue == $val[0] ? 'selected="selected"' : '';                   
                        if(count($val) > 1) {   
                            $return .= '<option value="'.$val[0].'" '.$sel.'>'.$val[1].'</option>'; 
                        } else {    
                            $return .= '<option value="'.$val[0].'" '.$sel.'>'.$val[0].'</option>'; 
                        }   
                    }   
                    $return .= '</select>'; 
            break;
            case 'selecta': 
                    $return = $label.'<select'.$name.$id.$class.$rel.$multi.$style.$readonly.$disabled.' '.$custom.' '.$required.'>';   
                    $options = (array) $options;
                    foreach($options as $k => $v) { 
                        $thevalue = isset($args['value']) ? $args['value'] : '';
                        $sel = $thevalue == $k ? 'selected="selected"' : '';
                        if($inarray) {
                            if( (count($inarray) && in_array($k,$inarray)) || count($inarray) < 1 ) {
                                $return .= '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';       
                            }
                        } else {
                            $return .= '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
                        }
                    }   
                    $return .= '</select>'; 
            break;
            case 'upload':
                $return = '<input '.$id.' class="fileup '.$args['class'].'" type="file" data-min-file-count="0" '.$name.' data-show-cancel="false" data-show-remove="false" data-show-upload="false" multiple '. $required .'>';            
            break;
            case 'uploadf':
                $return = '<input '.$id.' class="file '.$args['class'].'" type="file" data-min-file-count="0" '.$name.' data-show-upload="yes" '. $required .'>';            
            break;
        }
        $return = $type != 'hidden' ? $return.'<div class="clearfix"></div>' : $return;
        
        if($e) {
            echo $return;
        } else {
            return $return; 
        }
    }
}