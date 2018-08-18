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
require_once "includes/functions.php";
require_once "includes/execute.php";

function showLogViewer(){
    patch_logs(); 
    ?>

      <!-- Content Header -->
      <div data-role="header" id="content_header">
        <?php echo lang( "LOGVIEW", "TXT", "HEADER" ); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div data-role="content"  id="content_body">
        <form id="logviewer" action="" class="ui-body ui-body-a ui-corner-all">
          <select name="logfile" id="selLog" onchange="fillLogView( this.value )">
          <option value="" selected></option>
            <?php
            $constants = get_defined_constants( true );
            foreach ( $constants["user"] as $key => $val ) {
                if ( substr( $key, 0, 6 ) == "LOG_F_" ){
                  ?>
                    <option value="<?php echo $val;?>"><?php echo lang( "LOGVIEW", "LBL", $key );?></option>
                  <?php
                }
            }
            ?>
          </select>
          <textarea id="txtLog" rows="50" cols="50" readonly="readonly">
          
          </textarea>
        </form>
      </div>
      <!--./content_body -->

    <?php
}
?>