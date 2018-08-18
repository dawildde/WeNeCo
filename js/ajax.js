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
*                             AJAX REQUEST FUNCTIONS
*/

// RUN EXECUTE SCRIPT
function exec( fn, ifname=null, par=null ){
   return $.ajax({
        type: "GET",
        url: WEBROOT+"/includes/execute.php" ,
        data: { command: fn ,
                ifname: ifname,
				parameter: par
				},
        success : function(data) { 
			alert ( lang ( "GLOBAL", "MSG", "SUCCESS" ) );
        },
		error : function(data) {
			alert ( lang ( "GLOBAL", "MSG", "FAILED" )  + JSON.stringify( data ) ); 
		}
    });    
 }

 // SAVE SINGLE CONFIGURATIONS
 /*
 *    send configuration to PHP
 *         parameter:
 *              ifname - interface name
 *              section - section key
 *              data - data to save
 */
 function save_conf( ifname, section, data, bSilent = false ){
	return $.ajax({
		type: "POST",
		url: WEBROOT + "/includes/writefile.php",
		data: { 
				set : 'saveConf',
                ifname: $( "#interface" ).val(),
                section: section,              
				data : data
			},
		success : function(data) { 
			if ( bSilent != true ){
                alert ( lang( "GLOBAL", "MSG", "AJAX_SUCCESS" ) );
            }
		},
		error : function(data) {
			alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + JSON.stringify( data ) );			
		}
	});
 }

 
 
 // SUBMIT FULL CONFIGURATIONS
 /*
 *  save all configurations to config file
 */
 function submitFullConf( bSilent = false ){
    var apConf =  "ToDo : Get AP Conf!";
    var dhcpConf = "ToDo : Get DHCP Conf!";
    
    var fullConf = {};
    fullConf[CONF_KEY_GENERAL] = getGeneralNwData();
    fullConf[CONF_KEY_NETWORK] = getIfFrmData();
		//fullConf[CONF_KEY_WPA] = getWifiConf; //his will not be overwritten by this fn
	fullConf[CONF_KEY_AP] = getAPfrmData(); 

	return $.ajax({
		type: "POST",
		url: WEBROOT + "/includes/writefile.php",
		data: { 
				set : 'saveFullConf',
                ifname: $( "#interface" ).val(),                
				data : fullConf
			},
		success : function(data) { 
			if ( bSilent != false ){
                alert ( lang( "GLOBAL", "MSG", "AJAX_SUCCESS" ) );
            }
		},
		error : function(data) {
			alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + JSON.stringify( data ) );			
		}
	});
 }
 
 
 // WRITE FILE
 /*
 *  write's a full file
 */
 function writeFile( file , text, bSilent = false ){
    return $.ajax({
        type: "POST",
        url: WEBROOT+"/includes/writefile.php" ,
        data: { set : "writeFile" ,
                file : file,
				text: text
            },
        success : function(data) { 
			if ( bSilent == false ){
				alert( lang ( "GLOBAL", "MSG", "AJAX_SUCCESS" ) );
			}
        },
		error : function(data) {
			alert ( lang ( "GLOBAL", "MSG", "AJAX_FAILED" )  + JSON.stringify( data ) ); 
		}
    });
 }
 
 
// SHOW AJAX-LOADER
function loading(showOrHide) {
    setTimeout(function(){
       $.mobile.loading(showOrHide);
    }, 1); 
}
