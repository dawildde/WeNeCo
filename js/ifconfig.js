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
 function clearForm(){
	 $('#ifconfig').trigger("reset");
 };
 
 
 // FILL FORM DATA
 /*		
 *		parameters:
 *			ifname - name of interface
 */
 function fillForm(ifname){
	if ( ifname ) {
		$.ajax({
			type: "GET",
			url: g_webroot+"/includes/readfile.php",
			data: { 
					fn : 'read_network_conf', 
					par : ifname
				},
			success : function(data) { 
				// set if-name
				$( "#fs_ipmode" ).prop("disabled", false)
				if ( $( "#select-interface" ).val() != data["name"] ){
					$( "#select-interface" ).val( data["name"] ).change();
				}
				// set ip-mode
				$( "#radIpMode1" ).prop("checked", data["dhcp"] == "1").checkboxradio("refresh");
				$( "#radIpMode2" ).prop("checked", data["dhcp"] != "1").checkboxradio("refresh");
				// set static ip-fields	
				$( "#ipv4" ).val ( data["ipv4"] );
				$( "#netmask" ).val ( data["netmask"] );
				$( "#gateway" ).val ( data["gw"] );
				$( "#dns1" ).val ( data["dns"][0] );
				$( "#dns2" ).val ( data["dns"][1] );
				
				// enable static ip-fields
				ipmode_change();
			},
			error : function(data) {
				alert ( lang( "MSG_AJAX_FAILURE" ) + "\n" + JSON.stringify( data ) ); 
			}
		});
	} else {
		clearForm();
	}
 }
 
 
 // SEND AJAX
 function submit_ajax(){
	var frmDat = $('#ifconfig').serializeObject();
	return $.ajax({
		type: "POST",
		url: g_webroot+"/includes/writefile.php",
		data: { 
				fn : 'write_network_conf', 
				par : frmDat
			},
		success : function(data) { 
			alert ( lang( "MSG_AJAX_SUCCESS" ) );
		},
		error : function(data) {
			alert ( lang( "MSG_AJAX_FAILURE" ) + "\n" + JSON.stringify( data ) );			
		}
	});
 }
 
 // SAVE INTERFACE CONFIG
 function save_ifconf(){
	if ( checkForm() == true ){
		submit_ajax();
	}

 }
 
 // SAVE AND APPLY CONFIG
 function apply_ifconf(){ 
	if ( checkForm() == true ){
		 // SAVE DATA AND WAIT UNTIL DONE
		$.when( submit_ajax() ).then(function(){
			exec("apply_settings", $( "#select-interface" ).val());
		});
	}
 }
 
 // CHECK FORM DATA
 function checkForm(){
	if ( $( "#select-interface" ).val() != "" ){
		if ( $( "#ifconfig" )[0].checkValidity() ){
			return true;
		} else {
			alert ( lang( "MSG_FORM_ERROR" ) );
		}		
	} else {
		alert ( lang( "MSG_NO_IF" ) );
	}
	return false;
 }
 
 