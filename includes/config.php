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
*                            PHP Configuration-File
*/

//define('WENECO_LNG','de_DE');
define('WENECO_LNG','en_EN');
define('WENECO_THEME','default');
define('WENECO_DIR','/etc/weneco'); 
define('TMP_DIR','/tmp/weneco'); 
define('AUTH_FILE', WENECO_DIR."/weneco.auth");
define('AUTH_USER', "admin");

define('IP4_PATTERN', '((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$');

// CREATE TEMP DIR
if ( ! is_dir( TMP_DIR ) ){
  if ( ! mkdir( TMP_DIR, 0777, true ) ) {
    die("COULD NOT CREATE TEMP_DIR '" .TMP_DIR. "'");
  }
}
?>