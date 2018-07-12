#!/bin/bash
root_dir=$(dirname $(readlink -f $0))
source "$root_dir/common.sh"


for file in /etc/systemd/network/device*.network
do
    echo $file
done


log_ok