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
 *                               TEMPLATE
 */

 include_once( 'includes/config.php' );
 require_once( 'includes/getData.php' );
 
function showClientConf(){
        ?>
          <!-- KNOWN NETWORKS -->
          <div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-iconpos="right" data-collapsed="false" id="div_known">
            <h3><?php echo lang( "WPACONF", "TXT", "HDR_KNOWN_NW" );?></h3>
            <table data-role="table" class="ui-responsive" id="tblKnownWiFi">
              <thead>
                <th><?php echo lang( "GLOBAL", "WIFI", "SSID" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "DESCRIPTION" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "BSSID" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "SECURITY" );?></th>
                <th></th>
                <th></th>
                <th></th>
              </thead>
              <tbody>
              </tbody>
            </table>
            <input type="button" id="manAdd" name="manAdd" value="<?php echo lang( "WPACONF", "BTN", "ADD_MAN" );?>" />
          </div>
          <!-- ./known_networks -->
          
          <!-- NETWORK SCAN -->
          <div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-iconpos="right" data-collapsed="false" id="div_scanList"/>
            <h3><?php echo lang( "WPACONF", "TXT", "HDR_SCAN" );?></h3>
            <input type="button" id="cmdScan" name="rescan" value="<?php echo lang( "WPACONF", "BTN", "SCAN" );?>" />
            <table data-role="table" class="ui-responsive" id="tblScannedWiFi">
              <thead>
                <th><?php echo lang( "GLOBAL", "WIFI", "SSID" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "BSSID" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "SECURITY" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "FREQ" );?></th>
                <th><?php echo lang( "GLOBAL", "WIFI", "LVL" );?></th>
                <th></th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <!-- ./network_scan -->
          
          <!-- POPUP-PSK -->
          <div data-role="popup" id="dlgPsk" data-overlay-theme="a" data-theme="a" data-dismissible="false">
            <form id="frm_wifi_man_conf">
              <div data-role="header" data-theme="a">
                <h1><?php echo lang( "WPACONF", "TXT", "HDR_MANCONF" );?></h1>
              </div>
              <div role="main" class="ui-content">
                <label for="man_ssid"><?php echo lang( "GLOBAL", "WIFI", "SSID" );?></label>
                  <input type="text" name="man_ssid" id="man_ssid" value="" placeholder="<?php echo lang( "GLOBAL", "WIFI", "SSID" );?>" />
                  <!-- KEY_MGMT -->
                  <label for="man_security" class="select"><?php echo lang( "WPACONF", "LBL", "KEY_MGMT" );?></label>
                  <select name="man_security" id="man_security">
                    <option value="" selected></option>
                    <option value="open" selected>NONE (open)</option>
                    <option value="WEP" selected>WEP</option>
                    <option value="WPA" selected>WPA</option>
                    <option value="WPA2 (TKIP)" selected>WPA & WPA2</option>
                    <option value="WPA2 (CCMP)" selected>WPA2</option>
                  </select>
                  <!-- ./keymgmt -->
                <label for="man_clear_psk"><?php echo lang( "GLOBAL", "WIFI", "PASS" );?></label>
                  <input type="text" name="man_clear_psk" id="man_clear_psk" value="" placeholder="<?php echo lang( "GLOBAL", "WIFI", "PASS" );?>" />
                <label for="man_id_str"><?php echo lang( "GLOBAL", "LBL", "DESCRIPTION" );?></label>
                  <input type="text" name="man_id_str" id="man_id_str" value="" placeholder="<?php echo lang( "GLOBAL", "LBL", "DESCRIPTION" );?>" />
                 <label for="chkAutoConnect"><?php echo lang( "WPACONF", "LBL", "AUTO_CONNECT" );?></label>
                  <input type="checkbox" name="chkAutoConnect" id="chkAutoConnect" value="auto" checked="checked" />
                <!--
                <label for="man_chkSave"><?php echo lang( "WPACONF", "LBL", "SAVE_CONF" );?></label>
                  <input type="checkbox" name="man_chkSave" id="man_chkSave" value="save" />
                -->
                <!-- HIDDEN FIELDS-->
                <input type="hidden" name="man_crypt_psk" id="man_crypt_psk" value="" />
                <input type="hidden" name="man_bssid" id="man_bssid" value="" />
                <!-- ./hidden fields --> 
                <input type="button" id="man_save" name="man_save" value="<?php echo lang( "GLOBAL", "BTN", "SAVE" );?>" />
                <!--
                <input type="button" id="man_connect" name="man_connect"
                    value="<?php echo lang( "GLOBAL", "BTN", "CONNECT" );?>" />
                -->
                <input type="button" id="man_cancel" name="man_cancel" value="<?php echo lang( "GLOBAL", "BTN", "CANCEL" );?>" />
              </div>
            </form>
          </div>
          <!-- ./popup-psk -->
    <?php
}
?>