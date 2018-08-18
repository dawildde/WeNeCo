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
#  wpa_supplicant.sh [start/stop] [configfile]
#                    [start/stop] [configfile]

script_dir=$(dirname $(readlink -f $0))
source "$script_dir/common.sh"

# DEFAULT
t=3     # timeout (scan)

##########################################
#          WPA-Supplicant FN
##########################################

# START WPA-Supplicant INTERFACE SERVICE
function start_if_service(){
	log "sudo systemctl enable wpasupplicant@$1.service"
	eval "sudo systemctl enable wpasupplicant@$1.service"
	eval "sudo systemctl start wpasupplicant@$1.service" && pOK || pNOK
}

# STOP WPA-Supplicant INTERFACE SERVICE
function stop_if_service(){
	log "sudo systemctl disable wpasupplicant@$1.service"
	eval "sudo systemctl disable wpasupplicant@$1.service"
	eval "sudo systemctl stop wpasupplicant@$1.service" && pOK || pNOK
}

# SCAN WIFI AN RETUTN FOUND
function scan_wlan(){
    if [ $t -gt 0 ]; then
        log "scan wlan on device ${ifname}"
        eval "sudo wpa_cli scan -i ${ifname}" || pNOK 
        sleep $t   # WAIT SOME TIME
    else
        log "get scanresults on ${ifname}"
        pOK
    fi
    echo -ne ""
    eval "sudo wpa_cli scan_result -i ${ifname}" || pNOK
}

# COPY TEMP-CONFIG-FILE (COPY FROM /etc/weneco/network -> /etc/wpa_supplicant ) and restart service for connect
function connect_wifi(){
    echo "disconnect wifi on '${ifname}'"
    sudo wpa_cli -i ${ifname} disconnect 
    log "copy config"
    sudo cp "$weneco_dir/network/wpa_supplicant-$1.conf" "/etc/wpa_supplicant/wpa_supplicant-${ifname}.conf" || pNOK
    sudo chmod 644 "/etc/wpa_supplicant/wpa_supplicant-${ifname}.conf" || pNOK
    pRET
    log "reconfig wpa_cli"
    sudo wpa_cli -i ${ifname} reconfigure && pOK|| pNOK
    log "connecting"
    sudo wpa_cli -i ${ifname} reconnect && pOK || pNOK
    log "start dhclient"
    sudo dhclient ${ifname}
    pRET
}

# DISCONNECT WIFI
function disconnect_wifi(){
    log "disconnect wifi '${ifname}'"
    sudo wpa_cli -i ${ifname} disconnect && pOK || pNOK
}

##########################################
#            GETOPTS
##########################################

# PARSING OPTIONS
i=0
while [[ $# -gt 0 ]]
do
    opt="$1"                            # PUT Par1 into variable
    shift;                              # Clear first var
    case $opt in
        "-i" | "--interface" )          # if $opt is interface then par2 ($1) = value
            interface="$1" 
            shift                       # shift agein
            ;;                  # next loop
        "-t" | "--timeout" ) 
            t="$1" 
            shift
            ;;
        "-ssid" ) 
            ssid="$1" 
            shift
            ;;
        "-psk" ) 
            psk="$1" 
            shift
            ;;
        * ) opts[$i]="$opt"
            i=$(( $i + 1 )) 
            ;;
    esac
done

cmd=${opts[0]}              # set var $cmd
getIfName ${opts[1]}       # set var $interface name
devicename=${opts[1]}           
##########################################
#          CALL FUNCTIONS
##########################################

# CALL FUNCTIONS
device_list=$(ls -I lo /sys/class/net)
array=(${device_list// / })


# IF DEVICE IS AVAILABLE....
if [[ " ${array[@]} " =~ " ${ifname} " ]]; then
    case $cmd in
        start)
            start_if_service "${ifname}"
            xRET
            ;;
        stop)
            stop_if_service "${ifname}"
            xRET
            ;;
        scan)
            scan_wlan "${ifname}"
            xRET
            ;;
        connect)
            connect_wifi "${devicename}"
            xRET
            ;;
        disconnect)
            disconnect_wifi "${devicename}"
            xRET
            ;;
        *)
            echo "Unknown Command '$cmd'"
            xNOK
    esac
else
	echo "Unknown Device '$devicename'"
fi
