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


// WRITE NETWORK CONFIGURATION
 /*   parameter:
 *      data - JSON-OBJECT with network-data
 *    return:
 *      True / False
 */
function write_network_conf($data){
  $result["success"] = False; // SET SUCCESS TO FALSE
  
  // FIND ALL *.network-FILES
  foreach ( glob ( WENECO_DIR  . "/network/*.network" ) as $file) {
    // PARSE INI FILE
    $ini_content = parse_my_ini_file ( $file, True );
    
    // IF THE INTERFACE-FILE
    if ( $ini_content["Match"]["Name"] == $data["name"] ){
        $target_file = $file;
        // COPY TEMPLATE
        exec ("cp " .WENECO_DIR. "/config/template.network $target_file.temp"  );
        
        // BUILD ARRAY
        $nw_data = array();
        $nw_data["Match"] = array( "Name" => $data["name"] );
        
        if ( $data["ipmode"] == "DHCP" ){
            $nw_data["Network"] = array( "dhcp" => "yes" );
        } else {
            $cidr = netmask2cidr($data["netmask"]);
            $nw_data["Network"] = array(
                "Address" => $data["ipv4"] ."/". $cidr
                ,"Gateway" => $data["gateway"]
                ,"DNS" => array( $data["dns1"], $data["dns2"] )
            );
        }

        // WRITE FILE
        $fp = fopen("$target_file.temp", "a");
        fwrite($fp, arr2ini( $nw_data ));
        fclose($fp);
        
        // OVERWRITE ORIGINAL TARGET FILE
        exec (" mv $target_file.temp $target_file" );
        
        $result["success"] = true;
        $result["data"] = "";
    }
  }
  return $result;
  
  
}

 // CALL FUNCTION BY AJAX
 $fn = getVal($_REQUEST, "fn");
 $par = getVal($_REQUEST, "par");
 if ( function_exists($fn)) {
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
   print json_encode( "UNKNOWN FUNCTION CALL '$fn'"  );
 }
 ?>