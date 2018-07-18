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

include_once( 'includes/getData.php' );
include_once( 'includes/config.php' );
 
function showIfConfig(){
    $interfaces = getInterfaces();
    // Interface given
    $ifname = "";
    if ( isset( $_GET["interface"] ) ){
      if ( in_array( $_GET["interface"], $interfaces ) ){
        $ifname = $_GET["interface"];
      } 
    }
    
    ?>

      <!-- Content Header -->
      <div id="content_header">
        <?php echo lang("HDR_IF_CONFIG"); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div id="content_body">
        <form id="ifconfig" action="" class="ui-body ui-body-a ui-corner-all">
          <!-- Interface -->
          <div data-role="fieldcontain">
            <label for="select-interface" class="select"><?php echo lang("LBL_SEL_IF");?></label>
            <select name="name" id="select-interface" onchange="fillForm(this.value)">
              <option value="" selected><?php echo lang("OPT_SEL_IF");?></option>
              <?php
                foreach ($interfaces as $if){
                  echo "<option value='$if'>$if</option>";
                }
              ?>
            </select>
          </div>
          <!-- ./interface -->
          <!-- IP-Mode -->
          <div data-role="fieldcontain" id="div_ipMode">
            <fieldset data-role="controlgroup" id="fs_ipmode" disabled>
              <legend><?php echo lang("LBL_SEL_IP");?></legend>
                  <input type="radio" name="ipmode" id="radIpMode1" value="DHCP" onchange="ipmode_change()" />
                  <label for="radIpMode1"><?php echo lang("IP_DHCP");?></label>

                  <input type="radio" name="ipmode" id="radIpMode2" value="STATIC" onchange="ipmode_change()"  />
                  <label for="radIpMode2"><?php echo lang("IP_STATIC");?></label>
            </fieldset>
          </div>
          <!-- ./interface -->
          <!-- IP-Fields -->
          <div data-role="fieldcontain" id="div_ipmode_man">
            <label for="ipv4"><?php echo lang("IPV4");?></label>
              <input type="text" name="ipv4" id="ipv4" value="" placeholder="<?php echo lang("IPV4");?>" pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="netmask"><?php echo lang("NETMASK");?></label>
              <input type="text" name="netmask" id="netmask" value="" placeholder="<?php echo lang("NETMASK");?>" pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="gateway"><?php echo lang("GATEWAY");?></label>
              <input type="text" name="gateway" id="gateway" value="" placeholder="<?php echo lang("GATEWAY");?>" pattern="<?php echo IP4_PATTERN; ?>"/>
            <label for="dns1"><?php echo lang("DNS");?></label>
              <input type="text" name="dns1" id="dns1" value="" placeholder="<?php echo lang("DNS");?>" pattern="<?php echo IP4_PATTERN; ?>"/>     
            <label for="dns2"></label>
              <input type="text" name="dns2" id="dns2" value="" placeholder="<?php echo lang("DNS");?>" pattern="<?php echo IP4_PATTERN; ?>"/>              
          <!-- ./ip-fields -->
          </div>
          <button type="button" data-theme="b" name="cmdSave" value="save" onclick="save_ifconf()"><?php echo lang("BTN_SAVE");?></button>
          <button type="button" data-theme="e" name="cmdApply" value="apply" onclick="apply_ifconf()"><?php echo lang("BTN_APPLY");?></button>
          <button type="button" data-theme="c" name="cmdCancel" value="cancel" onclick="clearForm()"><?php echo lang("BTN_CANCEL");?></button>
        </form>
      </div>
      <!--./content_body -->
    <script language="javascript">
    <?php
    // QUERY AJAX IF INTERFACE IS GIVEN
    if ( $ifname != "" ){
      echo "fillForm('$ifname');";
    }
    ?>
    </script>
    <?php
} 
?>