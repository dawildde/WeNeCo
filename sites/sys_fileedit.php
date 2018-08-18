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

require_once "includes/config.php";
require_once "includes/functions.php";
 
function showFileEditor(){ 
    ?>

      <!-- Content Header -->
      <div data-role="header" id="content_header">
        <?php echo lang( "TXTEDIT", "TXT", "HEADER" ); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div data-role="content"  id="content_body">
        <!-- HTML form -->
        <form name="frmTxtEdit" id="frmTxtEdit" action="" method="post">
          <input type="hidden" id="txtFileName" value="">
          <select name="textfile" id="selTextFile" onchange="fillTextEdit( this.value )">
            <option value="" selected></option>
            <?php
            $constants = get_defined_constants( true );
            foreach ( $constants["user"] as $key => $val ) {
                if ( substr( $key, 0, 7 ) == "TEXT_F_" ){
                    ?>
                    <option value="<?php echo $val;?>"><?php echo lang( "TXTEDIT", "LBL", $key );?></option>
                    <?php
                } elseif ( substr( $key, 0, 7 ) == "TEXT_D_" ){
                    $files = scandir( $val );
                    print $val;
                    foreach( $files as $file ) {
                        if ( strlen( $file ) < 4 ) continue;
                        $file = $val . $file;
                        
                        ?>
                        <option value="<?php echo $file;?>"><?php echo lang( "TXTEDIT", "LBL", $key )." - ". basename($file) ;?></option>
                        <?php
                    }
                }
            }
            ?>
          </select>
          <textarea name="txtEditor" id="txtEditor"></textarea>
          <button type="button" data-theme="e" name="cmdApply" onclick="saveTextEdit()"><?php echo lang( "GLOBAL", "BTN", "APPLY" );?></button>
          <button type="button" data-theme="c" name="cmdCancel" onclick="$( '#frmTxtEdit' ).trigger('reset'); "><?php echo lang( "GLOBAL", "BTN","CANCEL" );?></button>
        </form>
      </div>
      <!--./content_body -->

    <?php
}
?>