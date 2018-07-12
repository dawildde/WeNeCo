#!/bin/bash                                                                        
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
#                             Manage WPA-Supplicant

#
#  ENABLES OR DISABLES WPA-Supplicant for device
#  wpa_supplicant.sh [devicename] [start/stop]
#

# START WPA-Supplicant INTERFACE SERVICE
function start_if_service(){
	echo "sudo systemctrl enable wpasupplicant@$1.service"
	#eval "sudo systemctrl enable wpasupplicant@$1.service"
	#eval "sudo systemctrl start wpasupplicant@$1.service"
}

# STOP WPA-Supplicant INTERFACE SERVICE
function stop_if_service(){
	echo "sudo systemctrl DISable wpasupplicant@$1.service"
	#eval "sudo systemctrl disable wpasupplicant@$1.service"
	#eval "sudo systemctrl stop wpasupplicant@$1.service"
}


cmd=$2
device=$1
device_list=$(ls -I lo /sys/class/net)
array=(${device_list// / })

# IF DEVICE IS AVAILABLE....
if [[ " ${array[@]} " =~ " ${device} " ]]; then
	# START OR STOP
	if [ "${cmd}" == "start" ]; then
		start_if_service "${device}"
	elif [ "${cmd}" == "stop" ]; then
		stop_if_service "${device}"
	else
		echo "Unknown Command '$cmd'"
		exit 1
	fi
else
	echo "Unknown Device '$device'"
fi
