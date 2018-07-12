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
#                                Update Script

# SOURCES
source_dir=$(dirname $(readlink -f $0))
source "$source_dir/common.sh"


# GET UPDATE STEPS
function get_update_steps(){
    # fetch versions
    old_etc_version=$(cat $weneco_dir/.version)
    old_web_version=$(cat $weneco_dir/.version)
    new_etc_version=$(cat $setup_root/.version)
    new_web_version=$(cat $setup_root/.version)
}




# UPDATE
function update_weneco(){
    echo -e "${gn}----------------------- ${nc}"
    echo -e "${gn}    Updating WeNeCo ${nc}"
    echo -e "${gn}-----------------------${nc}"
    download_latest
    copy_files
	set_permissions
    cleanup_setup
}

# ONLY START MAIN-SCRIPT
if [ $main != "weneco.sh" ]; then
	install_error "Please run 'weneco.sh'"
fi

