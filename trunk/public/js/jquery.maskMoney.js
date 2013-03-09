(function(a){a.fn.maskMoney=function(b){b=a.extend({symbol:"US$",decimal:".",precision:2,thousands:",",allowZero:false,allowNegative:false,showSymbol:false,symbolStay:false,defaultZero:true},b);return this.each(function(){function m(a){if(b.allowNegative){var c=a.val();if(a.val()!=""&&a.val().charAt(0)=="-"){return a.val().replace("-","")}else{return"-"+a.val()}}else{return a.val()}}function l(a){if(b.showSymbol){if(a.substr(0,b.symbol.length)!=b.symbol)return b.symbol+a}return a}function k(){var a=parseFloat("0")/Math.pow(10,b.precision);return a.toFixed(b.precision).replace(new RegExp("\\.","g"),b.decimal)}function j(a){a=a.replace(b.symbol,"");var c="0123456789";var d=a.length;var e="",f="",g="";if(d!=0&&a.charAt(0)=="-"){a=a.replace("-","");if(b.allowNegative){g="-"}}if(d==0){if(!b.defaultZero)return f;f="0.00"}for(var h=0;h<d;h++){if(a.charAt(h)!="0"&&a.charAt(h)!=b.decimal)break}for(;h<d;h++){if(c.indexOf(a.charAt(h))!=-1)e+=a.charAt(h)}var i=parseFloat(e);i=isNaN(i)?0:i/Math.pow(10,b.precision);f=i.toFixed(b.precision);h=b.precision==0?0:1;var j,k=(f=f.split("."))[h].substr(0,b.precision);for(j=(f=f[0]).length;(j-=3)>=1;){f=f.substr(0,j)+b.thousands+f.substr(j)}return b.precision>0?l(g+f+b.decimal+k+Array(b.precision+1-k.length).join(0)):l(g+f)}function i(a,b){var d=c.val().length;c.val(j(a.value));var e=c.val().length;b=b-(d-e);c.setCursorPosition(b)}function h(a){if(a.preventDefault){a.preventDefault()}else{a.returnValue=false}}function g(e){if(a.browser.msie){d(e)}if(c.val()==l(k())){if(!b.allowZero)c.val("")}else{if(!b.symbolStay)c.val(c.val().replace(b.symbol,""));else if(b.symbolStay&&c.val()==b.symbol)c.val("")}}function f(a){if(c.val()==""&&b.defaultZero){c.val(l(k()))}else{c.val(l(c.val()))}if(this.createTextRange){var d=this.createTextRange();d.collapse(false);d.select()}}function e(a){a=a||window.event;var b=a.charCode||a.keyCode||a.which; if(b==undefined)return false;var d=c.get(0);var e=c.getInputSelection(d);var f=e.start;var g=e.end;if(b==8){h(a);if(f==g){d.value=d.value.substring(0,f-1)+d.value.substring(g,d.value.length);f=f-1}else{d.value=d.value.substring(0,f)+d.value.substring(g,d.value.length)}i(d,f);return false}else if(b==9){return true}else if(b==46||b==63272){h(a);if(d.selectionStart==d.selectionEnd){d.value=d.value.substring(0,f)+d.value.substring(g+1,d.value.length)}else{d.value=d.value.substring(0,f)+d.value.substring(g,d.value.length)}i(d,f);return false}else{return true}}function d(a){a=a||window.event;var b=a.charCode||a.keyCode||a.which;if(b==undefined)return false;if(b<48||b>57){if(b==45){c.val(m(c));return false}if(b==43){c.val(c.val().replace("-",""));return false}else if(b==13){return true}else{h(a);return true}}else if(c.val().length==c.attr("maxlength")){return false}else{h(a);var d=String.fromCharCode(b);var e=c.get(0);var f=c.getInputSelection(e);var g=f.start;var j=f.end;e.value=e.value.substring(0,g)+d+e.value.substring(j,e.value.length);i(e,g+1);return false}}var c=a(this);c.bind("keypress",d);c.bind("keydown",e);c.bind("blur",g);c.bind("focus",f);c.one("unmaskMoney",function(){c.unbind("focus",f);c.unbind("blur",g);c.unbind("keydown",e);c.unbind("keypress",d);if(a.browser.msie){this.onpaste=null}else if(a.browser.mozilla){this.removeEventListener("input",g,false)}})})};a.fn.unmaskMoney=function(){return this.trigger("unmaskMoney")};a.fn.setCursorPosition=function(a){this.each(function(b,c){if(c.setSelectionRange){c.focus();c.setSelectionRange(a,a)}else if(c.createTextRange){var d=c.createTextRange();d.collapse(true);d.moveEnd("character",a);d.moveStart("character",a);d.select()}});return this};a.fn.getInputSelection=function(a){var b=0,c=0,d,e,f,g,h;if(typeof a.selectionStart=="number"&&typeof a.selectionEnd=="number"){b=a.selectionStart;c=a.selectionEnd}else{e=document.selection.createRange();if(e&&e.parentElement()==a){g=a.value.length;d=a.value.replace(/\r\n/g,"\n");f=a.createTextRange();f.moveToBookmark(e.getBookmark());h=a.createTextRange();h.collapse(false);if(f.compareEndPoints("StartToEnd",h)>-1){b=c=g}else{b=-f.moveStart("character",-g);b+=d.slice(0,b).split("\n").length-1;if(f.compareEndPoints("EndToEnd",h)>-1){c=g}else{c=-f.moveEnd("character",-g);c+=d.slice(0,c).split("\n").length-1}}}}return{start:b,end:c}}})(jQuery)