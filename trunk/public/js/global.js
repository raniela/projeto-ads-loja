$(function(){
    
    $('.converteLetraMaiscula').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });
    
    

    
});

function CapitalizeAll(elemId){
    var txt = $('#' + elemId).val();
    $('#' + elemId).val(txt.toUpperCase());
};


/** soma um mês na data. Data no formato Brasileiro */
function somaMesData(data, qtdMes){
    data = str_replace('-','/', data);
    data = data.split('/');
    dia = parseInt(data[0], 10);
    mes = parseInt(data[1], 10);
    ano = parseInt(data[2], 10);
	
    for(iM = 1; iM <= qtdMes; iM++){
        mes++;
        if(mes > 12){
            ano = ano + 1;
            mes = 1;
        }
    }
	
    nova_data = lpad(1, dia, '0') + '/' + lpad(1, mes, '0') + '/' + ano;
    dia_novo = dia;
    while(!isDate(reverseDate(nova_data)) && dia_novo > 28){
        dia_novo = dia_novo -1;
        nova_data = lpad(1, dia_novo, '0') + '/' + lpad(1, mes, '0') + '/' + ano;
    }
    return nova_data;
}

/** data no formato americano */
function isDate(value){
    /*var dateRegEx = new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/);
    if (dateRegEx.test(value)) {
    	if(value.substring(0,4) == '0000'){
    		return false;
    	}
        return true;
    }
    return false;*/
	
	
    var date=reverseDate(value);
    var ardt=new Array;
    var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
    ardt=date.split("/");
    erro=false;
    if ( date.search(ExpReg)==-1){
        erro = true;
    }
    else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30))
        erro = true;
    else if ( ardt[1]==2) {
        if ((ardt[0]>28)&&((ardt[2]%4)!=0))
            erro = true;
        if ((ardt[0]>29)&&((ardt[2]%4)==0))
            erro = true;
    }
    if (erro) {		
        return false;
    }
    return true;
}

/** formata com caracter v informado a esquerda ate que a string atinja o tamanho tam*/
function lpad(tam, n, v){
    while((n + '').length <= tam){
        n = v + n;
    }
    return n;
}

function validarCPF(cpf) {
 
    cpf = cpf.replace(/[^\d]+/g,'');
 
    if(cpf == '') return false;
 
    // Elimina CPFs invalidos conhecidos
    if (cpf.length != 11 || 
        cpf == "00000000000" || 
        cpf == "11111111111" || 
        cpf == "22222222222" || 
        cpf == "33333333333" || 
        cpf == "44444444444" || 
        cpf == "55555555555" || 
        cpf == "66666666666" || 
        cpf == "77777777777" || 
        cpf == "88888888888" || 
        cpf == "99999999999")
        return false;
        
    // Valida 1o digito
    add = 0;
    for (i=0; i < 9; i ++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
        return false;
     
    // Valida 2o digito
    add = 0;
    for (i = 0; i < 10; i ++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
        return false;
         
    return true;
}

jQuery.validator.addMethod("soLetras", function(value, element) {
    return this.optional(element) || soLetras(value);
}, "Campo Sigla deve conter apenas letras");


jQuery.validator.addMethod("soNum", function(value, element) {
    return this.optional(element) || soNum(value);
}, "Campo deve conter apenas numeros");


jQuery.validator.addMethod("validarCPF", function(value, element) {
    return this.optional(element) || validarCPF(value);
}, "Forneça um CPF válido");

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

function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
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