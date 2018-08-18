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
 
function showMiscNetOpts(){
        $interfaces = getInterfaces();
        ?>
          <!-- DHCP SERVER -->
          <div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-iconpos="right" data-collapsed="false" id="div_dhcp">
          <h3><?php echo lang( "NETMISC", "TXT", "HDR_DHCP" );?></h3>
            <form id="misc_dhcp">
              
                <label for="chkDHCPEnable"><?php echo lang( "NETMISC", "LBL", "DHCP_ENABLE" );?></label>
                    <input type="checkbox" id="chkDHCPEnable" value="yes" checked="checked" />
                <label for="txtDHCPstart"><?php echo lang( "NETMISC", "LBL", "DHCP_START" );?></label>
                    <input type="text" id="txtDHCPstart" value="" pattern="<?php echo IP4_PATTERN; ?>" />
                <label for="txtDHCPend"><?php echo lang( "NETMISC", "LBL", "DHCP_END" );?></label>
                    <input type="text" id="txtDHCPend" value="" pattern="<?php echo IP4_PATTERN; ?>" />
                <label for="txtDHClease"><?php echo lang( "NETMISC", "LBL", "DHCP_LEASE" );?></label>
                    <input type="text" id="txtDHCPlease" value="" pattern="([1-9][0-9]?)" />
            </form>
          </div>
          <!-- ./dhcp server -->

          <!-- ROUTE -->
          <div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-iconpos="right" data-collapsed="false" id="div_dhcp">
          <h3><?php echo lang( "NETMISC", "TXT", "HDR_ROUTE" );?></h3>
            <div data-role="fieldcontain">
            <form id="misc_route">
              <label for="sel_route" class="select"><?php echo lang( "NETMISC", "LBL", "ROUTE_MODE" );?></label>
              <select name="route" id="sel_route">
                <option value="" selected><?php echo lang( "GLOBAL", "VAL", "DISABLED" );?></option>
                <?php
                  foreach ($interfaces[1] as $if){
                    echo "<option value='FWD_$if'>FORWARD -> $if</option>";
                    echo "<option value='NAT_$if'>NAT -> $if</option>";
                  }
                ?>
              </select>
            </form>
            </div>
          </div>
          <!-- ./route -->
          
          <!-- XTND_COMMANDS -->
          <div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-iconpos="right" data-collapsed="false" id="div_dhcp">
          <h3><?php echo lang( "NETMISC", "TXT", "HDR_XCMDS" );?></h3>
            <div data-role="fieldcontain">
                <button type="button" data-theme="a" id="cmdRestartIf"><?php echo lang( "NETMISC","BTN", "RESTART_IF" );?></button>
                <button type="button" data-theme="a" id="cmdRestartWPA"><?php echo lang( "NETMISC","BTN", "RESTART_WPA" );?></button>
                <button type="button" data-theme="a" id="cmdRestartDNS"><?php echo lang( "SYSTEM", "BTN", "RESTART_DNS"); ?></button>
                <button type="button" data-theme="a" id="cmdQueryDhcp"><?php echo lang( "NETMISC", "BTN", "QUERY_DHCP" );?></button>
            </div>
          </div>
          <!-- ./xtnd_commands -->
          
          <button type="button" data-theme="b" id="cmdIfSave"><?php echo lang( "GLOBAL", "BTN", "SAVE" );?></button>
          <button type="button" data-theme="e" id="cmdIfApply"><?php echo lang( "GLOBAL", "BTN", "APPLY" );?></button>
          <button type="button" data-theme="c" id="cmdIfCancel"><?php echo lang( "GLOBAL", "BTN","CANCEL" );?></button>
      
    <?php
}
?>