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
 * @version    0.1.1
 * @link       https://github.com/dawildde/WeNeCo
 */
session_start();

$web_root = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$server_root = dirname(__FILE__);

// includes
include_once( 'includes/secure.php' );
include_once( 'includes/config.php' );
include_once( 'includes/language.php' );
include_once( 'sites/themes.php' );
include_once( 'sites/dashboard.php' );
include_once( 'sites/networkconf.php' );
include_once( 'sites/construction.php' );
include_once( 'sites/system.php' );
include_once( 'sites/sys_authconf.php' );
include_once( 'sites/sys_logview.php' );
include_once( 'sites/sys_fileedit.php' );

$output = $return = 0;
if ( isset($_REQUEST['page']) ){
  $site = $_REQUEST['page'];
} else{
  $site = "";
}

// CSFR TOKEN
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = createCSFRToken();
}
$csrf_token = $_SESSION['csrf_token'];

// THEME
$theme_url = 'style/' . WENECO_THEME . '.css';

// VALIDATE AUTH
validateAuth();
?>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Web Network Configuration Portal</title>
    
    <!-- JQUERY -->
    <link rel="stylesheet" href="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="js/jquery.mobile-1.4.5/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <!-- ./jquery -->
    
    <!-- Theme CSS -->
    <link href="<?php echo $theme_url; ?>" title="main" rel="stylesheet">
    
    <!-- JS-SCRIPTS -->  
    <script src="js/js_php.php"></script>
    <script src="js/ajax.js"></script>
    <script src="js/global.js"></script>    
    <script src="js/system.js"></script>
    <script src="js/dashboard.js"></script> 
    <script src="js/networkconf.js"></script>     
    <script src="js/net_ifconf.js"></script>
    <script src="js/net_wificonf.js"></script>
    <script src="js/net_hostapd.js"></script>
    <script src="js/net_misc.js"></script>
    <!-- ./js -->
  </head>
  
  <body>
   <div data-role="page" id="container">
    <!-- Header -->
    <div id="top">
      <a class="logo" href="index.php">Logo</a>
    </div>
    <!-- ./header -->

    <!-- Navigation -->
    <div data-role="navbar" id="nav">
      <ul>
        <li>
          <a href="index.php?page=dashboard"><?php echo lang( "MENU", "LNK", "DASHBOARD" ); ?></a>
        </li>
        <li>
          <a href="index.php?page=netconf"><?php echo lang( "MENU", "LNK", "NETCONF" ); ?></a>
        </li>
        <li>
          <a href="index.php?page=system"><?php echo lang( "MENU", "LNK", "SYSTEM" ); ?></a>
        </li>
      </ul>
    </div>
    <!-- ./navbar -->

    <!-- Content Wrapper-->
    <div id="content">
       <?php
        switch ( $site ){
          case "dashboard":
            showDashboard();
            break;
          case "netconf":
            showNetConf();
            break;
          case "themes":
            showConstruction();
            break;
          case "system":
            showSystem();
            break;
          case "authconf":
            showAuthConf();
            break;
          case "logviewer":
            showLogViewer();
            break;
          case "fileedit":
            showFileEditor();
            break;
          default:
            showDashboard();
        };
       
       ?>
    </div>
    <!-- ./content wrapper -->
    
    <!-- Footer -->
    <div id="footer">
      WeNeCo V<?php echo file_get_contents ( "$server_root/.version" ); ?> by <a href="http://www.dawild.de">dawild.de</a>
    </div>
    <!-- ./footer -->
    </div>
    
    <!-- SCRIPT -->
    <script language="javascript">
    <!-- AJAX-Loader -->
    $(document).on({
        ajaxSend: function () { loading('show'); },
        ajaxStart: function () { loading('show'); },
        ajaxStop: function () { loading('hide'); },
        ajaxError: function () { loading('hide'); }
    });
    <!-- ./loader -->
    </script>
    <!-- ./script -->
  </body>
</html>