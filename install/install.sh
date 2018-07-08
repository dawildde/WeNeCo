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

# Show Welcome Message
display_logo
echo -e  "\033[1;32mWelcome to WeNeCo\033[m"
echo -e " 1) install WeNeCo"
echo -e " 2) update WeNeCo"
echo -e " 3) patch Filesystem"
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
elif [[ $answer == "9" ]]; then
    exit 0
else
    install_error "UNKNOWN CHOICE"
fi