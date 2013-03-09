$(function(){
    
    $('.converteLetraMaiscula').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });
    
    

    
});


jQuery.validator.addMethod("soLetras", function(value, element) {
    return this.optional(element) || soLetras(value);
}, "Campo Sigla deve conter apenas letras");


jQuery.validator.addMethod("soNum", function(value, element) {
    return this.optional(element) || soNum(value);
}, "Campo deve conter apenas numeros");

function iniciarMascaras() {
    $('.money').unmaskMoney();            
    $('.money').maskMoney({
        allowZero:true,
        decimal:",", 
        thousands:".", 
        defaultZero:false
    });
    
    $('.numeric').numeric();        
}

function str_replace (search, replace, subject, count) {
    var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }
 
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}

function soLetras(valor){
    if (/[^a-z]/gi.test(valor)) {
        return false;
    }
    return true;            
}

function moneyToFloat($vl){
	
    if($.trim($vl) == ''){
        return 0;
    }else{	
        $vl = str_replace('.','',$vl);
        $vl = str_replace(',','.',$vl);
        return parseFloat($vl);
    }
}

function floatToMoney($vl){
    return number_format($vl,2, ',', '.');
}

function reverseDate(data){
    data = $.trim(data);
    data = data.replace('-','/');
    data = data.replace('-','/');
    data = data.split('/');
    data = data.reverse();
    data = $.trim(data.join('/'));
    return data;
}

function urlencode (str) {
    str = (str + '').toString();
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
    replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}

function urlencodeGet(str){
    str = (str + '').toString();
    str = str.replace(/!/g, '{1}');
    str = str_replace('/', '{2}', str);
    str = str_replace('(', '{3}', str);
    str = str_replace(')', '{4}', str);
    str = str_replace('*', '{5}', str);
    str = str_replace('%', '{6}', str);
    str = str_replace('#', '{7}', str);
    str = str_replace(' ','{}', str);
    
    str = str_replace('"', '{8}', str);
    str = str_replace("'", '{9}', str);
    str = str_replace('=', '{10}', str);        
     
    return str;
}

function soNum(valor){
    if (/[^a-z]/gi.test(valor)) {
        return true;
    }
    return false;
            
}