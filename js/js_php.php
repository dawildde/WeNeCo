<?php
/*                                                                       
*                                     ,--.                               
*             .---.                 ,--.'|            ,----..            
*            /. ./|             ,--,:  : |           /   /   \           
*        .--'.  ' ;          ,`--.'`|  ' :          |   :     :  ,---.   
*       /__./ \ : |          |   :  :  | |          .   |  ;. / '   ,'\  
*   .--'.  '   \' .   ,---.  :   |   \ | :   ,---.  .   ; /--` /   /   | 
*  /___/ \ |    ' '  /     \ |   : '  '; |  /     \ ;   | ;   .   ; ,. : 
*  ;   \  \;      : /    /  |'   ' ;.    ; /    /  ||   : |   '   | |: : 
*   \   ;  `      |.    ' / ||   | | \   |.    ' / |.   | '___'   | .; : 
*    .   \    .\  ;'   ;   /|'   : |  ; .''   ;   /|'   ; : .'|   :    | 
*     \   \   ' \ |'   |  / ||   | '`--'  '   |  / |'   | '/  :\   \  /  
*      :   '  |--" |   :    |'   : |      |   :    ||   :    /  `----'   
*       \   \ ;     \   \  / ;   |.'       \   \  /  \   \ .'            
*        '---"       `----'  '---'          `----'    `---`              
* 
*                          Web Network Configuration 
*     
*                               PHP To JS
*/
$server_root = dirname( dirname(__FILE__) );
$web_root = dirname ( "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) );

require_once "$server_root/includes/config.php";
require_once "$server_root/includes/getData.php";
require_once "$server_root/includes/language.php";
?>
// GENERAL
const LANG = JSON.parse('<?php echo lang_json();?>'); //LANGUAGE DATA 
const WEBROOT = "<?php echo $web_root; ?>"; // WEB-ROOT

// INTERFACE LIST
const WIRED_IF = <?php echo json_encode( getInterfaces("wired")[1] ); ?>; //ETH
const WIFI_IF = <?php echo json_encode( getInterfaces("wireless")[1] ); ?>; //WLAN

//CONFIG_KEYS
<?php
// LOOP THROUGH CONFIG KEYS AND APPEND TO JS
  $constants = get_defined_constants( true );
  foreach ( $constants["user"] as $key => $val ) {
    if ( 
      substr( $key, 0, 9 ) == "CONF_KEY_" or
      substr( $key, 0, 10 ) == "WIFI_MODE_" or
      substr( $key, 0, 11 ) == "WIRED_MODE_"
      ){
      if ( isset( $data[$val] ) === false ) {
          echo "const $key = '$val';" .PHP_EOL;
      }
    }
  }
?>