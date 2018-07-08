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
#                         Patching File Permissions

# SET FILE PERMISSIONS
function set_permissions(){
    # patch weneco_dir
    sudo chown -R $weneco_user:$weneco_user "$weneco_dir" || install_error "Unable to change file ownership for '$weneco_dir'"
    sudo chmod -R 750 $weneco_dir || install_error "Unable to change file permissions for '$weneco_dir'"
    sudo chmod -R 750 $weneco_dir/config/ || install_error "Unable to change file permissions for '$weneco_dir/config'"
    
    # patch html-dir
    sudo chown -R $weneco_user:$weneco_user "$webroot_dir" || install_error "Unable to change file ownership for '$webroot_dir'"
    sudo chmod -R 750 $webroot_dir || install_error "Unable to change file permissions for '$webroot_dir'" 
}

# Add a single entry to the sudoers file
function sudo_add() {
    sudo bash -c "echo \"$weneco_user ALL=(ALL) NOPASSWD:$1\" | (EDITOR=\"tee -a\" visudo)" \
        || install_error "Unable to patch /etc/sudoers"

}

# Adds www-data user to the sudoers file with restrictions on what the user can execute
function patch_sudoers() {
    # Set commands array
    cmds=(
        "/sbin/ifdown"
        "/sbin/ifup"
        "/bin/cat /etc/systemd/network/wired.network"
        "/bin/cat /etc/systemd/network/device[0-9].network"
        "/bin/cp $weneco_dir/config/wired.network /etc/systemd/network/wired.network"
        "/bin/cp $weneco_dir/config/device[0-9].network /etc/systemd/network/device[0-9].network"
    )

    # Check if sudoers needs patching
    if [ $(sudo grep -c $weneco_user /etc/sudoers) -ne ${#cmds[@]} ]
    then
        # Sudoers file has incorrect number of commands. Wiping them out.
        log "Cleaning sudoers file"
        eval "sudo sed -i '/$weneco_user/d' /etc/sudoers"
        log "Patching system sudoers file"
        # patch /etc/sudoers file
        for cmd in "${cmds[@]}"
        do
            sudo_add $cmd
            IFS=$'\n'
        done
    else
        log "Sudoers file already patched"
    fi
}


# PATCH FILES AND SUDOERS
function patch_all(){
    set_permissions
    patch_sudoers
}