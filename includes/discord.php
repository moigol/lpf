<?php
/**
 * Discord Object
 *
 * @category   Helper
 * @package    Discord
 * @author     Mo <moises.goloyugo@gmail.com>
 * @copyright  (c) 2020 Motility
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.0.0
 * @link       http://
 * @since      Class available since Release 1.2.0
 */
class Discord
{

    public static function doPostToDiscord( $message, $client_name, $link, $designers = false )
    {
        if($designers){
          $message = $message." ".$designers;
        }

        $data = array("content" => $message, "username" => $client_name);
        $curl = curl_init($link);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public static function send( $message, $postName, $postTo = false, $designers = false )
    {
        switch( $postTo ) {
            case "Support":
            case "support":
                $link = 'https://discordapp.com/api/webhooks/741330537045819411/vkcoDCrVjU0vbd8F23jiCdQPP5vlBuG12n80rR2mLXRvXBJeNwsusl8rI43D3i-XOxXV';
            break;
            case "Request":
            case "request":
                $link = 'https://discordapp.com/api/webhooks/741330368992510052/yGH_10kdCiOGlavlCxF2Y82kPT8xMCUGsuyeWb6Oy_DMgNkuAqPoHJJhgZ5Hi6KUh6Q5';
            break;
            case "Signup":
            case "signup":
                $link = 'https://discordapp.com/api/webhooks/741330078692147212/QBWN6Ey8ooZ5XxwrZkFjlhl2GMSFRfOIsfLxLhUtzXbscyU4nmuOHDe6_fsrwnPyefYu';
            break;
            case "Updates":
            case "updates":
            case "Update":
            case "update":
                $link = 'https://discordapp.com/api/webhooks/741330725818597418/SORaqwGXJPIRIbWw1lIYTTF2eO_FKwZcDdmVJbGrncmbQJYXS4zPz6E8sAGC5U9AgPDR';
            break;
            case "Status":
            case "status":
            case "clientstatus":
            case "ClientStatus":
                $link = 'https://discordapp.com/api/webhooks/775284797085057034/g0Tkn9HH19z_zYqZpsZd1A4p8YwUtXNgRh1Vp0WMW-VOBgFxhW5DX7BZ8CUXRJiK0BO8';
            break;
            default:
                $link = 'https://discordapp.com/api/webhooks/741330871843291177/cxncPm6_6kFBOzRKXvnWSJW_YLE8QNAj2USCety7Oc1X2XQSGKoX-mid7VTXvO78Wiam';
            break;
        }

        self::doPostToDiscord( strip_tags($message), $postName, $link, $designers );
        //self::doPostToDiscord( $message, $postName, $link, $designers );
    }

    public static function getDesignerDiscordId($DesignerIDs = false)
    {
      $ret  = '';

      if($DesignerIDs){
        foreach ($DesignerIDs as $id) {
          $db    = new DB();
          $sql = "SELECT DiscordBotID FROM user_meta WHERE UserID = " . $id;
          //Config::log("SQL[".$sql."]");
          $data = $db->get_row($sql);
          if ($data) {
              $botID = $data->DiscordBotID;
              $ret != "" && $ret .= ",";
              $ret .= $botID;
          }
        }
      }else {
        $ret = '<@&473958760532934668>';
      }

      return $ret;
    }

    /*public static function getBotID($DesignerName = false)
    {
      if($DesignerName){
        $DesignerName = strtolower($DesignerName);
        //Config::log("SQL[".$DesignerName."]");
        switch ($DesignerName) {
          case 'jethro':
              return '<@!97683599788093440>';
            break;

          case 'marlon':
              return '<@!342672723761430539>';
            break;

          case 'reymart':
              return '<@!674141883869560832>';
            break;

          default:
              return '<@&473958760532934668>';
            break;
        }
      }
    }*/
}
?>
