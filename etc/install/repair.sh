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
source_dir=$(dirname $(readlink -f $0))
source "$source_dir/common.sh"
source "$source_dir/patch.sh"

# SHOW REPAIR MENU
function repair_weneco(){
    display_logo
    echo -e "${gn}------------------------------ ${nc}"
    echo -e "${gn}    WeNeCo Repair Assistant ${nc}"
    echo -e "${gn}------------------------------${nc}"
    echo -e " 1) (Re)install dependencies"
    echo -e " 2) Set WeNeCo file-permissions"
    echo -e " 3) Patch sudoers"
    echo -e " 8) (Re)start networking"
    echo -e " 9) exit"
    while true;
    do
        echo -n "Select your choice: "
        read answer
        if [[ $answer == "1" ]]; then
            install_dependencies
        elif [[ $answer == "2" ]]; then
            set_permissions
        elif [[ $answer == "3" ]]; then
            patch_sudoers "force"
        elif [[ $answer == "8" ]]; then
            log_ne "restarting network services"
            eval "sudo bash $weneco_dir/script/restart_network.sh" && log_ok || log_failed
        elif [[ $answer == "9" ]]; then
            cleanup_setup
            break
        else
            echo -e "UNKNOWN CHOICE"
            sleep 1s
        fi
    done
}

# ONLY START WITH MAIN-SETUP-SCRIPT
if [ $main != "setup.sh" ]; then
    install_error "Please run 'setup.sh'"
fi


