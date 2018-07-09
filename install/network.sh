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
#                           Change network settings
#
source common.sh

# SHOW CONFIGURE NETWORK DIALOG
function configure_network(){
    echo -e ""
    echo -e "${gn}----------------------- ${nc}"
    echo -e "${gn} NETWORK CONFIGURATION ${nc}"
    echo -e "${gn}-----------------------${nc}"
    echo -e ""
    echo -e "You can configure your network devices"
    echo -e "You are asked for each device separately"
    echo -n "Continue? [y/N]: "
    read answer
    if [[ $answer == "y" ]]; then
        echo -n "Delete old files? (You may have to configure all devices) [y/N]: "
        read answer
        if [[ $answer == "y" ]]; then
            sudo rm -f "$weneco_dir/config/device*.network"  || install_error "Unable to delete old network config"
        fi
        config_network_devices
    fi
}

# SHOW CONFIGURATION DIALOG
# CREATE FILE /$weneco_dir/config/deviceX.network
function config_device(){
    echo -e "-----------------------------------"
	echo -e -n "${ye}CONFIGURE DEVICE '$1'? [y/N/c]: ${nc}"
    read answer
    if [[ $answer == "y" ]]; then
       	echo -n "use DHCP? [y/N]: "
        read dhcp
        if [[ $dhcp == "y" ]]; then
            dhcp="yes"
            answer="y"
        else
            echo -n "enter ip-address and subnet eg. [10.10.10.10/24]: "
            read ip
            echo -n "enter gateway eg. [10.10.10.1]: "
            read gw
            echo -n "enter dns-server eg. [10.10.10.1]: "
            read dns
            echo -e "-----------------"
            echo -e "${ye}Summary:${nc}"
            echo -e "ip-address: $ip"
            echo -e "gateway:    $gw"
            echo -e "dns-server: $dns"
            echo -n "save configuration? [y/N]: "
            read answer
        fi
        # SAVE FILE 
        if [[ $answer == "y" ]]; then
            # CREATE NETWORK CONFIG FILE
            file="$weneco_dir/config/device$2.network"
            log_ne "creating file '$file'"
            sudo cp "$weneco_dir/config/template.network" "$file" || install_error "Unable to copy network config"
            echo "[Match]" >> $file
            echo "Name=$1" >> $file
            echo "" >> $file
            echo "[Network]" >> $file
            if [ $dhcp == "yes" ]; then 
                echo "DHCP=yes" >> $file
            else
                echo "Address=$ip" >> $file
                echo "Gateway=$gw" >> $file
                echo "DNS=$dns" >> $file
            fi
            log_ok
        else
            log_warn -e "$file not saved"
        fi 
    elif [[ $answer == "c" ]]; then
        install_error "aborted by user"
    fi
}

# LOOP THROUG ALL DEVICES
function config_network_devices(){
	#get network device names
	
	devices=$(ls -I lo /sys/class/net)
	array=(${devices// / })
	for i in "${!array[@]}"
	do
        config_device ${array[$i]} $i
	done
    overwrite_networkfiles
}



