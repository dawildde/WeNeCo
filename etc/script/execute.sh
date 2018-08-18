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

source "$script_dir/common.sh"

hapdmode=('wifi_ap' 'wisp_m')       # modes using hostapd.service
wpamode=('wifi_client' 'wisp_s')    # modes using wpa_supplicant

##########################################
#            DO ACTIONS
##########################################

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
    sudo systemctl restart dnsmasq || nok
    sudo systemctl restart systemd-resolved.service || nok
    sudo systemctl restart hostapd.service || nok
    
    # BRING UP ALL DEVICES
    for i in "${!array[@]}"
    do
        # start wpa_supplicant if needed
        getMode $i
        if [[ " ${wpamode[@]} " =~ " ${ifmode} " ]]; then
            echo "start wpa_supplicant"
            sudo systemctl start wpa_supplicant@$i.service || nok
        fi
        eval "sudo ip link set ${array[$i]} up" || nok     # BRING UP INTERFACE 
    done
}

# RESTART INTERFACE
function restart_interface(){
    getIfName $1
    getMode $1
    
    echo "restarting interface '${ifname}'"
    
    log "bring down ${ifname}"
    eval "sudo ip link set ${ifname} down" && pOK || pNOK     # BRING DOWN INTERFACE 
    eval "sudo ip addr flush dev ${ifname}"  # FLUSH OLD IP CONFIG
    
    # (RE-)START SERVICE
    eval "sudo bash $script_dir/restart_service.sh network"
    
    # IF AP START HOSTAPD ELSE WPA SUPPLICANT
    if [[ " ${hapdmode[@]} " =~ " ${ifmode} " ]]; then
        log "start hostapd"
        sudo systemctl stop wpa_supplicant@${ifname}.service
        sudo systemctl restart hostapd.service && pOK || pNOK
    elif [[ " ${wpamode[@]} " =~ " ${ifmode} " ]]; then
        log "start wpa_supplicant"
        sudo systemctl restart wpa_supplicant@${ifname}.service && pOK || pNOK
    fi
    
    log "bring up ${ifname}"
    eval "sudo ip link set ${ifname} up" && pOK || pNOK   # BRING UP INTERFACE 
}


# COPY SYSTEM NETWORK CONFIGURATION INTO WENECO DIRECTORY
function getNetConf(){
    getIfName $1
    echo "COPY SYSTEMFILES FOR '${ifname}'"
    # WPA SUPPLICANT
    src_file="/etc/wpa_supplicant/wpa_supplicant-${ifname}.conf"
    trgt_file="$temp_dir/wpa_supplicant-$1.conf"
    if [ -f $src_file ]; then
        echo "copy $src_file -> $trgt_file" 
        sudo cp "$src_file" "$trgt_file" 
    fi
    # systemd-networkd
    src_file=$(grep -Ril "${ifname}" "/etc/systemd/network/"*".network") # find devicename in files
    trgt_file="$temp_dir/$1.network"
    if [ -f $src_file ]; then
        echo "copy $src_file -> $trgt_file" 
        sudo cp "$src_file" "$trgt_file" 
    fi
    #hostapd
    src_file=$(grep -Ril "${ifname}" "/etc/hostapd/hostapd.conf") # find devicename in files
    trgt_file="$temp_dir/hostapd-$1.conf"
    if [ -f $src_file ]; then
        echo "copy $src_file -> $trgt_file"
        sudo cp "$src_file" "$trgt_file" 
    fi
    patch_temp
    ok
    # dnsmasq 
    src_file=$(grep -Ril "${ifname}" "/etc/dnsmasq.s/dnsmasq-"*".conf") # find devicename in files
    trgt_file="$temp_dir/dnsmasq-$1.conf"
    if [ -f $src_file ]; then
        echo "copy $src_file -> $trgt_file"
        sudo cp "$src_file" "$trgt_file" 
    fi
}

