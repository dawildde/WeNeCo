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
#                              Common Functions
#

# SOURCES
root_dir=$(dirname $(readlink -f $0))
source "$root_dir/config.sh"

# GET BASESCRIPT NAME
main=$(basename ${BASH_SOURCE[${#BASH_SOURCE[@]} - 1]})

#------------------------------------
#          LOG FUNCTIONS
#------------------------------------

# Outputs a log line
function log() {
    echo -e "${gn}WeNeCo Install: $1 ${nc}"
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

# Outputs log text and executes something
function log_exec(){
	log_ne $1
	eval "$2" && log_ok || log_failed
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
    sudo mkdir -p "$weneco_dir/network"
	sudo mkdir -p "$weneco_dir/config"
    
    log_ok
}

# MOVE FILES TO APP DIRECTORIES
function move_files(){
    if [ -d "$webroot_dir" ] && [ -d "$weneco_dir" ]; then
        log_ne "moving files"
        sudo mv "$webroot_dir/config" "$weneco_dir" || install_error "Unable to move files to $weneco_dir"
        sudo mv "$webroot_dir/install" "$weneco_dir" || install_error "Unable to move files to $weneco_dir"
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
    if [ -f "$weneco_dir/network/device0.network" ]; then
        log_ne "copy network files to '/etc/systemd/network/'"
        # backup old files
        for file in /etc/systemd/network/device*.network
        do
            backup_file $file
        done
        sudo rm $weneco_dir/network/device*.network # remove old files
        sudo cp -p $weneco_dir/network/device*.network /etc/systemd/network/ || install_error "Unable to overwrite network files in '/etc/systemd/network/'"
        log_ok
    fi
}

#------------------------------------
#         BACKUP FUNCTIONS
#------------------------------------

# BACKUP SINGLE FILE
#   save first backup file as .org
#   save last backup file as .latest
#   save backups between as .filedate
function backup_file(){
    if [ -f $1 ]; then
        fname=$(basename $1)
        ftarget="$weneco_dir/backups/$fname"
        # save first backup file as filename.org
        if [ ! -f "$ftarget.org" ]; then
            ftarget="$ftarget.org"            
        else
            # if .latest exists move it to .filedate
            if [ -f "$ftarget.latest" ]; then
                fdate="$(date -r "$ftarget.latest" +%Y-%m-%d_%H:%M)"
                sudo mv "$ftarget.latest" "${ftarget}.${fdate}"
            fi
            # save latest version as .latest
            ftarget="$ftarget.latest"
        fi 

        sudo cp $1 $ftarget || install_error "Unable to backup '$1'"
    fi
}
    
# BACKUP OLD CONFIGURATIONS
function backup_config(){
    log_ne "backup old configuration files"
    backup_file /etc/network/interfaces
    backup_file /etc/hostapd/hostapd.conf 
    backup_file /etc/dnsmasq.conf 
    backup_file /etc/dhcpcd.conf
    backup_file /etc/rc.local
    log_ok
}

#------------------------------------
#         INSTALL FUNCTIONS
#------------------------------------

# SET PHP VERSION
function config_php_version(){
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
	# replace package in config.sh
	eval "sed -i '/php_package=/c\php_package=\"$php_package\"' $root_dir/config.sh"
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


#------------------------------------
#       DOWNLOAD FUNCTIONS
#------------------------------------

# DOWNLOAD NEWEST FILES
function download_lates(){
    log_ne "Cloning latest files from github"
    git clone https://github.com/dawildde/WeNeCo /tmp/weneco || install_error "Unable to download files from github"
	log_ok
	# backup old webroot
	log_ne "Moving files"
	if [ -d "$webroot_dir" ]; then
        sudo mv $webroot_dir "$webroot_dir.`date +%F-%R`" || install_error "Unable to backup old webroot directory"
    fi
	# move new files
    sudo mv /tmp/weneco $webroot_dir || install_error "Unable to move webgui to web root"
    log_ok
}

#------------------------------------
#       NETWORK SERVICE FUNCTIONS
#------------------------------------

# DISABLE SERVICE
function disable_service(){
    state=$(eval "systemctl is-active $1")
    if [ $state == "active"  ]
    then
        log_ne "disabling $1"
        eval "sudo systemctl stop $1" && log_ok || log_failed
        eval "sudo systemctl disable_service $1.service"
    else
        log "$1 not active"
    fi
}

# ENABLE SERVICE
function enable_service(){
    log_ne "Starting $1"
    eval "sudo systemctl start $1" && log_ok || log_failed
    eval "sudo systemctl enable $1" 
}

# DISABLE OTHER SERVICES
function disable_services(){
    disable_service "wicd"
    disable_service "dhcpcd"
    disable_service "NetworkManager"
    disable_service "connman"
}

# ENABLE SYSTEMD NETWORKING
function enable_systemd(){
    enable_service "systemd-networkd.service"
    enable_service "systemd-resolved.service"
}


# DISABLE SYSTEMD
function disable_systemd(){
    disable_service "systemd-networkd.service"
    disable_service "systemd-resolved.service"
}
