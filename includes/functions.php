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
 *                                 GLOBAL FUNCTIONS
 */
 
 require_once "iniparser.php";
 require_once "writefile.php";
 require_once "readfile.php";
 require_once "execute.php";
 
 // RETURN STRING FROM REGEX
 function preg_find($regex, $string){
    $count = preg_match($regex, $string, $match);
    if ($count > 0){
      return $match[1];
    }
    return "";
 }
 
 // RETURN DEFAULT VAL IF IS UNSET
 function getVal( $arr, $key, $default = "" ){
   if ( isset ( $arr[ $key ] ) ) {
      return $arr[ $key ];
   } else {
      return $default;
   }
 }
 
 // RETURN $_REQUEST VAL IF SET (or default)
 function REQ( $key, $default = "" ){
    return getVal( $_REQUEST, $key, $default );
 }
 
  // RETURN $_POST VAL IF SET (or default)
 function POST( $key, $default = "" ){
    return getVal( $_POST, $key, $default );
 }
 
  // RETURN $_GET VAL IF SET (or default)
 function GET( $key, $default = "" ){
    return getVal( $_GET, $key, $default );
 }
 
 // EVALUATE EXECUTION RESULT
 function eval_result( $returnval ){
    // CHECK IF LAST 2 LETTERS OF RETURN = "OK"
    $ret_trim = str_ireplace(array("\n", "\r", "<br>", "<br />", "<br/>"), '', $returnval);
    
    if ( substr( $ret_trim, -2 ) == "OK" ) {
        $ret[0] = true;
        $ret[1] = substr( $returnval, 0, -2);
    } else {
        $ret[0] = false;
        $ret[1] = $returnval;
    }
    return $ret;
 }
 
 // SHELL EXECUTE with LOG
 function my_shell_exec( $cmd ){
    $cmd_return = shell_exec( $cmd );
    $ret = "";
    // IF IS WENECO FUNCTION (RETURNS OK)
    if ( strpos( $cmd, WENECO_DIR ) ) {
        $res = eval_result( $cmd_return );
        if ( $res[0] === true ){
          $ret = $res[1];
          wlog( "EXECUTING: '$cmd' -> 'OK'", 2 );
        } else {
          $res = $cmd_return;
          wlog( "EXECUTING: '$cmd' -> 'FAILED: $res'", 4 );
        }
    } else {
      $ret = $cmd_return;
      wlog( "EXECUTING: '$cmd'", 1 );  // log command only
    }
    
    return $ret;
 }
 
 // GET DEVICE No BY INTERFACE NAME
 // 
 function getDeviceNo( $interface ){
     // FIND ALL *.conf-FILES
    foreach ( glob ( WENECO_DIR  . "/network/*.conf" ) as $file) {
        // READ JSON
        $content = file_get_contents ( $file );
        $data = json_decode( $content, true );
        
        // FILL DATA ARRAY
        if ( $data[CONF_KEY_GENERAL]["name"] == $interface ){
            $filename = basename( $file );
            return str_replace( ".conf", "", $filename );
            break;
        }
    }
    // INTERFACE NOT FOUND ==> CREATE NEW CONFIG
    $newfile = createNewConf( $interface )[1];
    return str_replace( ".conf", "", $newfile );
 }
 
 
 // CREATE DEFAULT Network-Config-ARRAY
function empty_nw_conf(){
  $nw_conf["name"] = "";
  $nw_conf["ipv4"] = "";
  $nw_conf["ipv6"] = "";
  $nw_conf["netmask"] = "";
  $nw_conf["gw"] = "";
  $nw_conf["dns"] = array("","");
  $nw_conf["mac"] = "";
  $nw_conf["dhcp"] = "";
  $nw_conf["description"] = "";
  return $nw_conf;
}

 // CREATE DEFAULT WiFi-Config-ARRAY
function empty_wifi_conf(){
  $nw_conf["bssid"] = "";
  $nw_conf["frequency"] = "";
  $nw_conf["channel"] = "";
  $nw_conf["level"] = "";
  $nw_conf["flags"] = "";
  $nw_conf["security"] = "";
  $nw_conf["ssid"] = "";
  $nw_conf["description"] = "";
  return $nw_conf;
}

// Convert Frequency into channel 
/*
*   parameter:
*         freq - Frequency
*   return: 
*         channel
*/
function Freq2Channel( $freq ) {
  if ($freq >= 2412 && $freq <= 2484) {
    $channel = ($freq - 2407)/5;
  } elseif ($freq >= 4915 && $freq <= 4980) {
    $channel = ($freq - 4910)/5 + 182;
  } elseif ($freq >= 5035 && $freq <= 5865) {
    $channel = ($freq - 5030)/5 + 6;
  } else {
    $channel = -1;
  }
  if ($channel >= 1 && $channel <= 196) {
    return $channel;
  } else {
    return 'Invalid Channel';
  }
}

