!function(e){var t={};function r(o){if(t[o])return t[o].exports;var s=t[o]={i:o,l:!1,exports:{}};return e[o].call(s.exports,s,s.exports,r),s.l=!0,s.exports}r.m=e,r.c=t,r.d=function(e,t,o){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(r.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var s in e)r.d(o,s,function(t){return e[t]}.bind(null,s));return o},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=39)}({12:function(e,t,r){function o(e,t){e.getMarkerClusterer().extend(o,google.maps.OverlayView),this.cluster_=e,this.className_=e.getMarkerClusterer().getClusterClass(),this.styles_=t,this.center_=null,this.div_=null,this.sums_=null,this.visible_=!1,this.setMap(e.getMap())}function s(e){this.markerClusterer_=e,this.map_=e.getMap(),this.gridSize_=e.getGridSize(),this.minClusterSize_=e.getMinimumClusterSize(),this.averageCenter_=e.getAverageCenter(),this.markers_=[],this.center_=null,this.bounds_=null,this.clusterIcon_=new o(this,e.getStyles())}function i(e,t,r){this.extend(i,google.maps.OverlayView),t=t||[],r=r||{},this.markers_=[],this.clusters_=[],this.listeners_=[],this.activeMap_=null,this.ready_=!1,this.gridSize_=r.gridSize||60,this.minClusterSize_=r.minimumClusterSize||2,this.maxZoom_=r.maxZoom||null,this.styles_=r.styles||[],this.title_=r.title||"",this.zoomOnClick_=!0,void 0!==r.zoomOnClick&&(this.zoomOnClick_=r.zoomOnClick),this.averageCenter_=!1,void 0!==r.averageCenter&&(this.averageCenter_=r.averageCenter),this.ignoreHidden_=!1,void 0!==r.ignoreHidden&&(this.ignoreHidden_=r.ignoreHidden),this.enableRetinaIcons_=!1,void 0!==r.enableRetinaIcons&&(this.enableRetinaIcons_=r.enableRetinaIcons),this.imagePath_=r.imagePath||i.IMAGE_PATH,this.imageExtension_=r.imageExtension||i.IMAGE_EXTENSION,this.imageSizes_=r.imageSizes||i.IMAGE_SIZES,this.calculator_=r.calculator||i.CALCULATOR,this.batchSize_=r.batchSize||i.BATCH_SIZE,this.batchSizeIE_=r.batchSizeIE||i.BATCH_SIZE_IE,this.clusterClass_=r.clusterClass||"cluster",-1!==navigator.userAgent.toLowerCase().indexOf("msie")&&(this.batchSize_=this.batchSizeIE_),this.setupStyles_(),this.addMarkers(t,!0),this.setMap(e)}o.prototype.onAdd=function(){var e,t,r=this,o=google.maps.version.split(".");o=parseInt(100*o[0],10)+parseInt(o[1],10),this.div_=document.createElement("div"),this.div_.className=this.className_,this.visible_&&this.show(),this.getPanes().overlayMouseTarget.appendChild(this.div_),this.boundsChangedListener_=google.maps.event.addListener(this.getMap(),"bounds_changed",(function(){t=e})),google.maps.event.addDomListener(this.div_,"mousedown",(function(){e=!0,t=!1})),o>=332&&google.maps.event.addDomListener(this.div_,"touchstart",(function(e){e.stopPropagation()})),google.maps.event.addDomListener(this.div_,"click",(function(o){if(e=!1,!t){var s,i,n=r.cluster_.getMarkerClusterer();google.maps.event.trigger(n,"click",r.cluster_),google.maps.event.trigger(n,"clusterclick",r.cluster_),n.getZoomOnClick()&&(i=n.getMaxZoom(),s=r.cluster_.getBounds(),n.getMap().fitBounds(s),setTimeout((function(){n.getMap().fitBounds(s),null!==i&&n.getMap().getZoom()>i&&n.getMap().setZoom(i+1)}),100)),o.cancelBubble=!0,o.stopPropagation&&o.stopPropagation()}})),google.maps.event.addDomListener(this.div_,"mouseover",(function(){var e=r.cluster_.getMarkerClusterer();google.maps.event.trigger(e,"mouseover",r.cluster_)})),google.maps.event.addDomListener(this.div_,"mouseout",(function(){var e=r.cluster_.getMarkerClusterer();google.maps.event.trigger(e,"mouseout",r.cluster_)}))},o.prototype.onRemove=function(){this.div_&&this.div_.parentNode&&(this.hide(),google.maps.event.removeListener(this.boundsChangedListener_),google.maps.event.clearInstanceListeners(this.div_),this.div_.parentNode.removeChild(this.div_),this.div_=null)},o.prototype.draw=function(){if(this.visible_){var e=this.getPosFromLatLng_(this.center_);this.div_.style.top=e.y+"px",this.div_.style.left=e.x+"px",this.div_.style.zIndex=google.maps.Marker.MAX_ZINDEX+1}},o.prototype.hide=function(){this.div_&&(this.div_.style.display="none"),this.visible_=!1},o.prototype.show=function(){if(this.div_){var e="",t=this.backgroundPosition_.split(" "),r=parseInt(t[0].replace(/^\s+|\s+$/g,""),10),o=parseInt(t[1].replace(/^\s+|\s+$/g,""),10),s=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(s),e="<img src='"+this.url_+"' style='position: absolute; top: "+o+"px; left: "+r+"px; ",this.cluster_.getMarkerClusterer().enableRetinaIcons_?e+="width: "+this.width_+"px; height: "+this.height_+"px;":e+="clip: rect("+-1*o+"px, "+(-1*r+this.width_)+"px, "+(-1*o+this.height_)+"px, "+-1*r+"px);",e+="'>",this.div_.innerHTML=e+"<div style='position: absolute;top: "+this.anchorText_[0]+"px;left: "+this.anchorText_[1]+"px;color: "+this.textColor_+";font-size: "+this.textSize_+"px;font-family: "+this.fontFamily_+";font-weight: "+this.fontWeight_+";font-style: "+this.fontStyle_+";text-decoration: "+this.textDecoration_+";text-align: center;width: "+this.width_+"px;line-height:"+this.height_+"px;'>"+this.sums_.text+"</div>",void 0===this.sums_.title||""===this.sums_.title?this.div_.title=this.cluster_.getMarkerClusterer().getTitle():this.div_.title=this.sums_.title,this.div_.style.display=""}this.visible_=!0},o.prototype.useStyle=function(e){this.sums_=e;var t=Math.max(0,e.index-1);t=Math.min(this.styles_.length-1,t);var r=this.styles_[t];this.url_=r.url,this.height_=r.height,this.width_=r.width,this.anchorText_=r.anchorText||[0,0],this.anchorIcon_=r.anchorIcon||[parseInt(this.height_/2,10),parseInt(this.width_/2,10)],this.textColor_=r.textColor||"black",this.textSize_=r.textSize||11,this.textDecoration_=r.textDecoration||"none",this.fontWeight_=r.fontWeight||"bold",this.fontStyle_=r.fontStyle||"normal",this.fontFamily_=r.fontFamily||"Arial,sans-serif",this.backgroundPosition_=r.backgroundPosition||"0 0"},o.prototype.setCenter=function(e){this.center_=e},o.prototype.createCss=function(e){var t=[];return t.push("cursor: pointer;"),t.push("position: absolute; top: "+e.y+"px; left: "+e.x+"px;"),t.push("width: "+this.width_+"px; height: "+this.height_+"px;"),t.push("-webkit-user-select: none;"),t.push("-khtml-user-select: none;"),t.push("-moz-user-select: none;"),t.push("-o-user-select: none;"),t.push("user-select: none;"),t.join("")},o.prototype.getPosFromLatLng_=function(e){var t=this.getProjection().fromLatLngToDivPixel(e);return t.x-=this.anchorIcon_[1],t.y-=this.anchorIcon_[0],t.x=parseInt(t.x,10),t.y=parseInt(t.y,10),t},s.prototype.getSize=function(){return this.markers_.length},s.prototype.getMarkers=function(){return this.markers_},s.prototype.getCenter=function(){return this.center_},s.prototype.getMap=function(){return this.map_},s.prototype.getMarkerClusterer=function(){return this.markerClusterer_},s.prototype.getBounds=function(){var e,t=new google.maps.LatLngBounds(this.center_,this.center_),r=this.getMarkers();for(e=0;e<r.length;e++)t.extend(r[e].getPosition());return t},s.prototype.remove=function(){this.clusterIcon_.setMap(null),this.markers_=[],delete this.markers_},s.prototype.addMarker=function(e){var t,r,o;if(this.isMarkerAlreadyAdded_(e))return!1;if(this.center_){if(this.averageCenter_){var s=this.markers_.length+1,i=(this.center_.lat()*(s-1)+e.getPosition().lat())/s,n=(this.center_.lng()*(s-1)+e.getPosition().lng())/s;this.center_=new google.maps.LatLng(i,n),this.calculateBounds_()}}else this.center_=e.getPosition(),this.calculateBounds_();if(e.isAdded=!0,this.markers_.push(e),r=this.markers_.length,null!==(o=this.markerClusterer_.getMaxZoom())&&this.map_.getZoom()>o)e.getMap()!==this.map_&&e.setMap(this.map_);else if(r<this.minClusterSize_)e.getMap()!==this.map_&&e.setMap(this.map_);else if(r===this.minClusterSize_)for(t=0;t<r;t++)this.markers_[t].setMap(null);else e.setMap(null);return this.updateIcon_(),!0},s.prototype.isMarkerInClusterBounds=function(e){return this.bounds_.contains(e.getPosition())},s.prototype.calculateBounds_=function(){var e=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(e)},s.prototype.updateIcon_=function(){var e=this.markers_.length,t=this.markerClusterer_.getMaxZoom();if(null!==t&&this.map_.getZoom()>t)this.clusterIcon_.hide();else if(e<this.minClusterSize_)this.clusterIcon_.hide();else{var r=this.markerClusterer_.getStyles().length,o=this.markerClusterer_.getCalculator()(this.markers_,r);this.clusterIcon_.setCenter(this.center_),this.clusterIcon_.useStyle(o),this.clusterIcon_.show()}},s.prototype.isMarkerAlreadyAdded_=function(e){var t;if(this.markers_.indexOf)return-1!==this.markers_.indexOf(e);for(t=0;t<this.markers_.length;t++)if(e===this.markers_[t])return!0;return!1},i.prototype.onAdd=function(){var e=this;this.activeMap_=this.getMap(),this.ready_=!0,this.repaint(),this.prevZoom_=this.getMap().getZoom(),this.listeners_=[google.maps.event.addListener(this.getMap(),"zoom_changed",function(){var e=this.getMap().getZoom(),t=this.getMap().minZoom||0,r=Math.min(this.getMap().maxZoom||100,this.getMap().mapTypes[this.getMap().getMapTypeId()].maxZoom);e=Math.min(Math.max(e,t),r),this.prevZoom_!=e&&(this.prevZoom_=e,this.resetViewport_(!1))}.bind(this)),google.maps.event.addListener(this.getMap(),"idle",(function(){e.redraw_()}))]},i.prototype.onRemove=function(){var e;for(e=0;e<this.markers_.length;e++)this.markers_[e].getMap()!==this.activeMap_&&this.markers_[e].setMap(this.activeMap_);for(e=0;e<this.clusters_.length;e++)this.clusters_[e].remove();for(this.clusters_=[],e=0;e<this.listeners_.length;e++)google.maps.event.removeListener(this.listeners_[e]);this.listeners_=[],this.activeMap_=null,this.ready_=!1},i.prototype.draw=function(){},i.prototype.setupStyles_=function(){var e,t;if(!(this.styles_.length>0))for(e=0;e<this.imageSizes_.length;e++)t=this.imageSizes_[e],this.styles_.push({url:this.imagePath_+(e+1)+"."+this.imageExtension_,height:t,width:t})},i.prototype.fitMapToMarkers=function(){var e,t=this.getMarkers(),r=new google.maps.LatLngBounds;for(e=0;e<t.length;e++)!t[e].getVisible()&&this.getIgnoreHidden()||r.extend(t[e].getPosition());this.getMap().fitBounds(r)},i.prototype.getGridSize=function(){return this.gridSize_},i.prototype.setGridSize=function(e){this.gridSize_=e},i.prototype.getMinimumClusterSize=function(){return this.minClusterSize_},i.prototype.setMinimumClusterSize=function(e){this.minClusterSize_=e},i.prototype.getMaxZoom=function(){return this.maxZoom_},i.prototype.setMaxZoom=function(e){this.maxZoom_=e},i.prototype.getStyles=function(){return this.styles_},i.prototype.setStyles=function(e){this.styles_=e},i.prototype.getTitle=function(){return this.title_},i.prototype.setTitle=function(e){this.title_=e},i.prototype.getZoomOnClick=function(){return this.zoomOnClick_},i.prototype.setZoomOnClick=function(e){this.zoomOnClick_=e},i.prototype.getAverageCenter=function(){return this.averageCenter_},i.prototype.setAverageCenter=function(e){this.averageCenter_=e},i.prototype.getIgnoreHidden=function(){return this.ignoreHidden_},i.prototype.setIgnoreHidden=function(e){this.ignoreHidden_=e},i.prototype.getEnableRetinaIcons=function(){return this.enableRetinaIcons_},i.prototype.setEnableRetinaIcons=function(e){this.enableRetinaIcons_=e},i.prototype.getImageExtension=function(){return this.imageExtension_},i.prototype.setImageExtension=function(e){this.imageExtension_=e},i.prototype.getImagePath=function(){return this.imagePath_},i.prototype.setImagePath=function(e){this.imagePath_=e},i.prototype.getImageSizes=function(){return this.imageSizes_},i.prototype.setImageSizes=function(e){this.imageSizes_=e},i.prototype.getCalculator=function(){return this.calculator_},i.prototype.setCalculator=function(e){this.calculator_=e},i.prototype.getBatchSizeIE=function(){return this.batchSizeIE_},i.prototype.setBatchSizeIE=function(e){this.batchSizeIE_=e},i.prototype.getClusterClass=function(){return this.clusterClass_},i.prototype.setClusterClass=function(e){this.clusterClass_=e},i.prototype.getMarkers=function(){return this.markers_},i.prototype.getTotalMarkers=function(){return this.markers_.length},i.prototype.getClusters=function(){return this.clusters_},i.prototype.getTotalClusters=function(){return this.clusters_.length},i.prototype.addMarker=function(e,t){this.pushMarkerTo_(e),t||this.redraw_()},i.prototype.addMarkers=function(e,t){var r;for(r in e)e.hasOwnProperty(r)&&this.pushMarkerTo_(e[r]);t||this.redraw_()},i.prototype.pushMarkerTo_=function(e){if(e.getDraggable()){var t=this;google.maps.event.addListener(e,"dragend",(function(){t.ready_&&(this.isAdded=!1,t.repaint())}))}e.isAdded=!1,this.markers_.push(e)},i.prototype.removeMarker=function(e,t){var r=this.removeMarker_(e);return!t&&r&&this.repaint(),r},i.prototype.removeMarkers=function(e,t){var r,o,s=!1;for(r=0;r<e.length;r++)o=this.removeMarker_(e[r]),s=s||o;return!t&&s&&this.repaint(),s},i.prototype.removeMarker_=function(e){var t,r=-1;if(this.markers_.indexOf)r=this.markers_.indexOf(e);else for(t=0;t<this.markers_.length;t++)if(e===this.markers_[t]){r=t;break}return-1!==r&&(e.setMap(null),this.markers_.splice(r,1),!0)},i.prototype.clearMarkers=function(){this.resetViewport_(!0),this.markers_=[]},i.prototype.repaint=function(){var e=this.clusters_.slice();this.clusters_=[],this.resetViewport_(!1),this.redraw_(),setTimeout((function(){var t;for(t=0;t<e.length;t++)e[t].remove()}),0)},i.prototype.getExtendedBounds=function(e){var t=this.getProjection(),r=new google.maps.LatLng(e.getNorthEast().lat(),e.getNorthEast().lng()),o=new google.maps.LatLng(e.getSouthWest().lat(),e.getSouthWest().lng()),s=t.fromLatLngToDivPixel(r);s.x+=this.gridSize_,s.y-=this.gridSize_;var i=t.fromLatLngToDivPixel(o);i.x-=this.gridSize_,i.y+=this.gridSize_;var n=t.fromDivPixelToLatLng(s),a=t.fromDivPixelToLatLng(i);return e.extend(n),e.extend(a),e},i.prototype.redraw_=function(){this.createClusters_(0)},i.prototype.resetViewport_=function(e){var t,r;for(t=0;t<this.clusters_.length;t++)this.clusters_[t].remove();for(this.clusters_=[],t=0;t<this.markers_.length;t++)(r=this.markers_[t]).isAdded=!1,e&&r.setMap(null)},i.prototype.distanceBetweenPoints_=function(e,t){var r=(t.lat()-e.lat())*Math.PI/180,o=(t.lng()-e.lng())*Math.PI/180,s=Math.sin(r/2)*Math.sin(r/2)+Math.cos(e.lat()*Math.PI/180)*Math.cos(t.lat()*Math.PI/180)*Math.sin(o/2)*Math.sin(o/2);return 6371*(2*Math.atan2(Math.sqrt(s),Math.sqrt(1-s)))},i.prototype.isMarkerInBounds_=function(e,t){return t.contains(e.getPosition())},i.prototype.addToClosestCluster_=function(e){var t,r,o,i,n=4e4,a=null;for(t=0;t<this.clusters_.length;t++)(i=(o=this.clusters_[t]).getCenter())&&(r=this.distanceBetweenPoints_(i,e.getPosition()))<n&&(n=r,a=o);a&&a.isMarkerInClusterBounds(e)?a.addMarker(e):((o=new s(this)).addMarker(e),this.clusters_.push(o))},i.prototype.createClusters_=function(e){var t,r,o,s=this;if(this.ready_){0===e&&(google.maps.event.trigger(this,"clusteringbegin",this),void 0!==this.timerRefStatic&&(clearTimeout(this.timerRefStatic),delete this.timerRefStatic)),o=this.getMap().getZoom()>3?new google.maps.LatLngBounds(this.getMap().getBounds().getSouthWest(),this.getMap().getBounds().getNorthEast()):new google.maps.LatLngBounds(new google.maps.LatLng(85.02070771743472,-178.48388434375),new google.maps.LatLng(-85.08136444384544,178.00048865625));var i=this.getExtendedBounds(o),n=Math.min(e+this.batchSize_,this.markers_.length);for(t=e;t<n;t++)!(r=this.markers_[t]).isAdded&&this.isMarkerInBounds_(r,i)&&(!this.ignoreHidden_||this.ignoreHidden_&&r.getVisible())&&this.addToClosestCluster_(r);n<this.markers_.length?this.timerRefStatic=setTimeout((function(){s.createClusters_(n)}),0):(delete this.timerRefStatic,google.maps.event.trigger(this,"clusteringend",this))}},i.prototype.extend=function(e,t){return function(e){var t;for(t in e.prototype)this.prototype[t]=e.prototype[t];return this}.apply(e,[t])},i.CALCULATOR=function(e,t){for(var r=0,o=e.length.toString(),s=o;0!==s;)s=parseInt(s/10,10),r++;return{text:o,index:r=Math.min(r,t),title:""}},i.BATCH_SIZE=2e3,i.BATCH_SIZE_IE=500,i.IMAGE_PATH="https://cdn.rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m",i.IMAGE_EXTENSION="png",i.IMAGE_SIZES=[53,56,66,78,90],e.exports=i},2:function(e,t){!function(){e.exports=this.wp.element}()},39:function(e,t,r){"use strict";r.r(t);var o=r(2);function s(e){return(s="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function n(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}function a(e,t){return(a=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function l(e,t){return!t||"object"!==s(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function p(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function c(e){return(c=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var u=wp.element,y=u.Component,h=u.Fragment,m=u.createPortal,f=wp.i18n,d=f.__,g=(f.setLocaleData,function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&a(e,t)}(y,e);var t,r,o,s,u=(t=y,function(){var e,r=c(t);if(p()){var o=c(this).constructor;e=Reflect.construct(r,arguments,o)}else e=r.apply(this,arguments);return l(this,e)});function y(){return i(this,y),u.apply(this,arguments)}return r=y,(o=[{key:"componentDidMount",value:function(){this.el=document.createElement("DIV"),this.infowindow=new google.maps.InfoWindow({content:this.el})}},{key:"componentDidUpdate",value:function(e){this.props.activeMarker&&(this.props.activeMarker?this.openInfowindow():this.closeInfowindow())}},{key:"render",value:function(){var e=this.props.activeMarker;if(this.el&&e){var t=e.point,r=t.title,o=t.image,s=t.permalink,i=t.address,n=t.postType;return m(wp.element.createElement(h,null,wp.element.createElement("div",{className:"infoBox "+n},wp.element.createElement("div",{class:"infobox-content"},wp.element.createElement("div",{class:"item-data"},wp.element.createElement("div",{class:"infobox-title"},r),i&&wp.element.createElement("p",null,i),wp.element.createElement("a",{class:"item-more-button",href:s},wp.element.createElement("span",{class:"item-button"},d("post"==n?"Read more":"Show more","citadela-directory")))),o&&wp.element.createElement("div",{class:"item-picture"},wp.element.createElement("img",{src:o}))))),this.el)}return null}},{key:"openInfowindow",value:function(){var e=this.props,t=e.map,r=e.activeMarker;this.infowindow.open(t,r.marker)}},{key:"closeInfowindow",value:function(){this.infowindow.remove()}}])&&n(r.prototype,o),s&&n(r,s),y}(y)),_=r(12),v=[{json:[{stylers:[{hue:""},{saturation:"20"},{lightness:"0"}]},{featureType:"landscape",stylers:[{visibility:"on"},{hue:""},{saturation:""},{lightness:""}]},{featureType:"administrative",stylers:[{visibility:"on"},{hue:""},{saturation:""},{lightness:""}]},{featureType:"road",stylers:[{visibility:"on"},{hue:""},{saturation:""},{lightness:""}]},{featureType:"water",stylers:[{visibility:"on"},{hue:"#c6e8eb"},{saturation:"-70"},{lightness:""}]},{featureType:"poi",stylers:[{visibility:"on"},{hue:""},{saturation:""},{lightness:""}]}],name:"Citadela",codeName:"citadela"},{json:[],name:"Standard",codeName:"standard"},{json:[{elementType:"geometry",stylers:[{color:"#f5f5f5"}]},{elementType:"labels.icon",stylers:[{visibility:"off"}]},{elementType:"labels.text.fill",stylers:[{color:"#616161"}]},{elementType:"labels.text.stroke",stylers:[{color:"#f5f5f5"}]},{featureType:"administrative.land_parcel",elementType:"labels.text.fill",stylers:[{color:"#bdbdbd"}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#eeeeee"}]},{featureType:"poi",elementType:"labels.text.fill",stylers:[{color:"#757575"}]},{featureType:"poi.park",elementType:"geometry",stylers:[{color:"#e5e5e5"}]},{featureType:"poi.park",elementType:"labels.text.fill",stylers:[{color:"#9e9e9e"}]},{featureType:"road",elementType:"geometry",stylers:[{color:"#ffffff"}]},{featureType:"road.arterial",elementType:"labels.text.fill",stylers:[{color:"#757575"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#dadada"}]},{featureType:"road.highway",elementType:"labels.text.fill",stylers:[{color:"#616161"}]},{featureType:"road.local",elementType:"labels.text.fill",stylers:[{color:"#9e9e9e"}]},{featureType:"transit.line",elementType:"geometry",stylers:[{color:"#e5e5e5"}]},{featureType:"transit.station",elementType:"geometry",stylers:[{color:"#eeeeee"}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#c9c9c9"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{color:"#9e9e9e"}]}],name:"Silver",codeName:"silver"},{json:[{elementType:"geometry",stylers:[{color:"#ebe3cd"}]},{elementType:"labels.text.fill",stylers:[{color:"#523735"}]},{elementType:"labels.text.stroke",stylers:[{color:"#f5f1e6"}]},{featureType:"administrative",elementType:"geometry.stroke",stylers:[{color:"#c9b2a6"}]},{featureType:"administrative.land_parcel",elementType:"geometry.stroke",stylers:[{color:"#dcd2be"}]},{featureType:"administrative.land_parcel",elementType:"labels.text.fill",stylers:[{color:"#ae9e90"}]},{featureType:"landscape.natural",elementType:"geometry",stylers:[{color:"#dfd2ae"}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#dfd2ae"}]},{featureType:"poi",elementType:"labels.text.fill",stylers:[{color:"#93817c"}]},{featureType:"poi.park",elementType:"geometry.fill",stylers:[{color:"#a5b076"}]},{featureType:"poi.park",elementType:"labels.text.fill",stylers:[{color:"#447530"}]},{featureType:"road",elementType:"geometry",stylers:[{color:"#f5f1e6"}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#fdfcf8"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#f8c967"}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#e9bc62"}]},{featureType:"road.highway.controlled_access",elementType:"geometry",stylers:[{color:"#e98d58"}]},{featureType:"road.highway.controlled_access",elementType:"geometry.stroke",stylers:[{color:"#db8555"}]},{featureType:"road.local",elementType:"labels.text.fill",stylers:[{color:"#806b63"}]},{featureType:"transit.line",elementType:"geometry",stylers:[{color:"#dfd2ae"}]},{featureType:"transit.line",elementType:"labels.text.fill",stylers:[{color:"#8f7d77"}]},{featureType:"transit.line",elementType:"labels.text.stroke",stylers:[{color:"#ebe3cd"}]},{featureType:"transit.station",elementType:"geometry",stylers:[{color:"#dfd2ae"}]},{featureType:"water",elementType:"geometry.fill",stylers:[{color:"#b9d3c2"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{color:"#92998d"}]}],name:"Retro",codeName:"retro"},{json:[{elementType:"geometry",stylers:[{color:"#212121"}]},{elementType:"labels.icon",stylers:[{visibility:"off"}]},{elementType:"labels.text.fill",stylers:[{color:"#757575"}]},{elementType:"labels.text.stroke",stylers:[{color:"#212121"}]},{featureType:"administrative",elementType:"geometry",stylers:[{color:"#757575"}]},{featureType:"administrative.country",elementType:"labels.text.fill",stylers:[{color:"#9e9e9e"}]},{featureType:"administrative.land_parcel",stylers:[{visibility:"off"}]},{featureType:"administrative.locality",elementType:"labels.text.fill",stylers:[{color:"#bdbdbd"}]},{featureType:"poi",elementType:"labels.text.fill",stylers:[{color:"#757575"}]},{featureType:"poi.park",elementType:"geometry",stylers:[{color:"#181818"}]},{featureType:"poi.park",elementType:"labels.text.fill",stylers:[{color:"#616161"}]},{featureType:"poi.park",elementType:"labels.text.stroke",stylers:[{color:"#1b1b1b"}]},{featureType:"road",elementType:"geometry.fill",stylers:[{color:"#2c2c2c"}]},{featureType:"road",elementType:"labels.text.fill",stylers:[{color:"#8a8a8a"}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#373737"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#3c3c3c"}]},{featureType:"road.highway.controlled_access",elementType:"geometry",stylers:[{color:"#4e4e4e"}]},{featureType:"road.local",elementType:"labels.text.fill",stylers:[{color:"#616161"}]},{featureType:"transit",elementType:"labels.text.fill",stylers:[{color:"#757575"}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#000000"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{color:"#3d3d3d"}]}],name:"Dark",codeName:"dark"},{json:[{elementType:"geometry",stylers:[{color:"#242f3e"}]},{elementType:"labels.text.fill",stylers:[{color:"#746855"}]},{elementType:"labels.text.stroke",stylers:[{color:"#242f3e"}]},{featureType:"administrative.locality",elementType:"labels.text.fill",stylers:[{color:"#d59563"}]},{featureType:"poi",elementType:"labels.text.fill",stylers:[{color:"#d59563"}]},{featureType:"poi.park",elementType:"geometry",stylers:[{color:"#263c3f"}]},{featureType:"poi.park",elementType:"labels.text.fill",stylers:[{color:"#6b9a76"}]},{featureType:"road",elementType:"geometry",stylers:[{color:"#38414e"}]},{featureType:"road",elementType:"geometry.stroke",stylers:[{color:"#212a37"}]},{featureType:"road",elementType:"labels.text.fill",stylers:[{color:"#9ca5b3"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#746855"}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#1f2835"}]},{featureType:"road.highway",elementType:"labels.text.fill",stylers:[{color:"#f3d19c"}]},{featureType:"transit",elementType:"geometry",stylers:[{color:"#2f3948"}]},{featureType:"transit.station",elementType:"labels.text.fill",stylers:[{color:"#d59563"}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#17263c"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{color:"#515c6d"}]},{featureType:"water",elementType:"labels.text.stroke",stylers:[{color:"#17263c"}]}],name:"Night",codeName:"night"},{json:[{elementType:"geometry",stylers:[{color:"#1d2c4d"}]},{elementType:"labels.text.fill",stylers:[{color:"#8ec3b9"}]},{elementType:"labels.text.stroke",stylers:[{color:"#1a3646"}]},{featureType:"administrative.country",elementType:"geometry.stroke",stylers:[{color:"#4b6878"}]},{featureType:"administrative.land_parcel",elementType:"labels.text.fill",stylers:[{color:"#64779e"}]},{featureType:"administrative.province",elementType:"geometry.stroke",stylers:[{color:"#4b6878"}]},{featureType:"landscape.man_made",elementType:"geometry.stroke",stylers:[{color:"#334e87"}]},{featureType:"landscape.natural",elementType:"geometry",stylers:[{color:"#023e58"}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#283d6a"}]},{featureType:"poi",elementType:"labels.text.fill",stylers:[{color:"#6f9ba5"}]},{featureType:"poi",elementType:"labels.text.stroke",stylers:[{color:"#1d2c4d"}]},{featureType:"poi.park",elementType:"geometry.fill",stylers:[{color:"#023e58"}]},{featureType:"poi.park",elementType:"labels.text.fill",stylers:[{color:"#3C7680"}]},{featureType:"road",elementType:"geometry",stylers:[{color:"#304a7d"}]},{featureType:"road",elementType:"labels.text.fill",stylers:[{color:"#98a5be"}]},{featureType:"road",elementType:"labels.text.stroke",stylers:[{color:"#1d2c4d"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#2c6675"}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#255763"}]},{featureType:"road.highway",elementType:"labels.text.fill",stylers:[{color:"#b0d5ce"}]},{featureType:"road.highway",elementType:"labels.text.stroke",stylers:[{color:"#023e58"}]},{featureType:"transit",elementType:"labels.text.fill",stylers:[{color:"#98a5be"}]},{featureType:"transit",elementType:"labels.text.stroke",stylers:[{color:"#1d2c4d"}]},{featureType:"transit.line",elementType:"geometry.fill",stylers:[{color:"#283d6a"}]},{featureType:"transit.station",elementType:"geometry",stylers:[{color:"#3a4762"}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#0e1626"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{color:"#4e6d70"}]}],name:"Aubergine",codeName:"aubergine"}];function b(e){return(b="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function T(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,o)}return r}function k(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function w(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}function x(e,t){return(x=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function M(e,t){return!t||"object"!==b(t)&&"function"!=typeof t?S(e):t}function S(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function C(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}function A(e){return(A=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function I(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}var L=wp.element,E=L.Component,O=L.createRef,P=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&x(e,t)}(n,e);var t,r,o,s,i=(t=n,function(){var e,r=A(t);if(C()){var o=A(this).constructor;e=Reflect.construct(r,arguments,o)}else e=r.apply(this,arguments);return M(this,e)});function n(){var e;return k(this,n),I(S(e=i.apply(this,arguments)),"fitAndZoom",(function(){var t=e.state,r=t.map,o=t.activeMarker;if(0!=e.markers.length){if(!o){var s=new google.maps.LatLngBounds;if(e.markers.forEach((function(e){s.extend(e.position)})),1==e.markers.length)return r.setCenter(s.getCenter()),r.setZoom(15),void e.updatePanorama();e.markers.length>1&&r.fitBounds(s,{top:40})}}else e.setEmptyMap()})),I(S(e),"onMarkerClick",(function(t,r){e.state.map;var o={marker:t,point:r};e.setState({activeMarker:o})})),e.getPoints=e.getPoints.bind(S(e)),e.checkFormInsideMap=e.checkFormInsideMap.bind(S(e)),e.onScreenResize=e.onScreenResize.bind(S(e)),e.setEmptyMap=e.setEmptyMap.bind(S(e)),e.state={points:[],map:null,panorama:null,activeMarker:null},e.blockNode=null,e.markers=[],e.currentOffset=0,e.markerClusterer=null,e.geolocation=!1,e.mapRef=O(),window.addEventListener("resize",e.onScreenResize),e}return r=n,(o=[{key:"render",value:function(){var e=this.state,t=e.map,r=e.activeMarker,o=this.props,s=o.mapHeight,i=o.noDataBehavior,n=o.noDataText,a=function(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?T(Object(r),!0).forEach((function(t){I(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):T(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}({},s?{height:s}:{}),l=t&&wp.element.createElement(g,{activeMarker:r,map:t});return wp.element.createElement(React.Fragment,null,wp.element.createElement("div",{class:"map-container no-markers",ref:this.mapRef,style:a}),"empty-map"==i&&n&&wp.element.createElement("div",{class:"empty-map-cover"},wp.element.createElement("div",{class:"text-wrapper"},n)),l)}},{key:"componentDidMount",value:function(){var e=this.props,t=e.endpoint,r=e.streetview,o=e.theme,s=e.customTheme,i=e.geolocation,n=e.clusterGridSize;this.blockNode=this.mapRef.current.parentNode.parentNode.parentNode,this.geolocation=!!i&&JSON.parse(i);var a=v.find((function(e){return e.codeName==o})),l=new google.maps.Map(this.mapRef.current,{center:{lat:41.8585,lng:5.029},zoom:2,styles:a?a.json:s});if(this.oms=new OverlappingMarkerSpiderfier(l,{keepSpiderfied:"yes",circleFootSeparation:50,nearbyDistance:1}),this.markerClusterer=new _(l,this.markers,{gridSize:parseInt(n),maxZoom:16}),google.maps.event.addListener(l,"idle",function(){this.oms.h.call(this.oms)}.bind(this)),r){var p=l.getStreetView();p.setPosition(l.getCenter()),p.setPov(r),p.setVisible(!0),this.setState({panorama:p})}this.setState({map:l}),this.getPoints(t)}},{key:"onScreenResize",value:function(){this.checkFormInsideMap()}},{key:"checkFormInsideMap",value:function(){window.innerWidth<this.props.outsideFormBreakpoint?this.blockNode.classList.add("outside-search-form"):this.blockNode.classList.remove("outside-search-form")}},{key:"componentDidUpdate",value:function(e){}},{key:"setEmptyMap",value:function(){var e=this.props,t=e.noDataBehavior,r=e.isHalfLayoutMap,o=this.state.map;if("empty-map"==t){var s=new google.maps.LatLng(0,0);o.setCenter(s),o.setZoom(2),this.blockNode.classList.add("empty-map")}if("hidden-map"==t&&(this.blockNode.classList.add("hidden-map"),r)){var i=document.querySelector("body");i.classList.remove("hidden-map"),i.classList.add("page-fullwidth"),window.dispatchEvent(new Event("resize"))}}},{key:"getPoints",value:function(e){var t=this;fetch(e).then((function(e){return e.json()})).then((function(e){var r=[];if(e.points.length>0)e.points.forEach((function(e){t.isValidPointLocation(e.coordinates)&&r.push(t.addMarker(e))})),parseInt(t.props.clusterGridSize)>0&&t.markerClusterer.addMarkers(r),e.total>t.currentOffset+e.points.length?(t.currentOffset+=e.points.length,t.getPoints(t.updateEndpointOffset())):t.geolocation?t.setGeolocation():t.fitAndZoom(),t.blockNode.classList.remove("loading-content");else{if(t.geolocation)return t.setGeolocation(),void t.blockNode.classList.remove("loading-content");t.setEmptyMap(),t.blockNode.classList.remove("loading-content")}}))}},{key:"setGeolocation",value:function(){var e=this.state.map,t=this.geolocation.lat,r=this.geolocation.lon,o=this.geolocation.rad,s=this.geolocation.unit,i=new google.maps.LatLng(t,r);new MarkerWithLabel({position:i,map:e,labelContent:'<div class="fa-map-label-marker geolocation-marker"></div><i class="fas fa-street-view"></i>',labelClass:"fa-map-label",icon:{url:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",anchor:{x:0,y:70},labelInBackground:!1}}),e.setCenter(i),e.setZoom(Math.round(14-Math.log("mi"==s?1.609*o:1*o)/Math.LN2));var n={strokeColor:"#005BB7",strokeOpacity:.8,strokeWeight:2,fillColor:"#008BB2",fillOpacity:.35,map:e,center:i,radius:"mi"==s?1609*o:1e3*o};new google.maps.Circle(n)}},{key:"updatePanorama",value:function(){var e=this.state,t=e.map,r=e.panorama;r&&r.setPosition(t.getCenter())}},{key:"isValidPointLocation",value:function(e){return!isNaN(e.latitude)&&!isNaN(e.longitude)&&(0!=e.latitude||0!=e.longitude)&&e.latitude>-90&&e.latitude<90&&e.longitude>-180&&e.longitude<180}},{key:"addMarker",value:function(e){var t=e.color&&"background-color: ".concat(e.color),r=new MarkerWithLabel({position:{lat:e.coordinates.latitude,lng:e.coordinates.longitude},labelContent:'<div style="'.concat(t,'" class="fa-map-label-marker ').concat(e.postType,'"></div><i class="').concat(e.faIcon,'"></i>'),labelClass:"fa-map-label",icon:{url:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",anchor:{x:0,y:70},labelInBackground:!1}});return google.maps.event.addListener(r,"spider_click",function(t){this.onMarkerClick(r,e)}.bind(this)),google.maps.event.addListener(r,"spider_format",function(o){o==OverlappingMarkerSpiderfier.markerStatus.SPIDERFIABLE?r.set("labelContent",'<div class="fa-map-label-marker"></div><i class="fas fa-plus"></i>'):r.set("labelContent",'<div style="'.concat(t,'" class="fa-map-label-marker ').concat(e.postType,'"></div><i class="').concat(e.faIcon,'"></i>'))}.bind(this)),this.oms.addMarker(r),this.markers.push(r),r}},{key:"updateEndpointOffset",value:function(){var e=this.props.endpoint,t=new URL(e),r=t.search,o=new URLSearchParams(r);return o.set("offset",parseInt(this.currentOffset)),t.search=o.toString(),t.toString()}}])&&w(r.prototype,o),s&&w(r,s),n}(E);function z(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,o=new Array(t);r<t;r++)o[r]=e[r];return o}var B,j=function(e){if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(e=function(e,t){if(!e)return;if("string"==typeof e)return z(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(r);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return z(e,t)}(e))){var t=0,r=function(){};return{s:r,n:function(){return t>=e.length?{done:!0}:{done:!1,value:e[t++]}},e:function(e){throw e},f:r}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var o,s,i=!0,n=!1;return{s:function(){o=e[Symbol.iterator]()},n:function(){var e=o.next();return i=e.done,e},e:function(e){n=!0,s=e},f:function(){try{i||null==o.return||o.return()}finally{if(n)throw s}}}}(document.querySelectorAll(".citadela-google-map .component-container"));try{for(j.s();!(B=j.n()).done;){var R=B.value,N=R.getAttribute("data-map-height"),D=R.getAttribute("data-outside-form-breakpoint"),Z=R.getAttribute("data-no-data-behavior"),H=R.getAttribute("data-no-data-text"),G=R.getAttribute("data-is-half-layout-map"),F=R.getAttribute("data-cluster"),V={};V.endpoint=R.getAttribute("data-endpoint"),V.theme=R.getAttribute("data-theme"),V.customTheme=JSON.parse(R.getAttribute("data-custom-theme")),V.streetview=JSON.parse(R.getAttribute("data-streetview"));var W=R.getAttribute("data-geolocation");V.geolocation=W||!1,N&&(V.mapHeight=N),D&&(V.outsideFormBreakpoint=D),V.noDataBehavior=Z,"empty-map"==Z&&(V.noDataText=H),V.isHalfLayoutMap=G,V.clusterGridSize=F;var U=Object(o.createElement)(P,V);Object(o.render)(U,R)}}catch(e){j.e(e)}finally{j.f()}}});