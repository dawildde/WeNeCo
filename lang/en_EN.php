<?php
//MENU
$_L["MENU"]["LNK"]["DASHBOARD"] = "Dashboard";
$_L["MENU"]["LNK"]["SYSTEM"] = "System";
$_L["MENU"]["LNK"]["AUTHCONF"] = "Authentification";      
$_L["MENU"]["LNK"]["NETCONF"] = "Network Configuration"; 
$_L["MENU"]["LNK"]["IPCONF"] = "IP Configuration"; 
$_L["MENU"]["LNK"]["CLIENT"] = "Wireless Client Configuration"; 
$_L["MENU"]["LNK"]["HOTSPOT"] = "Wireless Hotspot Configuration"; 
$_L["MENU"]["LNK"]["MISC"] = "Options"; 

// DASHBOARD
$_L["DASHBOARD"]["TXT"]["HEADER"] = "Network Overview";                                  // SITE-HEADER

// SYSTEM
$_L["SYSTEM"]["TXT"]["HEADER"] = "System";                                               // SITE-HEADER
$_L["SYSTEM"]["BTN"]["REBOOT"]  = "Reboot System";                                     // BUTTON-Text [Reboot]
$_L["SYSTEM"]["BTN"]["RESTART_NW"]  = "Restart Networking";                            // BUTTON-Text [Restart Network]
$_L["SYSTEM"]["BTN"]["RESTART_DNS"]  = "Restart DNSMASQ";                               // BUTTON-Text [Restart DNSMASQ]
$_L["SYSTEM"]["TAB"]["SET_ADMIN_PWD"] = "Set Admin Password";                          // TAB-SET ADMIN PASSWORD
$_L["SYSTEM"]["TAB"]["LOGVIEWER"] = "Show Logfiles";                                // TAB-SET LogViewer
$_L["SYSTEM"]["TAB"]["FILEEDIT"] = "Open FileEditor";                                     // TAB-SET FileEditor
$_L["SYSTEM"]["MSG"]["CONFIRM_REBOOT"] = "Reboot System? Your connection will be lost";
$_L["SYSTEM"]["MSG"]["CONFIRM_NW_RESTART"] = "Restart networking? Your connection may be lost";

// AUTH-CONF
$_L["AUTHCONF"]["TXT"]["HEADER"] = "Authentification Settings";                          // SITE-HEADER
$_L["AUTHCONF"]["LBL"]["USERNAME"] = "Username";                                        // LABEL Username
$_L["AUTHCONF"]["LBL"]["OLDPASS"] = "Old Password";                                     // LABEL Old Password
$_L["AUTHCONF"]["LBL"]["NEWPASS1"] = "New Password";                                    // LABEL New Password
$_L["AUTHCONF"]["LBL"]["NEWPASS2"] = "Confirm Password";                                // LABEL Password Confirmation
$_L["AUTHCONF"]["MSG"]["PWD_MISMATCH"] = "Old password does not match" ;                  // ALERT - Old Password mismatch
$_L["AUTHCONF"]["MSG"]["PWD_CONFIMATION"] = "Password confirmation does not match" ;      // ALERT - Password confirmation error
$_L["AUTHCONF"]["MSG"]["USERNAME_LEN"] = "Username is to short" ;                         // ALERT - Username is to short  

// NETCONF
$_L["NETCONF"]["TXT"]["HEADER"] = "Interface Configuration";                              // SITE-HEADER
$_L["NETCONF"]["LBL"]["SEL_IF"] = "Interface:";                                          // LABEL INTERFACE
$_L["NETCONF"]["VAL"]["SEL_IF"] = "Select Interface";                                    // OPTION SELECT-INTERFACE

// IFCONFIG
$_L["IFCONF"]["TXT"]["HEADER"] = "Interface Configuration";                              // SITE-HEADER
$_L["IFCONF"]["VAL"]["SEL_MODE"] = "Selcet Mode";                                       //VALUE - Select Mode (Default)
$_L["IFCONF"]["VAL"]["WIRED_MODE_CLIENT"] = "Client-Mode";                              //VALUE - Client-Mode
$_L["IFCONF"]["VAL"]["WIRED_MODE_WAN"] = "WAN-Mode";                                    //VALUE - WAN-Mode
$_L["IFCONF"]["LBL"]["SEL_IP"] = "IP-Mode:";                                            // LABEL IP-Mode
$_L["IFCONF"]["LBL"]["SEL_MODE"] = "Interface Mode";                                    // LABEL - Interface Mode
$_L["IFCONF"]["LBL"]["DESCRIPTION"] = "Descritpion:";                                   // LABEL Descritpion
$_L["IFCONF"]["MSG"]["NO_IF"] = "No interface selected" ;                               // ALERT - No interface selected
$_L["IFCONF"]["MSG"]["NO_MODE"] = "No mode selected" ;                                  // ALERT - No mode selected
$_L["IFCONF"]["MSG"]["FORM_ERROR"] = "Please validate form data";                       // ALERT - Form validation failed

