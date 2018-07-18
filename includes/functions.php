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
 
 // RETURN STRING FROM REGEX
 function preg_find($regex, $string){
    $count = preg_match($regex, $string, $match);
    if ($count > 0){
      return $match[1];
    }
 }
 
 // RETURN DEFAULT VAL IF IS UNSET
 function getVal($arr, $key, $default = ""){
   if ( isset ( $arr[ $key ] ) ) {
      return $arr[ $key ];
   } else {
      return $default;
   }
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