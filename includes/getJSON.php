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
 *                             RETURN DATA IN JSON-FORMAT
 */
 
 /*
 *    Calls a PHP-Function and returns his data in JSON-FORMAT
 *      PHP-Functions had to return fields:
 *        $result[0] = true;    - for successfully read data
 *                   = nodata;  - returns no data but it's not an error
 *        $result[1] = xyz;     - data returned
 */
 require_once "config.php";
 require_once "functions.php";
 require_once "readfile.php";
 require_once "getData.php";
 
 
 // EVALUATE RESULT AS JSON
 /*   parameter:
 *      $result - array with result of functions
 *    return:
 *      prints json data
 */
 
 function eval_result_JSN( $fnResult ){
    if ( is_array( $fnResult ) and array_key_exists( 0, $fnResult ) and ( $fnResult[0] === true or $fnResult[0] == "nodata") ){
        header('Content-Type: application/json');
        print json_encode( $fnResult[1] );
    } else {
        header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json; charset=UTF-8');
        if (  is_array( $fnResult ) and array_key_exists( 1, $fnResult ) ){
          print json_encode( $fnResult[1] );
        } else {
          print json_encode( $fnResult );
        }
    }
 }
  

  
// CALL FUNCTION BY AJAX
//SWITCH $FN...... HERE
$get = REQ( "get" );#
switch ( $get ){
  case "get_interfaces":
    // get interfaces
    $result = getInterfaces( REQ( "filter" ) );
    break;
  case "getNetConf":
    // get full config
    $result = getConfig( REQ( "ifname" ) );
    break;
  case "getIfConf":
    // get networkd config
    $result = getConfig( REQ( "ifname" ), "networkd" );
    if ( $result[0] === false or $result[0] == "nodata" ){
      $result[1] = empty_nw_conf();
    }
    break;
  case "getHAPDConf":
    $result = getConfig( REQ( "ifname" ), CONF_KEY_AP );
    break;
  case "scanWifiNetworks":
    $result = scan_wifi( REQ( "ifname" ), REQ( "timeout", 3 )  );
    break;
  case "crypt_psk":
    $result = wpa_pass( REQ( "ssid" ), REQ( "psk" ) );
    break;
  case "getKnownWifiNetworks":
    $result = getConfig( REQ( "ifname" ), CONF_KEY_WPA );
    break;
  case "read_content":
    $result = read_content( REQ("file"), REQ("lines") );
    break;
  
  default:
    $result[0] = false;
    $result[1] = "UNKNOWN FUNCTION '$fn'";
    break;
}

// print JSON-DATA
eval_result_JSN( $result );
    
    