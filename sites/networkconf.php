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
include_once( 'sites/net_ifconf.php' );
include_once( 'sites/net_wificonf.php' );
include_once( 'sites/net_hostapd.php' );
include_once( 'sites/net_misc.php' );
 
function showNetConf(){
    //$wiredIF = getInterfaces("wired");    //ETH
    //$wifiIF = getInterfaces("wireless");  //WLAN
    $interfaces = getInterfaces();          // BOTH
    $constants = get_defined_constants( true ); // GET DEFINED CONSTANTS
    // Interface given
    $ifname = "";
    if ( isset( $_GET["interface"] ) ){
      if ( in_array( $_GET["interface"], $interfaces ) ){
        $ifname = $_GET["interface"];
      } 
    }
    ?>

      <!-- Content Header -->
      <div data-role="header" id="content_header">
        <?php echo lang( "NETCONF", "TXT", "HEADER" ); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div data-role="content" id="content_body">
      
          <!-- Interface -->
          <div data-role="fieldcontain">
            <label for="interface" class="select"><?php echo lang( "NETCONF", "LBL", "SEL_IF" );?></label>
            <select name="name" id="interface">
              <option value="" selected><?php echo lang( "NETCONF", "VAL", "SEL_IF" );?></option>
              <?php
                foreach ($interfaces[1] as $if){
                  echo "<option value='$if'>$if</option>";
                }
              ?>
            </select>
          </div>
          <!-- ./interface -->
          
          <!-- WIRELESS Mode -->
          <div data-role="fieldcontain" id="divWiFiMode" style="display:none">
            <label for="wifimode" class="select"><?php echo lang( "WPACONF", "LBL", "SEL_MODE" );?></label>
            <select name="wifimode" id="wifimode" onchange="modeSelected( $(this).val() )">
              <option value="" selected><?php echo lang( "WPACONF", "VAL", "SEL_MODE" );?></option>
              <?php
              // LOOP THROUGH CONFIG KEYS AND APPEND TO OPTIONS
              foreach ( $constants["user"] as $key => $val ) {
                if ( substr( $key, 0, 10 ) == "WIFI_MODE_"  ){
                  ?>
                      <option value="<?php echo $val;?>"><?php echo lang( "WPACONF", "VAL", $key );?></option>
                  <?php
                }
              }
              ?>
            </select>
          </div>
          <!-- ./wireless mode -->
          <!-- WIRED Mode -->
          <div data-role="fieldcontain" id="divWiredMode" style="display:none">
            <label for="wiredmode" class="select"><?php echo lang( "IFCONF", "LBL", "SEL_MODE" );?></label>
            <select name="wiredmode" id="wiredmode" onchange="modeSelected( $(this).val() )">
              <option value="" selected><?php echo lang( "IFCONF", "VAL", "SEL_MODE" );?></option>
              <?php
              // LOOP THROUGH CONFIG KEYS AND APPEND TO OPTIONS
              foreach ( $constants["user"] as $key => $val ) {
                if ( substr( $key, 0, 11 ) == "WIRED_MODE_"  ){
                  ?>
                      <option value="<?php echo $val;?>"><?php echo lang( "IFCONF", "VAL", $key );?></option>
                  <?php
                }
              }
              ?>
            </select>
          </div>

          <!-- ./wired mode -->
          
          <!-- CONFIG-TABS -->
          <div data-role="tabs">
            <div data-role="navbar" id="navTabs">
              <ul>
                <li><a href="#tab1" class="ui-btn-active" id="lnkTabIF"><?php echo lang( "MENU", "LNK", "IPCONF" ); ?></a></li>
                <li><a href="#tab2" id="lnkTabClient"><?php echo lang( "MENU", "LNK", "CLIENT" ); ?></a></li>
                <li><a href="#tab3" id="lnkTabAP"><?php echo lang( "MENU", "LNK", "HOTSPOT" ); ?></a></li>
                <li><a href="#tab4" id="lnkTabMisc"><?php echo lang( "MENU", "LNK", "MISC" ); ?></a></li>
              </ul>
            </div>
            <div id="tab1">
              <?php showIfConfig(); ?>
            </div>
            <div id="tab2">
              <?php showClientConf(); ?>
            </div>
            <div id="tab3">
              <?php showHostapd(); ?>
            </div>
            <div id="tab4">
              <?php showMiscNetOpts(); ?>
            </div>
          </div>
          <!-- ./config-tabs -->
      </div>
         <!--./content_body -->
    <script language="javascript">
    <?php
    // QUERY AJAX IF INTERFACE IS GIVEN
    echo "ifSelected( '$ifname' );";
    ?>
    </script>
    <?php
} 
?>