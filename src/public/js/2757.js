(self.webpackChunk=self.webpackChunk||[]).push([[2757,8522],{7757:(t,e,r)=>{t.exports=r(5666)},5666:t=>{var e=function(t){"use strict";var e,r=Object.prototype,n=r.hasOwnProperty,o="function"==typeof Symbol?Symbol:{},i=o.iterator||"@@iterator",a=o.asyncIterator||"@@asyncIterator",s=o.toStringTag||"@@toStringTag";function c(t,e,r,n){var o=e&&e.prototype instanceof m?e:m,i=Object.create(o.prototype),a=new k(n||[]);return i._invoke=function(t,e,r){var n=u;return function(o,i){if(n===p)throw new Error("Generator is already running");if(n===d){if("throw"===o)throw i;return M()}for(r.method=o,r.arg=i;;){var a=r.delegate;if(a){var s=j(a,r);if(s){if(s===h)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(n===u)throw n=d,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n=p;var c=l(t,e,r);if("normal"===c.type){if(n=r.done?d:f,c.arg===h)continue;return{value:c.arg,done:r.done}}"throw"===c.type&&(n=d,r.method="throw",r.arg=c.arg)}}}(t,r,a),i}function l(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}t.wrap=c;var u="suspendedStart",f="suspendedYield",p="executing",d="completed",h={};function m(){}function v(){}function g(){}var y={};y[i]=function(){return this};var b=Object.getPrototypeOf,w=b&&b(b(L([])));w&&w!==r&&n.call(w,i)&&(y=w);var x=g.prototype=m.prototype=Object.create(y);function _(t){["next","throw","return"].forEach((function(e){t[e]=function(t){return this._invoke(e,t)}}))}function O(t,e){function r(o,i,a,s){var c=l(t[o],t,i);if("throw"!==c.type){var u=c.arg,f=u.value;return f&&"object"==typeof f&&n.call(f,"__await")?e.resolve(f.__await).then((function(t){r("next",t,a,s)}),(function(t){r("throw",t,a,s)})):e.resolve(f).then((function(t){u.value=t,a(u)}),(function(t){return r("throw",t,a,s)}))}s(c.arg)}var o;this._invoke=function(t,n){function i(){return new e((function(e,o){r(t,n,e,o)}))}return o=o?o.then(i,i):i()}}function j(t,r){var n=t.iterator[r.method];if(n===e){if(r.delegate=null,"throw"===r.method){if(t.iterator.return&&(r.method="return",r.arg=e,j(t,r),"throw"===r.method))return h;r.method="throw",r.arg=new TypeError("The iterator does not provide a 'throw' method")}return h}var o=l(n,t.iterator,r.arg);if("throw"===o.type)return r.method="throw",r.arg=o.arg,r.delegate=null,h;var i=o.arg;return i?i.done?(r[t.resultName]=i.value,r.next=t.nextLoc,"return"!==r.method&&(r.method="next",r.arg=e),r.delegate=null,h):i:(r.method="throw",r.arg=new TypeError("iterator result is not an object"),r.delegate=null,h)}function C(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function E(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function k(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(C,this),this.reset(!0)}function L(t){if(t){var r=t[i];if(r)return r.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var o=-1,a=function r(){for(;++o<t.length;)if(n.call(t,o))return r.value=t[o],r.done=!1,r;return r.value=e,r.done=!0,r};return a.next=a}}return{next:M}}function M(){return{value:e,done:!0}}return v.prototype=x.constructor=g,g.constructor=v,g[s]=v.displayName="GeneratorFunction",t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===v||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,g):(t.__proto__=g,s in t||(t[s]="GeneratorFunction")),t.prototype=Object.create(x),t},t.awrap=function(t){return{__await:t}},_(O.prototype),O.prototype[a]=function(){return this},t.AsyncIterator=O,t.async=function(e,r,n,o,i){void 0===i&&(i=Promise);var a=new O(c(e,r,n,o),i);return t.isGeneratorFunction(r)?a:a.next().then((function(t){return t.done?t.value:a.next()}))},_(x),x[s]="Generator",x[i]=function(){return this},x.toString=function(){return"[object Generator]"},t.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var n=e.pop();if(n in t)return r.value=n,r.done=!1,r}return r.done=!0,r}},t.values=L,k.prototype={constructor:k,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=e,this.done=!1,this.delegate=null,this.method="next",this.arg=e,this.tryEntries.forEach(E),!t)for(var r in this)"t"===r.charAt(0)&&n.call(this,r)&&!isNaN(+r.slice(1))&&(this[r]=e)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var r=this;function o(n,o){return s.type="throw",s.arg=t,r.next=n,o&&(r.method="next",r.arg=e),!!o}for(var i=this.tryEntries.length-1;i>=0;--i){var a=this.tryEntries[i],s=a.completion;if("root"===a.tryLoc)return o("end");if(a.tryLoc<=this.prev){var c=n.call(a,"catchLoc"),l=n.call(a,"finallyLoc");if(c&&l){if(this.prev<a.catchLoc)return o(a.catchLoc,!0);if(this.prev<a.finallyLoc)return o(a.finallyLoc)}else if(c){if(this.prev<a.catchLoc)return o(a.catchLoc,!0)}else{if(!l)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return o(a.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,h):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),h},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),E(r),h}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var o=n.arg;E(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,r,n){return this.delegate={iterator:L(t),resultName:r,nextLoc:n},"next"===this.method&&(this.arg=e),h}},t}(t.exports);try{regeneratorRuntime=e}catch(t){Function("r","regeneratorRuntime = r")(e)}},266:(t,e,r)=>{"use strict";function n(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}function o(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{},o=Object.keys(r);"function"==typeof Object.getOwnPropertySymbols&&(o=o.concat(Object.getOwnPropertySymbols(r).filter((function(t){return Object.getOwnPropertyDescriptor(r,t).enumerable})))),o.forEach((function(e){n(t,e,r[e])}))}return t}r.d(e,{HY:()=>c});var i=function(t,e,r){Object.defineProperty(t,e,{configurable:!0,get:function(){return r},set:function(t){console.warn("tried to set frozen property ".concat(e," with ").concat(t))}})},a=function(t,e){var r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null;Object.defineProperty(t,e,{configurable:!0,writable:!0,value:r})},s={abstract:!0,name:"Fragment",props:{name:{type:String,default:function(){return Math.floor(Date.now()*Math.random()).toString(16)}}},mounted:function(){var t=this.$el,e=t.parentNode,r=document.createComment("fragment#".concat(this.name,"#head")),n=document.createComment("fragment#".concat(this.name,"#tail"));e.insertBefore(r,t),e.insertBefore(n,t),t.appendChild=function(r){e.insertBefore(r,n),i(r,"parentNode",t)},t.insertBefore=function(r,n){e.insertBefore(r,n),i(r,"parentNode",t)},t.removeChild=function(t){e.removeChild(t),a(t,"parentNode")},Array.from(t.childNodes).forEach((function(e){return t.appendChild(e)})),e.removeChild(t),i(t,"parentNode",e),i(t,"nextSibling",n.nextSibling);var o=e.insertBefore;e.insertBefore=function(n,i){o.call(e,n,i!==t?i:r)};var s=e.removeChild;e.removeChild=function(i){if(i===t){for(;r.nextSibling!==n;)t.removeChild(r.nextSibling);e.removeChild(r),e.removeChild(n),a(t,"parentNode"),e.insertBefore=o,e.removeChild=s}else s.call(e,i)}},render:function(t){var e=this,r=this.$slots.default;return r&&r.length&&r.forEach((function(t){return t.data=o({},t.data,{attrs:o({fragment:e.name},(t.data||{}).attrs)})})),t("div",{attrs:{fragment:this.name}},r)}};var c=s},8522:(t,e,r)=>{"use strict";r.r(e),r.d(e,{default:()=>lt});var n=r(1433);function o(t){var e=t.getBoundingClientRect();return{width:e.width,height:e.height,top:e.top,right:e.right,bottom:e.bottom,left:e.left,x:e.left,y:e.top}}function i(t){if("[object Window]"!==t.toString()){var e=t.ownerDocument;return e&&e.defaultView||window}return t}function a(t){var e=i(t);return{scrollLeft:e.pageXOffset,scrollTop:e.pageYOffset}}function s(t){return t instanceof i(t).Element||t instanceof Element}function c(t){return t instanceof i(t).HTMLElement||t instanceof HTMLElement}function l(t){return t?(t.nodeName||"").toLowerCase():null}function u(t){return((s(t)?t.ownerDocument:t.document)||window.document).documentElement}function f(t){return o(u(t)).left+a(t).scrollLeft}function p(t){return i(t).getComputedStyle(t)}function d(t){var e=p(t),r=e.overflow,n=e.overflowX,o=e.overflowY;return/auto|scroll|overlay|hidden/.test(r+o+n)}function h(t,e,r){void 0===r&&(r=!1);var n,s,p=u(e),h=o(t),m=c(e),v={scrollLeft:0,scrollTop:0},g={x:0,y:0};return(m||!m&&!r)&&(("body"!==l(e)||d(p))&&(v=(n=e)!==i(n)&&c(n)?{scrollLeft:(s=n).scrollLeft,scrollTop:s.scrollTop}:a(n)),c(e)?((g=o(e)).x+=e.clientLeft,g.y+=e.clientTop):p&&(g.x=f(p))),{x:h.left+v.scrollLeft-g.x,y:h.top+v.scrollTop-g.y,width:h.width,height:h.height}}function m(t){return{x:t.offsetLeft,y:t.offsetTop,width:t.offsetWidth,height:t.offsetHeight}}function v(t){return"html"===l(t)?t:t.assignedSlot||t.parentNode||t.host||u(t)}function g(t){return["html","body","#document"].indexOf(l(t))>=0?t.ownerDocument.body:c(t)&&d(t)?t:g(v(t))}function y(t,e){void 0===e&&(e=[]);var r=g(t),n="body"===l(r),o=i(r),a=n?[o].concat(o.visualViewport||[],d(r)?r:[]):r,s=e.concat(a);return n?s:s.concat(y(v(a)))}function b(t){return["table","td","th"].indexOf(l(t))>=0}function w(t){if(!c(t)||"fixed"===p(t).position)return null;var e=t.offsetParent;if(e){var r=u(e);if("body"===l(e)&&"static"===p(e).position&&"static"!==p(r).position)return r}return e}function x(t){for(var e=i(t),r=w(t);r&&b(r)&&"static"===p(r).position;)r=w(r);return r&&"body"===l(r)&&"static"===p(r).position?e:r||function(t){for(var e=v(t);c(e)&&["html","body"].indexOf(l(e))<0;){var r=p(e);if("none"!==r.transform||"none"!==r.perspective||r.willChange&&"auto"!==r.willChange)return e;e=e.parentNode}return null}(t)||e}var _="top",O="bottom",j="right",C="left",E="auto",k=[_,O,j,C],L="start",M="end",P="viewport",D="popper",B=k.reduce((function(t,e){return t.concat([e+"-"+L,e+"-"+M])}),[]),S=[].concat(k,[E]).reduce((function(t,e){return t.concat([e,e+"-"+L,e+"-"+M])}),[]),A=["beforeRead","read","afterRead","beforeMain","main","afterMain","beforeWrite","write","afterWrite"];function N(t){var e=new Map,r=new Set,n=[];function o(t){r.add(t.name),[].concat(t.requires||[],t.requiresIfExists||[]).forEach((function(t){if(!r.has(t)){var n=e.get(t);n&&o(n)}})),n.push(t)}return t.forEach((function(t){e.set(t.name,t)})),t.forEach((function(t){r.has(t.name)||o(t)})),n}var z={placement:"bottom",modifiers:[],strategy:"absolute"};function R(){for(var t=arguments.length,e=new Array(t),r=0;r<t;r++)e[r]=arguments[r];return!e.some((function(t){return!(t&&"function"==typeof t.getBoundingClientRect)}))}function T(t){void 0===t&&(t={});var e=t,r=e.defaultModifiers,n=void 0===r?[]:r,o=e.defaultOptions,i=void 0===o?z:o;return function(t,e,r){void 0===r&&(r=i);var o,a,c={placement:"bottom",orderedModifiers:[],options:Object.assign(Object.assign({},z),i),modifiersData:{},elements:{reference:t,popper:e},attributes:{},styles:{}},l=[],u=!1,f={state:c,setOptions:function(r){p(),c.options=Object.assign(Object.assign(Object.assign({},i),c.options),r),c.scrollParents={reference:s(t)?y(t):t.contextElement?y(t.contextElement):[],popper:y(e)};var o=function(t){var e=N(t);return A.reduce((function(t,r){return t.concat(e.filter((function(t){return t.phase===r})))}),[])}(function(t){var e=t.reduce((function(t,e){var r=t[e.name];return t[e.name]=r?Object.assign(Object.assign(Object.assign({},r),e),{},{options:Object.assign(Object.assign({},r.options),e.options),data:Object.assign(Object.assign({},r.data),e.data)}):e,t}),{});return Object.keys(e).map((function(t){return e[t]}))}([].concat(n,c.options.modifiers)));return c.orderedModifiers=o.filter((function(t){return t.enabled})),c.orderedModifiers.forEach((function(t){var e=t.name,r=t.options,n=void 0===r?{}:r,o=t.effect;if("function"==typeof o){var i=o({state:c,name:e,instance:f,options:n}),a=function(){};l.push(i||a)}})),f.update()},forceUpdate:function(){if(!u){var t=c.elements,e=t.reference,r=t.popper;if(R(e,r)){c.rects={reference:h(e,x(r),"fixed"===c.options.strategy),popper:m(r)},c.reset=!1,c.placement=c.options.placement,c.orderedModifiers.forEach((function(t){return c.modifiersData[t.name]=Object.assign({},t.data)}));for(var n=0;n<c.orderedModifiers.length;n++)if(!0!==c.reset){var o=c.orderedModifiers[n],i=o.fn,a=o.options,s=void 0===a?{}:a,l=o.name;"function"==typeof i&&(c=i({state:c,options:s,name:l,instance:f})||c)}else c.reset=!1,n=-1}}},update:(o=function(){return new Promise((function(t){f.forceUpdate(),t(c)}))},function(){return a||(a=new Promise((function(t){Promise.resolve().then((function(){a=void 0,t(o())}))}))),a}),destroy:function(){p(),u=!0}};if(!R(t,e))return f;function p(){l.forEach((function(t){return t()})),l=[]}return f.setOptions(r).then((function(t){!u&&r.onFirstUpdate&&r.onFirstUpdate(t)})),f}}var H={passive:!0};function $(t){return t.split("-")[0]}function I(t){return t.split("-")[1]}function W(t){return["top","bottom"].indexOf(t)>=0?"x":"y"}function q(t){var e,r=t.reference,n=t.element,o=t.placement,i=o?$(o):null,a=o?I(o):null,s=r.x+r.width/2-n.width/2,c=r.y+r.height/2-n.height/2;switch(i){case _:e={x:s,y:r.y-n.height};break;case O:e={x:s,y:r.y+r.height};break;case j:e={x:r.x+r.width,y:c};break;case C:e={x:r.x-n.width,y:c};break;default:e={x:r.x,y:r.y}}var l=i?W(i):null;if(null!=l){var u="y"===l?"height":"width";switch(a){case L:e[l]=e[l]-(r[u]/2-n[u]/2);break;case M:e[l]=e[l]+(r[u]/2-n[u]/2)}}return e}var V={top:"auto",right:"auto",bottom:"auto",left:"auto"};function F(t){var e,r=t.popper,n=t.popperRect,o=t.placement,a=t.offsets,s=t.position,c=t.gpuAcceleration,l=t.adaptive,f=t.roundOffsets?function(t){var e=t.x,r=t.y,n=window.devicePixelRatio||1;return{x:Math.round(e*n)/n||0,y:Math.round(r*n)/n||0}}(a):a,p=f.x,d=void 0===p?0:p,h=f.y,m=void 0===h?0:h,v=a.hasOwnProperty("x"),g=a.hasOwnProperty("y"),y=C,b=_,w=window;if(l){var E=x(r);E===i(r)&&(E=u(r)),o===_&&(b=O,m-=E.clientHeight-n.height,m*=c?1:-1),o===C&&(y=j,d-=E.clientWidth-n.width,d*=c?1:-1)}var k,L=Object.assign({position:s},l&&V);return c?Object.assign(Object.assign({},L),{},((k={})[b]=g?"0":"",k[y]=v?"0":"",k.transform=(w.devicePixelRatio||1)<2?"translate("+d+"px, "+m+"px)":"translate3d("+d+"px, "+m+"px, 0)",k)):Object.assign(Object.assign({},L),{},((e={})[b]=g?m+"px":"",e[y]=v?d+"px":"",e.transform="",e))}var G={left:"right",right:"left",bottom:"top",top:"bottom"};function Z(t){return t.replace(/left|right|bottom|top/g,(function(t){return G[t]}))}var U={start:"end",end:"start"};function Y(t){return t.replace(/start|end/g,(function(t){return U[t]}))}function X(t,e){var r,n=e.getRootNode&&e.getRootNode();if(t.contains(e))return!0;if(n&&((r=n)instanceof i(r).ShadowRoot||r instanceof ShadowRoot)){var o=e;do{if(o&&t.isSameNode(o))return!0;o=o.parentNode||o.host}while(o)}return!1}function J(t){return Object.assign(Object.assign({},t),{},{left:t.x,top:t.y,right:t.x+t.width,bottom:t.y+t.height})}function K(t,e){return e===P?J(function(t){var e=i(t),r=u(t),n=e.visualViewport,o=r.clientWidth,a=r.clientHeight,s=0,c=0;return n&&(o=n.width,a=n.height,/^((?!chrome|android).)*safari/i.test(navigator.userAgent)||(s=n.offsetLeft,c=n.offsetTop)),{width:o,height:a,x:s+f(t),y:c}}(t)):c(e)?function(t){var e=o(t);return e.top=e.top+t.clientTop,e.left=e.left+t.clientLeft,e.bottom=e.top+t.clientHeight,e.right=e.left+t.clientWidth,e.width=t.clientWidth,e.height=t.clientHeight,e.x=e.left,e.y=e.top,e}(e):J(function(t){var e=u(t),r=a(t),n=t.ownerDocument.body,o=Math.max(e.scrollWidth,e.clientWidth,n?n.scrollWidth:0,n?n.clientWidth:0),i=Math.max(e.scrollHeight,e.clientHeight,n?n.scrollHeight:0,n?n.clientHeight:0),s=-r.scrollLeft+f(t),c=-r.scrollTop;return"rtl"===p(n||e).direction&&(s+=Math.max(e.clientWidth,n?n.clientWidth:0)-o),{width:o,height:i,x:s,y:c}}(u(t)))}function Q(t,e,r){var n="clippingParents"===e?function(t){var e=y(v(t)),r=["absolute","fixed"].indexOf(p(t).position)>=0&&c(t)?x(t):t;return s(r)?e.filter((function(t){return s(t)&&X(t,r)&&"body"!==l(t)})):[]}(t):[].concat(e),o=[].concat(n,[r]),i=o[0],a=o.reduce((function(e,r){var n=K(t,r);return e.top=Math.max(n.top,e.top),e.right=Math.min(n.right,e.right),e.bottom=Math.min(n.bottom,e.bottom),e.left=Math.max(n.left,e.left),e}),K(t,i));return a.width=a.right-a.left,a.height=a.bottom-a.top,a.x=a.left,a.y=a.top,a}function tt(t){return Object.assign(Object.assign({},{top:0,right:0,bottom:0,left:0}),t)}function et(t,e){return e.reduce((function(e,r){return e[r]=t,e}),{})}function rt(t,e){void 0===e&&(e={});var r=e,n=r.placement,i=void 0===n?t.placement:n,a=r.boundary,c=void 0===a?"clippingParents":a,l=r.rootBoundary,f=void 0===l?P:l,p=r.elementContext,d=void 0===p?D:p,h=r.altBoundary,m=void 0!==h&&h,v=r.padding,g=void 0===v?0:v,y=tt("number"!=typeof g?g:et(g,k)),b=d===D?"reference":D,w=t.elements.reference,x=t.rects.popper,C=t.elements[m?b:d],E=Q(s(C)?C:C.contextElement||u(t.elements.popper),c,f),L=o(w),M=q({reference:L,element:x,strategy:"absolute",placement:i}),B=J(Object.assign(Object.assign({},x),M)),S=d===D?B:L,A={top:E.top-S.top+y.top,bottom:S.bottom-E.bottom+y.bottom,left:E.left-S.left+y.left,right:S.right-E.right+y.right},N=t.modifiersData.offset;if(d===D&&N){var z=N[i];Object.keys(A).forEach((function(t){var e=[j,O].indexOf(t)>=0?1:-1,r=[_,O].indexOf(t)>=0?"y":"x";A[t]+=z[r]*e}))}return A}function nt(t,e,r){return Math.max(t,Math.min(e,r))}function ot(t,e,r){return void 0===r&&(r={x:0,y:0}),{top:t.top-e.height-r.y,right:t.right-e.width+r.x,bottom:t.bottom-e.height+r.y,left:t.left-e.width-r.x}}function it(t){return[_,j,O,C].some((function(e){return t[e]>=0}))}var at=T({defaultModifiers:[{name:"eventListeners",enabled:!0,phase:"write",fn:function(){},effect:function(t){var e=t.state,r=t.instance,n=t.options,o=n.scroll,a=void 0===o||o,s=n.resize,c=void 0===s||s,l=i(e.elements.popper),u=[].concat(e.scrollParents.reference,e.scrollParents.popper);return a&&u.forEach((function(t){t.addEventListener("scroll",r.update,H)})),c&&l.addEventListener("resize",r.update,H),function(){a&&u.forEach((function(t){t.removeEventListener("scroll",r.update,H)})),c&&l.removeEventListener("resize",r.update,H)}},data:{}},{name:"popperOffsets",enabled:!0,phase:"read",fn:function(t){var e=t.state,r=t.name;e.modifiersData[r]=q({reference:e.rects.reference,element:e.rects.popper,strategy:"absolute",placement:e.placement})},data:{}},{name:"computeStyles",enabled:!0,phase:"beforeWrite",fn:function(t){var e=t.state,r=t.options,n=r.gpuAcceleration,o=void 0===n||n,i=r.adaptive,a=void 0===i||i,s=r.roundOffsets,c=void 0===s||s,l={placement:$(e.placement),popper:e.elements.popper,popperRect:e.rects.popper,gpuAcceleration:o};null!=e.modifiersData.popperOffsets&&(e.styles.popper=Object.assign(Object.assign({},e.styles.popper),F(Object.assign(Object.assign({},l),{},{offsets:e.modifiersData.popperOffsets,position:e.options.strategy,adaptive:a,roundOffsets:c})))),null!=e.modifiersData.arrow&&(e.styles.arrow=Object.assign(Object.assign({},e.styles.arrow),F(Object.assign(Object.assign({},l),{},{offsets:e.modifiersData.arrow,position:"absolute",adaptive:!1,roundOffsets:c})))),e.attributes.popper=Object.assign(Object.assign({},e.attributes.popper),{},{"data-popper-placement":e.placement})},data:{}},{name:"applyStyles",enabled:!0,phase:"write",fn:function(t){var e=t.state;Object.keys(e.elements).forEach((function(t){var r=e.styles[t]||{},n=e.attributes[t]||{},o=e.elements[t];c(o)&&l(o)&&(Object.assign(o.style,r),Object.keys(n).forEach((function(t){var e=n[t];!1===e?o.removeAttribute(t):o.setAttribute(t,!0===e?"":e)})))}))},effect:function(t){var e=t.state,r={popper:{position:e.options.strategy,left:"0",top:"0",margin:"0"},arrow:{position:"absolute"},reference:{}};return Object.assign(e.elements.popper.style,r.popper),e.elements.arrow&&Object.assign(e.elements.arrow.style,r.arrow),function(){Object.keys(e.elements).forEach((function(t){var n=e.elements[t],o=e.attributes[t]||{},i=Object.keys(e.styles.hasOwnProperty(t)?e.styles[t]:r[t]).reduce((function(t,e){return t[e]="",t}),{});c(n)&&l(n)&&(Object.assign(n.style,i),Object.keys(o).forEach((function(t){n.removeAttribute(t)})))}))}},requires:["computeStyles"]},{name:"offset",enabled:!0,phase:"main",requires:["popperOffsets"],fn:function(t){var e=t.state,r=t.options,n=t.name,o=r.offset,i=void 0===o?[0,0]:o,a=S.reduce((function(t,r){return t[r]=function(t,e,r){var n=$(t),o=[C,_].indexOf(n)>=0?-1:1,i="function"==typeof r?r(Object.assign(Object.assign({},e),{},{placement:t})):r,a=i[0],s=i[1];return a=a||0,s=(s||0)*o,[C,j].indexOf(n)>=0?{x:s,y:a}:{x:a,y:s}}(r,e.rects,i),t}),{}),s=a[e.placement],c=s.x,l=s.y;null!=e.modifiersData.popperOffsets&&(e.modifiersData.popperOffsets.x+=c,e.modifiersData.popperOffsets.y+=l),e.modifiersData[n]=a}},{name:"flip",enabled:!0,phase:"main",fn:function(t){var e=t.state,r=t.options,n=t.name;if(!e.modifiersData[n]._skip){for(var o=r.mainAxis,i=void 0===o||o,a=r.altAxis,s=void 0===a||a,c=r.fallbackPlacements,l=r.padding,u=r.boundary,f=r.rootBoundary,p=r.altBoundary,d=r.flipVariations,h=void 0===d||d,m=r.allowedAutoPlacements,v=e.options.placement,g=$(v),y=c||(g===v||!h?[Z(v)]:function(t){if($(t)===E)return[];var e=Z(t);return[Y(t),e,Y(e)]}(v)),b=[v].concat(y).reduce((function(t,r){return t.concat($(r)===E?function(t,e){void 0===e&&(e={});var r=e,n=r.placement,o=r.boundary,i=r.rootBoundary,a=r.padding,s=r.flipVariations,c=r.allowedAutoPlacements,l=void 0===c?S:c,u=I(n),f=u?s?B:B.filter((function(t){return I(t)===u})):k,p=f.filter((function(t){return l.indexOf(t)>=0}));0===p.length&&(p=f);var d=p.reduce((function(e,r){return e[r]=rt(t,{placement:r,boundary:o,rootBoundary:i,padding:a})[$(r)],e}),{});return Object.keys(d).sort((function(t,e){return d[t]-d[e]}))}(e,{placement:r,boundary:u,rootBoundary:f,padding:l,flipVariations:h,allowedAutoPlacements:m}):r)}),[]),w=e.rects.reference,x=e.rects.popper,M=new Map,P=!0,D=b[0],A=0;A<b.length;A++){var N=b[A],z=$(N),R=I(N)===L,T=[_,O].indexOf(z)>=0,H=T?"width":"height",W=rt(e,{placement:N,boundary:u,rootBoundary:f,altBoundary:p,padding:l}),q=T?R?j:C:R?O:_;w[H]>x[H]&&(q=Z(q));var V=Z(q),F=[];if(i&&F.push(W[z]<=0),s&&F.push(W[q]<=0,W[V]<=0),F.every((function(t){return t}))){D=N,P=!1;break}M.set(N,F)}if(P)for(var G=function(t){var e=b.find((function(e){var r=M.get(e);if(r)return r.slice(0,t).every((function(t){return t}))}));if(e)return D=e,"break"},U=h?3:1;U>0;U--){if("break"===G(U))break}e.placement!==D&&(e.modifiersData[n]._skip=!0,e.placement=D,e.reset=!0)}},requiresIfExists:["offset"],data:{_skip:!1}},{name:"preventOverflow",enabled:!0,phase:"main",fn:function(t){var e=t.state,r=t.options,n=t.name,o=r.mainAxis,i=void 0===o||o,a=r.altAxis,s=void 0!==a&&a,c=r.boundary,l=r.rootBoundary,u=r.altBoundary,f=r.padding,p=r.tether,d=void 0===p||p,h=r.tetherOffset,v=void 0===h?0:h,g=rt(e,{boundary:c,rootBoundary:l,padding:f,altBoundary:u}),y=$(e.placement),b=I(e.placement),w=!b,E=W(y),k="x"===E?"y":"x",M=e.modifiersData.popperOffsets,P=e.rects.reference,D=e.rects.popper,B="function"==typeof v?v(Object.assign(Object.assign({},e.rects),{},{placement:e.placement})):v,S={x:0,y:0};if(M){if(i){var A="y"===E?_:C,N="y"===E?O:j,z="y"===E?"height":"width",R=M[E],T=M[E]+g[A],H=M[E]-g[N],q=d?-D[z]/2:0,V=b===L?P[z]:D[z],F=b===L?-D[z]:-P[z],G=e.elements.arrow,Z=d&&G?m(G):{width:0,height:0},U=e.modifiersData["arrow#persistent"]?e.modifiersData["arrow#persistent"].padding:{top:0,right:0,bottom:0,left:0},Y=U[A],X=U[N],J=nt(0,P[z],Z[z]),K=w?P[z]/2-q-J-Y-B:V-J-Y-B,Q=w?-P[z]/2+q+J+X+B:F+J+X+B,tt=e.elements.arrow&&x(e.elements.arrow),et=tt?"y"===E?tt.clientTop||0:tt.clientLeft||0:0,ot=e.modifiersData.offset?e.modifiersData.offset[e.placement][E]:0,it=M[E]+K-ot-et,at=M[E]+Q-ot,st=nt(d?Math.min(T,it):T,R,d?Math.max(H,at):H);M[E]=st,S[E]=st-R}if(s){var ct="x"===E?_:C,lt="x"===E?O:j,ut=M[k],ft=nt(ut+g[ct],ut,ut-g[lt]);M[k]=ft,S[k]=ft-ut}e.modifiersData[n]=S}},requiresIfExists:["offset"]},{name:"arrow",enabled:!0,phase:"main",fn:function(t){var e,r=t.state,n=t.name,o=r.elements.arrow,i=r.modifiersData.popperOffsets,a=$(r.placement),s=W(a),c=[C,j].indexOf(a)>=0?"height":"width";if(o&&i){var l=r.modifiersData[n+"#persistent"].padding,u=m(o),f="y"===s?_:C,p="y"===s?O:j,d=r.rects.reference[c]+r.rects.reference[s]-i[s]-r.rects.popper[c],h=i[s]-r.rects.reference[s],v=x(o),g=v?"y"===s?v.clientHeight||0:v.clientWidth||0:0,y=d/2-h/2,b=l[f],w=g-u[c]-l[p],E=g/2-u[c]/2+y,k=nt(b,E,w),L=s;r.modifiersData[n]=((e={})[L]=k,e.centerOffset=k-E,e)}},effect:function(t){var e=t.state,r=t.options,n=t.name,o=r.element,i=void 0===o?"[data-popper-arrow]":o,a=r.padding,s=void 0===a?0:a;null!=i&&("string"!=typeof i||(i=e.elements.popper.querySelector(i)))&&X(e.elements.popper,i)&&(e.elements.arrow=i,e.modifiersData[n+"#persistent"]={padding:tt("number"!=typeof s?s:et(s,k))})},requires:["popperOffsets"],requiresIfExists:["preventOverflow"]},{name:"hide",enabled:!0,phase:"main",requiresIfExists:["preventOverflow"],fn:function(t){var e=t.state,r=t.name,n=e.rects.reference,o=e.rects.popper,i=e.modifiersData.preventOverflow,a=rt(e,{elementContext:"reference"}),s=rt(e,{altBoundary:!0}),c=ot(a,n),l=ot(s,o,i),u=it(c),f=it(l);e.modifiersData[r]={referenceClippingOffsets:c,popperEscapeOffsets:l,isReferenceHidden:u,hasPopperEscaped:f},e.attributes.popper=Object.assign(Object.assign({},e.attributes.popper),{},{"data-popper-reference-hidden":u,"data-popper-escaped":f})}}]}),st=r(266);const ct={name:"CharacterApplication",components:{EveImage:n.Z,Fragment:st.HY},props:{enlistments:{type:Array},character_id:{type:Number}},data:function(){return{menuOpen:!1}},methods:{toggleMenu:function(){this.menuOpen=!this.menuOpen,this.menuOpen&&at(this.$refs.menu,this.$refs.list,{modifiers:[{name:"offset",options:{offset:[0,8]}}]})}},created:function(){}};const lt=(0,r(1900).Z)(ct,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("Fragment",[r("button",{ref:"menu",staticClass:"relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150",on:{click:t.toggleMenu}},[r("svg",{staticClass:"w-5 h-5 text-gray-400",attrs:{viewBox:"0 0 20 20",fill:"currentColor"}},[r("path",{attrs:{d:"M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"}}),t._v(" "),r("path",{attrs:{"fill-rule":"evenodd",d:"M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z","clip-rule":"evenodd"}})]),t._v(" "),r("span",{staticClass:"ml-3"},[t._v("Apply")])]),t._v(" "),t.menuOpen?r("div",{staticClass:"fixed inset-0 transition-opacity"},[r("div",{staticClass:"absolute inset-0 bg-transparent",on:{click:t.toggleMenu}})]):t._e(),t._v(" "),r("transition",{attrs:{"enter-active-class":"duration-150 ease-out","enter-class":"opacity-0 scale-95","enter-to-class":"opacity-100 scale-100","leave-active-class":"duration-100 ease-in","leave-class":"opacity-100 scale-100","leave-to-class":"opacity-0 scale-95"}},[r("div",{directives:[{name:"show",rawName:"v-show",value:t.menuOpen,expression:"menuOpen"}],ref:"list",staticClass:"origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg"},[r("div",{staticClass:"py-1 rounded-md bg-white ring-1 ring-black ring-opacity-5"},t._l(t.enlistments,(function(e){return r("inertia-link",{key:e.corporation_id,class:"block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150 text-left",attrs:{method:"post",href:t.$route("post.application"),data:{corporation_id:e.corporation_id,character_id:t.character_id}}},[r("div",{staticClass:"flex items-center"},[r("div",{staticClass:"flex-shrink-0"},[r("EveImage",{attrs:{tailwind_class:"h-8 w-8 rounded-full",size:256,object:e.corporation}})],1),t._v(" "),r("div",{staticClass:"ml-4"},[r("h3",{staticClass:"text-sm leading-6 font-medium text-gray-900"},[t._v("\n                                "+t._s(e.corporation.name)+"\n                            ")]),t._v(" "),r("p",{staticClass:"text-xs leading-5 text-gray-500"},[t._v("\n                                "+t._s(e.corporation.alliance?e.corporation.alliance.name:"")+"\n                            ")])])])])})),1)])])],1)}),[],!1,null,"a572378e",null).exports},2757:(t,e,r)=>{"use strict";r.r(e),r.d(e,{default:()=>i});var n=r(8522);const o={name:"Characters",components:{EveImage:r(1433).Z,CharacterApplication:n.default},props:{characters:{type:Array},enlistments:{type:Array,default:[]}},computed:{hasOpenEnlistments:function(){return!_.isEmpty(this.enlistments)}}};const i=(0,r(1900).Z)(o,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"pb-5 border-b border-gray-200 space-y-2"},[r("h3",{staticClass:"text-lg leading-6 font-medium text-gray-900"},[t._v("\n            Characters\n        ")]),t._v(" "),r("p",{staticClass:"max-w-4xl text-sm leading-5 text-gray-500"},[t._v("Below you find all characters you have added to this seatplus instance")]),t._v(" "),r("ul",{staticClass:"grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"},[t._l(t.characters,(function(e){return r("li",{key:e.character_id,staticClass:"col-span-1 bg-white rounded-lg shadow"},[r("div",{staticClass:"w-full flex items-center justify-between p-6 space-x-6"},[r("div",{staticClass:"flex-1 truncate"},[r("div",{staticClass:"flex items-center space-x-3"},[r("h3",{staticClass:"text-gray-900 text-sm leading-5 font-medium truncate"},[t._v(t._s(e.name))]),t._v(" "),r("span",{staticClass:"flex-shrink-0 inline-block px-2 py-0.5 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full"},[t._v(t._s(e.corporation.name)+" "+t._s(e.alliance?" - "+e.alliance.name:"")+" ")])])]),t._v(" "),r("EveImage",{attrs:{tailwind_class:"w-10 h-10 bg-gray-300 rounded-full flex-shrink-0",object:e,size:256}})],1),t._v(" "),t.hasOpenEnlistments?r("div",{staticClass:"border-t border-gray-200"},[r("div",{staticClass:"-mt-px flex"},[r("div",{staticClass:"-ml-px w-0 flex-1 flex"},[e.application?r("inertia-link",{staticClass:"relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-red-700 font-medium border border-transparent rounded-bl-lg hover:text-red-500 focus:outline-none focus:ring-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150",attrs:{href:t.$route("delete.character.application",e.character_id),method:"delete"}},[r("svg",{staticClass:"w-5 h-5 text-red-700",attrs:{viewBox:"0 0 20 20",fill:"currentColor"}},[r("path",{attrs:{d:"M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"}}),t._v(" "),r("path",{attrs:{"fill-rule":"evenodd",d:"M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z","clip-rule":"evenodd"}})]),t._v(" "),r("span",{staticClass:"ml-3"},[t._v("Remove Application")])]):r("CharacterApplication",{attrs:{enlistments:t.enlistments,character_id:e.character_id}})],1)])]):t._e()])})),t._v(" "),r("li",{staticClass:"col-span-1 bg-white rounded-lg shadow flex flex-wrap content-center"},[r("a",{staticClass:"py-4 inline-flex items-center justify-center items-center w-full h-full border border-transparent shadow-sm text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",attrs:{href:t.$route("auth.eve"),type:"button"}},[r("svg",{staticClass:"-ml-1 mr-3 h-5 w-5",attrs:{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20",fill:"currentColor"}},[r("path",{attrs:{d:"M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"}})]),t._v("\n                    Add characters\n                ")])])],2)])}),[],!1,null,"1f504964",null).exports},1433:(t,e,r)=>{"use strict";r.d(e,{Z:()=>s});var n=r(7757),o=r.n(n);function i(t,e,r,n,o,i,a){try{var s=t[i](a),c=s.value}catch(t){return void r(t)}s.done?e(c):Promise.resolve(c).then(n,o)}const a={name:"EveImage",props:{object:{type:Object,required:!0},size:{type:Number,default:32},tailwind_class:{required:!1,default:"h-12 w-12 rounded-full"},showName:{type:Boolean,required:!1,default:!1},bpo:{type:Boolean,required:!1,default:!1}},data:function(){return{img_url:"",img_class:this.tailwind_class,resource_variant:null,ready:!1}},computed:{resource_size:function(){var t=this.size<32?32:this.size;return this.isRetina?2*t:t},isRetina:function(){return window.devicePixelRatio>1||window.matchMedia&&window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches},resource_id:function(){var t=this;return _.chain(["type_id","character_id","corporation_id","alliance_id"]).map((function(e){return _.get(t.object,e)})).filter().head().value()},resource_type:function(){var t=this;return _.chain({character_id:"characters",corporation_id:"corporations",alliance_id:"alliances",type_id:"types"}).filter((function(e,r){return r in t.object})).map((function(t){return t})).head().value()},image_url:function(){return"https://images.evetech.net/".concat(this.resource_type,"/").concat(this.resource_id,"/").concat(this.resource_variant,"?size=").concat(this.resource_size,"&tenant=tranquility")}},created:function(){this.getImageVariant()},methods:{getImageVariant:function(){var t,e=this;return(t=o().mark((function t(){return o().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(!("character_id"in e.object)){t.next=2;break}return t.abrupt("return",e.resource_variant="portrait");case 2:if(!("corporation_id"in e.object)&&!("alliance_id"in e.object)){t.next=4;break}return t.abrupt("return",e.resource_variant="logo");case 4:return t.next=6,axios.get(e.$route("get.resource.variants",{resource_type:e.resource_type,resource_id:e.resource_id})).then((function(t){if(e.bpo&&_.has(_.invert(t.data),"bp"))return e.resource_variant="bp";e.resource_variant=t.data[0]}));case 6:case"end":return t.stop()}}),t)})),function(){var e=this,r=arguments;return new Promise((function(n,o){var a=t.apply(e,r);function s(t){i(a,n,o,s,c,"next",t)}function c(t){i(a,n,o,s,c,"throw",t)}s(void 0)}))})()}},watch:{resource_variant:function(t){t&&(this.ready=!0)}}};const s=(0,r(1900).Z)(a,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return t.ready?r("img",{class:t.img_class,attrs:{src:t.image_url,alt:this.object.name}}):r("svg",{staticClass:"text-indigo-600",class:t.img_class,attrs:{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20",fill:"currentColor"}},[r("path",{attrs:{"fill-rule":"evenodd",d:"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z","clip-rule":"evenodd"}})])}),[],!1,null,"61e791fc",null).exports},1900:(t,e,r)=>{"use strict";function n(t,e,r,n,o,i,a,s){var c,l="function"==typeof t?t.options:t;if(e&&(l.render=e,l.staticRenderFns=r,l._compiled=!0),n&&(l.functional=!0),i&&(l._scopeId="data-v-"+i),a?(c=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),o&&o.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(a)},l._ssrRegister=c):o&&(c=s?function(){o.call(this,(l.functional?this.parent:this).$root.$options.shadowRoot)}:o),c)if(l.functional){l._injectStyles=c;var u=l.render;l.render=function(t,e){return c.call(e),u(t,e)}}else{var f=l.beforeCreate;l.beforeCreate=f?[].concat(f,c):[c]}return{exports:t,options:l}}r.d(e,{Z:()=>n})}}]);