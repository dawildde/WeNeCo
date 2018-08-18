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
 *                                wpaconf.js
 */
 
 /*----------------------------------------------------------------------------
 *
 *                      FORM FUNCTIONS
 *
 *---------------------------------------------------------------------------*/
 
  // CLEAR_FORM
 function clearWPAForm(){
	$( '#wpaconf' ).trigger("reset");
 };
 
 function clearManConf(){
	$( '#frm_wifi_man_conf' ).trigger("reset");
	$( "#dlgPsk" ).popup( "close" ); 
 }
 
 function clearKnownWiFi(){
	 $( '#tblKnownWiFi' ).find("tr:gt(0)").remove();
 };
 
  function clearScanResult(){
	 $( '#tblScannedWiFi' ).find("tr:gt(0)").remove();
 };
 
 
// CLICK ON SAVE BUTTON
function manSaveClick(){
	if( checkWPAForm() === true ){
		$.when( cryptWPAPass ( $( '#man_ssid' ).val(), $( '#man_clear_psk' ).val(), '#man_crypt_psk' ) ).done(function( data ){	
			addWiFiData( true );
		});
	}
}
 
 // CLICK ON CONNECT BUTTON ( ! BUTTON DISABLED)
function man_connect_click(){
	if ( checkWPAForm() == true ){
		$.when( cryptWPAPass ( $( '#man_ssid' ).val(), $( '#man_clear_psk' ).val(), '#man_crypt_psk' ) ).done(function( data ){	
			var ssid = $( '#man_ssid' ).val();
			var psk = data["psk"];
			// Save Data and CONNECT or connect without saving
			if ( $( "#man_chkSave" ).is(':checked')) {
				$.when( addWiFiData( true ) ).done(function( data ){
					connect_wifi( $( '#interface' ).val(), ssid, psk );
				});
			} else {
				connect_wifi( $( '#interface' ).val(), ssid, psk );
			}
		});
	} else {
		alert ( lang("GLOBAL", "MSG", "FORM_ERROR"  ) ) ;
	}
}

 // CLICK ON CONNECT BUTTON
function known_connect_click( ssid ){
	connect_wifi ( $( "#interface").val(), ssid );
}

// CLICK ON EDIT BUTTON
function editKnown ( ssid ){
	alert ("ToDo: editKnown()")
}

 // CLICK ON CONNECT BUTTON
