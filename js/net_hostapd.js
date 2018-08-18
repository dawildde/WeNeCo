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
 *                                ifconfig.js
 */
  
 // CLEAR_FORM
 function clearHAPDForm(){
	 $('#hapdform').trigger("reset");
 };

 
 // LEAVE PSK FIELD
 function hapdPSKblur(){
	var ssid = $( "#hapd_ssid" ).val();
	var psk = $( "#hapd_clear_psk" ).val();
	
	if ( psk.length > 0 && $( "#hapd_clear_psk" )[0].checkValidity() &&  $( "#hapd_ssid" )[0].checkValidity() ){
		cryptWPAPass( ssid, psk, "#hapd_crypt_psk" );
	}
 }
 
 // SAVE HAPD_CONF
 function save_hapd(){
	if ( checkHAPDForm() ){
		submit_hapdconf()
	}
 }
 
 // SEND AJAX
 function submit_hapdconf( bSilent = false ){
	var data = getAPfrmData();
	save_conf( $( "#interface" ).val(), CONF_KEY_AP, data, bSilent );
 }
 
 
 
 // FILL FORM DATA
 /*		
 *		parameters:
 *			ifname - name of interface
 */
 function fillHAPDForm ( data ){
	//clearForm
	clearHAPDForm();
	if (  $.isArray( data ) || typeof data =='object' ){
		$( "#hapd_ssid" ).val( getVal( data, "ssid" ));
		$( "#hapd_security" ).val( getVal( data, "security" )).change();
		$( "#hapd_mode" ).val( getVal( data, "hw_mode" )).change();
		$( "#hapd_chan" ).val( getVal( data, "channel" )).change();
		$( "#hapd_crypt_psk" ).val( getVal( data, "wpa_psk" ));
	}
 }
 
 
  // GET INTERFACE-FORM-DATA
 /*
 *   return form data into JSON-Object
 */ 
 function getAPfrmData(){
	var data = {};
	data["interface"] = $( "#interface" ).val();
	data["ssid"] = $( "#hapd_ssid" ).val();
	data["security"] = $( "#hapd_security" ).val();
	data["hw_mode"] = $( "#hapd_mode" ).val();
	data["channel"] = $( "#hapd_chan" ).val();
	data["wpa_psk"] = $( "#hapd_crypt_psk" ).val();
	
	return data;
 }
 
 
 // READ INTERFACE CONFIGURATION
 /*	
 *   reads interface configuration from config and fills form  
 *		parameters:
 *			ifname - name of interface
 */
 function readHAPDConf( ifname ){
	if ( ifname ) {
		return $.ajax({
			type: "GET",
			url: WEBROOT + "/includes/getJSON.php",
			data: { 
					get : 'getHAPDConf', 
					ifname : ifname
				},
			success : function(data) { 
				fillHAPDForm ( data )
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + JSON.stringify( data ) ); 
			}
		});
	} else {
		clearHAPDForm();
	}
 }
 
 
  // CHECK FORM DATA
 function checkHAPDForm(){
	if ( [ WIFI_MODE_AP ].indexOf($( "#wifimode" ).val() ) > -1 ){
		if ( $( "#hapdform" )[0].checkValidity() ){
			return true;
		} else {
			alert ( lang( "HOSTPAD", "MSG", "FORM_ERROR" ) );
		}
		return false
	}
	return true;
 }
 
  // ADD HANDLERS
 $( document ).ready(function() {
	$(" #cmdHapdSave" ).click( function(){ saveFullConf(); } );
	$(" #cmdHapdApply" ).click( function(){ applyFullConf(); } );
	$(" #cmdHapdCancel" ).click( function(){ clearIFForm(); } );
	$(" #hapd_clear_psk" ).blur( function(){ hapdPSKblur(); } );
	$(" #hapd_ssid" ).blur( function(){ hapdPSKblur(); } );
 });
 