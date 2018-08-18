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
*                               READ FILE
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

/*************************************************
*
*           READ OWN CONFIG-FILE
*
**************************************************/

//  READ CONFIG-FILE
 /*   parameter:
 *      interface - name of interface
 *      section - section witch will be returned
 *    return:
 *      DICTIONARY
 */
 function getConfig( $interface, $section=null ){
    $return = array( false, "" );
    $device = getDeviceNo ( $interface );
    $file = WENECO_DIR  ."/network/$device.conf";
    
    // CHECK IF CONFIG EXISTS IN CONFIGFILE
    checkConfFile( $file, $interface, $section );
    
    $content = file_get_contents ( $file );
    $data = json_decode( $content, true );  
    
    // RETURN DATA
    if ( $section != null ){
      if( isset( $data[$section] )){
        $return[0] = true;
        $return[1] = $data[$section];
      } else {
        $return[0] = "nodata";
      }
    } else {
      $return[0] = true;
      $return[1] = $data;
    }
    return $return;
 }

 //  CHECK CONFIG-FILE
 /*   checks if key of section exists in config
 *    if section is null it checks for all keys
 *    creates them if not
 *    parameter:
 *      interface - name of interface
 *      section - section witch will be checked
 *    return:
 *      DICTIONARY
 */
 // CHECK OWN CONFIGURATION FILE
function checkConfFile( $file, $interface, $section = null ){
    $return = array( false, "" );
    
    $content = file_get_contents ( $file );
    $data = json_decode( $content, true );
     // is_file must be true because file is createt in 'getDeviceNo' if it's not existing
    if ( is_file ( $file ) ){
      // Check Configration file
      if ( $section == null){
          // LOOP THROUGH CONFIG KEYS
          $constants = get_defined_constants( true );
          copySystemConfFiles( $interface ); // COPY system_files 2 temp
          foreach ( $constants["user"] as $key => $val ) {
            if ( substr( $key, 0, 9 ) == "CONF_KEY_" ){
              if ( isset( $data[$val] ) === false ) {
                // read system files
                readSystemConfFiles( $interface, $val );
              }
            }
          }
      } else { 
          if ( isset( $data[$section] ) === false ) {
            // read system files
            copySystemConfFiles( $interface ); // COPY system_files 2 temp
            readSystemConfFiles( $interface, $section );
          }
      }
    $return[0] = true;
    $return[1] = "";
    }
    return $return;
}
 
 // GET_FILECONTENT
 /*   parameter:
 *      file - file to be read
 *    return:
 *      [1] - string with text
 */
 function read_content( $file, $lines = 0 ){
    $return = array( false, "" ); // = "nodata";

    if ( is_file( $file ) ){
      if ( $lines > 0 ){ 
        // READ FILE WITH TAIL
        ob_start();
        passthru( 'tail -'  . $lines . ' ' . escapeshellarg( $file ));
        $data = trim( ob_get_clean() );
        
        $return[0] = true;
        $return[1] = $data;
      } else {
        // READ FULL FILE
        $data = file_get_contents( $file );
        $return[0] = true;
        $return[1] = $data;
      }
      
    }
    return $return;
 }
 
/*************************************************
*
*           READ SYSTEM-CONFIG FILES
*
**************************************************/

// READ SYSTEM CONFI FILES
 /*   
 *    reads system-config files into own config file
 *    parameter:
 *      interface - name of interface
 *      section - key of section
 *    return:
 *      DICTIONARY
 */
function readSystemConfFiles( $interface, $section ){
    $return = array( false, "" );
    $device = getDeviceNo ( $interface );
    $file = WENECO_DIR  . "/network/$device.conf";
    
    // read sysfile by section 
    switch( $section ){
      case CONF_KEY_WPA:
          // WPA-SUPPLICANT
          $sys_file = TMP_DIR ."/wpa_supplicant-$interface.conf";
          $ret = read_wpa_supplicant( $sys_file );
          return write2conf ( $file, CONF_KEY_WPA, $ret[1] );    // write 2 config
          break;
      case CONF_KEY_AP:
          // HOST-APD
          $sys_file = TMP_DIR ."/hostapd-$interface.conf";
          $ret = read_hostapd( $sys_file );
          return write2conf ( $file, CONF_KEY_AP, $ret[1] );    // write 2 config
          break;
      case CONF_KEY_NETWORK:
          // SYSTEMD-NETWORK
          $sys_file = TMP_DIR ."/$interface.network";
          $ret = read_network_conf( $sys_file );
          return write2conf ( $file, CONF_KEY_NETWORK, $ret[1] );    // write 2 config
          break;
      default:
          return array( false, "UNKNOWN CONf_KEY '$section'" );
    }
}


// READ NETWORK CONFIGURATION- FILE (.network)
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      DICTIONARY
 */
function read_network_conf( $file ){
    $result = array( false, "");
  
    // PARSE INI FILE
    $data = parse_my_ini_file ( $file, True );
    
    $nw_conf = empty_nw_conf(); // create empty config
      
    // DNS has to be an array
    if ( ! isset ($data["Network"]["DNS"]) || ! is_array( $data["Network"]["DNS"] ) ){
      $tmp[0] = getVal( $data["Network"], "DNS" );
      $tmp[1] = "";
      $data["Network"]["DNS"] = $tmp;
    }
      
    // MERGE ARRAYs
    $ip = getVal( $data["Network"], "Address" );
    $ip = cidrIP2Netmask ( $ip );
    $nw_conf["name"] = getVal( $data["Match"], "Name" );
    $nw_conf["ipv4"] = $ip[0];
    $nw_conf["ipv6"] =  getVal( $data["Network"], "IPV6" );
    $nw_conf["netmask"] = $ip[1];
    $nw_conf["gw"] =  getVal( $data["Network"], "Gateway" );
    $nw_conf["dns"] = $data["Network"]["DNS"];
    $nw_conf["mac"] =  getVal( $data["Network"], "HW-Address" );
    $nw_conf["dhcp"] =  getVal( $data["Network"], "dhcp" );
    $nw_conf["description"] =  getVal( $data["Network"], "Description" );
      
    $result[0] = true;
    $result[1] = $nw_conf;
    
  return $result;
}

// READ HOSTAPD CONFIGURATION- FILE (hostapd.conf)
 /*   parameter:
 *      file - file to be read
 *    return:
 *      DICTIONARY
 */
function read_hostapd( $file ){
  $return = array( false, "" ); // = "nodata";
  if ( is_file( $file ) ){
      // PARSE INI FILE
      $data = parse_my_ini_file ( $file );
      
      // SECURITY HAS TO BE CONVERTED
      $data["security"] = "";//"HAS TO BE PARSED HERE"
      
      $return[1] = $data;
  }
  return $return;
}

// READ WPA-SUPPLICANT
 /*   parameter:
 *      file - file to be read
 *    return:
 *      Dictionary of known networks in WPA-SUPPLICANT
 */
 function read_wpa_supplicant( $file ){
      $return = array( false, "" ); // = "nodata";
    
      if ( is_file( $file ) ){
        $content = file_get_contents( $file );
        // find network configurations
        preg_match_all("/network={(.|\n|\r)*?}/m", $content, $networks );
        // loop through network configurations
        foreach( $networks[0] as $network ){
          $network = str_replace( "network={","", $network );
          $network = str_replace( "}","", $network );
          // put configurations to array
          $data[] = parse_ini_string( $network, false, INI_SCANNER_RAW );   
        }
        $return[0] = true;
        if ( isset( $data ) ){
          $return[1] = $data;
        }
      }
      return $return;
 }
 
