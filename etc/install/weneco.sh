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
#       \   \ ;     \   \  / ;   |.'       \   \  /  \   \ .~
#        '---"       `----'  '---'          `----'    `---`
#
#                          Web Network Configuration
#
#                                 Installer

# SOURCES
source_dir=$(dirname $(readlink -f $0))         # DIRECTORY FOR INSTALLER SOURCE FILES (.../weneco/etc/install)

source "$source_dir/common.sh"
source "$source_dir/install.sh"
source "$source_dir/welcome.sh"
source "$source_dir/update.sh"
source "$source_dir/patch.sh"
source "$source_dir/network.sh"
source "$source_dir/repair.sh"

function showMenu(){
    echo -e "${gn}----------------------- ${nc}"
    echo -e "${gn} Welcome to WeNeCo ${nc}"
    echo -e "${gn}-----------------------${nc}"
    echo -e " 1) Install WeNeCo"
    echo -e " 2) Update WeNeCo"
    echo -e " 3) WeNeCo Repair Assistant"
    echo -e " 4) WeNeCo Network Assistant"
    echo -e " 9) exit"
    echo -n "Select your choice: "
    read answer
    if [[ $answer == "1" ]]; then
        install_weneco
    elif [[ $answer == "2" ]]; then
        update_weneco
    elif [[ $answer == "3" ]]; then
        repair_weneco
    elif [[ $answer == "4" ]]; then
        configure_network
    elif [[ $answer == "9" ]]; then
        cleanup_setup
        exit 0
    else
        install_error "UNKNOWN CHOICE"
    fi
}


# SHOW MENU IF STARTET FROM DOWNLOAD-DIR
if [ "$setup_root" == "/tmp/weneco" ]; then
    showMenu
else
    display_logo
    download_latest
    exec sudo bash /tmp/weneco/etc/install/weneco.sh || install_error "Unable to execute installer" 
    exit 0
fi
