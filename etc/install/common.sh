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
source_dir=$(dirname $(readlink -f $0))         # DIRECTORY FOR INSTALLER SOURCE FILES (.../weneco/etc/install)
setup_root=$(dirname $(dirname $source_dir))    # WENECO ROOT DIRECTORY (.../weneco)
source "$source_dir/config.sh"
source "$source_dir/welcome.sh"

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
#       DOWNLOAD FUNCTIONS
#------------------------------------

# DOWNLOAD NEWEST FILES
function download_latest(){
    log_ne "Cloning latest files from github"
    # REMOVE OLD
    if [ -d /tmp/weneco ]; then
        sudo rm -R "/tmp/weneco" || install_error "Unable to delete old download from '/tmp/weneco'"
    fi
    # DOWNLOAD NEW
    #git clone https://github.com/dawildde/WeNeCo /tmp/weneco || install_error "Unable to download files from github"
    sudo cp -R "/var/www/html/weneco_dev/" "/tmp/weneco"
    log_ok
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
    log_ne "Creating WeNeCo directory '$weneco_dir'"
    # Main directory
    if [ -d "$weneco_dir" ]; then
        mv "$weneco_dir" "$weneco_dir.`date +%F-%R`" || install_error "Unable to move old '$weneco_dir' out of the way"
    fi
    mkdir -p "$weneco_dir" || install_error "Unable to create directory '$weneco_dir'"
    
    sudo mkdir -p "$weneco_dir/backups" # Backup directory
    sudo mkdir -p "$weneco_dir/network" # Network-config directory
    sudo mkdir -p "$weneco_dir/config"  # General-config / Template directory
    log_ok
    
    # Main directory
    log_ne "Creating WeNeCo Web-directory '$webroot_dir'"
    if [ -d "$webroot_dir" ]; then
        mv "$webroot_dir" "$webroot_dir.`date +%F-%R`" || install_error "Unable to move old '$webroot_dir' out of the way"
    fi
    sudo mkdir -p "$webroot_dir"
}

# COPY FILES TO /etc/weneco
function copy_etc_files(){
    if [ -d "/tmp/weneco/etc" ]; then
        if [ -d "$weneco_dir" ]; then
            # BACKUP OLD
            backup_weneco
            # COPY FILES
            log_ne "copy files to '$weneco_dir'"
            sudo cp -R "/tmp/weneco/etc/"* "$weneco_dir" || install_error "Unable to copy files to $weneco_dir"  # moving scripts
            sudo cp "/tmp/weneco/.version" "$weneco_dir"   # copy .version file
            sudo cp "/tmp/weneco/weneco.sh" "$weneco_dir"   # copy install-script file
            log_ok
        else
            install_error "WeNeCo directory ('$weneco_dir') is not existing. Please reinstall"
        fi
    else
        install_error "Install-Source directory ('/tmp/weneco') is not existing. Please reinstall"    
    fi
}

# COPY WEB-FILES
function copy_web_files(){
    if [ -d "/tmp/weneco" ]; then
        if [ -d "$webroot_dir" ]; then  
            # BACKUP OLD
            backup_webroot    
            # copy website exclude /etc
            log_ne "copy files to '$webroot_dir'"
            sudo cp -R "/tmp/weneco/"* "$webroot_dir" || install_error "Unable to move files to $webroot_dir"  # moving website
            sudo cp "/tmp/weneco/.version"  "$webroot_dir"   # copy .version file
            sudo rm -R "$webroot_dir/etc"    # remove /etc from webroot_dir
            sudo rm "$webroot_dir/weneco.sh"    # remove install-script
            log_ok
        else
            install_error "WeNeCo directory ('$webroot_dir') is not existing. Please reinstall"
        fi
    else
        install_error "Install-Source directory ('/tmp/weneco') is not existing. Please reinstall"    
    fi
}

# COPY BY PARAMETERS
function copy(){
    if [ -d "/tmp/weneco" ]; then
        src="/tmp/weneco/$1"
        tgt="$2"
        if [ ! -d "$src" ] || [ ! -f "$src" ]; then
            log_ne "copy '$src' -> '$tgt'"
            eval "sudo cp '$src' '$tgt'" || install_error "Unable to copy '$src' to '$tgt'"
            log_ok
        else
            install_error "Unable to copy - '$src' not existing" 
        fi
    else
        install_error "Install-Source directory ('/tmp/weneco') is not existing. Please reinstall"    
    fi
}

# MOVE FILES TO APP DIRECTORIES
function copy_files(){
    copy_etc_files
    copy_web_files
}

# CLEANUP SETUP FILES
function cleanup_setup(){
    log_ne "cleaning up"
    if [ -d /tmp/weneco ]; then
        sudo rm -R "/tmp/weneco"
    fi
    log_ok
}

# UPDATE SYSTEM FILES
function overwrite_systemfiles(){
    log_ne "overwrite system-files"
    sudo cp "$weneco_dir/config/interfaces" "/etc/network/interfaces"  || install_error "Unable to overwrite '/etc/network/interfaces/'"
    sudo cp "$weneco_dir/config/wpasupplicant@.service" "/etc/systemd/system/wpasupplicant@.service" || install_error "Unable to overwrite '/etc/systemd/system/wpasupplicant@.service'"
    if [ -f /etc/resolv.conf ]; then
        sudo rm /etc/resolv.conf
    fi
    log_ok
}

# COPY NETWORK SETTINGS
function overwrite_networkfiles(){
    log_ne "copy network files to '/etc/systemd/network/'"
    if [ -f "$weneco_dir/network/device0.network" ]; then
        # backup old files
        for file in /etc/systemd/network/device*.network
        do
            backup_file $file
            sudo rm $file # remove old files
        done
        sudo cp -p $weneco_dir/network/device*.network /etc/systemd/network/ || install_error "Unable to overwrite network files in '/etc/systemd/network/'"
        log_ok
    else
        log_warn "nothing to copy"
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

# BACKUP WENECO DIR
function backup_weneco(){
    log_ne "Backup old weneco dir"
    if [ -f "$weneco_dir/.version" ]; then
        oldv=$(cat "$weneco_dir/.version")
        sudo cp -R "$weneco_dir"  "${weneco_dir}_V${oldv}" || install_error "Unable to backup old version '${weneco_dir}_V${oldv}"
    fi
    log_ok
}

# BACKUP WEBROOT DIR
function backup_webroot(){
    log_ne "Backup old weneco dir"
    if [ -f "$webroot_dir/.version" ]; then
        oldv=$(cat "$webroot_dir/.version")
        sudo sudo cp -R "$webroot_dir"  "${webroot_dir}_V${oldv}" || install_error "Unable to backup old version '${webroot_dir}_V${oldv}"
    fi
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
    eval "sed -i '/php_package=/c\php_package=\"$php_package\"' $source_dir/config.sh"
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
