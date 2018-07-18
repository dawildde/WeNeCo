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
#                           Execute Commands Script

script_dir=$(dirname $(readlink -f $0))
weneco_dir=$(dirname $script_dir)       # WENECO ROOT DIRECTORY (.../weneco)

# DEVELOPMENT VERSION -> USE REAL DIR
if [ "$(basename $weneco_dir)" == "etc" ]; then
    weneco_dir="/etc/weneco"
fi

# RETURN OK
function ok(){
    echo -ne "OK"
    exit 0
}

# RETURN NOT OK
function nok(){
    echo -ne "FAILED"
    exit 1
}

# RESTART NETWORK
function restart_network(){
    echo "restarting network"
    
    devices=$(ls -I lo /sys/class/net)
    array=(${devices// / })
    
    # BRING DOWN ALL DEVICES
    for i in "${!array[@]}"
    do
        eval "sudo ip link set ${array[$i]} down" || nok     # BRING DOWN INTERFACE 
        eval "sudo ip addr flush dev ${array[$i]}" || nok    # FLUSH OLD IP CONFIG
    done
    # RESTART SERVICE
    sudo systemctl restart systemd-networkd.service || nok
    sudo systemctl restart systemd-resolved.service || nok
    
    # BRING UP ALL DEVICES
    for i in "${!array[@]}"
    do
        eval "sudo ip link set ${array[$i]} up" || nok     # BRING DOWN INTERFACE 
    done
}

# RESTART INTERFACE
function restart_interface(){
    echo "restarting interface $1"
    
    eval "sudo ip link set $1 down" || nok     # BRING DOWN INTERFACE 
    eval "sudo ip addr flush dev $1" || nok    # FLUSH OLD IP CONFIG
    # RESTART SERVICE
    sudo systemctl restart systemd-networkd.service || nok
    sudo systemctl restart systemd-resolved.service || nok
    
    eval "sudo ip link set $1 up" || nok   # BRING UP INTERFACE 
}


cmd=$1
par=$2
# REBOOT
if [ "$cmd" == "reboot" ]; then
    #sudo systemctl reboot && ok || nok
    echo "reboot command received"
    eval "sudo shutdown -r -t sec 5 &" && ok || nok
# RESTART NETWORK
elif [ "$cmd" == "restart_network" ]; then
    restart_network
    ok
# APPLY SETTINGS
elif [ "$cmd" == "apply_settings" ]; then
    echo "COPY NETWORK FILES" 
    sudo cp "$weneco_dir/network/"*".network" "/etc/systemd/network/" || nok
    # RESTART WHOLE NETWORK OR SINLE INTERFACE
    if [ "$par" == "" ]; then
        restart_network
    else
        restart_interface $par
    fi
    ok
else
    echo "UNKNOWN COMMAND"
    nok
fi

