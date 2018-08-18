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
 *                                  GET NETWORK-DATA
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
 require_once "functions.php";
 
 // GET NETWORK INTERFACES
 /*   
 *    return all interfaces in system
 *    automaticly checks for device_config_file or creates them
 *    parameter:
 *      filter devicetype ( wireless | wired )
 *    return: 
 *      list of interfaces
 */
 function getInterfaces( $filter = Null ){
    $return = array( false, "");
    $out = my_shell_exec("/bin/ls -I lo /sys/class/net");
    if ( $out ){
        $list = explode("\n", $out);
        $ret = array();
        foreach( $list as $interface ){
            getDeviceNo( $interface );  // check for config
            if ( $filter == "wireless" ){
                if ( is_dir( "/sys/class/net/$interface/wireless" ) ) {
                  array_push( $ret, $interface );
                }
            } elseif( $filter == "wired" ){
                if ( ! is_dir( "/sys/class/net/$interface/wireless" ) ) {
                  array_push( $ret, $interface );
                }  
            } elseif ( $filter == Null ){
              array_push( $ret, $interface );
            }
        }
        $return[0] = true;
        $return[1] = array_filter($ret);
        
        return $return;
    }
 }
 
 // GET NETWORK IFCONFIG
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      json object with interface data (IP-CONFIG)
 */
 function get_if_data( $interface ){
    $return = array( false, "");
    $out = my_shell_exec("/sbin/ifconfig $interface");   

    $data = [];
    $data["name"] = $interface;
    $data["type"] = getIFType( $interface );
    $data["ipv4"] = preg_find("/inet (\d*.\d*.\d*.\d*)/m", $out);
    $data["netmask"] = preg_find("/netmask (\d*.\d*.\d*.\d*)/m", $out);
    $data["ipv6"] = preg_find("/inet6 (\w*(::|:)\w*:\w*:\w*:\w*)/m", $out);
    $data["mac"] = preg_find("/ether (\w*:\w*:\w*:\w*:\w*:\w*)/m", $out);
    $data["rx_bytes"] = preg_find("/RX\s*packets\s*\d*\s*bytes\s*(\d*)/m", $out);
    $data["tx_bytes"] = preg_find("/TX\s*packets\s*\d*\s*bytes\s*(\d*)/m", $out);
    $data["rx_kib"] = round( $data["rx_bytes"] / 1024, 1 );
    $data["tx_kib"] = round( $data["tx_bytes"] / 1024, 1 );
    $data["rx_mib"] = round( $data["rx_bytes"] / 1048576, 2 );
    $data["tx_mib"] = round( $data["tx_bytes"] / 1048576, 2 );
    $data["dns"] = getDNS( $interface )[1];
    $data["gw"] = getGateway ( $interface )[1];
    
    $return[0] = true;
    $return[1] = $data;
    return $return;
 }
 
 // GET DNS-SERVERs OF INTERFACE
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      array of DNS-Server IP addresses
 */
 function getDNS( $interface ){
    $out = my_shell_exec("systemd-resolve --status");
    $out .= "\n(END)"; // APPEND (END) FOR REGEX 
    $ret = strstr( $out, "($interface)");  // find interface name
    $ret = str_replace( "($interface)", "" , $ret ); // remove interface name 
    $ret = strstr( $ret, "(", True);  // find & remove next interface
    preg_match_all( "/([0-9]{1,3}\.){3}[0-9]{1,3}/m", $ret, $ips ); // filter ip addresses
    
    return array( true, $ips[0] );
 }
 
 // GET DEFAULT GATEWAY
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      IP-Address of default gateway
 */
 function getGateway( $interface ){
    $return = array( false, "");
    $out = my_shell_exec("ip -4 route list type unicast dev $interface exact 0/0");
    //$ret = strstr( $out, "src", True);  // find & remove src
    $found = preg_match( "/default via (([0-9]{1,3}\.){3}[0-9]{1,3})/m", $out, $ips ); // filter ip address
    if ( $found ){
      $return[1] = $ips[1];
    }
    return $return;
 }
 
 // SCAN WIFI
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      list of found networks
 */
 function scan_wifi( $interface, $timeout ){
    $return = array( false, "");
    $deviceNo = getDeviceNo( $interface );
    
    $result = my_shell_exec ( "sudo " .WENECO_DIR. "/script/wpa_supplicant.sh scan -t $timeout $deviceNo" );
    $result = explode( "\n", $result ); 
    
    // SET RESULT TO TRUE HERE
    if ( substr( $result[0], -2 ) == "OK" ){
        if ( count( $result ) > 3 ){
            for ( $i = 2; $i < count( $result ); $i++) {
                $line = $result[ $i ];
                
                $cols = explode ( "\t", $line );          // Get LineColumnData
                $nw_conf = empty_wifi_conf();             // Get empty WiFi Conf 
                
                // bssid (cosl[0]) has to been set
                if ( ! getVal( $cols, 0) == "" ){
                    // Fill Data
                    $nw_conf["bssid"] = getVal( $cols, 0);
                    $nw_conf["frequency"] = getVal( $cols, 1);
                    $nw_conf["channel"] = Freq2Channel( getVal( $cols, 1, 0 ) );
                    $nw_conf["level"] = getVal( $cols, 2);
                    $nw_conf["flags"] = getVal( $cols, 3);
                    $nw_conf["security"] = flags2security( getVal( $cols, 3) );
                    $nw_conf["ssid"] = getVal( $cols, 4);
                    $nw_conf["description"] = getVal( $cols, 5);
                    $data[] = $nw_conf;       // Push to Array  
                }
                
            }
            $return[0] = true;
            $return[1] = $data;
        } else {
            $return[0] = "nodata";
            $return[1] = "";
        }
    } else {
        $return[1] = $result[0];
    }
    return $return;
 }
 
 // CREATE PSK
 /*   parameter:
 *      opt - ARRAY with:
 *          "ssid" : - ssid of network
 *          "psk" : - psk in clreartext   
 *    return:
 *      ARRAY - Object with NetworkConf 
 *          "ssid" : - ssid of network
 *          "#psk" : - psk in clreartext  
 *          "psk" : - crypted psk
 */
 function wpa_pass( $ssid, $psk ){
    $result = my_shell_exec ( "wpa_passphrase $ssid $psk" );
    $network = str_replace( "network={","", $result );
    $network = str_replace( "}","", $network );
    $network = trim( $network );
    $content = preg_split('/\s+/', $network);
    if ( count ( $content ) == 3 ){
      $data["ssid"] = substr( $content[0], strpos( $content[0], "=" ) + 1 );
      $data["#psk"] =  substr( $content[1], strpos( $content[1], "=" ) + 1 );
      $data["psk"] =  substr( $content[2], strpos( $content[2], "=" ) + 1 );
    
      $return[0] = true;
      $return[1] = $data;
    } else {
       $return[1] = $result;
    }
    return $return;   
    
    print $result;
 }
 
 // GET WIFI STATUS
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      object with wifi state
 */
 function getWiFiState( $interface ){
  $return = array( false, "");
  $out = shell_exec( "iwconfig $interface" );
  $ssid = preg_find( "/ESSID:\"(.*)\"/", $out );
  
  if ( $ssid == "off/any" or $ssid == "" ){
      $ret["ssid"] = "";
      $ret["connected"] = false;
  } else {
      $ret["ssid"] = $ssid;
      $ret["connected"] = true;
  }
  $ret["bitrate"] = "ToDo";
  $ret["level"] = "ToDo";
  $ret["frequency"] = "ToDo";
  $ret["channel"] = "ToDo";
  $ret["quality"] = "ToDo";
  
  $return[0] = true;
  $return[1] = $ret;
  return $return;
 }
 ?>