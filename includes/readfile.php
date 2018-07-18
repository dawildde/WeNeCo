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
*/

require_once "config.php";
require_once "iniparser.php";
require_once "functions.php";


// READ NETWORK CONFIGURATION
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      JSON-OBJECT
 */
function read_network_conf($interface){
  $result["success"] = False;
  
  // FIND ALL *.network-FILES
  foreach ( glob ( WENECO_DIR  . "/network/*.network" ) as $file) {
    // PARSE INI FILE
    $data = parse_my_ini_file ( $file, True );
    
    // FILL DATA ARRAY
    if ( $data["Match"]["Name"] == $interface ){
        //print_r ($data);
        //exit();
      
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
      $nw_conf["name"] = $interface;
      $nw_conf["ipv4"] = $ip[0];
      $nw_conf["ipv6"] =  getVal( $data["Network"], "IPV6" );
      $nw_conf["netmask"] = $ip[1];
      $nw_conf["gw"] =  getVal( $data["Network"], "Gateway" );
      $nw_conf["dns"] = $data["Network"]["DNS"];
      $nw_conf["mac"] =  getVal( $data["Network"], "HW-Address" );
      $nw_conf["dhcp"] =  getVal( $data["Network"], "DHCP" );
      
      $result["success"] = true;
      $result["data"] = $nw_conf;
      return $result;
      break;
    }
  }
}


  // CALL FUNCTION BY AJAX
 $fn = "";
 $par = "";
 if ( isset ( $_REQUEST["fn"] ) ) {
    $fn = $_REQUEST["fn"];
    if ( isset ( $_REQUEST["par"] ) ){
       $par = $_REQUEST["par"];
    }
    // EXECUTE FN
    $result = $fn($par);
    
    if ( $result["success"] == true ){
        header('Content-Type: application/json');
        print json_encode( $result["data"] );
    } else {
        header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json; charset=UTF-8');
        print json_encode( $result["data"] );
    }
 } else {
   // UNKNOWN FUNCTION
   header('HTTP/1.1 500 Internal Server Booboo');
   header('Content-Type: application/json; charset=UTF-8');
 }