// WPA CONFIG
$_L["WPACONF"]["TXT"]["HEADER"] = "Wireless Configuration";                              // SITE-HEADER
$_L["WPACONF"]["TXT"]["HDR_SCAN"] = "Found Networks";                                    //HEADER Scanned Networks
$_L["WPACONF"]["TXT"]["HDR_KNOWN_NW"] = "Known Networks";                                //HEADER Known Networks
$_L["WPACONF"]["TXT"]["HDR_MANCONF"] = "Connection Settings";                            //HEADER Manual CONFIG
$_L["WPACONF"]["BTN"]["SCAN"] = "Scan WiFi";                                             //BUTTON Scan WiFi
$_L["WPACONF"]["BTN"]["ADD_MAN"] = "Add manually";                                       //BUTTON Add manually
$_L["WPACONF"]["MSG"]["SCAN_FAIL"] = "Scan failed";                                     //ALERT - Scan failed
$_L["WPACONF"]["LBL"]["SEL_MODE"] = "Wireless Mode";                                     //LABEL - Mode
$_L["WPACONF"]["LBL"]["KEY_MGMT"] = "Key Management";                                    //LABEL - Key Management
$_L["WPACONF"]["LBL"]["SAVE_CONF"] = "Save Configuration";                              //LABEL - Save Config
$_L["WPACONF"]["LBL"]["AUTO_CONNECT"] = "Auto Connect";                                 //LABEL - Auto Connect
$_L["WPACONF"]["LBL"]["COUNTRY"] = "Country Code";                                      //LABEL - Country Code
$_L["WPACONF"]["VAL"]["SEL_MODE"] = "Selcet Mode";                                      //VALUE - Select Mode (Default)
$_L["WPACONF"]["VAL"]["WIFI_MODE_CLIENT"] = "Client";                                        //VALUE - Client-Mode
$_L["WPACONF"]["VAL"]["WIFI_MODE_AP"] = "AP";                                                //VALUE - AP-Mode
$_L["WPACONF"]["VAL"]["WIFI_MODE_WISP_M"] = "WISP-Master";                              //VALUE - WISP-Master
$_L["WPACONF"]["VAL"]["WIFI_MODE_WISP_S"] = "WISP-Slave";                                //VALUE - WISP-Slave

// NET-MISC
$_L["NETMISC"]["TXT"]["HDR_DHCP"] = "DHCP Server";                                     //HEADER - DHCP Server
$_L["NETMISC"]["TXT"]["HDR_ROUTE"] = "Route Mode";                                     //HEADER - Route Mode
$_L["NETMISC"]["TXT"]["HDR_XCMDS"] = "Xtended Commands";                               //HEADER - Xtended Commands
$_L["NETMISC"]["LBL"]["DHCP_ENABLE"] = "Enable DHCP Server";                           //LABEL - DHCP Enabled
$_L["NETMISC"]["LBL"]["DHCP_START"] = "DHCP Start Address";                            //LABEL - DHCP Offset
$_L["NETMISC"]["LBL"]["DHCP_END"] = "DHCP End Address";                                //LABEL - DHCP Pool Size
$_L["NETMISC"]["LBL"]["DHCP_LEASE"] = "DHCP Lease Time (h)";                           //LABEL - DHCP Lease Time
$_L["NETMISC"]["LBL"]["ROUTE_MODE"] = "Route Mode";                                    //LABEL - Route-Mode
$_L["NETMISC"]["BTN"]["RESTART_IF"] = "Restart Interface";                             //BUTTON - Restart Interface
$_L["NETMISC"]["BTN"]["RESTART_WPA"] = "Restart WPA_Supplicant";                       //BUTTON - Restart WPA_SUPPLICANT
$_L["NETMISC"]["BTN"]["QUERY_DHCP"] = "Query DHCP Address";                             //BUTTON - Query DHCP

// HOSTAPD
$_L["HOSTAPD"]["TXT"]["HEADER"] = "Accespoint Configuration";                              // SITE-HEADER
$_L["HOSTAPD"]["LBL"]["MODE"] = "Mode";                                                 // LABEL - Mode
$_L["HOSTAPD"]["MSG"]["PWD_ERROR"] = "SSID or PSK-Error ( to short? )";                 // ALERT - PSK Error

// SYS_LOGVIEWER  
$_L["LOGVIEW"]["TXT"]["HEADER"] = "Logfile Viewer";                                    // SITE-HEADER
$_L["LOGVIEW"]["LBL"]["LOG_F_WPA"] = "WPA Supplicant Log";                             // LABEL FILE "WPA_Supplicant"
$_L["LOGVIEW"]["LBL"]["LOG_F_LHTTPD"] = "LightHttpd Log";                              // LABEL FILE "LightHttpd"
$_L["LOGVIEW"]["LBL"]["LOG_F_WENECO"] = "WeNeCo Log";                                   // LABEL FILE "WeNeCo Log"
$_L["LOGVIEW"]["LBL"]["LOG_F_SYSLOG"] = "SysLog";                                       // LABEL FILE "SYSLOG"
$_L["LOGVIEW"]["LBL"]["LOG_F_AP"] = "Hostapd";                                       // LABEL FILE "HOSTAPD"
$_L["LOGVIEW"]["LBL"]["LOG_F_DNSMASQ"] = "DNSMASQ";                                       // LABEL FILE "DNSMASQ"