// GET INTERFACE TYPE
function getIFType( $interface ){
  $out = shell_exec( "iwconfig $interface" );
  if ( strpos( $out, "IEEE 802.11" ) ){
    return "wireless";
  } else {
    return "wired";
  }
}


// GET KEYMANAGEMENT
/**
*   parameter:
*         security - String with Security ( e.g. "WPA2 (CCMP)" )
*   return: 
*         object with WPA-SUPPLICANT SETTINGs
*             ["proto"]
*             ["pairwise"]
*             ["key_mgmt"]
*             ["group"]
*/
function getKeyMgmt( $security ){
  preg_match( "/(.*) \((.*)\)/", $security, $matches );
  $proto = getVal( $matches, 1, $security);
  $opt = getVal( $matches, 2 );
 
  //WPA2
  if ( $proto == "WPA2" ) {
    $ret["key_mgmt"] = "WPA-PSK";
    if ( $opt == "CCMP" ){
        $ret["proto"] = "RSN";
        $ret["group"] = "CCMP";
        $ret["pairwise"] = "CCMP";        
    } else if ( $opt == "TKIP" ){
        $ret["proto"] = "WPA RSN";
        $ret["group"] = "TKIP CCMP";
        $ret["pairwise"] = "TKIP CCMP";        
    }
  //WPA1
  } else if ( $proto == "WPA" ){
      $ret["key_mgmt"] = "WPA-PSK";
      $ret["proto"] = "WPA";
      $ret["pairwise"] = "TKIP";
      $ret["group"] = "TKIP"; 
  //WEP
  } else if ( strpos( $match, "WEP" ) !== false ){
      $ret["key_mgmt"] = "NONE";
      $ret["wep_tx_keyidx"] = 0;
      $ret["wep_key0"] = "";  // WEP KEY
  } else {
    $ret["key_mgmt"] = "NONE";
  }
  return $ret;
} 


// CONVERT SCAN FLAGS TO SECURITY
/**
*   parameter:
*       flags - flags string from wpa_cli scan ( e.g.: [WPA2-PSK-TKIP] ) 
*   return: 
*         security - String with Security ( e.g. "WPA2 (TKIP)" )
*/

function flags2security( $flags ){
    preg_match_all('/\[([^\]]+)\]/s', $flags, $matches);
    foreach($matches[1] as $match) {
      if ( strpos( $match, "WPA" ) !== false ){
        $opts = explode('-', $match);
        if ( count( $opts ) > 2) {
            return "$opts[0] ($opts[2])";
        } else {
            return $opts[0];
        }
      } else if( strpos( $match, "WEP" ) !== false ){
        return "WEP";
      }
    }
    // NO WPA-SECURITY
    return "open";
}



 
 // CONVERT CIDR IP TO IP & NETMASK
 /*   parameter:
 *      cidr - IP-Address "192.178.2.10/24"
 *
 *    return:
 *      array [0] -> IP-Address
 *            [1] -> Netmask
 */
  function cidrIP2Netmask ($cidr) {
    if ( strpos( $cidr, "/" ) ){
      $ret[0] = strstr( $cidr, "/", True) ;
      $ta = substr ($cidr, strpos ($cidr, '/') + 1) * 1;
      if ( $ta >= 0 ){
        $netmask = str_split (str_pad (str_pad ('', $ta, '1'), 32, '0'), 8);

        foreach ($netmask as &$element)
          $element = bindec ($element);
        
        $ret[1] = join ('.', $netmask);
      }
    } else {
      $ret[0] = $cidr;
      $ret[1] = "";
    }
    return $ret;
  }
  
  // CONVERT NETMASK TO CIDR
  /*   parameter:
  *      mask - netmask "255.255.255.0"
  *
  *    return:
  *      CIDR (int)
  */
  function netmask2cidr($mask){
    if ( filter_var($mask, FILTER_VALIDATE_IP) ){
       $long = ip2long($mask);
       $base = ip2long('255.255.255.255');
       return 32-log(($long ^ $base)+1,2);
    } else {
       return 24; 
    }
  }  
 
 // PRINT DEBUG MESSAGE
 function debug($msg){
   print ("<p>DEBUG: '" . $msg . "'</p>");
 }
 
 // APPEND LOG ENTRY
 /*  write weneco logfile
 *    parameters:
 *      msg - log message
 *      msg_level - loglevel of message (will not be logged if LOG_LEVEL < this )
 */
 function wlog( $msg, $msg_level ){
   if ( LOG_LEVEL > $msg_level ) {
      $date = date("Y.m.d H:i:s");
      $fp = fopen ( LOG_F_WENECO, "a" );
      fwrite ( $fp, "$date: $msg" .PHP_EOL );
      fclose ( $fp );
   }
 }