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
 *                           Interface Configuration
 */

include_once( 'includes/config.php' );
 
function showIfConfig(){
  ?> 
        <form id="ifconfig" action="" class="ui-body ui-body-a ui-corner-all">
          <!-- IP-Mode -->
          <div data-role="fieldcontain" id="div_ipMode">
            <fieldset data-role="controlgroup" id="fs_ipmode" disabled>
              <legend><?php echo lang( "IFCONF", "LBL", "SEL_IP" );?></legend>
                  <input type="radio" name="ipmode" id="radIpMode1" value="DHCP" />
                  <label for="radIpMode1"><?php echo lang( "GLOBAL", "IP", "DHCP" );?></label>

                  <input type="radio" name="ipmode" id="radIpMode2" value="STATIC"  />
                  <label for="radIpMode2"><?php echo lang( "GLOBAL", "IP", "STATIC" );?></label>
            </fieldset>
          </div>
          <!-- ./interface -->
          <!-- IP-Fields -->
          <div data-role="fieldcontain" id="div_ipmode_man">
            <label for="ipv4"><?php echo lang( "GLOBAL", "IP", "IPV4" );?></label>
              <input type="text" name="ipv4" id="ipv4" value="" placeholder="<?php echo lang( "GLOBAL", "IP", "IPV4" );?>" 
                pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="netmask"><?php echo lang( "GLOBAL", "IP", "NETMASK" );?></label>
              <input type="text" name="netmask" id="netmask" value="" placeholder="<?php echo lang( "GLOBAL", "IP", "NETMASK" );?>" 
                pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="gateway"><?php echo lang( "GLOBAL", "IP", "GATEWAY" );?></label>
              <input type="text" name="gateway" id="gateway" value="" placeholder="<?php echo lang( "GLOBAL", "IP", "GATEWAY" );?>" 
                pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="dns1"><?php echo lang( "GLOBAL", "IP", "DNS" );?></label>
              <input type="text" name="dns1" id="dns1" value="" placeholder="<?php echo lang( "GLOBAL", "IP", "DNS" );?>" 
                pattern="<?php echo IP4_PATTERN; ?>"/>     
            <label for="dns2"></label>
              <input type="text" name="dns2" id="dns2" value="" placeholder="<?php echo lang( "GLOBAL", "IP", "DNS" );?>" 
                pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="description"></label>
              <input type="text" name="description" id="description" value="" placeholder="<?php echo lang( "IFCONF", "LBL", "DESCRIPTION" );?>" />                   
          <!-- ./ip-fields -->
          </div>
          <button type="button" data-theme="b" id="cmdIfSave"><?php echo lang( "GLOBAL", "BTN", "SAVE" );?></button>
          <button type="button" data-theme="e" id="cmdIfApply"><?php echo lang( "GLOBAL", "BTN", "APPLY" );?></button>
          <button type="button" data-theme="c" id="cmdIfCancel"><?php echo lang( "GLOBAL", "BTN","CANCEL" );?></button>
        </form>
        
    <?php
} 
?>