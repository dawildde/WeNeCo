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
*/

require_once "config.php";
require_once "functions.php";

$cmd = getVal($_REQUEST, "command");
$par = getVal($_REQUEST, "parameter");

// EXECUTE COMMANDS
switch ( $cmd ){
  case "reboot":
    $result = shell_exec( "sudo " .WENECO_DIR . "/script/execute.sh reboot" );
    break;
  case "restart_network":
    $result = shell_exec ( "sudo " .WENECO_DIR . "/script/execute.sh restart_network" );
    break;
  case "apply_settings":
    $result = shell_exec ( "sudo " .WENECO_DIR. "/script/execute.sh apply_settings $par" );
    break;
  default:
    $result = "UNKNOWN COMMAND";
    break;
}

$result = nl2br ( $result );

// CHECK IF LAST 2 LETTERS OF RETURN = "OK"
if ( substr( $result, -2 ) == "OK" ) {
    header('Content-Type: application/json');
    print json_encode ( $result );
} else {
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    #die( json_encode( array( 'message' => $result ) ) );
    print json_encode ( $result );
}    


?>