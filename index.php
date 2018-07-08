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
 * Enables use of simple web interface using systemd
 * lighttpd (I have version 1.4.31-2 installed via apt)
 * php5-cgi (I have version 5.4.4-12 installed via apt)
 * along with their supporting packages, php5 will also need to be enabled.
 * 
 * @author     Christian Wild <christian@dawild.de>
 * @license    GNU General Public License, version 3 (GPL-3.0)
 * @version    0.0.1
 * @link       https://github.com/dawildde/WeNeCo
 */
session_start();

include_once( 'includes/config.php' );
include_once( 'includes/language.php' );
include_once( 'sites/themes.php' );
include_once( 'sites/dashboard.php' );
include_once( 'sites/construction.php' );

$output = $return = 0;
if ( isset($_GET['page']) ){
  $site = $_GET['page'];
} else{
  $site = "";
}

// CSFR TOKEN
if (empty($_SESSION['csrf_token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['csrf_token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$csrf_token = $_SESSION['csrf_token'];

// THEME
$theme_url = 'style/' . WENECO_THEME . '.css';
?>


<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Web Network Configuration Portal</title>

    <!-- Theme CSS -->
    <link href="<?php echo $theme_url; ?>" title="main" rel="stylesheet">
  </head>
  
  <body>
   <div id="container">
    <!-- Header -->
    <div id="header">
      <a class="logo" href="">Logo</a>
    </div>
    <!-- ./header -->

    <!-- Navigation -->
    <div id="nav">
      <ul>
        <li>
          <a href="index.php"><?php echo lang('DASHBOARD_LINK'); ?></a>
        </li>
        <!--
        <li>
          <a href="index.php?page="><?php echo lang('WIFI_CLIENT_LINK'); ?></a>
        </li>
        -->
      </ul>
    </div>
    <!-- ./navbar -->

    <!-- Content Wrapper-->
    <div id="content">
       <?php
        switch ( $site ){
          case "themes":
            showThemeConfig();
            break;
          default:
            //showDashboard();
            showConstruction();
        };
       
       ?>
    </div>
    <!-- ./content wrapper -->
    
    <!-- Footer -->
    <div id="footer">
      
    </div>
    <!-- ./footer -->
    </div>
  </body>
</html>