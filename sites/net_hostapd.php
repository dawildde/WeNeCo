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

 
function showHostapd(){
    ?>
      <!-- HOSTAPD FORM -->
      <form id="hapdform" action="" class="ui-body ui-body-a ui-corner-all">
          <div data-role="fieldcontain" id="div_ipMode">
              <label for="hapd_ssid"><?php echo lang( "GLOBAL", "WIFI", "SSID" );?></label>
                  <input type="text" name="hapd_ssid" id="hapd_ssid" value=""
                      placeholder="<?php echo lang( "GLOBAL", "WIFI", "SSID" );?>"  pattern=".{1,}" />
              <!-- KEY_MGMT -->
              <label for="hapd_security" class="select"><?php echo lang( "WPACONF", "LBL", "KEY_MGMT" );?></label>
                  <select name="hapd_security" id="hapd_security" onchange="" required>
                    <option value="" selected></option>
                    <!--
                    THIS SHOULD NOT BE ENABLED
                    <option value="open" selected>NONE (open)</option>
                    <option value="WEP" selected>WEP</option>
                    <option value="WPA" selected>WPA</option>
                    -->
                    <option value="WPA2 (TKIP)" selected>WPA & WPA2</option>
                    <option value="WPA2 (CCMP)" selected>WPA2</option>
                  </select>
              <!-- ./keymgmt -->
              <label for="hapd_clear_psk"><?php echo lang( "GLOBAL", "WIFI", "PASS" );?></label>
                  <input type="text" name="hapd_clear_psk" id="hapd_clear_psk" value=""
                      placeholder="<?php echo lang( "GLOBAL", "WIFI", "PASS" );?>" pattern=".{8,63}" />
              
              <!-- HW-MODE -->
              <label for="hapd_mode" class="select"><?php echo lang( "HOSTAPD", "LBL", "MODE" );?></label>
                  <select name="hapd_mode" id="hapd_mode" required>
                    <option value="" selected></option>
                    <?php
                    foreach( WIFI_HAPD_MODES as $mode ){
                        ?>
                        <option value="<?php echo $mode;?>"><?php echo $mode;?></option>
                        <?php
                    }
                    ?>
                  </select>
              <!-- ./hw-mode -->
              
              <!-- CHANNEL -->
              <label for="hapd_chan" class="select"><?php echo lang( "GLOBAL", "WIFI", "CHAN" );?></label>
                  <select name="hapd_chan" id="hapd_chan" required>
                    <option value="" selected></option>
                    <?php
                    for ( $i=1; $i <=14; $i++ ){
                        ?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                        <?php
                    }
                    ?>
                  </select>
              <!-- ./channel -->
              <!-- HIDDEN FIELDS-->
              <input type="hidden" name="hapd_crypt_psk" id="hapd_crypt_psk" value="" />
              <input type="hidden" name="hapd_country_code" id="hapd_country_code" value="<?php echo WIFI_COUNTRY;?>" />
              <!-- ./hidden fields -->
          </div>
          <button type="button" data-theme="b" id="cmdHapdSave" ><?php echo lang( "GLOBAL", "BTN", "SAVE" );?></button>
          <button type="button" data-theme="e" id="cmdHapdApply" ><?php echo lang( "GLOBAL", "BTN", "APPLY" );?></button>
          <button type="button" data-theme="c" id="cmdHapdCancel" ><?php echo lang( "GLOBAL", "BTN","CANCEL" );?></button>              
          
      </form>
      <!-- ./ hostapd form -->
    <?php
}
?>