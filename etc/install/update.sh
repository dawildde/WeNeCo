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

update_steps=""

# GET UPDATE STEPS
function get_update_steps(){
    # fetch versions
    old_version=$(cat $weneco_dir/.version)
    new_version=$(cat $setup_root/.version)
    
    log_ne "Generating Update-steps from $old_version to $new_version"
    
    # GENERATE UPDATE STEPS FROM
    # V0.0.4 -> V0.0.5
    if [ "$old_version" == "0.0.4" ]; then
        add_step "copy_etc_files"
        add_step "set_permissions"
        add_step "patch_sudoers"
        old_version="0.0.5"     # set next version here
    fi
    # V0.0.5 -> V0.0.6
    if [ "$old_version" == "0.0.5" ]; then
        full_update
        old_version="0.0.6"     # set next version here
    fi
    # V0.0.6 -> V0.0.7
    if [ "$old_version" == "0.0.6" ]; then
        full_update
        old_version="0.0.7"     # set next version here
    fi
    
    # VXXXX
    if [ "$old_version" == "9.9.9" ]; then
        #add_step "DO SOMETHING LIKE:"
        #add_step "copy .version $weneco_dir/.test"
        #add_step "copy_web_files"
        #add_step "copy_etc_files"
        #add_step "set_permissions"
        #add_step "patch_sudoers"
        old_version="0.0.0"     # set next version here
    fi
    
    log_ok
}

# ADD STEP TO ARRAY
function add_step(){
    # add if not already exists
    if [[ ! " ${update_steps[@]} " =~ " $* " ]]; then
        update_steps+=("$*")
    fi
}

# FULL UPDATE
function full_update(){
    copy_files
    set_permissions
    patch_sudoers
    cleanup_setup
}

# UPDATE
function update_weneco(){
    echo -e "${gn}----------------------- ${nc}"
    echo -e "${gn}    Updating WeNeCo ${nc}"
    echo -e "${gn}-----------------------${nc}"
    
    #download_latest # done by weneco.sh
    get_update_steps
    for step in "${update_steps[@]}"
    do
        # executing step
        eval $step || install_error "Update Error - Unable to perform '${step}'"
    done
    cleanup_setup
}

# ONLY START WITH MAIN-SETUP-SCRIPT
if [ $main != "setup.sh" ]; then
    install_error "Please run 'setup.sh'"
fi