# APPLY SETTINGS
function apply_all(){  
    getIfName $1
    #COPY NETWORK FILES
    src_file="$weneco_dir/network/$1.network"
    tgt_file="/etc/systemd/network/$1.network"
    if [ -f $src_file ]; then
        echo  "COPY NETWORK FILES" 
        log "copy $src_file -> $tgt_file"
        sudo cp "$src_file" "$tgt_file" || pNOK
        sudo chown root:root "$tgt_file"
        sudo chmod 755 "$tgt_file"
        pOK
    fi
    # if wpa_supplicant of device exists copy
    #COPY WPA_SUPPLICANT 
    src_file="$weneco_dir/network/wpa_supplicant-$1.conf"
    tgt_file="/etc/wpa_supplicant/wpa_supplicant-${ifname}.conf"
    if [ -f $src_file ]; then
        echo "COPY WPA_SUPPLICANT" 
        log "copy $src_file -> $tgt_file"
        sudo cp "$src_file" "$tgt_file" || pNOK
        sudo chown root:root "$tgt_file"
        sudo chmod 755 "$tgt_file"
        pOK
    fi
    # if hostapd of device exists copy
    # "COPY HOSTAPD" 
    src_file="$weneco_dir/network/hostapd-$1.conf"
    tgt_file="/etc/hostapd/hostapd.conf" 
    if [ -f $src_file ]; then
        echo "COPY HOSTAPD" 
        log "copy $src_file -> $tgt_file"
        sudo cp "$src_file" "$tgt_file" || pNOK
        sudo chown root:root "$tgt_file"
        sudo chmod 755 "$tgt_file"
        pOK
    fi
    # if dnsmasq of device exists copy
    # "COPY HOSTAPD" 
    src_file="$weneco_dir/network/dnsmasq-$1.conf"
    tgt_file="/etc/dnsmasq.d/dnsmasq-${ifname}.conf" 
    if [ -f $src_file ]; then
        echo "COPY DNSMASQ" 
        log "copy $src_file -> $tgt_file"
        sudo cp "$src_file" "$tgt_file" || pNOK
        sudo chown root:root "$tgt_file"
        sudo chmod 755 "$tgt_file"
        pOK
    fi
  
    # RESTART INTERFACE
    restart_interface $1
}

# QUERY NEW DHCP ADDRESS
function query_dhcp(){
    getIfName $1
    sudo dhclient ${ifname} && pOK || pNOK
}


# PATCH LOG DIRECTORY
function patch_logs(){
    log "patching log-dir"
    eval "sudo find /var/log -type f -exec chmod o+r {} \;" && pOK || pNOK
    eval "sudo find /tmp -type f -name *.log -exec chmod o+r {} \;" && pOK || pNOK
}

# PATCH NETWORK DIRECTORY
function patch_network(){
    log "patching weneco/network"
    eval "sudo chown root:www-data $weneco_dir/network/*.*" || pNOK
    eval "sudo chmod 664 $weneco_dir/network/*.*" || pNOK
    pOK
}

# PATCH TEMP DIRECTORY
function patch_temp(){
    log "patching temp_dir"
    eval "sudo chmod -R 777 $temp_dir" || pNOK
    pOK
}

cmd=$1
par=$2

# REBOOT
case $cmd in
    reboot)
        #sudo systemctl reboot && ok || nok
        log "reboot command received"
        eval "sudo shutdown -r -t sec 5 &" && xOK || xNOK   
        ;;
    restart_network)
        # RESTART NETWORKING
        restart_network
        xRET
        ;;
    restart_interface)
        # RESTART INTERFACE
        restart_interface $par
        xRET
        ;;
    patch_logs) 
        # PATCH LOG-DIR
        patch_logs
        xRET
        ;;
    apply_settings)
        # APPLY SETTINGS FOR INTERFACE
        apply_all $par
        xRET
        ;;
    query_dhcp)
        # QUERY NEW DHCP ADDRESS
        query_dhcp $par
        xRET
        ;;
    *)
        echo "UNKNOWN COMMAND '$cmd'"
        xNOK
esac
