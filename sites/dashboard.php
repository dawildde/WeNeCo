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
      <div id="content_header">
        <?php echo lang("HDR_DASHBOARD_OVERVIEW"); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div id="content_body">
        <?php
          $iflist = getInterfaces();
          foreach ($iflist as $if){
            $ifdata = json_decode( getIFdata($if) );
            ?>
            <p class="table_container">
              <!--<table data-role="table" id="tbl_<?php echo $if;?>" data-mode="reflow" class="ui-responsive table-stroke">-->
              <h3><?php echo $if;?></h3>
              <table id="tbl_<?php echo $if;?>" class="iptable">
                <thead><tr><th></th></tr></thead>
                <tbody>
                  <tr>
                    <th><?php echo lang("IPV4");?></th><td><?php echo $ifdata -> ipv4; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang("NETMASK");?></th><td><?php echo $ifdata -> netmask; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang("GATEWAY");?></th><td><?php echo $ifdata -> gw; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang("DNS");?></th>
                      <td>
                        <?php 
                        // dns-server is array
                        foreach ($ifdata -> dns as $dns){
                          echo "$dns<br />";
                        }                        
                        ?>
                      </td>
                  </tr>
                  <tr>
                    <th><?php echo lang("MAC");?></th><td><?php echo $ifdata -> mac; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo lang("RXTX");?></th><td><?php echo $ifdata -> rx_mib; ?> / <?php echo $ifdata -> tx_mib; ?> <?php echo lang("MIB");?></td>
                  </tr>  
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
