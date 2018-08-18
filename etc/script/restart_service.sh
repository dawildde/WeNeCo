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
#                        RESTART NETWORKING AND SERVICES
#
# ##########################################################################
#
#                 use: restart_service.sh [service] [device]
#                         'networking' - restart networking
#                         'dns'        - restart dns servers
#
# ##########################################################################

script_dir=$(dirname $(readlink -f $0))

source "$script_dir/common.sh"
#weneco_dir=$(dirname $script_dir)       # WENECO ROOT DIRECTORY (.../weneco)

# RESTART NETWORKING
function restart_networking(){
    log "restart systemd-networkd"
    sudo systemctl restart systemd-networkd && pOK || pNOK
    restart_dns
}

# RESTART DNS SERVICES
function restart_dns(){
    log "restart dnsmasq"
    sudo systemctl restart dnsmasq && pOK || pNOK
    sleep 0.2
    log "restart systemd-resolved" 
    sudo systemctl restart systemd-resolved && pOK || pNOK
}

# START WPA_SUPPLICANT
function restart_wpa(){
    log "restart wpasupplicant@$1.service"
	sudo systemctl stop wpasupplicant@$1.service
    sudo rm "/var/run/wpa_supplicant/$1"
    sudo systemctl start wpasupplicant@$1.service && pOK || pNOK
}

# START WPA_SUPPLICANT
function restart_hostapd(){
    log "restart hostapd"
    sudo systemctl restart hostapd && pOK || pNOK
}

# GET PARAMETER
srvc=$1
device=$2
ifname=getIfName ${device}
case $srvc in
    dns)
        restart_dns
        xRET
        ;;
    network)
        restart_networking
        xRET
        ;;
    wpasupplicant) 
        restart_wpa ${ifname}
        xRET
        ;;
    hostapd)
        restart_hostapd
        xRET
        ;;
    *)
        echo "UNKNOWN SERVICE '$srvc'"
        xNOK
esac
