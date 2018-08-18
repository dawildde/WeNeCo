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
 *                                  WRITE FILE
 *
 *   GENERAL INFORMATIONS:
 *       $interface = interface name (eg. wlan0, wl2pxsse, etc )
 *       $device = device number in configs ( device0, device1, etc )
 *
 *       each function returns array with result and data
 *          array[0] => result ( true, false, nodata )
 *          array[1] => data ( return value )
 *
 */

require_once "config.php";
require_once "iniparser.php";
require_once "functions.php";
require_once "getData.php";


/********************************************************************
*
*                  OWN CONFIGURATION FILE
*
*********************************************************************/

 // CREATE NEW CONF-FILE
 /*   parameter:
 *      interface - interface name
 *      data - data with general values (optional)
 *    return:
 *      [0]True / False  [1] filename created
 */
 function createNewConf( $interface, $data = null ){
    $return = array( false, "" ); // = "nodata";
    if ( strlen( $interface ) > 1 ){
      for ( $i=0; $i<=10; $i++){
        $file = WENECO_DIR  . "/network/device$i.conf";
        if ( ! is_file( $file ) ){
          $data[CONF_KEY_GENERAL]["name"] = $interface;
         
          $json_str = json_encode( $data, JSON_PRETTY_PRINT );
          $fp = fopen( $file, 'w' );
          $bytes = fwrite( $fp, $json_str );
          fclose( $fp );
          
          if ( $bytes != false and $bytes > 0 ){
              $return[0] = true;
              $return[1] = basename( $file );
          }
          break;
        }
      }
    }
    return $return;
 }

// ADD WiFi-CONFIGURATION TO CONF-FILE
 /*   parameter:
 *      interface - interface name
 *      data - JSON-OBJECT with one network-data (ssid)
 *    return:
 *      True / False
 */
 function addWifiConf( $interface, $data ){
    $return = array( false, "" ); // = "nodata";
    $device = getDeviceNo ( $interface );
    $file = WENECO_DIR  . "/network/$device.conf";
    $newList = array();
    
     // read full config
    if ( is_file ( $file ) ){
      $content = file_get_contents ( $file );
      $oldData = json_decode( $content, true );
      
      // append old settings to new excluding actual network
      if ( isset ( $oldData[CONF_KEY_WPA] ) and count($oldData[CONF_KEY_WPA] > 0) ) {
          foreach ( $oldData[CONF_KEY_WPA] as $idx => $network){
              if ( isset( $network["ssid"] ) and $network["ssid"] != $data["ssid"] ){
                  $newList[] = $network;
              }
          }
      }
    }
    // append new data
    $newList[] = $data;
    $return = write2conf ( $file, CONF_KEY_WPA, $newList );
    return $return;
 }

 // REMOVE WiFi-CONFIGURATION
 /*   parameter:
 *      interface - interface name
 *      ssid - ssid will be removed
 *    return:
 *      True / False
 */
 function removeWifiConf( $interface, $ssid ){
    $return = array( false, "" ); // = "nodata";
    $device = getDeviceNo ( $interface );
    $file = WENECO_DIR  . "/network/$device.conf";
    $newList = array();
    
    // read full config
    if ( is_file ( $file ) ){
      $content = file_get_contents ( $file );
      $oldData = json_decode( $content, true );
   
      // append old settings to new excluding actual network
      if ( isset ( $oldData[CONF_KEY_WPA] ) ) {
          foreach ( $oldData[CONF_KEY_WPA] as $idx => $network ) {
              if ( $network["ssid"] != $ssid ){
                  $newList[] = $network;
              }
          }
      }
      $return = write2conf ( $file, CONF_KEY_WPA, $newList );
    }
    return $return;
 }
 
 // SAVE NETWORK-CONFIGURATION TO CONF-FILE
 /*   parameter:
 *      interface - interface name
 *      data - JSON-OBJECT with one network-data
 *    return:
 *      True / False
 */
 function saveNetworkConf( $interface, $data ){
    return saveConf( $interface, CONF_KEY_NETWORK, $data );
 }
 
 // SAVE NETWORK-CONFIGURATION TO CONF-FILE
 /*   parameter:
 *      interface - interface name
 *      section - section is config saved to
 *      data - JSON-OBJECT with one network-data
 *    return:
 *      True / False
 */
 function saveConf( $interface, $section, $data ){
    $return = array( false, "" ); // = "nodata";
    $device = getDeviceNo ( $interface );
    $file = WENECO_DIR  . "/network/$device.conf";
    return write2conf( $file, $section, $data );
 }
 
 // SAVE FULL-NETWORK-CONFIGURATION TO CONF-FILE
 /*   parameter:
 *      interface - interface name
 *      data - JSON-OBJECT with one network-data
 *    return:
 *      True / False
 */
 function saveFullConf( $interface, $data ){
    $return = array( false, "" ); // = "nodata";
    $device = getDeviceNo ( $interface );
    $file = WENECO_DIR  . "/network/$device.conf";
    
    // get WPA-Config (will not be overwritten here)
    if( isset( $data[CONF_KEY_WPA] )){
      $data[CONF_KEY_WPA] = getConfig( $interface, CONF_KEY_WPA )[1];
    }
    
    $json_str = json_encode( $data, JSON_PRETTY_PRINT );
    $fp = fopen( $file, 'w' );
    $bytes = fwrite( $fp, $json_str );
    fclose( $fp );
    
    if ( $bytes != false and $bytes > 0 ){
        $return[0] = true;
        $return[1] = json_decode(file_get_contents( $file ));
    }

    return $return;
 }
 
 