function scan_connect_click( ssid ){
    var psk = $( "#psk_" + ssid ).val();
    var security = $( "#security_" + ssid ).val();
    var bssid = $( "#bssid_" + ssid ).val();
    if ( psk == "" ){
        $( "#man_ssid" ).val( ssid );
        $( "#man_security" ). val( security )
        $( "#man_bssid" ).val( bssid );
        $( "#dlgPsk" ).popup( "open" ); 
    } else {
        connect_wifi( $( '#interface' ).val(), ssid, psk );
    }
}
 

 
 // SELECT WiFi-Mode
 /*		
 *		parameters:
 *			mode - wifi mode
 */
 function wifiModeSelect( mode ){
    switch ( mode ){
        case "ap":
        case "wisp_m":
            $( "#div_known" ).hide();
            $( "#div_scanList" ).hide();
            $( "#div_manConf" ).show();
            break;
        case "client":
        case "wisp_s":
            $( "#div_known" ).show();
            $( "#div_scanList" ).show();
            $( "#div_manConf" ).hide();
            break;
    }
 }
 
 
 
  //  READ WIFI-DATA 
 /*		
 *   read wifi-data from config and scan results
 *		parameters:
 *			ifname - name of interface
 */
 function readWiFiData ( ifname ){
    readKnownWiFi ( ifname );
    readScanResults ( ifname, 0 );
 }
 
 
 // FILL KNOWN WIFI
 /*		create table and hidden form fields
 *		parameters:
 *			data
 */
 function fillKnownWiFi ( data ){
	clearKnownWiFi();
	if ( $.isArray( data ) || typeof data =='object' ){
		$.each(data, function( idx ) {
			var ssid = data[idx].ssid;
			var auto = getVal( data[idx], "disabled", 0 );  // toDo: Autoconnect
			var active = getVal( data[idx], "#active", 0 );  // toDo: Is Active (WPA-SUPLLICANT)
			var row = $("<tr />");
			$( "#tblKnownWiFi" ).append(row);
			row.append($("<td>" + ssid + "</td>"));
            row.append($("<td>" + getVal( data[idx], "id_str" ) + "</td>"));
			row.append($("<td>" + getVal( data[idx], "bssid" ) + "</td>"));
			row.append($("<td>" + getVal( data[idx], "#security" ) + "</td>"));
            //row.append($("<td>" + getVal( data[idx], "#psk" ) + "</td>"));
			row.append($("<td><input type='hidden' id='psk_"+ ssid + "' value='" +  getVal( data[idx], "psk" ) + "'></td>"));
			row.append($("<td></td>"));
			row.append($('<td>' + 
						'<input type="button" id="delete_'+ ssid +'" onclick="delKnown( \'' + ssid  + '\' )" ' +
							'value="' + lang( "GLOBAL", "BTN", "DELETE" ) + '" />' +
						'<input type="button" id="edit_'+ ssid +'" onclick="editKnown( \'' + ssid  + '\' )" ' +
							'value="' + lang( "GLOBAL", "BTN", "EDIT" ) + '" />' +
						'<input type="button" id="connect_'+ ssid +'" onclick="known_connect_click( \'' + ssid  + '\' )" ' +
                            'value="' + lang( "GLOBAL", "BTN", "CONNECT" ) + '" />' + 
						'</td>'
			));
		});  
	}
 }
 
 // READ KNOWN WIFI-DATA
 /*		
 *   read known wifi's from config and fill table
 *		parameters:
 *			ifname - name of interface
 */
 function readKnownWiFi ( ifname ){
    if ( ifname ) {
        clearKnownWiFi(); // ClearOld-Results
		$.ajax({
			type: "GET",
			url: WEBROOT + "/includes/getJSON.php",
			data: { 
					get : 'getKnownWifiNetworks', 
					ifname : ifname
				},
			success : function(data) { 
				fillKnownWiFi( ifname )
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
			}
		});
	} else {
		clearKnownWiFi();
	}
 }
 
 
 // FILL WiFi-Scan-Results
 /*		
 *		parameters:
 *			data
 */
 function fillScanResults( data ){
	clearScanResult();
	if ( $.isArray( data ) || typeof data =='object' ){
		$.each(data, function( idx ) {
			var ssid = data[idx].ssid
			var row = $("<tr />");
			$( "#tblScannedWiFi" ).append(row);
			row.append($("<td>" + ssid + "</td>"));
			row.append($("<td>" + data[idx].bssid + "</td>"));
			row.append($("<td>" + data[idx].security + "</td>"));
			row.append($("<td>" + data[idx].channel + " ( " + data[idx].frequency + " )</td>"));
			row.append($("<td>" + data[idx].level + "</td>"));
			//row.append($("<td>" + data[idx].description + "</td>"));
            row.append($('<td>' +
                        '<input type="button" id="connect_'+ ssid +'" name="connect" onclick="scan_connect_click( \'' + ssid  + '\' )" ' +
                            'value="' + lang( "GLOBAL", "BTN", "CONNECT" ) + '" />' + 
                        '<input type="hidden" id="psk_' + ssid + '" name="psk" value="">' + 
                        '<input type="hidden" id="security_' + ssid + '" value="' + data[idx].security + '">' + 
                        '<input type="hidden" id="bssid_' + ssid + '" value="' + data[idx].bssid + '">' + 
                        '</td>'
            ));
		});
    }
 }
 
 
 // READ WIFI SCAN RESULTS
 /*		
 *  read scan results from system and fill table
 *		parameters:
 *			ifname - name of interface
 *			timeout - wait for x-seconds for scan result
 */
 function readScanResults( ifname, timeout=3 ){
    if ( ifname ) {
        clearScanResult(); // ClearOld-Results
		$.ajax({
			type: "GET",
			url: WEBROOT + "/includes/getJSON.php",
			data: { 
					get : 'scanWifiNetworks', 
					ifname : ifname,
					timeout: timeout
				},
			success : function(data) { 
				fillScanResults( data )
			},
			error : function(data) {
				alert ( lang( "WPACONF", "MSG", "SCAN_FAIL" ) + "\n" + data.responseText ); 
			}
		});
	} else {
		clearScanResult();
	}
 }
 
 /*----------------------------------------------------------------------------
 *
 *                      OTHER FUNCTIONS
 *
 *---------------------------------------------------------------------------*/
 
 // CONNECT WiFi
 /*		
 *		parameters:
 *			ifname - name of interface
 *			parameters - dictionary with parameters
 *							ssid:  ssid of network
 *							psk: private passphrase 
 */
 function connect_wifi( ifname, ssid, psk ){
    if ( ifname ) {
		return	$.ajax({
			type: "GET",
			url: WEBROOT + "/includes/execute.php",
			data: { 
					command : 'connect_wifi', 
                    ifname: ifname,
					ssid: ssid,
					psk: psk
				},
			success : function(data) { 
				alert ( lang( "GLOBAL", "MSG", "AJAX_SUCCESS" ) );
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
			}
		});
	} else {
		
	}
 }
 
 
 // RECONNECT WiFi
 function reconnect_wifi( ifname ){
	if ( ifname ) {
		return	$.ajax({
			type: "GET",
			url: WEBROOT + "/includes/execute.php",
			data: { 
					command : 'reconnect_wifi',
					ifname: ifname
				},
			success : function(data) { 
				//alert ( lang( "GLOBAL", "MSG", "AJAX_SUCCESS" ) );
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
			}
		});
	}
 }
 
 // DISCONNECT WiFi
 function disconnect_wifi( ifname ){
	if ( ifname ) {
		return	$.ajax({
			type: "GET",
			url: WEBROOT + "/includes/execute.php",
			data: { 
					command : 'disconnect_wifi',
					ifname: ifname
				},
			success : function(data) { 
				//alert ( lang( "GLOBAL", "MSG", "AJAX_SUCCESS" ) );
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
			}
		});
	}
 }
 
 // SAVE WiFi-DATA
 /*		
 *   Saves wifi-data from man_form into Config  
 */
 function addWiFiData( bSilent = false ){
        var ifname = $( "#interface" ).val();
        var frmDat = {};
        frmDat["ssid"] = $( "#man_ssid" ).val();
        frmDat["bssid"] = $( "#man_bssid" ).val();
        frmDat["#psk"] = $( "#man_clear_psk" ).val();
		frmDat["psk"] = $( "#man_crypt_psk" ).val();
        frmDat["id_str"] = $( "#man_id_str" ).val();
		frmDat["#security"] = $( "#man_security" ).val();
		if (!$( "#chkAutoConnect").is(":checked")) {
			frmDat["disabled"] = 1;
		}

		return $.ajax({
			type: "GET",
			url: WEBROOT + "/includes/writefile.php",
			data: { 
					set : 'addWiFi', 
                    ifname: ifname,
					data:  frmDat
				},
			success : function(data) { 
				//console.log ( " Saved:  " + JSON.stringify( data ));
				clearManConf();
				fillKnownWiFi( data[CONF_KEY_WPA] );
                if ( bSilent == false ) {
                    alert ( lang( "GLOBAL", "MSG", "SAVED" ) );
                }
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
			}
		});
 }
 
 // CLICK ON DELETE BUTTON
