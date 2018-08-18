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
#                           IP TABLE FIREWALL SCRIPT

IPT="/sbin/iptables"

# WAN Interface Name
WAN="wlan1"
LAN="wlan0"

# Server IP
SERVER_IP="$(ip addr show $WAN | grep 'inet ' | cut -f2 | awk '{ print $2}')"

# Your DNS servers you use: cat /etc/resolv.conf
DNS_SERVER="192.168.61.10 8.8.8.8"

# Allow connections to this package servers
PACKAGE_SERVER="ftp.us.debian.org security.debian.org"

echo "flush iptable rules"
$IPT -F
$IPT -X
$IPT -t nat -F
$IPT -t nat -X
$IPT -t mangle -F
$IPT -t mangle -X

echo "WARNING: Set default policy to ACCEPT ALL !!!!"
# Set default chain policies
$IPT -P INPUT ACCEPT
$IPT -P FORWARD ACCEPT
$IPT -P OUTPUT ACCEPT

# ALLOW UNLIMITED TRAFFIC ON LOOPBACK
$IPT -A INPUT -i lo -j ACCEPT
$IPT -A OUTPUT -o lo -j ACCEPT

# ALLOW LAN COMMUNICATION
echo "Allow LAN communication"
$IPT -A INPUT -i $LAN -j ACCEPT
$IPT -A OUTPUT -o $LAN -j ACCEPT
$IPT -A FORWARD -i $LAN -j ACCEPT
$IPT -A FORWARD -o $LAN -j ACCEPT

# MASQUERADE WAN (NAT)
echo "Set WAN rules"
$IPT -t nat -A POSTROUTING -o $WAN -j MASQUERADE
$IPT -A FORWARD -i $WAN -o $LAN -m state --state RELATED,ESTABLISHED -j ACCEPT
$IPT -A FORWARD -i $LAN -o $WAN -j ACCEPT