// WRITE CONFIGURATIONS TO CONFIG-FILE
 /*   parameter:
 *      device - number of device
 *      section - section data is written ( "general", "networkd", "wpa_supplicant", "hostapd", .... )
 *      data - JSON-OBJECT with complete network-data
 *    return:
 *      True / False
 */
 function write2conf( $file, $section, $data ){
    $return = array( false, "" ); // = "nodata";
    
     // read full config
    if ( is_file ( $file ) ){
      $content = file_get_contents ( $file );
      $wData = json_decode( $content, true );
      $wData[$section] = $data;  // REPLACE SECTION
    } else {
      $wData[$section] = $data;  // ADD SECTION
    }
    $json_str = json_encode( $wData, JSON_PRETTY_PRINT );
    $fp = fopen( $file, 'w' );
    $bytes = fwrite( $fp, $json_str );
    fclose( $fp );
    
    if ( $bytes != false and $bytes > 0 ){
        $return[0] = true;
        $return[1] = json_decode(file_get_contents( $file ));
    }

    return $return;
 }

 
 /********************************************************************
*
*                      SYSTEM FILES
*
*********************************************************************/
 
 // WRITE FULL FILE
 /*   parameter:
 *      file - filename
 *      text - text will be saved
 *    return:
 *      True / False - File content
 */
 function writeFile( $file, $text ){
   $result = array( false, "" ); // = "nodata";
   $f_res = file_put_contents( $file, $text );
   if ( ! $f_res === false ){
      $result[0] = true;
      $result[1] = file_get_contents( $file );
   }
   return $result;
 }
 
 
 
 // CREATE NETWORK CONFIGURATION-FILE
 /*   parameter:
 *      interface - interface
 *      data - JSON-OBJECT with network-data
 *    return:
 *      True / False
 */
