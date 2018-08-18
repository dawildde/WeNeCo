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

declare -a update_steps

# GET UPDATE STEPS
function get_update_steps(){
    # fetch versions
    old_version=$(cat $weneco_dir/.version)
    new_version=$(cat $setup_root/.version)
    
    log_ne "Generating Update-steps from $old_version to $new_version"
    
    # GENERATE UPDATE STEPS FROM
    
    # V0.1.0 -> V0.1.1
    if [ "$old_version" == "0.1.0" ]; then
        full_update
        old_version="0.1.1"     # set next version here
    fi
    
    # V0.1.1 -> V0.1.2
    if [ "$old_version" == "0.1.1" ]; then
        full_update
        old_version="0.1.2"     # set next version here
    fi
    
    # V0.1.2 -> V0.2.0
    if [ "$old_version" == "0.1.2" ]; then
        full_update
        old_version="0.2.0"     # set next version here
    fi
    
    # V0.20 -> V0.2.1
    if [ "$old_version" == "0.2.0" ]; then
        full_update
        old_version="0.2.1"     # set next version here
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
    
    #NOT-FOUND -> FULL-UPDATE
    if [ "$old_version" != "$new_version" ]; then
        echo "STEPS: '${#update_steps[@]}'"
        if [ ${#update_steps[@]} -eq 0 ]; then
            full_update
        fi
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
    add_step copy_files
    add_step set_permissions
    add_step patch_sudoers
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
    exit 0
}

# ONLY START WITH MAIN-SETUP-SCRIPT
if [ $main != "setup.sh" ]; then
    install_error "Please run 'setup.sh'"
fi

