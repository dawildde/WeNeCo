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
*                         AUTHENTICATION VERIFICATION
*/

require_once( 'includes/config.php' );
require_once( 'includes/functions.php' );


// RETURN AUTH DATA
/*
*    return: array[0] = user
*            array[1] = pwd hash
*/
function getAuthData(){
  if ( is_file ( AUTH_FILE) ){
    $fauth_lines = file( AUTH_FILE );
    return $fauth_lines;
  } else {
    return array( "", "" );
  }
}

// VALIDATE AUTHENTIFICATION
function validateAuth( $redirect = true ){
  // read auth-file
  if ( is_file ( AUTH_FILE ) ){
      $php_user = getVal( $_SERVER, "PHP_AUTH_USER" );
      $php_pass = getVal( $_SERVER, "PHP_AUTH_PW" );
    
      $fauth_lines = getAuthData();
      $auth_user = $fauth_lines[0]; // Line 0 is user
      $auth_pass = $fauth_lines[1]; // Line 1 is password

      $user_ok = ( $auth_user == $php_user );
      $pwd_is_ok = password_verify( $php_pass, $auth_pass ) ;

      if ( $pwd_is_ok == true ){
          return true;

      } else {
          header('WWW-Authenticate: Basic realm="WeNeCo"');
          header('HTTP/1.0 401 Unauthorized');
          die ("Not authorized");
      }
  }
}

// Validate CSRF Token
function CSRFValidate() {
  if ( hash_equals($_POST['csrf_token'], $_SESSION['csrf_token']) ) {
      return true;
  } else {
      error_log('CSRF violation');
      return false;
  }
}

// CREATE CSFR Token
function createCSFRToken(){
  if (function_exists('mcrypt_create_iv')) {
      return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
  } else {
      return bin2hex(openssl_random_pseudo_bytes(32));
  }
}

// Add CSRF Token to form
function printCSRFToken() {
    return "<input id='csrf_token' type='hidden' name='csrf_token' value='" .$_SESSION['csrf_token']. "' />" ;
}
?>

