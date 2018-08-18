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
 *                                System.js
 */
 
 //QUERY AND EXECUTE REBOOT
 function reboot(){
	if ( confirm ( lang( "SYSTEM", "MSG", "CONFIRM_REBOOT" ) ) ){
		exec ( "reboot" ); 
	}
 }
 
 //QUERY AND RESTART NETWORKING
 function restart_network(){
	if ( confirm ( lang( "SYSTEM", "MSG", "NW_RESTART")) ){
		exec ( "restart_network" ) ;
	} 
 }
 
 // FILL LOGVIEWER
 function fillLogView( file ){
    $.ajax({
        type: "GET",
        url: WEBROOT+"/includes/getJSON.php" ,
        data: { get: "read_content" ,
                file : file,
                lines: 100
            },
        success : function(data) { 
            $( "#txtLog" ).val ( data );
        },
		error : function(data) {
			alert ( lang ( "GLOBAL", "MSG", "AJAX_FAILED" )  + JSON.stringify( data ) ); 
		}
    });
 }
 
 // FILL FILEEDITOR
 function fillTextEdit( file ){
    $.ajax({
        type: "GET",
        url: WEBROOT+"/includes/getJSON.php" ,
        data: { get: "read_content" ,
                file : file
            },
        success : function(data) { 
            $( "#txtEditor" ).val ( data );
			$( "#txtFileName" ).val( file );
        },
		error : function(data) {
			alert ( lang ( "GLOBAL", "MSG", "AJAX_FAILED" )  + JSON.stringify( data ) ); 
		}
    });
 }
 
 function saveTextEdit(){
	 writeFile ( $( "#txtFileName" ).val(), $( "#txtEditor" ).val () );
 }
 
 // ADD HANDLERS
 $( document ).ready(function() {
	$(" #cmdRestartNw" ).click( function(){ restart_network(); } );
	$(" #cmdRestartDNS" ).click( function(){ exec( "restart_dns" ) } );
    $(" #cmdReboot" ).click( function(){ reboot() } );

 });