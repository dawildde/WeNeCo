#!/bin/bash
#                                                                        
#                                     ,--.                               
#             .---.                 ,--.'|            ,----..            
#            /. ./|             ,--,:  : |           /   /   \           
#        .--'.  ' ;          ,`--.'`|  ' :          |   :     :  ,---.   
#       /__./ \ : |          |   :  :  | |          .   |  ;. / '   ,'\  
#   .--'.  '   \' .   ,---.  :   |   \ | :   ,---.  .   ; /--` /   /   | 
#  /___/ \ |    ' '  /     \ |   : '  '; |  /     \ ;   | ;   .   ; ,. : 
#  ;   \  \;      : /    /  |'   ' ;.    ; /    /  ||   : |   '   | |: : 
#   \   ;  `      |.    ' / ||   | | \   |.    ' / |.   | '___'   | .; : 
#    .   \    .\  ;'   ;   /|'   : |  ; .''   ;   /|'   ; : .'|   :    | 
#     \   \   ' \ |'   |  / ||   | '`--'  '   |  / |'   | '/  :\   \  /  
#      :   '  |--" |   :    |'   : |      |   :    ||   :    /  `----'   
#       \   \ ;     \   \  / ;   |.'       \   \  /  \   \ .'            
#        '---"       `----'  '---'          `----'    `---`              
# 
#                          Web Network Configuration                                                                       
#
#                                Repair Script

# SOURCES
root_dir=$(dirname $(readlink -f $0))
source "$root_dir/common.sh"
source "$root_dir/patch.sh"

# SHOW REPAIR MENU
function repair_weneco(){
    echo -e "${gn}------------------------------ ${nc}"
    echo -e "${gn}    WeNeCo Repair Assistant ${nc}"
    echo -e "${gn}------------------------------${nc}"
	echo -n "(Re)install dependencies? [y/N]: "
	read answer
    if [[ $answer == "y" ]]; then
		# config_php_version # maybe allow reconfiguration of php_package
		install_package "lighttpd"
		install_package $php_package
		install_package "git" 
		install_package "hostapd" 
		install_package "dnsmasq"
		log_ok
	fi
	echo -e ""
	echo -n "Set WeNeCo file-permissions? [y/N]: "
	read answer
    if [[ $answer == "y" ]]; then
		set_permissions
		log_ok
	fi
	echo -e ""
	echo -n "Patch sudoers? [y/N]: "
	read answer
    if [[ $answer == "y" ]]; then
		patch_sudoers "force"
		log_ok
	fi
}

# ONLY START MAIN-SCRIPT
if [ $main != "weneco.sh" ]; then
	install_error "Please run 'weneco.sh'"
fi


