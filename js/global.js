
/*                                                                       
*                                     ,--.                               
*             .---.                 ,--.'|            ,----..            
*            /. ./|             ,--,:  : |           /   /   \           
*        .--'.  ' ;          ,`--.'`|  ' :          |   :     :  ,---.   
*       /__./ \ : |          |   :  :  | |          .   |  ;. / '   ,'\  
*   .--'.  '   \' .   ,---.  :   |   \ | :   ,---.  .   ; /--` /   /   | 
*  /___/ \ |    ' '  /     \ |   : '  '; |  /     \ ;   | ;   .   ; ,. : 
*  ;   \  \;      : /    /  |'   ' ;.    ; /    /  ||   : |   '   | |: : 
*   \   ;  `      |.    ' / ||   | | \   |.    ' / |.   | '___'   | .; : 
*    .   \    .\  ;'   ;   /|'   : |  ; .''   ;   /|'   ; : .'|   :    | 
*     \   \   ' \ |'   |  / ||   | '`--'  '   |  / |'   | '/  :\   \  /  
*      :   '  |--" |   :    |'   : |      |   :    ||   :    /  `----'   
*       \   \ ;     \   \  / ;   |.'       \   \  /  \   \ .'            
*        '---"       `----'  '---'          `----'    `---`              
* 
*                          Web Network Configuration 
*     
*                               GLOBAL JS FUNCTIONS
*/



// RETURN LANGUAGE TEXT
function lang( section, type, str ){
	if ( LANG.hasOwnProperty( section ) ) {
        if ( LANG[section].hasOwnProperty( type ) ) {
            if ( LANG[section][type].hasOwnProperty( str ) ) {
                return LANG[section][type][str];
            }
        }
    }
	return section + "_" + type + "_" + str; 
}

// RETURN LANGUAGE TEXT
function langOLD( section, type, str ){
	if ( g_Lang.hasOwnProperty( section ) ) {
        if ( g_Lang[section].hasOwnProperty( type ) ) {
            if ( g_Lang[section][type].hasOwnProperty( str ) ) {
                return g_Lang[section][type][str];
            }
        }
    }
	return section + "_" + type + "_" + str; 
}


// RETURN DEFAULT IF ELEMENT NOT EXISTs
function getVal ( arrayObj, section, defVal = "" ){
	if ( (Array.isArray( arrayObj )) && (arrayObj.includes( section )) ){
		return arrayObj[section];
	} else if ( (typeof arrayObj === "object") && (arrayObj !== null) && (arrayObj.hasOwnProperty(section)) ) {
		return arrayObj[section];
	} else {
		return defVal;
	}
}

// SHOW TAB
/*  show s/ hides a tab
*		parameter: 
*           Tab Object e.g.: $(#tabid)
*           visibility - true/false
*/
function showTab( oTab, visibility ){
    if ( visibility == true ){ 
        oTab.prop('disabled', false).removeClass('ui-disabled');
    } else {
        oTab.prop('disabled', true).addClass('ui-disabled');
    }
}


// SERIALIZE FORM DATA
(function($){
    $.fn.serializeObject = function(){

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
})(jQuery);
