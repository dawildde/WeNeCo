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
*                             EXECUTE FUNCTIONS
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
require_once "functions.php";

// CONNECT TO WIFI-NETWORK
function connect_wifi( $interface, $ssid, $psk ){
    $deviceNo = getDeviceNo( $interface );
    // READ CONFIG FILE IF POSSIBLE
    $savedConf = getConfig( $interface, CONF_KEY_WPA );
    
    
    // CREATE NEW WPA_SUPPLICANT-FILE
    $par["ssid"] = $ssid;
    $par["psk"] = $psk;
    if ( $savedConf[0] === true ){
        // loop through saved Networks
        foreach ( $savedConf[1] as $wifi ){
            // search for ssid in config
            if ( $wifi["ssid"] == $ssid ){
                //overwrite psk if given
                if ( strlen( $psk) > 0 ){
                    $wifi["psk"] = $psk;
                }
                $par = $wifi;
                break;
            }
        }  
    }
    $result = create_wpa_supplicant ( $interface , $par );
    
    // COPY WPA_SUPPLICANT-FILE
    if ( $result[0] === true ){
        $result = my_shell_exec ( "sudo " .WENECO_DIR. "/script/wpa_supplicant.sh connect $deviceNo" );
    } else {
        $result = $result[1];
    }
    return $result;
}

// READ SYSTEM CONFIGURATION FILES
function copySystemConfFiles( $interface ){
    $deviceNo = getDeviceNo( $interface );
    // copy system-files to weneco_dir
    my_shell_exec ( "sudo " .WENECO_DIR. "/script/execute.sh getNetConf $deviceNo" );  
}

// RECONNECT WIFI-NETWORK
function reconnect_wifi( $interface ){
  $deviceNo = getDeviceNo( $interface );
  $result = my_shell_exec ( "sudo " .WENECO_DIR. "/script/wpa_supplicant.sh connect $deviceNo" );
  return $result;
}

// DISCONNECT WIFI-NETWORK
function disconnect_wifi( $interface ){
  $deviceNo = getDeviceNo( $interface );
  $result = my_shell_exec ( "sudo " .WENECO_DIR. "/script/wpa_supplicant.sh disconnect $deviceNo" );
  return $result;
}

// REBOOT
function reboot_system(){
  return my_shell_exec( "sudo " .WENECO_DIR . "/script/execute.sh reboot" );
}

// RESTART NETWORKING
function restart_network(){
  return my_shell_exec ( "sudo " .WENECO_DIR . "/script/execute.sh restart_network" );
}

// RESTART NETWORKING
function restart_interface( $ifname ){
  $device = getDeviceNo ( $ifname );
  return my_shell_exec ( "sudo " .WENECO_DIR . "/script/execute.sh restart_interface $device" );
}

// QUERY NEW DHCP ADDRESS
function query_dhcp( $ifname ){
  $device = getDeviceNo ( $ifname );
  return my_shell_exec ( "sudo " .WENECO_DIR . "/script/execute.sh query_dhcp $device" );
}


// RESTART SERVICE
function restart_service( $service, $ifname=null ){
  $device = "";
  if ( $ifname ) {
    $device = getDeviceNo( $ifname );
  }
  return my_shell_exec ( "sudo " .WENECO_DIR . "/script/restart_service.sh $service $device" );
}

// PATCH LOG-FILES
function patch_logs(){
  return my_shell_exec ( "sudo " .WENECO_DIR . "/script/execute.sh patch_logs" );
}

// REMOVE OLD SETTINGS
function rmConfFiles( $device ){
  rmFile ( WENECO_DIR  . "/network/$device.network" );
  rmFile ( WENECO_DIR  . "/network/wpa_supplicant-$device.conf" );
  rmFile ( WENECO_DIR  . "/network/hostapd-$device.conf" );
}

function rmFile( $file ){
  if ( is_file( $file ) ){
      return unlink ( $file );
  }
}


// APPLY SETTINGS
function apply_settings( $interface ){
  $device = getDeviceNo ( $interface );
  $general = getConfig ( $interface, CONF_KEY_GENERAL )[1];
  
  rmConfFiles( $device );  // remove old files
  
  // CREATE FILES WHEN MODE...
  switch ( $general["mode"] ){
      // create hostapd
      case WIFI_MODE_AP:
      //case WIFI_MODE_WISP_M:
        $hapdconf = getConfig( $interface, CONF_KEY_AP )[1];
        create_hostapd( $interface, $hapdconf ); // create hostapd
        break;
     
      // create wpa_supplicant ( do this only with "connect" button )
      case WIFI_MODE_CLIENT:
      //case WIFI_MODE_WISP_S:  
        //$wpaconf = getConfig( $interface, CONF_KEY_WPA )[1];
        //create_wpa_supplicant( $interface, $wpaconf ); // create wpa_supplicant
        break;
  }
  // create .network file  
  $ifconf = getConfig( $interface, CONF_KEY_NETWORK )[1];
  create_network_conf( $interface, $ifconf );   // create .network file

  return my_shell_exec ( "sudo " .WENECO_DIR. "/script/execute.sh apply_settings $device" );
}

// EXECUTE COMMANDS
/*
*   CALL EXECUTION FUNCTIONS HERE
*
*/
// GET COMMAND
if ( isset( $_REQUEST["command"] ) ){
  $cmd = getVal($_REQUEST, "command");
  switch ( $cmd ){
    case "reboot":
      $result = reboot_system();
      break;
    case "restart_network":
      $result = restart_network();
      break;
    case "restart_if":
      $result = restart_interface( REQ("ifname") );
      break;
    case "restart_dns":
      $result = restart_service( "dns", REQ("ifname") );
      break;  
    case "restart_wpa":
      $result = restart_service( "wpa", REQ("ifname") );
      break;  
    case "apply_settings":
      $result = apply_settings( REQ("ifname") );
      break;
    case "connect_wifi":
      $result = connect_wifi( REQ("ifname") , REQ("ssid"), REQ("psk") );
      break;
    case "reconnect_wifi":
      $result = reconnect_wifi( REQ("ifname") );
      break;
    case "disconnect_wifi":
      $result = disconnect_wifi( REQ("ifname") );
      break;
    case "query_dhcp":
      $result = query_dhcp( REQ("ifname") );
      break;
    default:
      $result = "UNKNOWN COMMAND '$cmd'";
      break;
  }

  // evaluate result
  $result = eval_result( $result );
  $result[1] = nl2br ( $result[1] );

  // CHECK FOR RESULT
  if ( $result[0] === true or $result[0] == "nodata" ) {
      header('Content-Type: application/json');
      print json_encode ( $result[1] );
  } else {
      header('HTTP/1.1 500 Internal Server Booboo');
      header('Content-Type: application/json; charset=UTF-8');
      #die( json_encode( array( 'message' => $result ) ) );
      print json_encode ( $result[1] );
  }    
}

?>