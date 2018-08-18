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
*                            PHP Language Parser
*/


// SET DEFAULT LANGUAGE IF FILE NOT EXISTS
$lngFile = "$server_root/lang/"+ WENECO_LNG +".php";
if ( file_exists($lngFile)){
  require_once( $lngFile );
} else {
  require_once( "$server_root/lang/en_EN.php" );
};

// RETURNS LANGUAGE STRING IF EXISTS
function lang( $section, $type, $str){
  global $_L;

  if ( isset( $_L[$section][$type][$str] ) ){
    return $_L[$section][$type][$str];
  } else {
    return $section ."_". $type ."_". $str;
  }
}

// RETURN JSON OBJECT
function lang_json(){
  global $_L;
  
  return json_encode( $_L );
}
?>