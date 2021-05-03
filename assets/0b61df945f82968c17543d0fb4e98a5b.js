"use strict";var _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},windowIsDefined="object"===("undefined"==typeof window?"undefined":_typeof(window));!function(t){if("function"==typeof define&&define.amd)define(["jquery"],t);else if("object"===("undefined"==typeof module?"undefined":_typeof(module))&&module.exports){var e;try{e=require("jquery")}catch(t){e=null}module.exports=t(e)}else window&&(window.Slider=t(window.jQuery))}(function(t){var e,p,n="slider",a="bootstrapSlider";function i(){}return windowIsDefined&&!window.console&&(window.console={}),windowIsDefined&&!window.console.log&&(window.console.log=function(){}),windowIsDefined&&!window.console.warn&&(window.console.warn=function(){}),p=Array.prototype.slice,function(r){if(r){var d="undefined"==typeof console?i:function(t){console.error(t)};r.bridget=function(t,e){var i,h,l;(i=e).prototype.option||(i.prototype.option=function(t){r.isPlainObject(t)&&(this.options=r.extend(!0,this.options,t))}),h=t,l=e,r.fn[h]=function(e){if("string"==typeof e){for(var t=p.call(arguments,1),i=0,s=this.length;i<s;i++){var o=this[i],n=r.data(o,h);if(n)if(r.isFunction(n[e])&&"_"!==e.charAt(0)){o=n[e].apply(n,t);if(void 0!==o&&o!==n)return o}else d("no such method '"+e+"' for "+h+" instance");else d("cannot call methods on "+h+" prior to initialization; attempted to call '"+e+"'")}return this}var a=this.map(function(){var t=r.data(this,h);return t?(t.option(e),t._init()):(t=new l(this,e),r.data(this,h,t)),r(this)});return 1===a.length?a[0]:a}},r.bridget}}(t),function(P){var i=void 0,s=function(t){return"Invalid input value '"+t+"' passed in"},A={linear:{getValue:function(t,e){return t<e.min?e.min:t>e.max?e.max:t},toValue:function(t){var e=t/100*(this.options.max-this.options.min),i=!0;if(0<this.options.ticks_positions.length){for(var s,o,n,a=0,h=1;h<this.options.ticks_positions.length;h++)if(t<=this.options.ticks_positions[h]){s=this.options.ticks[h-1],n=this.options.ticks_positions[h-1],o=this.options.ticks[h],a=this.options.ticks_positions[h];break}e=s+(t-n)/(a-n)*(o-s),i=!1}e=(i?this.options.min:0)+Math.round(e/this.options.step)*this.options.step;return A.linear.getValue(e,this.options)},toPercentage:function(t){if(this.options.max===this.options.min)return 0;if(0<this.options.ticks_positions.length){for(var e,i,s,o=0,n=0;n<this.options.ticks.length;n++)if(t<=this.options.ticks[n]){e=0<n?this.options.ticks[n-1]:0,s=0<n?this.options.ticks_positions[n-1]:0,i=this.options.ticks[n],o=this.options.ticks_positions[n];break}if(0<n)return s+(t-e)/(i-e)*(o-s)}return 100*(t-this.options.min)/(this.options.max-this.options.min)}},logarithmic:{toValue:function(t){var e=1-this.options.min,i=Math.log(this.options.min+e),s=Math.log(this.options.max+e),e=Math.exp(i+(s-i)*t/100)-e;return Math.round(e)===s?s:(e=this.options.min+Math.round((e-this.options.min)/this.options.step)*this.options.step,A.linear.getValue(e,this.options))},toPercentage:function(t){if(this.options.max===this.options.min)return 0;var e=1-this.options.min,i=Math.log(this.options.max+e),s=Math.log(this.options.min+e);return 100*(Math.log(t+e)-s)/(i-s)}}};function o(t,e){this._state={value:null,enabled:null,offset:null,size:null,percentage:null,inDrag:!1,over:!1,tickIndex:null},this.ticksCallbackMap={},this.handleCallbackMap={},"string"==typeof t?this.element=document.querySelector(t):t instanceof HTMLElement&&(this.element=t),e=e||{};for(var i=Object.keys(this.defaultOptions),s=e.hasOwnProperty("min"),o=e.hasOwnProperty("max"),n=0;n<i.length;n++){var a=i[n],h=e[a];h=null!==(h=void 0!==h?h:function(t,e){e="data-slider-"+e.replace(/_/g,"-"),e=t.getAttribute(e);try{return JSON.parse(e)}catch(t){return e}}(this.element,a))?h:this.defaultOptions[a],this.options||(this.options={}),this.options[a]=h}this.ticksAreValid=Array.isArray(this.options.ticks)&&0<this.options.ticks.length,this.ticksAreValid||(this.options.lock_to_ticks=!1),"auto"===this.options.rtl&&(L=window.getComputedStyle(this.element),this.options.rtl=null!=L?"rtl"===L.direction:"rtl"===this.element.style.direction),"vertical"!==this.options.orientation||"top"!==this.options.tooltip_position&&"bottom"!==this.options.tooltip_position?"horizontal"!==this.options.orientation||"left"!==this.options.tooltip_position&&"right"!==this.options.tooltip_position||(this.options.tooltip_position="top"):this.options.rtl?this.options.tooltip_position="left":this.options.tooltip_position="right";var l,r,d,p=this.element.style.width,c=!1,u=this.element.parentNode;if(this.sliderElem)c=!0;else{this.sliderElem=document.createElement("div"),this.sliderElem.className="slider";var m=document.createElement("div");m.className="slider-track",(r=document.createElement("div")).className="slider-track-low",(l=document.createElement("div")).className="slider-selection",(d=document.createElement("div")).className="slider-track-high",(T=document.createElement("div")).className="slider-handle min-slider-handle",T.setAttribute("role","slider"),T.setAttribute("aria-valuemin",this.options.min),T.setAttribute("aria-valuemax",this.options.max),(M=document.createElement("div")).className="slider-handle max-slider-handle",M.setAttribute("role","slider"),M.setAttribute("aria-valuemin",this.options.min),M.setAttribute("aria-valuemax",this.options.max),m.appendChild(r),m.appendChild(l),m.appendChild(d),this.rangeHighlightElements=[];var _=this.options.rangeHighlights;if(Array.isArray(_)&&0<_.length)for(var v=0;v<_.length;v++){var g=document.createElement("div"),f=_[v].class||"";g.className="slider-rangeHighlight slider-selection "+f,this.rangeHighlightElements.push(g),m.appendChild(g)}var y=Array.isArray(this.options.labelledby);if(y&&this.options.labelledby[0]&&T.setAttribute("aria-labelledby",this.options.labelledby[0]),y&&this.options.labelledby[1]&&M.setAttribute("aria-labelledby",this.options.labelledby[1]),!y&&this.options.labelledby&&(T.setAttribute("aria-labelledby",this.options.labelledby),M.setAttribute("aria-labelledby",this.options.labelledby)),this.ticks=[],Array.isArray(this.options.ticks)&&0<this.options.ticks.length){for(this.ticksContainer=document.createElement("div"),this.ticksContainer.className="slider-tick-container",n=0;n<this.options.ticks.length;n++){var b,k,E=document.createElement("div");E.className="slider-tick",this.options.ticks_tooltip&&(b=(k=this._addTickListener()).addMouseEnter(this,E,n),k=k.addMouseLeave(this,E),this.ticksCallbackMap[n]={mouseEnter:b,mouseLeave:k}),this.ticks.push(E),this.ticksContainer.appendChild(E)}l.className+=" tick-slider-selection"}if(this.tickLabels=[],Array.isArray(this.options.ticks_labels)&&0<this.options.ticks_labels.length)for(this.tickLabelContainer=document.createElement("div"),this.tickLabelContainer.className="slider-tick-label-container",n=0;n<this.options.ticks_labels.length;n++){var C=document.createElement("div"),w=0===this.options.ticks_positions.length,w=this.options.reversed&&w?this.options.ticks_labels.length-(n+1):n;C.className="slider-tick-label",C.innerHTML=this.options.ticks_labels[w],this.tickLabels.push(C),this.tickLabelContainer.appendChild(C)}var x=function(t){var e=document.createElement("div");e.className="tooltip-arrow";var i=document.createElement("div");i.className="tooltip-inner",t.appendChild(e),t.appendChild(i)},t=document.createElement("div");t.className="tooltip tooltip-main",t.setAttribute("role","presentation"),x(t);var L=document.createElement("div");L.className="tooltip tooltip-min",L.setAttribute("role","presentation"),x(L);y=document.createElement("div");y.className="tooltip tooltip-max",y.setAttribute("role","presentation"),x(y),this.sliderElem.appendChild(m),this.sliderElem.appendChild(t),this.sliderElem.appendChild(L),this.sliderElem.appendChild(y),this.tickLabelContainer&&this.sliderElem.appendChild(this.tickLabelContainer),this.ticksContainer&&this.sliderElem.appendChild(this.ticksContainer),this.sliderElem.appendChild(T),this.sliderElem.appendChild(M),u.insertBefore(this.sliderElem,this.element),this.element.style.display="none"}if(P&&(this.$element=P(this.element),this.$sliderElem=P(this.sliderElem)),this.eventToCallbackMap={},this.sliderElem.id=this.options.id,this.touchCapable="ontouchstart"in window||window.DocumentTouch&&document instanceof window.DocumentTouch,this.touchX=0,this.touchY=0,this.tooltip=this.sliderElem.querySelector(".tooltip-main"),this.tooltipInner=this.tooltip.querySelector(".tooltip-inner"),this.tooltip_min=this.sliderElem.querySelector(".tooltip-min"),this.tooltipInner_min=this.tooltip_min.querySelector(".tooltip-inner"),this.tooltip_max=this.sliderElem.querySelector(".tooltip-max"),this.tooltipInner_max=this.tooltip_max.querySelector(".tooltip-inner"),A[this.options.scale]&&(this.options.scale=A[this.options.scale]),!0===c&&(this._removeClass(this.sliderElem,"slider-horizontal"),this._removeClass(this.sliderElem,"slider-vertical"),this._removeClass(this.sliderElem,"slider-rtl"),this._removeClass(this.tooltip,"hide"),this._removeClass(this.tooltip_min,"hide"),this._removeClass(this.tooltip_max,"hide"),["left","right","top","width","height"].forEach(function(t){this._removeProperty(this.trackLow,t),this._removeProperty(this.trackSelection,t),this._removeProperty(this.trackHigh,t)},this),[this.handle1,this.handle2].forEach(function(t){this._removeProperty(t,"left"),this._removeProperty(t,"right"),this._removeProperty(t,"top")},this),[this.tooltip,this.tooltip_min,this.tooltip_max].forEach(function(t){this._removeProperty(t,"left"),this._removeProperty(t,"right"),this._removeProperty(t,"top"),this._removeClass(t,"right"),this._removeClass(t,"left"),this._removeClass(t,"top")},this)),"vertical"===this.options.orientation?(this._addClass(this.sliderElem,"slider-vertical"),this.stylePos="top",this.mousePos="pageY",this.sizePos="offsetHeight"):(this._addClass(this.sliderElem,"slider-horizontal"),this.sliderElem.style.width=p,this.options.orientation="horizontal",this.options.rtl?this.stylePos="right":this.stylePos="left",this.mousePos="clientX",this.sizePos="offsetWidth"),this.options.rtl&&this._addClass(this.sliderElem,"slider-rtl"),this._setTooltipPosition(),Array.isArray(this.options.ticks)&&0<this.options.ticks.length&&(o||(this.options.max=Math.max.apply(Math,this.options.ticks)),s||(this.options.min=Math.min.apply(Math,this.options.ticks))),Array.isArray(this.options.value)?(this.options.range=!0,this._state.value=this.options.value):this.options.range?this._state.value=[this.options.value,this.options.max]:this._state.value=this.options.value,this.trackLow=r||this.trackLow,this.trackSelection=l||this.trackSelection,this.trackHigh=d||this.trackHigh,"none"===this.options.selection?(this._addClass(this.trackLow,"hide"),this._addClass(this.trackSelection,"hide"),this._addClass(this.trackHigh,"hide")):"after"!==this.options.selection&&"before"!==this.options.selection||(this._removeClass(this.trackLow,"hide"),this._removeClass(this.trackSelection,"hide"),this._removeClass(this.trackHigh,"hide")),this.handle1=T||this.handle1,this.handle2=M||this.handle2,!0===c)for(this._removeClass(this.handle1,"round triangle"),this._removeClass(this.handle2,"round triangle hide"),n=0;n<this.ticks.length;n++)this._removeClass(this.ticks[n],"round triangle hide");var T,M;if(-1!==["round","triangle","custom"].indexOf(this.options.handle))for(this._addClass(this.handle1,this.options.handle),this._addClass(this.handle2,this.options.handle),n=0;n<this.ticks.length;n++)this._addClass(this.ticks[n],this.options.handle);this._state.offset=this._offset(this.sliderElem),this._state.size=this.sliderElem[this.sizePos],this.setValue(this._state.value),this.handle1Keydown=this._keydown.bind(this,0),this.handle1.addEventListener("keydown",this.handle1Keydown,!1),this.handle2Keydown=this._keydown.bind(this,1),this.handle2.addEventListener("keydown",this.handle2Keydown,!1),this.mousedown=this._mousedown.bind(this),this.touchstart=this._touchstart.bind(this),this.touchmove=this._touchmove.bind(this),this.touchCapable&&(this.sliderElem.addEventListener("touchstart",this.touchstart,!1),this.sliderElem.addEventListener("touchmove",this.touchmove,!1)),this.sliderElem.addEventListener("mousedown",this.mousedown,!1),this.resize=this._resize.bind(this),window.addEventListener("resize",this.resize,!1),"hide"===this.options.tooltip?(this._addClass(this.tooltip,"hide"),this._addClass(this.tooltip_min,"hide"),this._addClass(this.tooltip_max,"hide")):"always"===this.options.tooltip?(this._showTooltip(),this._alwaysShowTooltip=!0):(this.showTooltip=this._showTooltip.bind(this),this.hideTooltip=this._hideTooltip.bind(this),this.options.ticks_tooltip?(M=(T=this._addTickListener()).addMouseEnter(this,this.handle1),c=T.addMouseLeave(this,this.handle1),this.handleCallbackMap.handle1={mouseEnter:M,mouseLeave:c},M=T.addMouseEnter(this,this.handle2),c=T.addMouseLeave(this,this.handle2),this.handleCallbackMap.handle2={mouseEnter:M,mouseLeave:c}):(this.sliderElem.addEventListener("mouseenter",this.showTooltip,!1),this.sliderElem.addEventListener("mouseleave",this.hideTooltip,!1),this.touchCapable&&(this.sliderElem.addEventListener("touchstart",this.showTooltip,!1),this.sliderElem.addEventListener("touchmove",this.showTooltip,!1),this.sliderElem.addEventListener("touchend",this.hideTooltip,!1))),this.handle1.addEventListener("focus",this.showTooltip,!1),this.handle1.addEventListener("blur",this.hideTooltip,!1),this.handle2.addEventListener("focus",this.showTooltip,!1),this.handle2.addEventListener("blur",this.hideTooltip,!1),this.touchCapable&&(this.handle1.addEventListener("touchstart",this.showTooltip,!1),this.handle1.addEventListener("touchmove",this.showTooltip,!1),this.handle1.addEventListener("touchend",this.hideTooltip,!1),this.handle2.addEventListener("touchstart",this.showTooltip,!1),this.handle2.addEventListener("touchmove",this.showTooltip,!1),this.handle2.addEventListener("touchend",this.hideTooltip,!1))),this.options.enabled?this.enable():this.disable()}(e=function(t,e){return o.call(this,t,e),this}).prototype={_init:function(){},constructor:e,defaultOptions:{id:"",min:0,max:10,step:1,precision:0,orientation:"horizontal",value:5,range:!1,selection:"before",tooltip:"show",tooltip_split:!1,lock_to_ticks:!1,handle:"round",reversed:!1,rtl:"auto",enabled:!0,formatter:function(t){return Array.isArray(t)?t[0]+" : "+t[1]:t},natural_arrow_keys:!1,ticks:[],ticks_positions:[],ticks_labels:[],ticks_snap_bounds:0,ticks_tooltip:!1,scale:"linear",focus:!1,tooltip_position:null,labelledby:null,rangeHighlights:[]},getElement:function(){return this.sliderElem},getValue:function(){return this.options.range?this._state.value:this._state.value[0]},setValue:function(t,e,i){t=t||0;var s=this.getValue();this._state.value=this._validateInputValue(t);t=this._applyPrecision.bind(this);this.options.range?(this._state.value[0]=t(this._state.value[0]),this._state.value[1]=t(this._state.value[1]),this.ticksAreValid&&this.options.lock_to_ticks&&(this._state.value[0]=this.options.ticks[this._getClosestTickIndex(this._state.value[0])],this._state.value[1]=this.options.ticks[this._getClosestTickIndex(this._state.value[1])]),this._state.value[0]=Math.max(this.options.min,Math.min(this.options.max,this._state.value[0])),this._state.value[1]=Math.max(this.options.min,Math.min(this.options.max,this._state.value[1]))):(this._state.value=t(this._state.value),this.ticksAreValid&&this.options.lock_to_ticks&&(this._state.value=this.options.ticks[this._getClosestTickIndex(this._state.value)]),this._state.value=[Math.max(this.options.min,Math.min(this.options.max,this._state.value))],this._addClass(this.handle2,"hide"),"after"===this.options.selection?this._state.value[1]=this.options.max:this._state.value[1]=this.options.min),this._setTickIndex(),this.options.max>this.options.min?this._state.percentage=[this._toPercentage(this._state.value[0]),this._toPercentage(this._state.value[1]),100*this.options.step/(this.options.max-this.options.min)]:this._state.percentage=[0,0,100],this._layout();t=this.options.range?this._state.value:this._state.value[0];this._setDataVal(t),!0===e&&this._trigger("slide",t);return(Array.isArray(t)?s[0]!==t[0]||s[1]!==t[1]:s!==t)&&!0===i&&this._trigger("change",{oldValue:s,newValue:t}),this},destroy:function(){this._removeSliderEventHandlers(),this.sliderElem.parentNode.removeChild(this.sliderElem),this.element.style.display="",this._cleanUpEventCallbacksMap(),this.element.removeAttribute("data"),P&&(this._unbindJQueryEventHandlers(),i===n&&this.$element.removeData(i),this.$element.removeData(a))},disable:function(){return this._state.enabled=!1,this.handle1.removeAttribute("tabindex"),this.handle2.removeAttribute("tabindex"),this._addClass(this.sliderElem,"slider-disabled"),this._trigger("slideDisabled"),this},enable:function(){return this._state.enabled=!0,this.handle1.setAttribute("tabindex",0),this.handle2.setAttribute("tabindex",0),this._removeClass(this.sliderElem,"slider-disabled"),this._trigger("slideEnabled"),this},toggle:function(){return this._state.enabled?this.disable():this.enable(),this},isEnabled:function(){return this._state.enabled},on:function(t,e){return this._bindNonQueryEventHandler(t,e),this},off:function(t,e){P?(this.$element.off(t,e),this.$sliderElem.off(t,e)):this._unbindNonQueryEventHandler(t,e)},getAttribute:function(t){return t?this.options[t]:this.options},setAttribute:function(t,e){return this.options[t]=e,this},refresh:function(t){var e=this.getValue();return this._removeSliderEventHandlers(),o.call(this,this.element,this.options),t&&!0===t.useCurrentValue&&this.setValue(e),P&&(i===n&&P.data(this.element,n,this),P.data(this.element,a,this)),this},relayout:function(){return this._resize(),this},_removeTooltipListener:function(t,e){this.handle1.removeEventListener(t,e,!1),this.handle2.removeEventListener(t,e,!1)},_removeSliderEventHandlers:function(){if(this.handle1.removeEventListener("keydown",this.handle1Keydown,!1),this.handle2.removeEventListener("keydown",this.handle2Keydown,!1),this.options.ticks_tooltip){for(var t=this.ticksContainer.getElementsByClassName("slider-tick"),e=0;e<t.length;e++)t[e].removeEventListener("mouseenter",this.ticksCallbackMap[e].mouseEnter,!1),t[e].removeEventListener("mouseleave",this.ticksCallbackMap[e].mouseLeave,!1);this.handleCallbackMap.handle1&&this.handleCallbackMap.handle2&&(this.handle1.removeEventListener("mouseenter",this.handleCallbackMap.handle1.mouseEnter,!1),this.handle2.removeEventListener("mouseenter",this.handleCallbackMap.handle2.mouseEnter,!1),this.handle1.removeEventListener("mouseleave",this.handleCallbackMap.handle1.mouseLeave,!1),this.handle2.removeEventListener("mouseleave",this.handleCallbackMap.handle2.mouseLeave,!1))}this.handleCallbackMap=null,this.ticksCallbackMap=null,this.showTooltip&&this._removeTooltipListener("focus",this.showTooltip),this.hideTooltip&&this._removeTooltipListener("blur",this.hideTooltip),this.showTooltip&&this.sliderElem.removeEventListener("mouseenter",this.showTooltip,!1),this.hideTooltip&&this.sliderElem.removeEventListener("mouseleave",this.hideTooltip,!1),this.sliderElem.removeEventListener("mousedown",this.mousedown,!1),this.touchCapable&&(this.showTooltip&&(this.handle1.removeEventListener("touchstart",this.showTooltip,!1),this.handle1.removeEventListener("touchmove",this.showTooltip,!1),this.handle2.removeEventListener("touchstart",this.showTooltip,!1),this.handle2.removeEventListener("touchmove",this.showTooltip,!1)),this.hideTooltip&&(this.handle1.removeEventListener("touchend",this.hideTooltip,!1),this.handle2.removeEventListener("touchend",this.hideTooltip,!1)),this.showTooltip&&(this.sliderElem.removeEventListener("touchstart",this.showTooltip,!1),this.sliderElem.removeEventListener("touchmove",this.showTooltip,!1)),this.hideTooltip&&this.sliderElem.removeEventListener("touchend",this.hideTooltip,!1),this.sliderElem.removeEventListener("touchstart",this.touchstart,!1),this.sliderElem.removeEventListener("touchmove",this.touchmove,!1)),window.removeEventListener("resize",this.resize,!1)},_bindNonQueryEventHandler:function(t,e){void 0===this.eventToCallbackMap[t]&&(this.eventToCallbackMap[t]=[]),this.eventToCallbackMap[t].push(e)},_unbindNonQueryEventHandler:function(t,e){var i=this.eventToCallbackMap[t];if(void 0!==i)for(var s=0;s<i.length;s++)if(i[s]===e){i.splice(s,1);break}},_cleanUpEventCallbacksMap:function(){for(var t=Object.keys(this.eventToCallbackMap),e=0;e<t.length;e++){var i=t[e];delete this.eventToCallbackMap[i]}},_showTooltip:function(){!1===this.options.tooltip_split?(this._addClass(this.tooltip,"in"),this.tooltip_min.style.display="none",this.tooltip_max.style.display="none"):(this._addClass(this.tooltip_min,"in"),this._addClass(this.tooltip_max,"in"),this.tooltip.style.display="none"),this._state.over=!0},_hideTooltip:function(){!1===this._state.inDrag&&!0!==this._alwaysShowTooltip&&(this._removeClass(this.tooltip,"in"),this._removeClass(this.tooltip_min,"in"),this._removeClass(this.tooltip_max,"in")),this._state.over=!1},_setToolTipOnMouseOver:function(t){var i=this,e=this.options.formatter((t||this._state).value[0]),t=s(t||this._state,this.options.reversed);function s(t,e){return e?[100-t.percentage[0],i.options.range?100-t.percentage[1]:t.percentage[1]]:[t.percentage[0],t.percentage[1]]}this._setText(this.tooltipInner,e),this.tooltip.style[this.stylePos]=t[0]+"%"},_copyState:function(){return{value:[this._state.value[0],this._state.value[1]],enabled:this._state.enabled,offset:this._state.offset,size:this._state.size,percentage:[this._state.percentage[0],this._state.percentage[1],this._state.percentage[2]],inDrag:this._state.inDrag,over:this._state.over,dragged:this._state.dragged,keyCtrl:this._state.keyCtrl}},_addTickListener:function(){return{addMouseEnter:function(s,o,n){function t(){var t=s._copyState(),e=o===s.handle1?t.value[0]:t.value[1],i=void 0,i=void 0!==n?(e=s.options.ticks[n],0<s.options.ticks_positions.length&&s.options.ticks_positions[n]||s._toPercentage(s.options.ticks[n])):s._toPercentage(e);t.value[0]=e,t.percentage[0]=i,s._setToolTipOnMouseOver(t),s._showTooltip()}return o.addEventListener("mouseenter",t,!1),t},addMouseLeave:function(t,e){function i(){t._hideTooltip()}return e.addEventListener("mouseleave",i,!1),i}}},_layout:function(){var t,e,i,s=this.options.reversed?[100-this._state.percentage[0],this.options.range?100-this._state.percentage[1]:this._state.percentage[1]]:[this._state.percentage[0],this._state.percentage[1]];if(this.handle1.style[this.stylePos]=s[0]+"%",this.handle1.setAttribute("aria-valuenow",this._state.value[0]),t=this.options.formatter(this._state.value[0]),isNaN(t)?this.handle1.setAttribute("aria-valuetext",t):this.handle1.removeAttribute("aria-valuetext"),this.handle2.style[this.stylePos]=s[1]+"%",this.handle2.setAttribute("aria-valuenow",this._state.value[1]),t=this.options.formatter(this._state.value[1]),isNaN(t)?this.handle2.setAttribute("aria-valuetext",t):this.handle2.removeAttribute("aria-valuetext"),0<this.rangeHighlightElements.length&&Array.isArray(this.options.rangeHighlights)&&0<this.options.rangeHighlights.length)for(var o=0;o<this.options.rangeHighlights.length;o++){var n,a=this._toPercentage(this.options.rangeHighlights[o].start),h=this._toPercentage(this.options.rangeHighlights[o].end);this.options.reversed&&(n=100-h,h=100-a,a=n);h=this._createHighlightRange(a,h);h?"vertical"===this.options.orientation?(this.rangeHighlightElements[o].style.top=h.start+"%",this.rangeHighlightElements[o].style.height=h.size+"%"):(this.options.rtl?this.rangeHighlightElements[o].style.right=h.start+"%":this.rangeHighlightElements[o].style.left=h.start+"%",this.rangeHighlightElements[o].style.width=h.size+"%"):this.rangeHighlightElements[o].style.display="none"}if(Array.isArray(this.options.ticks)&&0<this.options.ticks.length){var l="vertical"===this.options.orientation?"height":"width",r="vertical"===this.options.orientation?"marginTop":this.options.rtl?"marginRight":"marginLeft",d=this._state.size/(this.options.ticks.length-1);if(this.tickLabelContainer){var p=0;if(0===this.options.ticks_positions.length)"vertical"!==this.options.orientation&&(this.tickLabelContainer.style[r]=-d/2+"px"),p=this.tickLabelContainer.offsetHeight;else for(c=0;c<this.tickLabelContainer.childNodes.length;c++)this.tickLabelContainer.childNodes[c].offsetHeight>p&&(p=this.tickLabelContainer.childNodes[c].offsetHeight);"horizontal"===this.options.orientation&&(this.sliderElem.style.marginBottom=p+"px")}for(var c=0;c<this.options.ticks.length;c++){var u=this.options.ticks_positions[c]||this._toPercentage(this.options.ticks[c]);this.options.reversed&&(u=100-u),this.ticks[c].style[this.stylePos]=u+"%",this._removeClass(this.ticks[c],"in-selection"),this.options.range?u>=s[0]&&u<=s[1]&&this._addClass(this.ticks[c],"in-selection"):("after"===this.options.selection&&u>=s[0]||"before"===this.options.selection&&u<=s[0])&&this._addClass(this.ticks[c],"in-selection"),this.tickLabels[c]&&(this.tickLabels[c].style[l]=d+"px","vertical"!==this.options.orientation&&void 0!==this.options.ticks_positions[c]?(this.tickLabels[c].style.position="absolute",this.tickLabels[c].style[this.stylePos]=u+"%",this.tickLabels[c].style[r]=-d/2+"px"):"vertical"===this.options.orientation&&(this.options.rtl?this.tickLabels[c].style.marginRight=this.sliderElem.offsetWidth+"px":this.tickLabels[c].style.marginLeft=this.sliderElem.offsetWidth+"px",this.tickLabelContainer.style[r]=this.sliderElem.offsetWidth/2*-1+"px"),this._removeClass(this.tickLabels[c],"label-in-selection label-is-selection"),this.options.range?u>=s[0]&&u<=s[1]&&(this._addClass(this.tickLabels[c],"label-in-selection"),u!==s[0]&&!s[1]||this._addClass(this.tickLabels[c],"label-is-selection")):(("after"===this.options.selection&&u>=s[0]||"before"===this.options.selection&&u<=s[0])&&this._addClass(this.tickLabels[c],"label-in-selection"),u===s[0]&&this._addClass(this.tickLabels[c],"label-is-selection")))}}this.options.range?(i=this.options.formatter(this._state.value),this._setText(this.tooltipInner,i),this.tooltip.style[this.stylePos]=(s[1]+s[0])/2+"%",e=this.options.formatter(this._state.value[0]),this._setText(this.tooltipInner_min,e),e=this.options.formatter(this._state.value[1]),this._setText(this.tooltipInner_max,e),this.tooltip_min.style[this.stylePos]=s[0]+"%",this.tooltip_max.style[this.stylePos]=s[1]+"%"):(i=this.options.formatter(this._state.value[0]),this._setText(this.tooltipInner,i),this.tooltip.style[this.stylePos]=s[0]+"%"),"vertical"===this.options.orientation?(this.trackLow.style.top="0",this.trackLow.style.height=Math.min(s[0],s[1])+"%",this.trackSelection.style.top=Math.min(s[0],s[1])+"%",this.trackSelection.style.height=Math.abs(s[0]-s[1])+"%",this.trackHigh.style.bottom="0",this.trackHigh.style.height=100-Math.min(s[0],s[1])-Math.abs(s[0]-s[1])+"%"):("right"===this.stylePos?this.trackLow.style.right="0":this.trackLow.style.left="0",this.trackLow.style.width=Math.min(s[0],s[1])+"%","right"===this.stylePos?this.trackSelection.style.right=Math.min(s[0],s[1])+"%":this.trackSelection.style.left=Math.min(s[0],s[1])+"%",this.trackSelection.style.width=Math.abs(s[0]-s[1])+"%","right"===this.stylePos?this.trackHigh.style.left="0":this.trackHigh.style.right="0",this.trackHigh.style.width=100-Math.min(s[0],s[1])-Math.abs(s[0]-s[1])+"%",e=this.tooltip_min.getBoundingClientRect(),i=this.tooltip_max.getBoundingClientRect(),"bottom"===this.options.tooltip_position?e.right>i.left?(this._removeClass(this.tooltip_max,"bottom"),this._addClass(this.tooltip_max,"top"),this.tooltip_max.style.top="",this.tooltip_max.style.bottom="22px"):(this._removeClass(this.tooltip_max,"top"),this._addClass(this.tooltip_max,"bottom"),this.tooltip_max.style.top=this.tooltip_min.style.top,this.tooltip_max.style.bottom=""):e.right>i.left?(this._removeClass(this.tooltip_max,"top"),this._addClass(this.tooltip_max,"bottom"),this.tooltip_max.style.top="18px"):(this._removeClass(this.tooltip_max,"bottom"),this._addClass(this.tooltip_max,"top"),this.tooltip_max.style.top=this.tooltip_min.style.top))},_createHighlightRange:function(t,e){return this._isHighlightRange(t,e)?e<t?{start:e,size:t-e}:{start:t,size:e-t}:null},_isHighlightRange:function(t,e){return 0<=t&&t<=100&&0<=e&&e<=100},_resize:function(t){this._state.offset=this._offset(this.sliderElem),this._state.size=this.sliderElem[this.sizePos],this._layout()},_removeProperty:function(t,e){t.style.removeProperty?t.style.removeProperty(e):t.style.removeAttribute(e)},_mousedown:function(t){if(!this._state.enabled)return!1;t.preventDefault&&t.preventDefault(),this._state.offset=this._offset(this.sliderElem),this._state.size=this.sliderElem[this.sizePos];var e,i,s=this._getPercentage(t);this.options.range?(e=Math.abs(this._state.percentage[0]-s),i=Math.abs(this._state.percentage[1]-s),this._state.dragged=e<i?0:1,this._adjustPercentageForRangeSliders(s)):this._state.dragged=0,this._state.percentage[this._state.dragged]=s,this.touchCapable&&(document.removeEventListener("touchmove",this.mousemove,!1),document.removeEventListener("touchend",this.mouseup,!1)),this.mousemove&&document.removeEventListener("mousemove",this.mousemove,!1),this.mouseup&&document.removeEventListener("mouseup",this.mouseup,!1),this.mousemove=this._mousemove.bind(this),this.mouseup=this._mouseup.bind(this),this.touchCapable&&(document.addEventListener("touchmove",this.mousemove,!1),document.addEventListener("touchend",this.mouseup,!1)),document.addEventListener("mousemove",this.mousemove,!1),document.addEventListener("mouseup",this.mouseup,!1),this._state.inDrag=!0;s=this._calculateValue();return this._trigger("slideStart",s),this.setValue(s,!1,!0),t.returnValue=!1,this.options.focus&&this._triggerFocusOnHandle(this._state.dragged),!0},_touchstart:function(t){this._mousedown(t)},_triggerFocusOnHandle:function(t){0===t&&this.handle1.focus(),1===t&&this.handle2.focus()},_keydown:function(t,e){if(!this._state.enabled)return!1;var i,s,o,n;switch(e.keyCode){case 37:case 40:i=-1;break;case 39:case 38:i=1}if(i){this.options.natural_arrow_keys&&(s="horizontal"===this.options.orientation,o="vertical"===this.options.orientation,n=this.options.rtl,a=this.options.reversed,s?n?a||(i=-i):a&&(i=-i):o&&(a||(i=-i))),a=this.ticksAreValid&&this.options.lock_to_ticks?(h=void 0,-1===(h=this.options.ticks.indexOf(this._state.value[t]))&&(h=0,window.console.warn("(lock_to_ticks) _keydown: index should not be -1")),h+=i,h=Math.max(0,Math.min(this.options.ticks.length-1,h)),this.options.ticks[h]):this._state.value[t]+i*this.options.step;var a,h=this._toPercentage(a);return this._state.keyCtrl=t,a=this.options.range?(this._adjustPercentageForRangeSliders(h),t=this._state.keyCtrl?this._state.value[0]:a,h=this._state.keyCtrl?a:this._state.value[1],[Math.max(this.options.min,Math.min(this.options.max,t)),Math.max(this.options.min,Math.min(this.options.max,h))]):Math.max(this.options.min,Math.min(this.options.max,a)),this._trigger("slideStart",a),this.setValue(a,!0,!0),this._trigger("slideStop",a),this._pauseEvent(e),delete this._state.keyCtrl,!1}},_pauseEvent:function(t){t.stopPropagation&&t.stopPropagation(),t.preventDefault&&t.preventDefault(),t.cancelBubble=!0,t.returnValue=!1},_mousemove:function(t){if(!this._state.enabled)return!1;t=this._getPercentage(t);this._adjustPercentageForRangeSliders(t),this._state.percentage[this._state.dragged]=t;t=this._calculateValue(!0);return this.setValue(t,!0,!0),!1},_touchmove:function(t){void 0!==t.changedTouches&&t.preventDefault&&t.preventDefault()},_adjustPercentageForRangeSliders:function(t){var e,i;this.options.range&&(e=this._getNumDigitsAfterDecimalPlace(t),i=this._applyToFixedAndParseFloat(t,e=e?e-1:0),0===this._state.dragged&&this._applyToFixedAndParseFloat(this._state.percentage[1],e)<i?(this._state.percentage[0]=this._state.percentage[1],this._state.dragged=1):1===this._state.dragged&&this._applyToFixedAndParseFloat(this._state.percentage[0],e)>i?(this._state.percentage[1]=this._state.percentage[0],this._state.dragged=0):0===this._state.keyCtrl&&this._toPercentage(this._state.value[1])<t?(this._state.percentage[0]=this._state.percentage[1],this._state.keyCtrl=1,this.handle2.focus()):1===this._state.keyCtrl&&this._toPercentage(this._state.value[0])>t&&(this._state.percentage[1]=this._state.percentage[0],this._state.keyCtrl=0,this.handle1.focus()))},_mouseup:function(t){if(!this._state.enabled)return!1;t=this._getPercentage(t);this._adjustPercentageForRangeSliders(t),this._state.percentage[this._state.dragged]=t,this.touchCapable&&(document.removeEventListener("touchmove",this.mousemove,!1),document.removeEventListener("touchend",this.mouseup,!1)),document.removeEventListener("mousemove",this.mousemove,!1),document.removeEventListener("mouseup",this.mouseup,!1),(this._state.inDrag=!1)===this._state.over&&this._hideTooltip();t=this._calculateValue(!0);return this.setValue(t,!1,!0),this._trigger("slideStop",t),this._state.dragged=null,!1},_setValues:function(t,e){this._state.percentage[t]!==(0===t?0:100)&&(e.data[t]=this._toValue(this._state.percentage[t]),e.data[t]=this._applyPrecision(e.data[t]))},_calculateValue:function(t){var e={};return this.options.range?(e.data=[this.options.min,this.options.max],this._setValues(0,e),this._setValues(1,e),t&&(e.data[0]=this._snapToClosestTick(e.data[0]),e.data[1]=this._snapToClosestTick(e.data[1]))):(e.data=this._toValue(this._state.percentage[0]),e.data=parseFloat(e.data),e.data=this._applyPrecision(e.data),t&&(e.data=this._snapToClosestTick(e.data))),e.data},_snapToClosestTick:function(t){for(var e=[t,1/0],i=0;i<this.options.ticks.length;i++){var s=Math.abs(this.options.ticks[i]-t);s<=e[1]&&(e=[this.options.ticks[i],s])}return e[1]<=this.options.ticks_snap_bounds?e[0]:t},_applyPrecision:function(t){var e=this.options.precision||this._getNumDigitsAfterDecimalPlace(this.options.step);return this._applyToFixedAndParseFloat(t,e)},_getNumDigitsAfterDecimalPlace:function(t){t=(""+t).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);return t?Math.max(0,(t[1]?t[1].length:0)-(t[2]?+t[2]:0)):0},_applyToFixedAndParseFloat:function(t,e){e=t.toFixed(e);return parseFloat(e)},_getPercentage:function(t){t=(t=this.touchCapable&&("touchstart"===t.type||"touchmove"===t.type||"touchend"===t.type)?t.changedTouches[0]:t)[this.mousePos]-this._state.offset[this.stylePos],t=(t="right"===this.stylePos?-t:t)/this._state.size*100,t=Math.round(t/this._state.percentage[2])*this._state.percentage[2];return this.options.reversed&&(t=100-t),Math.max(0,Math.min(100,t))},_validateInputValue:function(t){if(isNaN(+t)){if(Array.isArray(t))return this._validateArray(t),t;throw new Error(s(t))}return+t},_validateArray:function(t){for(var e=0;e<t.length;e++){var i=t[e];if("number"!=typeof i)throw new Error(s(i))}},_setDataVal:function(t){this.element.setAttribute("data-value",t),this.element.setAttribute("value",t),this.element.value=t},_trigger:function(t,e){e=e||0===e?e:void 0;var i=this.eventToCallbackMap[t];if(i&&i.length)for(var s=0;s<i.length;s++)(0,i[s])(e);P&&this._triggerJQueryEvent(t,e)},_triggerJQueryEvent:function(t,e){e={type:t,value:e};this.$element.trigger(e),this.$sliderElem.trigger(e)},_unbindJQueryEventHandlers:function(){this.$element.off(),this.$sliderElem.off()},_setText:function(t,e){void 0!==t.textContent?t.textContent=e:void 0!==t.innerText&&(t.innerText=e)},_removeClass:function(t,e){for(var i=e.split(" "),s=t.className,o=0;o<i.length;o++)var n=i[o],n=new RegExp("(?:\\s|^)"+n+"(?:\\s|$)"),s=s.replace(n," ");t.className=s.trim()},_addClass:function(t,e){for(var i=e.split(" "),s=t.className,o=0;o<i.length;o++){var n=i[o];new RegExp("(?:\\s|^)"+n+"(?:\\s|$)").test(s)||(s+=" "+n)}t.className=s.trim()},_offsetLeft:function(t){return t.getBoundingClientRect().left},_offsetRight:function(t){return t.getBoundingClientRect().right},_offsetTop:function(t){for(var e=t.offsetTop;(t=t.offsetParent)&&!isNaN(t.offsetTop);)e+=t.offsetTop,"BODY"!==t.tagName&&(e-=t.scrollTop);return e},_offset:function(t){return{left:this._offsetLeft(t),right:this._offsetRight(t),top:this._offsetTop(t)}},_css:function(t,e,i){P?P.style(t,e,i):(e=e.replace(/^-ms-/,"ms-").replace(/-([\da-z])/gi,function(t,e){return e.toUpperCase()}),t.style[e]=i)},_toValue:function(t){return this.options.scale.toValue.apply(this,[t])},_toPercentage:function(t){return this.options.scale.toPercentage.apply(this,[t])},_setTooltipPosition:function(){var e,i,t=[this.tooltip,this.tooltip_min,this.tooltip_max];"vertical"===this.options.orientation?(e=this.options.tooltip_position||(this.options.rtl?"left":"right"),i="left"===e?"right":"left",t.forEach(function(t){this._addClass(t,e),t.style[i]="100%"}.bind(this))):"bottom"===this.options.tooltip_position?t.forEach(function(t){this._addClass(t,"bottom"),t.style.top="22px"}.bind(this)):t.forEach(function(t){this._addClass(t,"top"),t.style.top=-this.tooltip.outerHeight-14+"px"}.bind(this))},_getClosestTickIndex:function(t){for(var e=Math.abs(t-this.options.ticks[0]),i=0,s=0;s<this.options.ticks.length;++s){var o=Math.abs(t-this.options.ticks[s]);o<e&&(e=o,i=s)}return i},_setTickIndex:function(){this.ticksAreValid&&(this._state.tickIndex=[this.options.ticks.indexOf(this._state.value[0]),this.options.ticks.indexOf(this._state.value[1])])}},P&&P.fn&&(i=P.fn.slider?(windowIsDefined&&window.console.warn("bootstrap-slider.js - WARNING: $.fn.slider namespace is already bound. Use the $.fn.bootstrapSlider namespace instead."),a):(P.bridget(n,e),n),P.bridget(a,e),P(function(){P("input[data-provide=slider]")[i]()}))}(t),e});