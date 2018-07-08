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
#                       Common functions for installation
#

weneco_dir="/etc/weneco"
webroot_dir="/var/www/html/weneco"
weneco_user="www-data"

# Outputs a log line
function log() {
    echo -e "\033[1;32mWeNeCo Install: $*\033[m"
}

# Outputs install error log line and exits with status code 1
function install_error() {
    echo -e "\033[1;37;41mWeNeCo Install Error: $*\033[m"
    exit 1
}

# CHECK SYSTEM 
function check_system() {
    version=$( systemctl --version | grep -o 'systemd [0-9]*'  | grep -o '[0-9]*' )
    if [ $version -lt 216 ]
    then
        install_error "systetmd version $version is to old. please upgrade your system"
    fi
}

# UPDATE SYSTEM
function update_system(){
    log "Updating sources"
    sudo apt-get update || install_error "Unable to update package list"
}

# CONFIGURE INSTALLATION BY USER
function config_install(){
    log "CONFIGURE INSTALLATION"
    echo -e "Install directory: ${weneco_dir}"
    echo -e "Lighttpd directory: ${webroot_dir}"
    echo -e "\033[1;33mServices wicd, dhcpcd, conman and NetworkManager will be DISABLED\033[m"
    echo -e "\033[1;33mIt's recommed to do installation local not over network, ssh\033[m"
    echo -e "Old configuration files will be backed up to install directory"
    if [ -d "$webroot_dir" ]; then
        echo -e "content of '${webroot_dir}' will be moved to ${webroot_dir}.date" 
    fi
    echo -n "Complete installation with these values? [y/N]: "
    read answer
    if [[ $answer != "y" ]]; then
        echo -e "\033[1;31mInstallation aborted by user\033[m"
        exit 0
    fi
}

# CREATE DIRECTORIES
function create_directories(){
    install_log "Creating RaspAP directories"
    # Main directory
    if [ -d "$weneco_dir" ]; then
        mv $weneco_dir "$weneco_dir.`date +%F-%R`" || install_error "Unable to move old '$weneco_dir' out of the way"
    fi
    mkdir -p "$weneco_dir" || install_error "Unable to create directory '$weneco_dir'"
    
    # Backup directory
    sudo mkdir -p "$weneco_dir/backups"
    
    # Backup directory
    sudo mkdir -p "$weneco_dir/network"
}
    
# BACKUP OLD CONFIGURE
function backup_config(){
    log "backup old configuration files"
    if [ -f /etc/network/interfaces ]; then
        sudo cp /etc/network/interfaces "$weneco_dir/backups/interfaces.`date +%F-%R`"
    fi

    if [ -f /etc/hostapd/hostapd.conf ]; then
        sudo cp /etc/hostapd/hostapd.conf "$weneco_dir/backups/hostapd.conf.`date +%F-%R`"
    fi

    if [ -f /etc/dnsmasq.conf ]; then
        sudo cp /etc/dnsmasq.conf "$weneco_dir/backups/dnsmasq.conf.`date +%F-%R`"
    fi

    if [ -f /etc/dhcpcd.conf ]; then
        sudo cp /etc/dhcpcd.conf "$weneco_dir/backups/dhcpcd.conf.`date +%F-%R`"
    fi

    if [ -f /etc/rc.local ]; then
        sudo cp /etc/rc.local "$weneco_dir/backups/rc.local.`date +%F-%R`"
    fi
}

# INSTALL PACKAGE
function install_package(){
    state=$(eval "systemctl is-active $1")
    if [ $state == "inactive"  ]
    then
        log "instaling $1"
        eval "sudo apt-get install $1"
    else
        log "$1 already installed"
    fi
}

function install_dependencies(){
    sudo apt-get install lighttpd $php_package git hostapd dnsmasq || install_error "Unable to install dependencies"
}

# DOWNLOAD NEWEST FILES
function download_lates(){
    if [ -d "$webroot_dir" ]; then
        sudo mv $webroot_dir "$webroot_dir.`date +%F-%R`" || install_error "Unable to remove old webroot directory"
    fi

    install_log "Cloning latest files from github"
    git clone https://github.com/dawildde/WeNeCo /tmp/weneco || install_error "Unable to download files from github"
    sudo mv /tmp/weneco $webroot_dir || install_error "Unable to move webgui to web root"
}

# MOVE FILES TO APP DIRECTORIES
function move_files(){
    if [ -d "$webroot_dir" ] && [ -d "$weneco_dir" ]; then
        log "moving files"
        echo "'$webroot_dir && '$weneco_dir'"
        sudo mv "$webroot_dir/config" "$weneco_dir" || install_error "Unable to move files to $weneco_dir"
    else
        install_error "WeNeCo directories are not existing. Please reinstall"
    fi
}

# UPDATE SYSTEM FILES
function overwrite_systemfiles(){
    sudo cp "$weneco_dir/config/interfaces" /etc/network/interfaces
    sudo rm /etc/resolv.conf
}

# DISABLE SERVICE
function disable_service(){
    state=$(eval "systemctl is-active $1")
    if [ $state == "active"  ]
    then
        log "disabling $1"
        eval "systemctl stop $1"
        eval "systemctl disable_service $1.service"
    else
        log "$1 not active"
    fi
}

# DISABLE OTHER SERVICES
function disable_services(){
    disable_service wicd
    disable_service dhcpcd
    disable_service NetworkManager
    disable_service connman
}

# ENABLE SYSTEMD NETWORKING
function enable_systemd(){
    sudo systemctl start systemd-networkd.service
    sudo systemctl start systemd-resolved.service
    
    sudo systemctl enable systemd-networkd.service
    sudo systemctl enable systemd-resolved.service 
    
    sudo ln -s /var/run/systemd/resolve/resolv.conf /etc/resolv.conf 
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
    overwrite_systemfiles
    disable_services 
    enable_systemd
}

# UPDATE
function update_weneco(){
    download_latest
}

