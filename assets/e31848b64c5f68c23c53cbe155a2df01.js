function clamp(t,r,e){return void 0!==r&&t<r?r:void 0!==e&&e<t?e:t}function urlParam(t,r){r=r||window.location.href,t=t.replace(/[\[\]]/g,"\\$&");r=new RegExp("[?&]"+t+"(=([^&#]*)|&|#|$)").exec(r);return r?r[2]?decodeURIComponent(r[2].replace(/\+/g," ")):"":null}function sprintf(){function y(t,r,e,n){return e=e||" ",e=t.length>=r?"":Array(1+r-t.length>>>0).join(e),n?t+e:e+t}function m(t,r,e,n,i,o){var s=n-t.length;return t=0<s?e||!i?y(t,n,o,e):t.slice(0,r.length)+y("",s,"0",!0)+t.slice(r.length):t}function S(t,r,e,n,i,o,s){var c=t>>>0;return t=(e=e&&c&&{2:"0b",8:"0",16:"0x"}[r]||"")+y(c.toString(r),o||0,"0",!1),m(t,e,n,i,s)}function w(t,r,e,n,i,o){return null!=n&&(t=t.slice(0,n)),m(t,"",r,e,i,o)}var E=arguments,F=0,t=E[F++];return t.replace(/%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g,function(t,r,e,n,i,o,s){var c,u,a,f,l;if("%%"==t)return"%";for(var p=!1,g="",h=!1,d=!1,b=" ",v=e.length,x=0;e&&x<v;x++)switch(e.charAt(x)){case" ":g=" ";break;case"+":g="+";break;case"-":p=!0;break;case"'":b=e.charAt(x+1);break;case"0":h=!0;break;case"#":d=!0}if((n=n?"*"==n?+E[F++]:"*"==n.charAt(0)?+E[n.slice(1,-1)]:+n:0)<0&&(n=-n,p=!0),!isFinite(n))throw new Error("sprintf: (minimum-)width must be finite");switch(o=o?"*"==o?+E[F++]:"*"==o.charAt(0)?+E[o.slice(1,-1)]:+o:-1<"fFeE".indexOf(s)?6:"d"==s?0:void 0,l=r?E[r.slice(0,-1)]:E[F++],s){case"s":return w(String(l),p,n,o,h,b);case"c":return w(String.fromCharCode(+l),p,n,o,h);case"b":return S(l,2,d,p,n,o,h);case"o":return S(l,8,d,p,n,o,h);case"x":return S(l,16,d,p,n,o,h);case"X":return S(l,16,d,p,n,o,h).toUpperCase();case"u":return S(l,10,d,p,n,o,h);case"i":case"d":return l=(u=(c=0|+l)<0?"-":g)+y(String(Math.abs(c)),o,"0",!1),m(l,u,p,n,h);case"e":case"E":case"f":case"F":case"g":case"G":return u=(c=+l)<0?"-":g,a=["toExponential","toFixed","toPrecision"]["efg".indexOf(s.toLowerCase())],f=["toString","toUpperCase"]["eEfFgG".indexOf(s)%2],l=u+Math.abs(c)[a](o),m(l,u,p,n,h)[f]();default:return t}})}function vsprintf(t,r){return sprintf.apply(this,[t].concat(r))}function in_array(t,r,e){if(e){for(var n in r)if(r[n]===t)return!0}else for(var n in r)if(r[n]==t)return!0;return!1}function array_values(t){if(t&&"object"==typeof t&&t.change_key_case)return t.values();var r,e=[];for(r in t)e[e.length]=t[r];return e}function explode(t,r,e){if(arguments.length<2||void 0===t||void 0===r)return null;if(""===t||!1===t||null===t)return!1;if("function"==typeof t||"object"==typeof t||"function"==typeof r||"object"==typeof r)return{0:""};!0===t&&(t="1");var n=(r+="").split(t+="");return void 0===e?n:0<(e=0===e?1:e)?e>=n.length?n:n.slice(0,e-1).concat([n.slice(e-1).join(t)]):-e>=n.length?[]:(n.splice(n.length+e),n)}String.prototype.ltrim=function(t){return t=t||"\\s",this.replace(new RegExp("^["+t+"]+","g"),"")},String.prototype.rtrim=function(t){return t=t||"\\s",this.replace(new RegExp("["+t+"]+$","g"),"")},String.prototype.trim=function(t){return this.rtrim(t).ltrim(t)},String.prototype.startsWith=function(t){return null!==this.match(new RegExp("^"+t,"i"))},String.prototype.endsWith=function(t){return null!==this.match(new RegExp(t+"$","i"))},String.prototype.contains=function(t){return null!==this.match(new RegExp(t,"i"))},String.prototype.substrFrom=function(t,r){var e=this.indexOf(t);return-1===e?r:this.substr(e+t.length)},String.prototype.rsubstrFrom=function(t,r){var e=this.lastIndexOf(t);return-1===e?r:this.substr(e+t.length)},String.prototype.substrUntil=function(t,r){t=this.indexOf(t);return-1===t?r:this.substring(0,t)},String.prototype.rsubstrUntil=function(t,r){t=this.lastIndexOf(t);return-1===t?r:this.substring(0,t)};