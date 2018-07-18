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
*                            My CUSTOM INI PARSER
*/

// PARSING INI FILE
/*
*  Parses INI-File into Array-Object
*   as opposed to PHP "parse_ini_file()" function it could handle the same key multiple times (put into array)
*   it also can handle '#' and ';' comments  
*
*   parameters:
*     file - (string) path to ini-file
*     groups - (bool) - handle groups (True) or ignore them (False)
*
*   returns:
*     Array-Object with INI-content
*/
function parse_my_ini_file($file, $groups = False){
    $arr = array();
    $ret = array();
    $handle = fopen($file, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if(!$line || $line[0] == "#" || $line[0] == ";") { continue; }  // IGNORE COMMENTS
            // ADD GROUPS
            if ( $groups == True && $line[0] == "[") {
              $group = substr( $line, 1, strpos( $line, "]" ) - 1 ) ;
              continue;
            }
            //PARSE LINE
            $parsed = parse_ini_string($line);
            if(empty($parsed)){ continue; }
            $key = key($parsed);
            if(isset($arr[$key])){
                if(!is_array($arr[$key])){
                    $tmp = $arr[$key];
                    $arr[$key] = array($tmp);
                }
                $arr[$key][] = $parsed[$key];
            }else{
                $arr[$key] = $parsed[$key];
            }
            if ( isset ( $group ) ){
              $ret[$group] = $arr;
            } else {
              $ret = $arr;
            }
        }
        fclose($handle);
        return $ret;
    } else {
        die ("CANNOT OPEN FILE '" + $file + "'");
    } 
}

// ARR2INI
/*  
*  PASRSING ARRAY INTO INI-FORMAT
*   can only parse 1 level at moment, but it can handle multiple same keys
*
*     parameters:
*       data - array ( "section1" => 
*                         array( "val1" => value, "val2" => "second" ),
                       "section2" => array( "key1" => "val33" )
*     return:
*       string in ini-format
*/
function arr2ini(array $data){
    $output = '';
 
    foreach ($data as $section => $values)
    {
        //values must be an array
        if (!is_array($values)) {
            continue;
        }
 
        //add section
        $output .= "[$section]".PHP_EOL;
 
        //add key/value pairs
        foreach ($values as $key => $val) {
            if ( is_array($val) ){
              foreach ( $val as $subkey => $subval ){
                  if ( is_int( $subkey ) ){
                    // MULTIPLE SAME ENTRY
                    $output .= "$key=$subval".PHP_EOL;
                  } else {
                    // ARRAY WITH ANOTHER SECTION
                    // HANDLE HERE
                    $output .= "$subkey=$subval".PHP_EOL;
                    // !!!!!!!!!!!!!
                  }
              }
            } else {
              $output .= "$key=$val".PHP_EOL;
            }
        }
        $output .= PHP_EOL;
    }
 
    //write data to file
    return $output;
}
?>