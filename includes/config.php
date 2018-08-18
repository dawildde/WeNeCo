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

/* LOG_LEVEL:
*     logging level
*       0 -> disabled
*       +1 -> log executed commands ( shell-exec )
*       +2 -> log execution results ( full return if failed )
*       +4 -> log execution results ( full return if success )
*
*/

// GENERAL
define( 'WENECO_LNG', 'en_EN' );
define( 'WENECO_THEME', 'default' );
define( 'WENECO_DIR', '/etc/weneco' );
define( 'TMP_DIR', '/tmp/weneco' ); 
define( 'AUTH_FILE', WENECO_DIR."/weneco.auth" );
define( 'AUTH_USER', "admin" );
define( 'IP4_PATTERN', '((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$');
define( 'LOG_LEVEL', 7 );

//  WIFI SETTINGS
define( 'WIFI_SCAN_TIMEOUT', 3 );
define( 'WIFI_COUNTRY', "DE" );
define( 'WIFI_DRIVER', "wext" );
define( 'WIFI_HAPD_MODES', [ "a","b","g","ad","a/g" ] ) ;

// SECTION KEYS IN DEVICEx.CONFIG
define( 'CONF_KEY_GENERAL', "general");
define( 'CONF_KEY_WPA', "wpa_supplicant");
define( 'CONF_KEY_AP', "hostapd");
define( 'CONF_KEY_NETWORK', "networkd");
define( 'CONF_KEY_FIREWALL', "firewall");

// LOG SETTINGS
define( 'LOG_F_WENECO', TMP_DIR."/weneco.log" );
define( 'LOG_F_WPA', "/var/log/daemon.log" );
define( 'LOG_F_AP', "/tmp/hostapd.log" );
define( 'LOG_F_DNSMASQ', "/tmp/dnsmasq.log" );
define( 'LOG_F_SYSLOG', "/var/log/syslog" );
define( 'LOG_F_LHTTPD', "/var/log/lighttpd/error.log" );

// TEXT EDITOR SETTINGS
//   TXT_D_xxx - all files of directory
//   TXT_F_xxx - simple file
define( 'TEXT_D_WENECO', WENECO_DIR."/network/" ); 

// INTERFACE MODES
define( 'WIRED_MODE_WAN', "wan" );
define( 'WIRED_MODE_CLIENT', "lan_client" );
define( 'WIFI_MODE_AP', "wifi_ap" );
define( 'WIFI_MODE_CLIENT', "wifi_client" );
//define( 'WIFI_MODE_WISP_M', "wisp_m" );
//define( 'WIFI_MODE_WISP_S', "wisp_s" );

// CREATE TEMP DIR
if ( ! is_dir( TMP_DIR ) ){
  if ( ! mkdir( TMP_DIR, 0777, true ) ) {
    die("COULD NOT CREATE TEMP_DIR '" .TMP_DIR. "'");
  }
}
?>