function create_network_conf( $interface, $data ){
  $result = array( false, "" ); // = "nodata";
  $device = getDeviceNo( $interface );
  
  $file = "$device.network";
  $target_file = WENECO_DIR  . "/network/$file";
  $tmp_file = TMP_DIR. "/$file";
  // copy template
  exec ("cp " .WENECO_DIR. "/config/template.network $tmp_file"  );
        
  // BUILD ARRAY
  $nw_data = array();
  $nw_data["Match"] = array( "Name" => $interface );
  
  // DHCP Client 
  if ( $data["ipmode"] == "DHCP" ){
      $nw_data["Network"] = array( "dhcp" => "yes" );
  } else {
      $cidr = netmask2cidr($data["netmask"]);
      $nw_data["Network"] = array(
                "Address" => $data["ipv4"] ."/". $cidr
                ,"Gateway" => $data["gateway"]
                ,"DNS" => $data["dns"]
                ,"Description" => $data["description"]
      );
  }
  
  // DHCP Server
  create_dnsmasq( $interface, $data );
  
  // ROUTING
  /*
  if ( isset( $data["routing"] ) ){
    $matches = explode( "_",  $data["routing"] );
    $mode = getVal( $matches, 0 );
    $target = getVal( $matches, 1 );
    
    // GET TARGET INTERFACE CONFIG-FILE
    $tgt_conf = getConfig( $target, CONF_KEY_NETWORK )[1];
    
    switch ( $mode ){
      case "NAT":
        //$nw_data["Network"]["Gateway"] = $tgt_conf["gateway"];
        //$nw_data["Network"]["DNS"] = $tgt_conf["dns"];
        $nw_data["Network"]["IPForward"] = "no";
        $nw_data["Network"]["IPMasquerade"] = "yes";
        break;  
      case "FWD":
        $nw_data["Network"]["IPForward"] = "yes";
        $nw_data["Network"]["IPMasquerade"] = "no";
        break;
    }
  }
  */
  
  // WRITE FILE
  $fp = fopen("$tmp_file", "a");
  fwrite($fp, arr2ini( $nw_data ));
  fclose($fp);
        
  // OVERWRITE ORIGINAL TARGET FILE
  exec (" mv $tmp_file $target_file" );
        
  $result[0] = true;
  $result[1] = "";

  return $result;
}
 
 // CREATE WPA-SUPPLICANT
 /*   parameter:
 *      data - JSON-OBJECT with WiFi network data
 *    return:
 *      True / False
 */
 function create_wpa_supplicant( $interface, $jsnDat ){
    $return = array( false, "" ); // = "nodata";
    $device = getDeviceNo( $interface );
    
    // convert security
    if ( isset( $jsnDat["#security"] ) ){
      $keyMgmt = getKeyMgmt( $jsnDat["#security"] );
      $jsnDat = array_merge( $jsnDat, $keyMgmt );
    }

    // write file in temp
    $file = WENECO_DIR . "/network/wpa_supplicant-$device.conf";
    $default_conf = file_get_contents ( WENECO_DIR ."/config/wpa_supplicant.conf" );
    $fp = fopen( $file, 'w' );
    fwrite( $fp, $default_conf );    // write default config
    fwrite( $fp, "network={" . PHP_EOL );
    foreach ( $jsnDat as $key => $val ){
        if ( strlen( $val ) > 0 ){
            // add values with or without ""
            switch ( $key ){
              case "ssid":
              case "id_str":
                fwrite ( $fp, "\t$key=\"$val\"" . PHP_EOL ); // with ""
                break;
              default:
                fwrite ( $fp, "\t$key=$val" . PHP_EOL );  // without ""
            }
        }
    }
    fwrite( $fp, "}" );
    fclose( $fp );
    $return[0] = true;
    $return[1] = file_get_contents( $file );
    return $return;
 }
 
 
 // CREATE HOSTAPD CONFIGURATION-FILE
 /*   parameter:
 *      interface - interface
 *      data - JSON-OBJECT with network-data
 *    return:
 *      True / False
 */
