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
#       \   \ ;     \   \  / ;   |.'       \   \  /  \   \ .~
#        '---"       `----'  '---'          `----'    `---`
#
#                          Web Network Configuration
#
#                                 Installer

# Outputs install error log line and exits with status code 1
function install_error() {
    echo -e "\033[1;37;41mWeNeCo Install Error: $*\033[m"
    exit 1
}

# REMOVE OLD Installer
if [ -d "/tmp/weneco" ]; then
    sudo rm -R "/tmp/weneco" || install_error "Unable to delete old download from '/tmp/weneco'"
fi

# DOWNLOAD NEW INSTALLER 

if [ "$(basename $(dirname $(readlink -f $0)))" == "weneco_dev" ]; then
    # DEVELOPMENT VERSION (LOCAL)
    echo -ne "\033[1;33mWeNeCo Install:\033[0m USING LOCAL DEVELOPER_VERSION..."
    sudo cp -R "/var/www/html/weneco_dev/" "/tmp/weneco"  || install_error "Unable to copy DEVELOPER_VERSION" 
else
    # OFFICIAL VERSION (GIT)
    echo -ne "\033[1;30mWeNeCo Install:\033[0m Downloading files from github..."
    git clone https://github.com/dawildde/WeNeCo "/tmp/weneco" || install_error "Unable to download files from github"  
fi
echo -e "\033[1;32m OK \033[0m"

sleep 1s

# RUN SETUP
exec sudo bash /tmp/weneco/etc/install/setup.sh || install_error "Unable to execute installer" 
exit 0

