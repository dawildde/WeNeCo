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

script_dir=$(dirname $(readlink -f $0))

# Outputs install error log line and exits with status code 1
function install_error() {
    echo -e "\033[1;37;41mWeNeCo Install Error: $*\033[m"
    exit 1
}

# MERGE CONFIGURATION FILE
function merge_config() {
    new_conf="/tmp/weneco/etc/config/config.sh"
    old_conf="$script_dir/config/config.sh"

    if [ -f $old_conf ]; then
        echo -ne "merging configuration file '$old_conf' with '$new_conf'"
        # Loop old conf-file
        while IFS= read -r line; do
            if [[ $line =~ .*=.* ]]; then
              par=${line%=*}
              # Replace in new-config
              eval "sed -i '/$par=/c$line' $new_conf" || install_error "COULD NOT REPLACE CONFIG SETTINGS"
            fi
        done < "$old_conf"
        echo -e "\033[1;32m OK \033[0m"
    fi
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
merge_config

sleep 1s

# RUN SETUP
exec sudo bash /tmp/weneco/etc/install/setup.sh || install_error "Unable to execute installer" 
exit 0

