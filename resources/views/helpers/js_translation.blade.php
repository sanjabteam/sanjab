var __sanjab_translations = @json($sanjabTrans);

function sanjabTrans(key, replaces)
{
    if (replaces === undefined) {
        replaces = {};
    }
    var out = __stransHelper('sanjab::sanjab.' + key, replaces, __sanjab_translations, '');
    for(var replace in replaces){
        out = out.replace(new RegExp("\:"+parameter), replaces[replace]);
    }
    return out;
}

function __stransHelper(key, parameters, __data, previous_key)
{
    var key_array = key.split(".");
    var this_group = key_array[0];
    key_array = key_array.splice(1, key_array.length-1);
    var searchingKey = key_array.join('.');
    if( typeof __data[this_group] !== 'undefined' ) {
        if(searchingKey.length>0){
            return __stransHelper(searchingKey, parameters, __data[this_group], previous_key+(previous_key.length>0?'.':'')+this_group);
        } else {
            return __data[this_group];
        }
    }
    return previous_key+'.'+this_group;
}
