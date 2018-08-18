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
 *                                Dashboard-Page
 */

require_once( 'includes/config.php' );
require_once( 'includes/functions.php' );
require_once( 'includes/getData.php' );
 
function showDashboard(){
    ?>

      <!-- Content Header -->
      <div data-role="header" id="content_header">
        <?php echo lang( "DASHBOARD", "TXT", "HEADER" ); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div data-role="content"  id="content_body">
        <?php
          $iflist = getInterfaces()[1];
          foreach ($iflist as $if){
            $ifdata = get_if_data( $if )[1];
            ?>
            <p class="table_container">
              <!--<table data-role="table" id="tbl_<?php echo $if;?>" data-mode="reflow" class="ui-responsive table-stroke">-->
              <h3><?php echo $if;?></h3>
              <table id="tbl_<?php echo $if;?>" class="iptable">
                <thead><tr><th></th></tr></thead>
                <tbody>
                  <tr>
                    <th><?php echo lang( "GLOBAL", "IP", "IPV4" );?></th><td><?php echo $ifdata["ipv4"]; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang( "GLOBAL", "IP", "NETMASK" );?></th><td><?php echo $ifdata["netmask"]; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang( "GLOBAL", "IP", "GATEWAY" );?></th><td><?php echo $ifdata["gw"]; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang( "GLOBAL", "IP", "DNS" );?></th>
                      <td>
                        <?php 
                        // dns-server is array
                        foreach ($ifdata["dns"] as $dns){
                          echo "$dns<br />";
                        }                        
                        ?>
                      </td>
                  </tr>
                  <tr>
                    <th><?php echo lang( "GLOBAL", "IP", "MAC" );?></th><td><?php echo $ifdata["mac"]; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang( "GLOBAL", "IP", "RXTX" );?></th>
                        <td><?php echo $ifdata["rx_mib"]; ?> / <?php echo $ifdata["tx_mib"] ?> <?php echo lang( "GLOBAL", "IP", "MIB" );?></td>
                  </tr>
                  <!-- WIRELESS -->
                  <?php
                    if ( $ifdata["type"] == "wireless" ){
                      $wifidata = getWiFiState( $if )[1];
                      if ( $wifidata["connected"] === true ){
                        $bConPar = "false";
                        $sConBtn = lang( "GLOBAL", "BTN", "DISCONNECT" );
                      } else {
                        $sConBtn = lang( "GLOBAL", "BTN", "CONNECT" );
                        $bConPar = "true";
                      }
                     ?>
                      <tr>
                        <th><?php echo lang( "GLOBAL", "WIFI", "SSID" );?></th>
                          <td><?php echo $wifidata["ssid"]; ?></td>
                      </tr>
                      <tr>
                        <th><?php echo lang( "GLOBAL", "WIFI", "LVL" );?> / <?php echo lang( "GLOBAL", "WIFI", "QUALITY" );?></th>
                          <td><?php echo $wifidata["level"]; ?> / <?php echo $wifidata["quality"]; ?></td>
                      </tr>
                      <tr>
                        <th></th>
                        <td>
                          <button class="ui-btn ui-mini" id="cmdConnect" onclick="dashbdConClick('<?php echo $if ."', ". $bConPar;?>)">
                          <?php echo $sConBtn; ?></button>
                        </td>
                      </tr>
                    <?php                     
                    }
                  ?>
                  <!-- ./wireless -->
                </tbody>                
              </table>
            </p>
            <?php
          }
        ?>
      </div>
      <!--./content_body -->

    <?php
}
?>
