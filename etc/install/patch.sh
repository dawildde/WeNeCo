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

# SOURCES
source_dir=$(dirname $(readlink -f $0))
source "$source_dir/common.sh"

# SET FILE PERMISSIONS
function set_permissions(){
    # patch weneco_dir
    log_ne "set WeNeCo file permissions"
    sudo chown -R root:$weneco_user "$weneco_dir" || install_error "Unable to change file ownership for '$weneco_dir'"
    sudo find "$weneco_dir" -type d -exec chmod 755 {} + || install_error "Unable to change file permissions for '$weneco_dir'"
    sudo find "$weneco_dir" -type f -exec chmod 644 {} + || install_error "Unable to change file permissions for '$weneco_dir'"
    sudo chmod 775 "$weneco_dir/network" || install_error "Unable to change file permissions for '$weneco_dir/network'"
    sudo find "$weneco_dir/network" -type f -exec chmod 664 {} + || install_error "Unable to change file permissions for '$weneco_dir/network'"
    sudo find "$weneco_dir/script" -type f -exec chmod 754 {} + || install_error "Unable to change file permissions for '$weneco_dir/script'"
    
    # patch html-dir
    sudo chown -R $weneco_user:$weneco_user "$webroot_dir" || install_error "Unable to change file ownership for '$webroot_dir'"
    sudo find "$webroot_dir" -type d -exec chmod 755 {} + || install_error "Unable to change file permissions for '$webroot_dir'" 
    sudo find "$webroot_dir" -type f -exec chmod 644 {} + || install_error "Unable to change file permissions for '$webroot_dir'" 
    
    # path log-dir
    sudo find /var/log -type f -exec chmod o+r {} \;
    
    log_ok
    
}

# Add a single entry to the sudoers file
function sudo_add() {
    sudo bash -c "echo \"$weneco_user ALL=(ALL) NOPASSWD:$1\" | (EDITOR=\"tee -a\" visudo)" &>/dev/null \
        || install_error "Unable to patch /etc/sudoers"

}

# Adds www-data user to the sudoers file with restrictions on what the user can execute
# use parameter "force" to overwrite even count is eval
function patch_sudoers() {
    # Set commands array
    cmds=(
        "/bin/cat /etc/systemd/network/device[0-9].network"
        "/bin/rm /etc/systemd/network/device[0-9].network"
        "/bin/cp $weneco_dir/network/device[0-9].network /etc/systemd/network/device[0-9].network"
        "/bin/cp $tmp_dir/weneco.auth $weneco_dir/weneco.auth"
        "/bin/rm $weneco_dir/weneco.auth"
        "/bin/ls -I lo /sys/class/net"
        "$weneco_dir/script/execute.sh patch_logs"
        "$weneco_dir/script/execute.sh apply_settings"
        "$weneco_dir/script/execute.sh apply_settings device[0-9]"
        "$weneco_dir/script/execute.sh restart_interface device[0-9]"
        "$weneco_dir/script/execute.sh restart_network"
        "$weneco_dir/script/execute.sh reboot"
        "$weneco_dir/script/execute.sh getNetConf device[0-9]"
        "$weneco_dir/script/execute.sh query_dhcp device[0-9]"
        "$weneco_dir/script/wpa_supplicant.sh start device[0-9]"
        "$weneco_dir/script/wpa_supplicant.sh stop device[0-9]" 
        "$weneco_dir/script/wpa_supplicant.sh scan device[0-9]"
        "$weneco_dir/script/wpa_supplicant.sh scan -t [0-9] device[0-9]"
        "$weneco_dir/script/wpa_supplicant.sh copy_conf device[0-9]"
        "$weneco_dir/script/wpa_supplicant.sh connect device[0-9]"
        "$weneco_dir/script/wpa_supplicant.sh disconnect device[0-9]"
        "$weneco_dir/script/restart_service.sh dns"
        "$weneco_dir/script/restart_service.sh network"
        "$weneco_dir/script/restart_service.sh wpasupplicant device[0-9]"
    )

    # Check if sudoers needs patching
    if [[ $(sudo grep -c $weneco_user /etc/sudoers) -ne ${#cmds[@]} ]] || [[ "$1" == "force" ]]
    then
        # Sudoers file has incorrect number of commands. Wiping them out.
        log_ne "Cleaning sudoers file"
        eval "sudo sed -i '/$weneco_user/d' /etc/sudoers"
        log_ok
        log_ne "Patching system sudoers file"
        # patch /etc/sudoers file
        for cmd in "${cmds[@]}"
        do
            sudo_add $cmd
            IFS=$'\n'
        done
        log_ok
    else
        log "Sudoers file already patched"
    fi
}

# PATCH FILES AND SUDOERS
function patch_all(){
    set_permissions
    patch_sudoers
}

# ONLY START WITH MAIN-SETUP-SCRIPT
if [ $main != "setup.sh" ]; then
    install_error "Please run 'setup.sh'"
fi