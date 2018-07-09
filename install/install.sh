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

source welcome.sh
source common.sh
source patch.sh
source network.sh

# Show Welcome Message
display_logo
echo -e "${gn}----------------------- ${nc}"
echo -e "${gn} Welcome to WeNeCo ${nc}"
echo -e "${gn}-----------------------${nc}"
echo -e " 1) install WeNeCo"
echo -e " 2) update WeNeCo"
echo -e " 3) patch Filesystem"
echo -e " 4) change network settings"
echo -e " 9) exit"
echo -n "Select your choice: "
read answer
if [[ $answer == "1" ]]; then
    install_weneco
elif [[ $answer == "2" ]]; then
    update_weneco
elif [[ $answer == "3" ]]; then
    set_permissions
    patch_sudoers
elif [[ $answer == "4" ]]; then
    configure_network
elif [[ $answer == "9" ]]; then
    exit 0
else
    install_error "UNKNOWN CHOICE"
fi