// SYS_TEXTEDITOR
$_L["TXTEDIT"]["TXT"]["HEADER"] = "Texteditor";                                    // SITE-HEADER
$_L["TXTEDIT"]["LBL"]["TEXT_D_WENECO"] = "WeNeCo NetworkConfig";                    // LABEL FILE "WeNeCo Network Conf"

// GLOBAL
$_L["GLOBAL"]["BTN"]["SAVE"] = "Save";                                                 // BUTTON [Save]
$_L["GLOBAL"]["BTN"]["APPLY"] = "Apply";                                               // BUTTON [Apply]
$_L["GLOBAL"]["BTN"]["CANCEL"] = "Cancel";                                             // BUTTON [Cancel]
$_L["GLOBAL"]["BTN"]["REMOVE"] = "Remove";                                             // BUTTON [Remove]
$_L["GLOBAL"]["BTN"]["YES"] = "Yes";                                                   // BUTTON [Yes]
$_L["GLOBAL"]["BTN"]["NO"] = "No";                                                     // BUTTON [No]
$_L["GLOBAL"]["BTN"]["EDIT"] = "Edit";                                                 // BUTTON [Edit]
$_L["GLOBAL"]["BTN"]["DELETE"] = "X";                                                  // BUTTON [Delete]
$_L["GLOBAL"]["BTN"]["CONNECT"] = "Connect";                                           // BUTTON [Connect]
$_L["GLOBAL"]["BTN"]["DISCONNECT"] = "Disconnect";                                     // BUTTON [Disconnect]
$_L["GLOBAL"]["LBL"]["DESCRIPTION"] = "Description";                                     // LABEL [Description]
$_L["GLOBAL"]["MSG"]["SUCCESS"] = "Operation successfull";                                // ALERT - Success
$_L["GLOBAL"]["MSG"]["SAVED"] = "Data saved";                                           // ALERT - Success
$_L["GLOBAL"]["MSG"]["AJAX_SUCCESS"] = "Request Successfull";                             // ALERT -  AJAX- Success
$_L["GLOBAL"]["MSG"]["AJAX_FAILED"] = "AN ERROR OCCURED :";                               // ALERT -  AJAX - Failure
$_L["GLOBAL"]["MSG"]["CSFR_VIOLATION"] = "CSFR Violation Error" ;                         // ALERT - CSFR Violation
$_L["GLOBAL"]["MSG"]["F_W_ERROR"] = "File write error occured";                           // ALERT - File write error
$_L["GLOBAL"]["MSG"]["FORM_ERROR"] = "Please validate form data";                      // ALERT - Form error
$_L["GLOBAL"]["VAL"]["DISABLED"] = "None / Disabled";                                   // VALUE - Disabled

// GLOBAL IP-STUFF
$_L["GLOBAL"]["IP"]["DHCP"] = "DHCP";
$_L["GLOBAL"]["IP"]["STATIC"] = "Static";
$_L["GLOBAL"]["IP"]["IPV4"] = "IP-Address";
$_L["GLOBAL"]["IP"]["IPV6"] = "IPv6-Address";
$_L["GLOBAL"]["IP"]["NETMASK"] = "Netmask";
$_L["GLOBAL"]["IP"]["GATEWAY"] = "Gateway";
$_L["GLOBAL"]["IP"]["DNS"] = "DNS-Server";
$_L["GLOBAL"]["IP"]["MAC"] = "MAC-Address";
$_L["GLOBAL"]["IP"]["RX"] = "Received";
$_L["GLOBAL"]["IP"]["TX"] = "Sent";
$_L["GLOBAL"]["IP"]["RXTX"] = "Received / Sent";
$_L["GLOBAL"]["IP"]["BYTES"] = "Bytes";
$_L["GLOBAL"]["IP"]["KIB"] = "KIB";
$_L["GLOBAL"]["IP"]["MIB"] = "MIB";

// GLOBAL WIFI-STUFF
$_L["GLOBAL"]["WIFI"]["BSSID"] = "BSSID";
$_L["GLOBAL"]["WIFI"]["SSID"] = "SSID";
$_L["GLOBAL"]["WIFI"]["DESCRIPTION"] = "Description";
$_L["GLOBAL"]["WIFI"]["CHAN"] = "Channel";
$_L["GLOBAL"]["WIFI"]["SECURITY"] = "Security";
$_L["GLOBAL"]["WIFI"]["PASS"] = "Passphrase";
$_L["GLOBAL"]["WIFI"]["FREQ"] = "Frequency";
$_L["GLOBAL"]["WIFI"]["LVL"] = "Level";
$_L["GLOBAL"]["WIFI"]["QUALITY"] = "Quality";

?>