function create_hostapd( $interface, $jsnDat ){
  $result = array( false, "" ); // = "nodata";
  $device = getDeviceNo( $interface );
 
  // CHECK IF INTERFACE IS SET
  if ( ! isset( $jsnDat["interface"] ) ){
    $jsnDat["interface"] = $interface;
  }
 
  // convert security ( ONLY WPA )
  if ( isset( $jsnDat["security"] ) ){
    preg_match( "/(.*) \((.*)\)/", $jsnDat["security"], $matches );
    $proto = getVal( $matches, 1, $jsnDat["security"]);
    $opt = getVal( $matches, 2, "CCMP" );

    $jsnDat["auth_algs"] = 1;
    //WPA 2
    if ( $opt == "CCMP" ){
        $jsnDat["wpa"] = 2;
        $jsnDat["rsn_pairwise"] = "CCMP";
    //WPA 1&2
    } else {
        $jsnDat["wpa"] = 3;
        $jsnDat["wpa_pairwise"] = "TKIP CCMP";
    }
    unset( $jsnDat["security"] );
  }
  
  $file = WENECO_DIR . "/network/hostapd-$device.conf";
  $default_conf = file_get_contents ( WENECO_DIR ."/config/hostapd.conf" );
  $fp = fopen( $file, 'w' );
  fwrite( $fp, $default_conf .PHP_EOL );    // write default config
  fwrite( $fp, arr2ini( $jsnDat ));
  fclose( $fp );
  
  $return[0] = true;
  $return[1] = file_get_contents( $file );
  return $return;
 }
 
 
  
 // CREATE DNSMASQ-CONF
 /*   parameter:
 *      interface - interface name
 *      data - JSON-OBJECT with WiFi network data
 *    return:
 *      True / False
 */
 function create_dnsmasq( $interface, $data ){
  $return = array( false, "" ); // = "nodata";
  $device = getDeviceNo( $interface );
 
  // create write string
  $wData = "interface=$interface            # Use interface xxxxxxx" .PHP_EOL ;
  // OWN IP ADDRESS
  if ( strlen( $data['ipv4'] ) > 5 ){ 
    $wData .= "listen-address=" .$data['ipv4']. "  # Explicitly specify the address to listen on" .PHP_EOL ; 
  }
  /* DNS-SERVERS READ FROM /etc/resolv.conf
  foreach ( $data["dns"] as $dns ){
      $wData .= "server=$dns                # Forward DNS requests to Google xxxxxx" .PHP_EOL;
  }
  */
  $wData .= "domain-needed                  # Don't forward short names" .PHP_EOL; 
  $wData .= "bind-interfaces                # Bind to the interface to make sure we aren't sending things elsewhere" .PHP_EOL;
  $wData .= "bogus-priv                     # Never forward addresses in the non-routed address spaces" .PHP_EOL;
  // DHCP Server
  if ( $data["dhcp_server"] == "yes" ){
    $wData .= "dhcp-range=" .$data["dhcp_start"] .",". $data["dhcp_end"] .",". getVal($data, "dhcp_lease", 24) ."h".PHP_EOL;
  }

  
  // WRITE DATA
  $file = WENECO_DIR . "/network/dnsmasq-$device.conf";
  $fp = fopen( $file, 'w' );
  fwrite( $fp, $wData);
  fclose( $fp );
  
  $return[0] = true;
  $return[1] = file_get_contents( $file );
  return $return;
 }
 
 
 // CREATE FIREWALL-RULES (IPTABLES)
 /*   parameter:
 *      interface - interface name
 *      data - JSON-OBJECT with WiFi network data firewall rules
 *    return:
 *      True / False
 */
 function create_firewall_rules( $interface, $data ){
  $return = array( false, "" ); // = "nodata";
  $device = getDeviceNo( $interface );
  
  
 }
 
 /********************************************************************
*
*                     AJAX FUNCTION CALLER
*
*********************************************************************/

 // CALL FUNCTION BY AJAX 
 $set = REQ( "set" );
 if ( isset( $_REQUEST["set"] ) ){
   switch ( $set ){
      case "saveNetworkConf":
        $result = saveNetworkConf( REQ("ifname"), REQ("data") );
        break;
      case "saveConf":
        $result = saveConf( REQ("ifname"), REQ("section"), REQ("data") );
        break;
      case "saveFullConf":
        $result = saveFullConf( REQ("ifname"), REQ("data") );
        break;
      case "writeFile":
        $result = writeFile( REQ("file"), REQ("text") );
        break;
      case "addWiFi":
        $result = addWifiConf( REQ("ifname"), REQ("data") );
        break;
      case "removeWiFi":
        $result = removeWifiConf( REQ("ifname"), REQ("ssid") );
        break;
   }
      
   if ( isset ( $result[0] ) and $result[0] == true ){
      header('Content-Type: application/json');
   } else {
      header('HTTP/1.1 500 Internal Server Booboo');
      header('Content-Type: application/json; charset=UTF-8');
   }
   if ( isset ( $result[1] )){
     if (  is_array( $result ) and array_key_exists( 1, $result ) ){
        print json_encode ($result[1]) ;
     } else {
        print $result[1] ;
     }        
   }
 }
 ?>