!function(e){var t={};function o(n){if(t[n])return t[n].exports;var r=t[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,o),r.l=!0,r.exports}o.m=e,o.c=t,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)o.d(n,r,function(t){return e[t]}.bind(null,r));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="",o(o.s=0)}([function(e,t,o){"use strict";function n(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){if("undefined"==typeof Symbol||!(Symbol.iterator in Object(e)))return;var o=[],n=!0,r=!1,a=void 0;try{for(var l,i=e[Symbol.iterator]();!(n=(l=i.next()).done)&&(o.push(l.value),!t||o.length!==t);n=!0);}catch(e){r=!0,a=e}finally{try{n||null==i.return||i.return()}finally{if(r)throw a}}return o}(e,t)||function(e,t){if(!e)return;if("string"==typeof e)return r(e,t);var o=Object.prototype.toString.call(e).slice(8,-1);"Object"===o&&e.constructor&&(o=e.constructor.name);if("Map"===o||"Set"===o)return Array.from(o);if("Arguments"===o||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(o))return r(e,t)}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function r(e,t){(null==t||t>e.length)&&(t=e.length);for(var o=0,n=new Array(t);o<t;o++)n[o]=e[o];return n}function a(e){return(a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function l(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function i(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function c(e,t){return(c=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function p(e,t){return!t||"object"!==a(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function u(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function d(e){return(d=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}o.r(t);var m=wp.i18n.__,s=(wp.hooks.applyFilters,wp.blockEditor),g=s.MediaUpload,f=s.MediaUploadCheck,b=(wp.plugins.registerPlugin,wp.editPost.PluginDocumentSettingPanel,wp.data),h=b.withSelect,_=b.withDispatch,y=wp.element,w=y.Fragment,v=y.Component,C=wp.components,E=(C.Spinner,C.ResponsiveWrapper),S=C.withNotices,P=(C.withFilters,C.DropZoneProvider,C.DropZone),k=(C.CheckboxControl,C.TextControl,C.ColorIndicator,C.FocalPointPicker,C.BaseControl,C.PanelBody,C.RadioControl,C.RangeControl,C.ColorPalette,C.Button),x=(C.Placeholder,C.ToggleControl,C.SelectControl,wp.compose),O=x.compose,R=(x.withState,["image"]),I=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&c(e,t)}(s,e);var t,o,n,r,a=(t=s,function(){var e,o=d(t);if(u()){var n=d(this).constructor;e=Reflect.construct(o,arguments,n)}else e=o.apply(this,arguments);return p(this,e)});function s(){return l(this,s),a.apply(this,arguments)}return o=s,(n=[{key:"render",value:function(){var e=this.props,t=e.onUpdateImage,o=e.onDropImage,n=e.onRemoveImage,r=e.media,a=e.noticeUI,l=e.mediaPopupLabel,i=e.dropzoneLabel,c=e.removeImageLabel,p=e.replaceImageLabel,u=l||m("Custom image","citadela-pro"),d=i||m("Set custom image","citadela-pro"),s=c||m("Remove image","citadela-pro"),b=p||m("Replace image","citadela-pro"),h=!(!r||!r.id);return wp.element.createElement(w,null,a,wp.element.createElement("div",{className:"editor-post-featured-image"},wp.element.createElement(f,{fallback:m("To edit the image, you need permission to upload media.","citadela-pro")},wp.element.createElement(g,{title:u,onSelect:t,allowedTypes:R,modalClass:"editor-post-featured-image__media-modal",render:function(e){var t=e.open;return wp.element.createElement("div",{className:"editor-post-featured-image__container"},wp.element.createElement(k,{className:h?"editor-post-featured-image__preview":"editor-post-featured-image__toggle",onClick:t,"aria-label":h?m("Edit or update the image","citadela-pro"):null},h&&wp.element.createElement(E,{naturalWidth:r.size.width,naturalHeight:r.size.height,isInline:!0},wp.element.createElement("img",{src:r.url,alt:""})),!1,!h&&d),wp.element.createElement(P,{onFilesDrop:o}))},value:r&&r.id?r.id:null})),h&&wp.element.createElement(f,null,wp.element.createElement(g,{title:u,onSelect:t,allowedTypes:R,modalClass:"editor-post-featured-image__media-modal",render:function(e){var t=e.open;return wp.element.createElement(k,{onClick:t,isSecondary:!0},b)}})),h&&wp.element.createElement(f,null,wp.element.createElement(k,{onClick:n,isLink:!0,isDestructive:!0},s))))}}])&&i(o.prototype,n),r&&i(o,r),s}(v),j=O(S,h((function(e,t){var o=e("core/editor").getEditedPostAttribute,n=t.meta,r=o("meta")[n];return{media:r||null}})),_((function(e,t,o){var r=t.noticeOperations,a=t.onChange,l=(t.state,o.select);e("core/editor").editPost;return{onUpdateImage:function(e){var t={id:e.id,url:e.url,size:{width:e.width,height:e.height}};a(t)},onDropImage:function(e){l("core/block-editor").getSettings().mediaUpload({allowedTypes:["image"],filesList:e,onFileChange:function(e){var t=n(e,1)[0];if(void 0!==t.id){var o={id:t.id,url:t.url,size:{width:t.media_details.width,height:t.media_details.height}};a(o)}},onError:function(e){r.removeAllNotices(),r.createErrorNotice(e)}})},onRemoveImage:function(){a(null)}}})))(I);function z(e){return(z="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function T(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function D(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function F(e,t){return(F=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function B(e,t){return!t||"object"!==z(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function N(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function A(e){return(A=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var L=wp.i18n,U=L.__,M=L._x,H=(wp.hooks.applyFilters,wp.element),Z=(H.Fragment,H.Component),V=wp.components,W=V.ColorPicker,$=(V.ResponsiveWrapper,V.withNotices,V.withFilters,V.DropZoneProvider,V.DropZone,V.CheckboxControl),q=(V.TextControl,V.ColorIndicator),G=V.FocalPointPicker,J=V.BaseControl,K=(V.PanelBody,V.RadioControl,V.RangeControl,V.ColorPalette,V.Button),Q=(V.Placeholder,V.ToggleControl,V.SelectControl),X=wp.compose,Y=(X.compose,X.withState,function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&F(e,t)}(l,e);var t,o,n,r,a=(t=l,function(){var e,o=A(t);if(N()){var n=A(this).constructor;e=Reflect.construct(o,arguments,n)}else e=o.apply(this,arguments);return B(this,e)});function l(){return T(this,l),a.apply(this,arguments)}return o=l,(n=[{key:"render",value:function(){var e=this,t=this.props,o=t.meta,n=t.image,r=t.color,a=t.repeat,l=t.size,i=t.position,c=t.fixed,p=t.overlayColor,u=(t.colorsSet,t.supportOverlay),d=void 0!==u&&u;return wp.element.createElement(React.Fragment,null,wp.element.createElement("div",{className:"citadela-background-component"},wp.element.createElement(J,{label:U("Background image","citadela-pro")},wp.element.createElement(j,{media:n,meta:"".concat(o,"_image"),onChange:function(t){e.props.onChange(t,"image")},mediaPopupLabel:U("Background image","citadela-pro"),dropzoneLabel:U("Set background image","citadela-pro")})),n&&0!=n.length&&wp.element.createElement(React.Fragment,null,wp.element.createElement(J,{label:U("Image size","citadela-pro")},wp.element.createElement(Q,{value:l,options:[{label:M("Cover",'label for css property "cover": image in background cover entire place',"citadela-pro"),value:"cover"},{label:U("100% horizontal","citadela-pro"),value:"full-horizontal"},{label:U("100% vertical","citadela-pro"),value:"full-vertical"},{label:M("Default size","default size of image in background","citadela-pro"),value:"auto"}],onChange:function(t){e.props.onChange(t,"size")}})),"cover"!==l&&wp.element.createElement(J,null,wp.element.createElement(Q,{label:U("Image repeat","citadela-pro"),value:a,options:[{label:M("No repeat",'label for css property "no-repeat": do not repeat image in background',"citadela-pro"),value:"no-repeat"},{label:M("Repeat",'label for css property "repeat": repeat image in background',"citadela-pro"),value:"repeat"},{label:M("Repeat vertically",'label for css property "repeat-y": repeat vertically image in background',"citadela-pro"),value:"repeat-y"},{label:M("Repeat horizontally",'label for css property "repeat-x": repeat horizontally image in background',"citadela-pro"),value:"repeat-x"}],onChange:function(t){e.props.onChange(t,"repeat")}})),wp.element.createElement(J,null,wp.element.createElement($,{label:U("Fixed image","citadela-pro"),checked:c,onChange:function(t){e.props.onChange(t,"fixed")}})),!c&&wp.element.createElement(J,{label:U("Image position","citadela-pro")},wp.element.createElement(G,{url:n.url,value:i&&0!=i.length?i:{x:"0.5",y:"0.5"},onChange:function(t){e.props.onChange(t,"position")}})),d&&wp.element.createElement(J,{label:U("Image overlay color","citadela-pro"),className:"block-editor-panel-color-settings"},p&&wp.element.createElement(q,{colorValue:p}),wp.element.createElement("div",{class:"reset-button",style:{marginBottom:"3px"}},wp.element.createElement(K,{disabled:void 0===p,isSecondary:!0,isSmall:!0,onClick:function(){e.props.onChange("","image_overlay")}},U("Reset","citadela-pro"))),wp.element.createElement(W,{color:p,onChangeComplete:function(t){e.props.onChange(t,"image_overlay")}}))),wp.element.createElement(J,{label:U("Background color","citadela-pro"),className:"block-editor-panel-color-settings"},r&&wp.element.createElement(q,{colorValue:r}),wp.element.createElement("div",{class:"reset-button",style:{marginBottom:"3px"}},wp.element.createElement(K,{disabled:void 0===r,isSecondary:!0,isSmall:!0,onClick:function(){e.props.onChange("","color")}},U("Reset","citadela-pro"))),wp.element.createElement(W,{color:r,onChangeComplete:function(t){e.props.onChange(t,"color")}}))))}}])&&D(o.prototype,n),r&&D(o,r),l}(Z)),ee=wp.plugins.registerPlugin,te=wp.editPost.PluginDocumentSettingPanel,oe=wp.i18n.__,ne=wp.data,re=ne.withSelect,ae=ne.withDispatch,le=wp.components,ie=le.ColorPicker,ce=le.CheckboxControl,pe=le.ColorIndicator,ue=le.BaseControl,de=le.Button,me=function(e){var t=e.useHeader,o=e.overContent,n=e.textColor,r=e.logoImage,a=e.bgImageOverlay,l=e.bgImage,i=e.bgPosition,c=e.bgFixed,p=e.bgRepeat,u=e.bgSize,d=e.bgColor,m=e.transparentBg;return wp.element.createElement(React.Fragment,null,wp.element.createElement(ue,null,wp.element.createElement(ce,{label:oe("Use custom header","citadela-pro"),checked:t,onChange:function(t){e.onChange(t,"_citadela_header")}})),t&&wp.element.createElement(React.Fragment,null,wp.element.createElement(ue,null,wp.element.createElement(ce,{label:oe("Show header over content","citadela-pro"),help:oe("Note: standard page title will be hidden.","citadela-pro"),checked:o,onChange:function(t){e.onChange(t,"_citadela_header_over_content")}})),wp.element.createElement(ue,{label:oe("Custom logo image","citadela-pro"),help:oe("Logo image in custom header.","citadela-pro")},wp.element.createElement(j,{media:r,meta:"_citadela_header_logo",onChange:function(t){e.onChange(t,"_citadela_header_logo")},mediaPopupLabel:oe("Custom logo","citadela-pro"),dropzoneLabel:oe("Set custom logo image","citadela-pro")})),wp.element.createElement(ue,null,wp.element.createElement(ce,{label:oe("Transparent header","citadela-pro"),help:oe("Show transparent header without additional background settings.","citadela-pro"),checked:m,onChange:function(t){e.onChange(t,"_citadela_header_transparent_bg")}})),!m&&wp.element.createElement(ue,null,wp.element.createElement(Y,{meta:"_citadela_header_bg",image:l,size:u,position:i,repeat:p,fixed:c,color:d,overlayColor:a,onChange:function(t,o){e.onChange(t,"_citadela_header_bg_".concat(o))},supportOverlay:!0})),wp.element.createElement(ue,{label:oe("Text color","citadela-pro"),help:oe("Text color in custom header.","citadela-pro"),className:"block-editor-panel-color-settings"},n&&wp.element.createElement(pe,{colorValue:n}),wp.element.createElement("div",{class:"reset-button",style:{marginBottom:"3px"}},wp.element.createElement(de,{disabled:void 0===n,isSecondary:!0,isSmall:!0,onClick:function(){e.onChange("","_citadela_header_text_color")}},oe("Reset","citadela-pro"))),wp.element.createElement(ie,{color:n,onChangeComplete:function(t){e.onChange(t,"_citadela_header_text_color")},disableAlpha:!0}))))};me=re((function(e){var t=wp.data.select("core/editor").getEditedPostAttribute;return{useHeader:"1"==t("meta")._citadela_header,textColor:t("meta")._citadela_header_text_color,logoImage:t("meta")._citadela_header_logo,overContent:"1"==t("meta")._citadela_header_over_content,bgImage:t("meta")._citadela_header_bg_image,bgRepeat:t("meta")._citadela_header_bg_repeat,bgFixed:t("meta")._citadela_header_bg_fixed,bgPosition:t("meta")._citadela_header_bg_position,bgSize:t("meta")._citadela_header_bg_size,bgColor:t("meta")._citadela_header_bg_color,bgImageOverlay:t("meta")._citadela_header_bg_image_overlay,transparentBg:t("meta")._citadela_header_transparent_bg}}))(me),me=ae((function(e){return{onChange:function(t,o){var n={};["_citadela_header_bg_image_overlay","_citadela_header_bg_color","_citadela_header_text_color"].includes(o)?n[o]=t?"rgba(".concat(t.rgb.r,", ").concat(t.rgb.g,", ").concat(t.rgb.b,", ").concat(t.rgb.a,")"):"":n[o]=void 0===t?"":t,e("core/editor").editPost({meta:n})}}}))(me),ee("citadela-header-settings-panel",{render:function(){var e=wp.data.select("core/editor"),t=(e.getCurrentPost,e.getCurrentPostType),o=e.getEditedPostAttribute;if(!["page","special_page","citadela-item"].includes(t()))return null;var n="1"==o("meta")._citadela_ignore_special_page;return"citadela-item"!=t()||n?wp.element.createElement(te,{name:"citadela-header-settings-panel",title:oe("Citadela Header Settings","citadela-pro")},wp.element.createElement(me,null)):null},icon:""})}]);