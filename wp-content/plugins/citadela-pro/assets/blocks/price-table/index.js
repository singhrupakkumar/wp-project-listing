!function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=1)}([function(e){e.exports=JSON.parse('{"apiVersion":1,"name":"citadela-blocks/price-table","category":"citadela-blocks","icon":"clipboard","attributes":{"rows":{"type":"array","default":[{"text":""},{"text":""},{"text":""}]},"title":{"type":"string","default":""},"subtitle":{"type":"string","default":""},"price":{"type":"string","default":""},"showOldPrice":{"type":"boolean","default":false},"oldPrice":{"type":"string","default":""},"showButton":{"type":"boolean","default":true},"buttonText":{"type":"string","default":""},"buttonUrl":{"type":"string","default":""},"buttonLinkNewTab":{"type":"boolean","default":false},"buttonBorderRadius":{"type":"number"},"featuredTable":{"type":"boolean","default":false},"featuredTableText":{"type":"string","default":""},"alignment":{"type":"string","default":"center"},"colorHeaderBg":{"type":"string"},"colorHeaderText":{"type":"string"},"colorButtonBg":{"type":"string"},"colorButtonText":{"type":"string"}},"editorScript":"citadela-price-table-block"}')},function(e,t,n){"use strict";function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function l(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function c(e,t){return(c=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function a(e,t){return!t||"object"!==r(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function i(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function u(e){return(u=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}n.r(t);var s=wp.i18n.__,p=wp.element,f=p.Component,b=p.useCallback,d=wp.blockEditor.InspectorControls,m=wp.components,y=m.PanelBody,w=m.BaseControl,h=m.ColorPalette,g=m.ToggleControl,v=m.RangeControl,O=function(e){var t=e.borderRadius,n=void 0===t?"":t,r=e.setAttributes,o=b((function(e){r({buttonBorderRadius:e})}),[r]);return wp.element.createElement(v,{value:n,label:s("Border radius","citadela-pro"),min:0,max:20,initialPosition:20,allowReset:!0,onChange:o})},P=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&c(e,t)}(b,e);var t,n,r,p,f=(t=b,function(){var e,n=u(t);if(i()){var r=u(this).constructor;e=Reflect.construct(n,arguments,r)}else e=n.apply(this,arguments);return a(this,e)});function b(){return o(this,b),f.apply(this,arguments)}return n=b,(r=[{key:"render",value:function(){var e=this.props,t=e.attributes,n=e.setAttributes,r=t.buttonLinkNewTab,o=t.colorHeaderBg,l=t.colorHeaderText,c=t.colorButtonBg,a=t.colorButtonText,i=t.buttonBorderRadius,u=[{color:"#f78da7"},{color:"#cf2e2e"},{color:"#ff6900"},{color:"#fcb900"},{color:"#7bdcb5"},{color:"#00d084"},{color:"#8ed1fc"},{color:"#0693e3"},{color:"#9b51e0"},{color:"#eeeeee"},{color:"#abb8c3"},{color:"#313131"}];return wp.element.createElement(d,{key:"inspector"},wp.element.createElement(y,{title:s("Colors","citadela-pro"),initialOpen:!1,className:"citadela-panel"},wp.element.createElement(w,{label:s("Header background","citadela-pro")},wp.element.createElement(h,{value:o,className:"block-editor-color-palette-control__color-palette",onChange:function(e){n({colorHeaderBg:e})},colors:u})),wp.element.createElement(w,{label:s("Header text","citadela-pro")},wp.element.createElement(h,{value:l,className:"block-editor-color-palette-control__color-palette",onChange:function(e){n({colorHeaderText:e})},colors:u})),wp.element.createElement(w,{label:s("Button background","citadela-pro")},wp.element.createElement(h,{value:c,className:"block-editor-color-palette-control__color-palette",onChange:function(e){n({colorButtonBg:e})},colors:u})),wp.element.createElement(w,{label:s("Button text","citadela-pro")},wp.element.createElement(h,{value:a,className:"block-editor-color-palette-control__color-palette",onChange:function(e){n({colorButtonText:e})},colors:u}))),wp.element.createElement(y,{title:s("Button settings","citadela-pro"),initialOpen:!1,className:"citadela-panel"},wp.element.createElement(g,{label:s("Open link in new tab","citadela-pro"),checked:r,onChange:function(e){return n({buttonLinkNewTab:e})}}),wp.element.createElement(O,{borderRadius:i,setAttributes:n})))}}])&&l(n.prototype,r),p&&l(n,p),b}(f);function E(e){return(E="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function R(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function j(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function x(e,t){return(x=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function _(e,t){return!t||"object"!==E(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function k(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function T(e){return(T=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var S=wp.i18n.__,B=wp.element.Component,C=wp.components.DropdownMenu,N=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&x(e,t)}(c,e);var t,n,r,o,l=(t=c,function(){var e,n=T(t);if(k()){var r=T(this).constructor;e=Reflect.construct(n,arguments,r)}else e=n.apply(this,arguments);return _(this,e)});function c(){return R(this,c),l.apply(this,arguments)}return n=c,(r=[{key:"render",value:function(){var e=this.props,t=e.toggleProps,n=e.value,r=e.onChange,o=e.label,l=void 0===o?S("Select alignment","citadela-pro"):o,c={left:"editor-alignleft",center:"editor-aligncenter",right:"editor-alignright"};return wp.element.createElement(C,{icon:c[n],label:l,toggleProps:t,controls:[{title:S("Align Text Left","citadela-pro"),icon:c.left,isActive:"left"===n,onClick:function(){r("left")}},{title:S("Align Text Center","citadela-pro"),icon:c.center,isActive:"center"===n,onClick:function(){r("center")}},{title:S("Align Text Right","citadela-pro"),icon:c.right,isActive:"right"===n,onClick:function(){r("right")}}]})}}])&&j(n.prototype,r),o&&j(n,o),c}(B);function D(e){return(D="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function H(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function F(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function A(e,t){return(A=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function U(e,t){return!t||"object"!==D(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function M(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function L(e){return(L=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var I=wp.i18n.__,$=wp.element.Component,G=wp.components,J=G.ToolbarGroup,V=G.ToolbarItem,W=G.ToolbarButton,q=wp.blockEditor.BlockControls,z=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&A(e,t)}(c,e);var t,n,r,o,l=(t=c,function(){var e,n=L(t);if(M()){var r=L(this).constructor;e=Reflect.construct(n,arguments,r)}else e=n.apply(this,arguments);return U(this,e)});function c(){return H(this,c),l.apply(this,arguments)}return n=c,(r=[{key:"render",value:function(){var e=this.props,t=e.attributes,n=e.setAttributes,r=t.featuredTable,o=t.showOldPrice,l=t.showButton,c=t.alignment;return wp.element.createElement(q,{key:"controls"},wp.element.createElement(J,null,wp.element.createElement(V,{as:function(e){return wp.element.createElement(N,{label:I("Price table alignment","citadela-pro"),value:c,onChange:function(e){return n({alignment:e})},toggleProps:e})}})),wp.element.createElement(J,null,wp.element.createElement(W,{icon:"star-filled",label:I(r?"Disable featured table":"Enable featured table","citadela-pro"),isPressed:r,onClick:function(){return n({featuredTable:!r})}}),wp.element.createElement(W,{icon:"tag",label:I(o?"Hide discount price":"Show discount price","citadela-pro"),isPressed:o,onClick:function(){return n({showOldPrice:!o})}}),wp.element.createElement(W,{icon:"admin-links",label:I(l?"Hide button with link":"Show button with link","citadela-pro"),isPressed:l,onClick:function(){return n({showButton:!l})}})))}}])&&F(n.prototype,r),o&&F(n,o),c}($);function K(e){return(K="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function Q(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function X(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function Y(e,t){return(Y=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function Z(e,t){return!t||"object"!==K(t)&&"function"!=typeof t?ee(e):t}function ee(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function te(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function ne(e){return(ne=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var re=wp.i18n.__,oe=wp.element,le=oe.Component,ce=oe.Fragment,ae=wp.components.Button,ie=wp.blockEditor.RichText,ue=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&Y(e,t)}(c,e);var t,n,r,o,l=(t=c,function(){var e,n=ne(t);if(te()){var r=ne(this).constructor;e=Reflect.construct(n,arguments,r)}else e=n.apply(this,arguments);return Z(this,e)});function c(){var e;return Q(this,c),(e=l.apply(this,arguments)).handleRowUpdate=e.handleRowUpdate.bind(ee(e)),e.insertRow=e.insertRow.bind(ee(e)),e.deleteRow=e.deleteRow.bind(ee(e)),e.moveRow=e.moveRow.bind(ee(e)),e.newRows=[],e}return n=c,(r=[{key:"handleRowUpdate",value:function(e,t){var n=this.props.rows;this.newRows=n.slice(0,n.length+1);var r={text:e};this.newRows[t]=r,this.props.setAttributes({rows:this.newRows})}},{key:"insertRow",value:function(e){var t=this.props.rows;this.newRows=t.slice(0,t.length+1),this.newRows.splice(e+1,0,{text:""}),this.props.setAttributes({rows:this.newRows})}},{key:"deleteRow",value:function(e){var t=this.props.rows;this.newRows=t.slice(0,t.length+1),this.newRows.splice(e,1),this.props.setAttributes({rows:this.newRows})}},{key:"moveRow",value:function(e,t){var n=this.props.rows;this.newRows=n.slice(0,n.length+1);var r=this.newRows[e];switch(t){case"up":this.newRows.splice(e-1,0,r),this.newRows.splice(e+1,1);break;case"down":this.newRows.splice(e+2,0,r),this.newRows.splice(e,1)}this.props.setAttributes({rows:this.newRows})}},{key:"render",value:function(){var e=this,t=this.props,n=t.rows,r=t.isSelected,o=n.length-1,l=n.map((function(t,n){return wp.element.createElement("div",{class:"row"},wp.element.createElement(ie,{tagName:"div",className:classNames("row-text",{"empty-row":""==t.text}),onChange:function(t){e.handleRowUpdate(t,n)},value:t.text,placeholder:r?re("row text...","citadela-pro"):"",keepPlaceholderOnFocus:!0,multiline:!1}),wp.element.createElement("div",{class:"row-tools"},n>0&&wp.element.createElement(ae,{icon:"arrow-up",label:re("Move up","citadela-pro"),onClick:function(){return e.moveRow(n,"up")}}),n<o&&wp.element.createElement(ae,{icon:"arrow-down",label:re("Move down","citadela-pro"),onClick:function(){return e.moveRow(n,"down")}}),o>0&&wp.element.createElement(ae,{icon:"no",label:re("Delete row","citadela-pro"),onClick:function(){return e.deleteRow(n)}}),wp.element.createElement(ae,{icon:"plus",label:re("Insert row after","citadela-pro"),onClick:function(){return e.insertRow(n)}})))}));return wp.element.createElement(ce,null,l)}}])&&X(n.prototype,r),o&&X(n,o),c}(le);function se(e){return(se="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function pe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function fe(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?pe(Object(n),!0).forEach((function(t){be(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):pe(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function be(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function de(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function me(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function ye(e,t){return(ye=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function we(e,t){return!t||"object"!==se(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function he(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function ge(e){return(ge=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var ve=wp.i18n.__,Oe=wp.element,Pe=Oe.Component,Ee=Oe.Fragment,Re=wp.blockEditor,je=Re.RichText,xe=Re.URLInput,_e=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&ye(e,t)}(c,e);var t,n,r,o,l=(t=c,function(){var e,n=ge(t);if(he()){var r=ge(this).constructor;e=Reflect.construct(n,arguments,r)}else e=n.apply(this,arguments);return we(this,e)});function c(){return de(this,c),l.apply(this,arguments)}return n=c,(r=[{key:"render",value:function(){var e=this.props,t=e.attributes,n=e.setAttributes,r=e.className,o=e.isSelected,l=t.title,c=t.subtitle,a=t.price,i=t.oldPrice,u=t.showButton,s=t.buttonText,p=t.buttonUrl,f=t.featuredTable,b=t.featuredTableText,d=t.showOldPrice,m=t.rows,y=t.alignment,w=t.colorHeaderBg,h=t.colorHeaderText,g=t.colorButtonBg,v=t.colorButtonText,O=t.buttonBorderRadius,E={colorHeaderBg:w?{backgroundColor:w}:void 0,colorHeaderText:h?{color:h}:void 0,colorButtonBg:g?{backgroundColor:g}:void 0,colorButtonText:v?{color:v}:void 0},R=fe(fe({},E.colorHeaderBg),E.colorHeaderText),j=fe(fe(fe({},E.colorButtonBg),E.colorButtonText),{borderRadius:void 0!==O?O+"px":void 0}),x=!(!u||""==s||""==p);return wp.element.createElement(Ee,null,wp.element.createElement(z,{attributes:t,setAttributes:n}),wp.element.createElement(P,{attributes:t,setAttributes:n}),wp.element.createElement("div",{className:classNames(r,"citadela-block-price-table",{"is-selected":o},{"is-featured":f},{"with-old-price":d},{"with-button":x},"align-"+y)},wp.element.createElement("div",{class:"price-table-content"},wp.element.createElement("div",{class:"price-table-header",style:R},wp.element.createElement("div",{class:"title-part"},f&&wp.element.createElement(je,{tagName:"div",className:"featured-text",onChange:function(e){n({featuredTableText:e})},value:b,placeholder:ve("Featured","citadela-pro"),keepPlaceholderOnFocus:!0,allowedFormats:[]}),wp.element.createElement(je,{tagName:"h3",onChange:function(e){n({title:e})},value:l,placeholder:ve("Table title","citadela-pro"),keepPlaceholderOnFocus:!0,allowedFormats:[]}),wp.element.createElement(je,{tagName:"p",className:"subtitle-text",onChange:function(e){n({subtitle:e})},value:c,placeholder:ve("Table subtitle","citadela-pro"),keepPlaceholderOnFocus:!0,allowedFormats:[]})),wp.element.createElement("div",{class:"price-part"},wp.element.createElement(je,{tagName:"span",className:"current-price",onChange:function(e){n({price:e})},value:a,placeholder:"$99",keepPlaceholderOnFocus:!0,allowedFormats:[]}),d&&wp.element.createElement(je,{tagName:"span",className:"old-price",onChange:function(e){n({oldPrice:e})},value:i,placeholder:"$199",keepPlaceholderOnFocus:!0,allowedFormats:[]}))),wp.element.createElement("div",{class:"price-table-body"},wp.element.createElement("div",{class:"rows-part"},wp.element.createElement(ue,{rows:m,setAttributes:n,isSelected:o})),u&&wp.element.createElement("div",{class:"button-part"},wp.element.createElement(je,{placeholder:ve("button text","citadela-pro"),value:s,onChange:function(e){return n({buttonText:e})},keepPlaceholderOnFocus:!0,allowedFormats:[],className:"readmore-button",style:j}),o&&wp.element.createElement(xe,{label:ve("Button link","citadela-pro"),value:p,autoFocus:!1,onChange:function(e){return n({buttonUrl:e})},disableSuggestions:!o,isFullWidth:!0,hasBorder:!0}))))))}}])&&me(n.prototype,r),o&&me(n,o),c}(Pe);function ke(e){return(ke="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function Te(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function Se(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?Te(Object(n),!0).forEach((function(t){Be(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Te(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function Be(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Ce(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function Ne(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function De(e,t){return(De=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function He(e,t){return!t||"object"!==ke(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function Fe(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function Ae(e){return(Ae=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}wp.i18n.__;var Ue=wp.element,Me=Ue.Component,Le=(Ue.Fragment,wp.blockEditor.RichText),Ie=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&De(e,t)}(c,e);var t,n,r,o,l=(t=c,function(){var e,n=Ae(t);if(Fe()){var r=Ae(this).constructor;e=Reflect.construct(n,arguments,r)}else e=n.apply(this,arguments);return He(this,e)});function c(){return Ce(this,c),l.apply(this,arguments)}return n=c,(r=[{key:"render",value:function(){var e=this.props,t=e.attributes,n=e.className,r=t.title,o=t.subtitle,l=t.price,c=t.oldPrice,a=t.showButton,i=t.buttonText,u=t.buttonUrl,s=t.featuredTable,p=t.featuredTableText,f=t.showOldPrice,b=t.rows,d=t.alignment,m=t.buttonLinkNewTab,y=t.colorHeaderBg,w=t.colorHeaderText,h=t.colorButtonBg,g=t.colorButtonText,v=t.buttonBorderRadius,O={colorHeaderBg:y?{backgroundColor:y}:null,colorHeaderText:w?{color:w}:null,colorButtonBg:h?{backgroundColor:h}:null,colorButtonText:g?{color:g}:null},P=Se(Se({},O.colorHeaderBg),O.colorHeaderText),E=Se(Se(Se({},O.colorButtonBg),O.colorButtonText),{borderRadius:void 0!==v?v+"px":void 0}),R=b.map((function(e,t){return wp.element.createElement(Le.Content,{tagName:"div",className:classNames("row-text",{"empty-row":""==e.text}),value:e.text})})),j=m?"_blank":void 0,x=m?"noopener noreferrer":void 0,_=!(!f||""==c),k=!(!a||""==i||""==u);return wp.element.createElement("div",{className:classNames(n,"citadela-block-price-table",{"is-featured":s},{"with-old-price":f},{"with-button":k},"align-"+d)},wp.element.createElement("div",{class:"price-table-content"},wp.element.createElement("div",{class:"price-table-header",style:P},(r||o||s&&p)&&wp.element.createElement("div",{class:"title-part"},s&&""!==p&&wp.element.createElement(Le.Content,{tagName:"div",className:"featured-text",value:p}),r&&wp.element.createElement(Le.Content,{tagName:"h3",value:r}),o&&wp.element.createElement(Le.Content,{tagName:"p",className:"subtitle-text",value:o})),(l||_)&&wp.element.createElement("div",{class:"price-part"},l&&wp.element.createElement(Le.Content,{tagName:"span",className:"current-price",value:l}),_&&wp.element.createElement(Le.Content,{tagName:"span",className:"old-price",value:c}))),wp.element.createElement("div",{class:"price-table-body"},wp.element.createElement("div",{class:"rows-part"},R),k&&wp.element.createElement("div",{class:"button-part"},wp.element.createElement(Le.Content,{tagName:"a",className:"readmore-button",href:u,value:i,target:j,rel:x,style:E})))))}}])&&Ne(n.prototype,r),o&&Ne(n,o),c}(Me),$e=[],Ge=n(0);function Je(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function Ve(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?Je(Object(n),!0).forEach((function(t){We(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Je(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function We(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var qe=wp.i18n.__;(0,wp.blocks.registerBlockType)(Ge.name,Ve(Ve({},Ge),{},{title:qe("Price Table","citadela-pro"),description:qe("Display product, service or packages offers in the table. Set the best deal, show discounted price and write important features for comparison.","citadela-pro"),edit:_e,save:Ie,deprecated:$e,example:{attributes:{rows:[{text:"Suspendisse facilisis purus"},{text:"Sed fringilla libero augue"},{text:"Praesent id mi et diam mollis"}],title:qe("Membership","citadela-pro"),subtitle:"Nunc mattis consectetur nisl",price:"$70/mo",colorHeaderBg:"#3178d8",showButton:!0,buttonText:"Lorem ipsum"}}}))}]);