#!/bin/bash                                                                        
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
#                           COMMON SCRIPT FUNCTIONS

script_dir=$(dirname $(readlink -f $0))
#weneco_dir=$(dirname $script_dir)       # WENECO ROOT DIRECTORY (.../weneco)
weneco_dir="/etc/weneco"
temp_dir="/tmp/weneco"
retCode=0   # RETURN CODE

##########################################
#             GLOBAL
##########################################

# <!-----   LOGGING   ------>

# LOG WAITING FOR RESULT
function log(){
    printf "$1: "
}

# PRINT OK
function pOK(){
    printf "OK\n"
}

# PRINT NOK
function pNOK(){
    printf "FAILED\n"
    retCode=1
}

# PRINT RETURN-CODE
function pRET(){
    if [ $retCode -gt 0 ]; then
        echo -ne "FAILED"
    else
        echo -ne "OK"
    fi
}

# RETURN OK AND EXIT
function xOK(){
    echo -ne "OK"
    exit 0
}

# RETURN NOT OK AND EXIT
function xNOK(){
    echo -ne "FAILED"
    exit 1
}

# RETURN RETURN-CODE AND EXIT
function xRET(){
    pRET
    exit $retCode
}

# <!-----   ./logging    ------>

# <!-----   READ CONFIG FILES   ------>
# GET INTERFACE FROM NETWORK-CONFIG-FILE
function getIfName(){
    file="$weneco_dir/network/$1.conf"

    if [ -f $file ]; then
        ifname=$(php -r "\$jsn = json_decode( file_get_contents( '$file' ) ); print \$jsn -> general -> name;")  
    else
        echo "NOFILE: $file"
        ifname=$1
    fi
}

# GET MODE FROM NETWOK-CONFIG-FILE
function getMode(){
    file="$weneco_dir/network/$1.conf"
    if [ -f $file ]; then
        ifmode=$(php -r "\$jsn = json_decode( file_get_contents( '$file' ) ); print \$jsn -> general -> mode;")  
    fi
}
# <!-----   ./read config files   ------>
