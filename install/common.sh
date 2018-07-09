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

# SETTINGS
weneco_dir="/etc/weneco"
webroot_dir="/var/www/html/weneco"
weneco_user="www-data"


# TEXT COLORS
rd="\033[1;31m"
gn="\033[1;32m"
ye="\033[1;33m"
nc="\033[0m"

#------------------------------------
#    CONFIG INSTALL MENU
#------------------------------------

# CONFIGURE INSTALLATION BY USER
function config_install(){
    echo -e ""
    echo -e "${gn}------------------------------ ${nc}"
    echo -e "${gn}  Installation configuration ${nc}"
    echo -e "${gn}------------------------------${nc}"
    echo -e "${ye}Services wicd, dhcpcd, conman and NetworkManager will be DISABLED${nc}"
    echo -e "${ye}It's recommed to do installation local not over ssh or network ${nc}"
    echo -e "Old configuration files will be backed up to install directory"
    echo -e ""
    # PHP-Version
    echo -e "Which PHP-Version should be installed?"
    echo -e "1) PHP 5"
    echo -e "2) PHP 7 (recommed)"
    echo -n "Choose Version [1/2] :"
    read answer
    if [[ $answer == "1" ]]; then
        php_package="php5-cgi"
    else
        php_package="php7.0-cgi" 
    fi
    # weneco_dir
    echo -n "Change install directory: '${weneco_dir}'? [y/N]:"
    read answer
    if [[ $answer == "y" ]]; then
        echo -n "enter new directory: "
        read new_dir
        weneco_dir=$new_dir
    fi
    if [ -d "$weneco_dir" ]; then
        echo -e "content of '${weneco_dir}' will be moved to ${weneco_dir}.date" 
    fi
    # webroot_dir
    echo -n "Change Lighttpd directory: '${webroot_dir}'? [y/N]:"
    read answer
    if [[ $answer == "y" ]]; then
        echo -n "enter new directory: "
        read new_dir
        webroot_dir=$new_dir
    fi
    if [ -d "$webroot_dir" ]; then
        echo -e "content of '${webroot_dir}' will be moved to ${webroot_dir}.date" 
    fi
    
    echo -e ""
    echo -e "${ye}Summary:${nc}"
    echo -e "-----------------"    
    echo -e "PHP-Version: $php_package"
    echo -e "install-directory: '$weneco_dir'"
    echo -e "website-directory: '$webroot_dir'"
    echo -e ""
    echo -n "Complete installation with these values? [y/N]: "
    read answer
    if [[ $answer != "y" ]]; then
        echo -e "${rd}Installation aborted by user${nc}"
        exit 0
    fi
}

#------------------------------------
#          LOG FUNCTIONS
#------------------------------------

# Outputs a log line
function log() {
    echo -e "${gn}WeNeCo Install: $${nc}"
}

# Outputs a log line without newline
function log_ne(){
    echo -ne "\033[1;30mWeNeCo Install:${nc} $*"
}

# append OK
function log_ok(){
    echo -e "${gn} OK${nc} $*"
}

# append FAILED
function log_failed(){
    echo -e "${rd} FAILED${nc} $*"
}

# append OK
function log_warn(){
    echo -e "${ye} WARNING${nc} $*"
}

# Outputs install error log line and exits with status code 1
function install_error() {
    echo -e "\033[1;37;41mWeNeCo Install Error: $*\033[m"
    exit 1
}

#------------------------------------
#          SYSTEM FUNCTIONS
#------------------------------------

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

# CREATE DIRECTORIES
function create_directories(){
    log_ne "Creating WeNeCo directories"
    # Main directory
    if [ -d "$weneco_dir" ]; then
        mv $weneco_dir "$weneco_dir.`date +%F-%R`" || install_error "Unable to move old '$weneco_dir' out of the way"
    fi
    mkdir -p "$weneco_dir" || install_error "Unable to create directory '$weneco_dir'"
    
    # Backup directory
    sudo mkdir -p "$weneco_dir/backups"
    
    # Backup directory
    sudo mkdir -p "$weneco_dir/network"
    
    log_ok
}
    
# BACKUP OLD CONFIGURE
function backup_config(){
    log_ne "backup old configuration files"
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
    
    log_ok
}

# INSTALL PACKAGE
function install_package(){
    state=$(eval "systemctl is-active $1")
    if [ $state == "inactive"  ]
    then
        log_ne "instaling $1"
        eval "sudo apt-get install $1" || install_error "Unable to install $1"
        log_ok
    else
        log_warn "$1 already installed - skipped"
    fi
}

function install_dependencies(){
    install_package "lighttpd"
    install_package $php_package
    install_package "git" 
    install_package "hostapd" 
    install_package "dnsmasq"
}

# DOWNLOAD NEWEST FILES
function download_lates(){
    if [ -d "$webroot_dir" ]; then
        sudo mv $webroot_dir "$webroot_dir.`date +%F-%R`" || install_error "Unable to remove old webroot directory"
    fi

    log_ne "Cloning latest files from github"
    git clone https://github.com/dawildde/WeNeCo /tmp/weneco || install_error "Unable to download files from github"
    sudo mv /tmp/weneco $webroot_dir || install_error "Unable to move webgui to web root"
    log_ok
}

# MOVE FILES TO APP DIRECTORIES
function move_files(){
    if [ -d "$webroot_dir" ] && [ -d "$weneco_dir" ]; then
        log_ne "moving files"
        echo "'$webroot_dir && '$weneco_dir'"
        sudo mv "$webroot_dir/config" "$weneco_dir" || install_error "Unable to move files to $weneco_dir"
        log_ok
    else
        install_error "WeNeCo directories are not existing. Please reinstall"
    fi
}

# UPDATE SYSTEM FILES
function overwrite_systemfiles(){
    log_ne "overwrite system-files"
    sudo cp "$weneco_dir/config/interfaces" /etc/network/interfaces  || install_error "Unable to overwrite '/etc/network/interfaces/'"
    if [ -f /etc/resolv.conf ]; then
        sudo rm /etc/resolv.conf
    fi
    log_ok
}

# COPY NETWORK SETTINGS
function overwrite_networkfiles(){
    if [ -f "$weneco_dir/config/device0.network" ]; then
        log_ne "copy network files to '/etc/systemd/network/'"
        sudo mv "/etc/systemd/network/device*.network" "$weneco_dir/backups/network.`date +%F-%R`/" 2>/dev/nul
        sudo cp $weneco_dir/config/device*.network /etc/systemd/network/ || install_error "Unable to overwrite network files in '/etc/systemd/network/'"
        log_ok
    fi
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
    configure_network
    overwrite_systemfiles
    disable_services 
    enable_systemd
}

# UPDATE
function update_weneco(){
    echo -e "${gn}----------------------- ${nc}"
    echo -e "${gn}    Updating WeNeCo ${nc}"
    echo -e "${gn}-----------------------${nc}"
    download_latest
    move_files
}