function delKnown( ssid ){
	$.ajax({
		type: "GET",
		url: WEBROOT + "/includes/writefile.php",
		data: { 
				set : 'removeWiFi', 
				ifname : $( '#interface' ).val(),
				ssid: ssid
			},
		success : function(data) { 
			fillKnownWiFi( data[CONF_KEY_WPA] );
		},
		error : function(data) {
			alert ( lang( "WPACONF", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
		}
	});
}
 
	 
 // getWPAPassphrase
 /*		
 *   returns WPA-Passphrase
 */
 function cryptWPAPass( ssid, psk, target_id ){     
	return $.ajax({
		type: "GET",
		url: WEBROOT + "/includes/getJSON.php",
		data: { 
				get : 'crypt_psk', 
				ssid: ssid,
                psk: psk
			},
		success : function(data) { 
			$( target_id ).val( data["psk"] );
		},
		error : function(data) {
			alert ( lang( "WPACONF", "MSG", "AJAX_FAILED" ) + "\n" + data.responseText ); 
		}
	});
}
  
 // CHECK WPA-FORM
 /*		
 *   checks form input
 */
 function checkWPAForm(){
	return true;
 }
 
 
 // ADD HANDLERS
 $( document ).ready(function() {
	$(" #manAdd" ).click( function(){ $( '#dlgPsk' ).popup( 'open' ); } );
	$(" #cmdScan" ).click( function(){ readScanResults( $( '#interface' ).val() ); } );
	$(" #man_save" ).click( function(){ manSaveClick(); } );
	$(" #man_cancel" ).click( function(){ $( '#dlgPsk' ).popup( 'close' ); } );
 });