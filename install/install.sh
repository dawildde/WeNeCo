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
#                                 Installer

# SOURCES
root_dir=$(dirname $(readlink -f $0))
source "$root_dir/common.sh"
source "$root_dir/welcome.sh"
source "$root_dir/common.sh"
source "$root_dir/patch.sh"

#------------------------------------
#    CONFIG INSTALL MENU
#------------------------------------

# CONFIGURE INSTALLATION BY USER
function config_install(){
    echo -e ""
    echo -e "${gn}------------------------------ ${nc}"
    echo -e "${gn}  Installation configuration ${nc}"
    echo -e "${gn}------------------------------${nc}"
    echo -e "${ye}Services wicd, dhcpcd, conman and NetworkManager will be DISABLED${nc}"
    echo -e "${ye}It's recommed to do installation local not over ssh or network ${nc}"
    echo -e "Old configuration files will be backed up to install directory"
    echo -e ""
	# config php version
	config_php_version
    # weneco_dir
    echo -n "Change install directory: '${weneco_dir}'? [y/N]:"
    read answer
    if [[ $answer == "y" ]]; then
        echo -n "enter new directory: "
        read new_dir
        weneco_dir=$new_dir
		# replace dir in config.sh
		eval "sed -i '/weneco_dir=/c\weneco_dir=\"$new_dir\"' $root_dir/config.sh"
    fi
    if [ -d "$weneco_dir" ]; then
        echo -e "content of '${weneco_dir}' will be moved to ${weneco_dir}.date" 
    fi
    # webroot_dir
    echo -n "Change Lighttpd directory: '${webroot_dir}'? [y/N]:"
    read answer
    if [[ $answer == "y" ]]; then
        echo -n "enter new directory: "
        read new_dir
        webroot_dir=$new_dir
		# replace dir in config.sh
		eval "sed -i '/webroot_dir=/c\webroot_dir=\"$new_dir\"' $root_dir/config.sh"
    fi
    if [ -d "$webroot_dir" ]; then
        echo -e "content of '${webroot_dir}' will be moved to ${webroot_dir}.date" 
    fi
    
    echo -e ""
    echo -e "${ye}Summary:${nc}"
    echo -e "-----------------"    
    echo -e "PHP-Version: $php_package"
    echo -e "install-directory: '$weneco_dir'"
    echo -e "website-directory: '$webroot_dir'"
    echo -e ""
    echo -n "Complete installation with these values? [y/N]: "
    read answer
    if [[ $answer != "y" ]]; then
        echo -e "${rd}Installation aborted by user${nc}"
        exit 0
    fi
}


# INSTALL ALL
function install_weneco(){
    check_system
    config_install
    create_directories
    backup_config
    update_system
    install_dependencies
    download_latest
    move_files
    patch_all
    configure_network
    overwrite_systemfiles
    disable_services 
    enable_systemd
}

# ONLY START MAIN-SCRIPT
if [ $main != "weneco.sh" ]; then
	install_error "Please run 'weneco.sh'"
fi