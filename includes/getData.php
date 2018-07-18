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
 */
 require_once "functions.php";
 
 // GET NETWORK INTERFACES
 /*   return: 
 *      list of interfaces
 */
 function getInterfaces(){
    $out = shell_exec("/bin/ls -I lo /sys/class/net");
    if ($out){
      $ret = explode("\n", $out);
      $ret = array_filter($ret);
      return $ret;
    }
 }
 
 // GET NETWORK IFCONFIG
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      json object with interface data (IP-CONFIG)
 */
 function getIFdata($interface){
    $out = shell_exec("/sbin/ifconfig $interface");   

    $data = [];
    $data["name"] = $interface;
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
    $data["dns"] = getDNS( $interface );
    $data["gw"] = getGateway ( $interface );

    
    return json_encode ( $data );
 }
 
 // GET DNS-SERVERs OF INTERFACE
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      array of DNS-Server IP addresses
 */
 function getDNS($interface){
    $out = shell_exec("systemd-resolve --status");
    $out .= "\n(END)"; // APPEND (END) FOR REGEX 
    $ret = strstr( $out, "($interface)");  // find interface name
    $ret = str_replace( "($interface)", "" , $ret ); // remove interface name 
    $ret = strstr( $ret, "(", True);  // find & remove next interface
    preg_match_all( "/([0-9]{1,3}\.){3}[0-9]{1,3}/m", $ret, $ips ); // filter ip addresses
    return $ips[0];
 }
 
 // GET DEFAULT GATEWAY
 /*   parameter:
 *      interface - name of interface
 *    return:
 *      IP-Address of default gateway
 */
 function getGateway($interface){
    $out = shell_exec("ip -4 route list type unicast dev $interface exact 0/0");
    //$ret = strstr( $out, "src", True);  // find & remove src
    $found = preg_match( "/default via (([0-9]{1,3}\.){3}[0-9]{1,3})/m", $out, $ips ); // filter ip address
    if ( $found ){
      return $ips[1];
    }
 }
 
 
 // CALL FUNCTION BY AJAX
 $fn = "";
 $par = "";
 if ( isset ( $_GET["fn"] ) ) {
   $fn = $_GET["fn"];
   if ( isset ( $_GET["par"] ) ){
     $par = $_GET["par"];
   }
   header('Content-Type: application/json');
   print $fn($par);
 }
 ?>