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
 
 // IP-MODE CHANGE
 function ipmode_change(){
	// ENABLE OR DISABLE FORM FIELDS
	var bool = true;
	if ( $( "#fs_ipmode :radio:checked" ).val() == "STATIC"){ 
		bool = false;
	}
	$( "#div_ipmode_man" ).find("*").prop("disabled", bool);
 }
 
 
 // CLEAR_FORM
 function clearIFForm(){
	 $('#ifconfig').trigger("reset");
 };
 
 
 // FILL FORM DATA
 /*		
 *		parameters:
 *			ifname - name of interface
 */
 function fillIfForm ( data ){
	//clearForm
	clearIFForm();
	if (  $.isArray( data ) || typeof data =='object' ){
		// set if-name
		$( "#fs_ipmode" ).prop("disabled", false)
		// set ip-mode
		$( "#radIpMode1" ).prop("checked", data["dhcp"] == "1").checkboxradio("refresh");
		$( "#radIpMode2" ).prop("checked", data["dhcp"] != "1").checkboxradio("refresh");
		// set static ip-fields	
		$( "#ipv4" ).val ( data["ipv4"] );
		$( "#netmask" ).val ( data["netmask"] );
		$( "#gateway" ).val ( data["gateway"] );
		$( "#dns1" ).val ( getVal( data["dns"], 0) );
		$( "#dns2" ).val ( getVal( data["dns"], 1) );
		$( "#description" ).val ( data["description"] );
		
		// MISC - DHCP-Server
		$( "#chkDHCPEnable" ).prop("checked", data["dhcp_server"] == "yes").checkboxradio("refresh");
		$( "#txtDHCPstart" ).val( getVal( data, "dhcp_start" ) );
		$( "#txtDHCPend" ).val( getVal( data, "dhcp_end" ) );
		$( "#txtDHCPlease" ).val( getVal( data, "dhcp_lease" ) );
		
		// ROUTING-MODE
		$( "#sel_route" ).val( getVal( data, "routing" ) ).change();			
		// enable static ip-fields
		ipmode_change();
	}
 }
 
 // READ INTERFACE CONFIGURATION
 /*	
 *   reads interface configuration from config and fills form  
 *		parameters:
 *			ifname - name of interface
 */
 function readIfConf( ifname ){
	if ( ifname ) {
		return $.ajax({
			type: "GET",
			url: WEBROOT + "/includes/getJSON.php",
			data: { 
					get : 'getIfConf', 
					ifname : ifname
				},
			success : function(data) { 
				fillIfForm ( data )
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + JSON.stringify( data ) ); 
			}
		});
	} else {
		clearIFForm();
	}
 }
 
 // GET INTERFACE-FORM-DATA
 /*
 *   return form data into JSON-Object
 */ 
 function getIfFrmData(){
	var data = $( "#ifconfig" ).serializeObject();
	data["name"] = $( "#interface" ).val()
	// dns has to be an array
	data["dns"] = [ data["dns1"], data["dns2"] ];	
	delete data["dns1"];	
	delete data["dns2"];
	
	// MISC - DHCP-Server
	data["dhcp_server"] = $( "#chkDHCPEnable" ).val();
	data["dhcp_start"] = $( "#txtDHCPstart" ).val();
	data["dhcp_end"] = $( "#txtDHCPend" ).val();
	data["dhcp_lease"] = $( "#txtDHCPlease" ).val();
	
	// ROUTING-MODE
	data["routing"] = $( "#sel_route" ).val();
	return data;
 }
 
 
 // SEND AJAX
 function submit_ifconf( showMsgBox = true ){
	var data = $( "#ifconfig" ).serializeObject();
	save_conf( $( "#interface" ).val(), CONF_KEY_NETWORK, data );
 }
 
 // SAVE INTERFACE CONFIG
 function save_ifconf(){
	if ( checkIFForm() == true ){
		submit_ifconf();
	}

 }
 
 // SAVE AND APPLY CONFIG
 function apply_ifconf(){ 
	if ( checkIFForm() == true ){
		 // SAVE DATA AND WAIT UNTIL DONE
		$.when( submit_ifconf( false ) ).then(function(){
			exec("apply_settings", $( "#interface" ).val());
		});
	}
 }
 
 // CHECK FORM DATA
 function checkIFForm(){
	if ( $( "#interface" ).val() != "" ){
		if ( $( "#wifimode" ).val() != "" || $( "#wiredmode" ).val() != ""){
			if ( $( "#ifconfig" )[0].checkValidity() ){
				return true;
			} else {
				alert ( lang( "IFCONF", "MSG", "FORM_ERROR" ) );
			}
		} else {
			alert ( lang( "IFCONF", "MSG", "NO_MODE" ) );
		}		
	} else {
		alert ( lang( "IFCONF", "MSG", "NO_IF" ) );
	}
	return false;
 }
 
 
 // ADD HANDLERS
 $( document ).ready(function() {
	$(" #cmdIfSave" ).click( function(){ saveFullConf() } );
	$(" #cmdIfApply" ).click( function(){ applyFullConf() } );
	$(" #cmdIfCancel" ).click( function(){ clearIFForm() } );
	$(" #radIpMode1" ).change( function(){ ipmode_change() } );
	$(" #radIpMode2" ).change( function(){ ipmode_change() } );
 });