$(function(){
    
    $('.converteLetraMaiscula').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });
        
    
})


jQuery.validator.addMethod("soLetras", function(value, element) {
    return this.optional(element) || soLetras(value);
}, "Campo Sigla deve conter apenas letras");


jQuery.validator.addMethod("soNum", function(value, element) {
    return this.optional(element) || soNum(value);
}, "Campo deve conter apenas numeros");



function soLetras(valor){
    if (/[^a-z]/gi.test(valor)) {
        return false;
    }
    return true;
            
}

function soNum(valor){
    if (/[^a-z]/gi.test(valor)) {
        return true;
    }
    return false;
            
}