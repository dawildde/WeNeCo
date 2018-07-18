
# SHOW WELCOME MESSAGE
function display_logo(){
    col1="\033[1;32m"
    col2="\033[1;35m"
    col3="\033[1;34m"
    nc="\033[0m" # No Color
    #clear
    echo -e  "${col1}                          ${col2}         ,--.          ${col3}                     "
    echo -e  "${col1}           .---.          ${col2}       ,--.'|          ${col3}  ,----..            "
    echo -e  "${col1}         /. ./ |          ${col2}  ,--, :  : |          ${col3}/   /   \            "
    echo -e  "${col1}      .--'.  ' ;          ${col2},\`--.'\`|  ' :        ${col3}  |   :     :  ,---.   ";
    echo -e  "${col1}     /__./ \ : |          ${col2}|   :  :  | |          ${col3}.   |  ;. / '   ,'\  "
    echo -e  "${col1} .--'.  '   \' .   ,---.  ${col2}:   |   \ | :   ,---.  ${col3}.   ; /--\` /   /   |"
    echo -e  "${col1}/___/ \ |    ' '  /     \ ${col2}|   : '  '; |  /     \ ${col3};   | ;   .   ; ,. : "
    echo -e  "${col1};   \  \;      : /    /  |${col2}'   ' ;.    ; /    /  |${col3}|   : |   '   | |: : "
    echo -e  "${col1} \   ;  \`      |.    ' / |${col2}|   | | \   |.    ' / |${col3}.   | '___'   | .; :"
    echo -e  "${col1}  .   \    .\  ;'   ;   /|${col2}'   : |  ; .''   ;   /|${col3}'   ; : .'|   :    | "
    echo -e  "${col1}   \   \   ' \ |'   |  / |${col2}|   | '\`--'  '   |  / |${col3}'   | '/  :\   \  / "
    echo -e  "${col1}    :   '  |--\' |   :   |${col2}'   : |       |   :   |${col3}|   :    /  \`----' "
    echo -e  "${col1}     \   \ ;     \   \  / ${col2};   |.'       \   \  / ${col3} \   \ .'            "
    echo -e  "${col1}      '---\'       \`----'${col2} '---'          \`----' ${col3}   \`----'              "
    echo -e  "\033[1;32m                         Web Network Configuration                                \033[m"
    echo -e  "${col1}                          ${col2}                       ${col3}                      ${nc}"
} 

