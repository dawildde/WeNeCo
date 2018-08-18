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
 *                                networkconf.js
 */
 
 
 
 // FILL GENERAL DATA
 /*
 *   fill general data
 */
 function fillGeneral( data ){
	var ifname = getVal( data[CONF_KEY_GENERAL], "name");
	var mode = getVal( data[CONF_KEY_GENERAL], "mode");
	setModeSelect( ifname, mode );
 }
 
 
 // INTERFACE SELECTED
 /*
 *   FILL FORMS AND ENABLE TABS
 */
 function ifSelected( ifname ){
    var isWiFi = $.inArray( ifname, WIFI_IF ) != -1;
    var isWired = $.inArray( ifname, WIRED_IF ) != -1;
	readNetworkConf( ifname );	// fill form data
	
    showTab( $( '#lnkTabIF' ), isWiFi || isWired );
	showTab( $( '#lnkTabClient' ), false );
	showTab( $( '#lnkTabAP' ), false );
	showTab( $( '#lnkTabDHCP' ), false );
 }
 
 // MODE SELECTED
 /*  mode was selected enable tabs:
 *   	parameters:
 *			mode: new mode
 */
 function modeSelected( mode ){  
	if ( mode == "" || mode == "undefined" ){ return; }
	
    showTab( $( '#lnkTabClient'), [ WIFI_MODE_CLIENT ].indexOf(mode) > -1 ); 
    showTab( $( '#lnkTabAP'),  [ WIFI_MODE_AP ].indexOf(mode) > -1 ); 
    showTab( $( '#lnkTabDHCP'),  [ WIFI_MODE_AP, WIRED_MODE_CLIENT ].indexOf(mode) > -1 ); 
    
	// SAVE MODE
	save_conf( $( "#interface" ).val(), CONF_KEY_GENERAL, getGeneralNwData(), true);

 }
 
 // SET MODE SELCT
 /*
 *   set combobox of mode selection
 */
 function setModeSelect( ifname, newVal ){
	if ( $.inArray( ifname, WIFI_IF ) != -1 ){
		$( '#divWiFiMode' ).show();
		$( '#divWiredMode' ).hide();
		$( "#wifimode" ).val( newVal ).change();
		$( "#wiredmode" ).val( "" ).change();
	} else {
		$( '#divWiFiMode' ).hide();
		$( '#divWiredMode' ).show();
		$( "#wifimode" ).val( "" ).change();	
		$( "#wiredmode" ).val( newVal ).change();			
	}
 }
 
 // GET FORM-DATA
 /*
 *   return form data into JSON-Object
 */ 
 function getGeneralNwData(){
	var data = {};
	var ifname = $( "#interface" ).val();
	data["name"] = ifname;
	if ( $.inArray( ifname, WIFI_IF ) != -1 ){
		data["mode"] = $( "#wifimode" ).val();
	} else {
		data["mode"] = $( "#wiredmode" ).val();
	}
	return data;
 }
 
 
 
 // READ NETWORK CONFIGURATIONS
 /*
 *   read full configuration data from configfile
 *   fill form with data
 */
 function readNetworkConf( ifname ){
	if ( ifname ) {
		return $.ajax({
			type: "GET",
			url: WEBROOT + "/includes/getJSON.php",
			data: { 
					get : 'getNetConf', 
					ifname : ifname
				},
			success : function(data) { 
				fillGeneral ( data );
				fillIfForm ( data[CONF_KEY_NETWORK] );
				fillKnownWiFi ( data[CONF_KEY_WPA] );
				fillHAPDForm ( data[CONF_KEY_AP] );
			},
			error : function(data) {
				alert ( lang( "GLOBAL", "MSG", "AJAX_FAILED" ) + "\n" + JSON.stringify( data ) ); 
			}
		});
	} else {
		clearIFForm();
	}
 }
 
// CHECK ALL FORM DATA
 /*
 *  check all form data's
 */
 function checkAllForms(){
   return checkIFForm() && checkHAPDForm();
 } 
  
 // SAVE FULL CONFIGURATIONS
 /*
 *  check form data's and submit
 */
 function saveFullConf(){
    if ( checkAllForms() ){
        submitFullConf( false );
    } 
 }
 
  
 // SAVE AND APPLY FULL CONFIG
 /*
 *  check form data's, submits and applys them all
 */
 function applyFullConf(){ 
	if ( checkAllForms() == true ){
		 // SAVE DATA AND WAIT UNTIL DONE
		$.when( submitFullConf( true ) ).then(function(){
				$.ajax({
				type: "GET",
				url: WEBROOT+"/includes/execute.php" ,
				data: { command: "apply_settings",
						ifname: $( "#interface" ).val()
						},
				success : function(data) { 
					alert ( lang ( "GLOBAL", "MSG", "SUCCESS" ) );
				},
				error : function(data) {
					alert ( lang ( "GLOBAL", "MSG", "FAILED" )  + JSON.stringify( data ) ); 
				}
			});
		});
	}
 }
 
 // ADD HANDLERS
 $( document ).ready(function() {
	$(" #interface" ).change( function(){ ifSelected( $(" #interface" ).val() )} );

 });
 