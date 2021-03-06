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
 
function showSystem(){
  ?>
      <!-- Content Header -->
      <div data-role="header" id="content_header">
        <?php echo lang( "SYSTEM", "TXT", "HEADER" ); ?>
      </div>
      <!--./content_header -->

      <!-- Content Body -->
      <div data-role="content" id="content_body">
                
        <a href="index.php?page=authconf"><?php echo lang( "SYSTEM", "TAB", "SET_ADMIN_PWD" ); ?></a>
        <a href="index.php?page=logviewer"><?php echo lang( "SYSTEM", "TAB", "LOGVIEWER" ); ?></a>
        <a href="index.php?page=fileedit"><?php echo lang( "SYSTEM", "TAB", "FILEEDIT" ); ?></a>
        <form name="frm_system">
          <button type="button" id="cmdRestartNw"><?php echo lang( "SYSTEM", "BTN", "RESTART_NW"); ?></button>
          <button type="button" id="cmdRestartDNS"><?php echo lang( "SYSTEM", "BTN", "RESTART_DNS"); ?></button>
          <button type="button" id="cmdReboot"><?php echo lang( "SYSTEM", "BTN", "REBOOT" ); ?></button>
        <form>
      </div>
      <!--./content_body -->
  <?php
  
}
?>