const { Component, createRef } = wp.element;
import { Infowindow } from './infowindow';
import * as MarkerClusterer from '@google/markerclustererplus';
import mapStyles from './map-styles.js';

export class Map extends Component {
    constructor() {
        super( ...arguments );

        this.getPoints = this.getPoints.bind(this);
        this.checkFormInsideMap = this.checkFormInsideMap.bind(this);
        this.onScreenResize = this.onScreenResize.bind(this);
        this.setEmptyMap = this.setEmptyMap.bind(this);

        this.state = {
            points: [],
            map: null,
            panorama: null,
            activeMarker: null,
        };

        this.blockNode = null;

        this.markers = [];
        this.currentOffset = 0;
        this.markerClusterer = null;
        this.geolocation = false;

        // Refs
        this.mapRef = createRef();

        window.addEventListener("resize", this.onScreenResize);

    }

    render() {
        const { map, activeMarker } = this.state;
        const { mapHeight, noDataBehavior, noDataText } = this.props;
        const styles = {
			...( mapHeight ? { height: mapHeight } : {} ),
		}
        const infoWindow = map && (
            <Infowindow
                activeMarker={ activeMarker }
                map={ map }
            />
        );

        return (
            <>
                <div class="map-container no-markers" ref={ this.mapRef } style={ styles }></div>
                { ( noDataBehavior == 'empty-map' && noDataText ) &&
                    <div class="empty-map-cover"><div class="text-wrapper">{ noDataText }</div></div>
                }
                { infoWindow }
            </>
        );

    }

    componentDidMount() {
        const { 
            endpoint, 
            streetview, 
            theme, 
            customTheme,
            geolocation,
            clusterGridSize,
        } = this.props;
        
        this.blockNode = this.mapRef.current.parentNode.parentNode.parentNode;
        this.geolocation = geolocation ? JSON.parse( geolocation ) : false;

        let predefinedStyle = mapStyles.find(obj => {
            return obj.codeName == theme;
        })

        const map = new google.maps.Map(this.mapRef.current, {
            center: {lat: 41.8585, lng: 5.029},
            zoom: 2,
            styles: predefinedStyle ? predefinedStyle.json : customTheme,
        });

        this.oms = new OverlappingMarkerSpiderfier(map, {
            keepSpiderfied: 'yes',
            circleFootSeparation: 50,
            nearbyDistance: 1,
        });
        
        this.markerClusterer = new MarkerClusterer(map, this.markers, {gridSize: parseInt( clusterGridSize ), maxZoom: 16});

        // https://github.com/jawj/OverlappingMarkerSpiderfier/issues/154
        google.maps.event.addListener(map, 'idle', function() {
            this.oms.h.call(this.oms);
        }.bind(this));


        if (streetview) {
            const panorama = map.getStreetView();
            panorama.setPosition(map.getCenter());
            panorama.setPov(streetview);
            panorama.setVisible(true);
            this.setState( {panorama: panorama} );
        }
        this.setState( {map: map} );

        this.getPoints(endpoint);
    }

    onScreenResize(){
        this.checkFormInsideMap();
    }
    
    checkFormInsideMap(){
        if( window.innerWidth < this.props.outsideFormBreakpoint ){
            this.blockNode.classList.add('outside-search-form');
        }else{
            this.blockNode.classList.remove('outside-search-form');
        }
    }
    
    componentDidUpdate(prevProps) {
        //this.fitAndZoom();
    }

    fitAndZoom = () => {
        const { map, activeMarker } = this.state;

        if ( this.markers.length == 0 ) {
            this.setEmptyMap();
			return;
		}

        if ( activeMarker ) {
			return;
		}

        const bounds = new google.maps.LatLngBounds();
		this.markers.forEach( marker => {
			bounds.extend( marker.position );
        } );

        // either single item map or search with only one result
        if (this.markers.length == 1) {
            map.setCenter( bounds.getCenter() );
            map.setZoom( 15 );
            this.updatePanorama();
            return;
        }

        if (this.markers.length > 1) {
            map.fitBounds(bounds, {
                top: 40,
            });
            return;
        }
        
    };

    setEmptyMap(){
        const { noDataBehavior, isHalfLayoutMap } = this.props;
        const { map } = this.state;
        
        if( noDataBehavior == 'empty-map' ){
            const pos = new google.maps.LatLng( 0, 0 );
            map.setCenter( pos );
            map.setZoom(2);
            this.blockNode.classList.add('empty-map');
        }

        if( noDataBehavior == 'hidden-map' ){
            this.blockNode.classList.add('hidden-map');

            //toggle body class to disable half layout for hidden map
            if( isHalfLayoutMap ){
                let body = document.querySelector( 'body' );
                body.classList.remove('hidden-map');
                body.classList.add('page-fullwidth');
                window.dispatchEvent(new Event('resize'));
            }
        }
    }

    onMarkerClick = (marker, point) => {
        const { map } = this.state;

        const activeMarker = {
            marker: marker,
            point: point
        };

        this.setState({ activeMarker: activeMarker });
    }

    getPoints(url) {
        let json = fetch(url)
        .then(response => { return response.json() })
        .then(data => {
            const newMarkers = [];
            if(data.points.length > 0){
                data.points.forEach(point => {
                    if (this.isValidPointLocation(point.coordinates)) {
                        newMarkers.push(this.addMarker(point));
                    }
                });
                if( parseInt( this.props.clusterGridSize ) > 0 ){
                    this.markerClusterer.addMarkers(newMarkers);
                }
                // check if can load more points
                if (data.total > this.currentOffset + data.points.length) {
                    this.currentOffset += data.points.length;
                    this.getPoints(this.updateEndpointOffset());
                } else {
                    // set geolocation layer first, fix gpx track hidden under geolocation circle
                    if( this.geolocation ){
                        this.setGeolocation();
                    }else{
                        this.fitAndZoom();
                    }
                }
                this.blockNode.classList.remove('loading-content');
            }else{
                //set geolocation center even there are no results to show on map
                if( this.geolocation ){
                    this.setGeolocation();
                    this.blockNode.classList.remove('loading-content');
                    return;
                }

                this.setEmptyMap();
                this.blockNode.classList.remove('loading-content');
            }
        });
    }

    setGeolocation(){
        const { map } = this.state;
        const lat = this.geolocation['lat'];
        const lon = this.geolocation['lon'];
        const radius = this.geolocation['rad'];
        const unit = this.geolocation['unit'];
        const pos = new google.maps.LatLng( lat, lon );

        const marker = new MarkerWithLabel({
            position: pos,
            map: map,
            labelContent: `<div class="fa-map-label-marker geolocation-marker"></div><i class="fas fa-street-view"></i>`,
            labelClass: "fa-map-label",
            icon: {
                url: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                anchor: { x:0, y:70 },
                labelInBackground: false,
            }
        });  
        
        map.setCenter( pos );

        map.setZoom(Math.round(14-Math.log(unit == 'mi' ? radius * 1.609 : radius * 1 )/Math.LN2));

        const radiusOptions = {
            strokeColor: '#005BB7',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#008BB2',
            fillOpacity: 0.35,
            map: map,
            center: pos,
            radius: unit == 'mi' ? radius * 1609 : radius * 1000,
        };
        var radiusCircle = new google.maps.Circle(radiusOptions);
    }

    updatePanorama() {
        const { map, panorama } = this.state;
        if (panorama) {
            panorama.setPosition(map.getCenter());
        }
    }

    isValidPointLocation(coordinates) {
        if ( isNaN(coordinates.latitude) || isNaN(coordinates.longitude) ) {
            return false;
        }

        if ( coordinates.latitude == 0 && coordinates.longitude == 0 ) {
            return false;
        }

        if ( (coordinates.latitude > -90 && coordinates.latitude < 90) && (coordinates.longitude > -180 && coordinates.longitude < 180) ) {
            return true;
        }

        return false;
    }

    addMarker(point) {
        const backgroundStyle = point.color && (`background-color: ${point.color}`);

        const marker = new MarkerWithLabel({
            position: {lat: point.coordinates.latitude, lng: point.coordinates.longitude},
            labelContent: `<div style="${backgroundStyle}" class="fa-map-label-marker ${point.postType}"></div><i class="${point.faIcon}"></i>`,
            labelClass: "fa-map-label",
            icon: {
                url: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                anchor: { x:0, y:70 },
                labelInBackground: false,
            }
        });

        google.maps.event.addListener(marker, 'spider_click', function(e) {  // 'spider_click', not plain 'click'
            this.onMarkerClick(marker, point)
        }.bind(this));

        google.maps.event.addListener(marker, 'spider_format', function(status) {
            if (status == OverlappingMarkerSpiderfier.markerStatus.SPIDERFIABLE) {
                marker.set("labelContent", `<div class="fa-map-label-marker"></div><i class="fas fa-plus"></i>`);
            } else {
                marker.set("labelContent", `<div style="${backgroundStyle}" class="fa-map-label-marker ${point.postType}"></div><i class="${point.faIcon}"></i>`);
            }
        }.bind(this));

        this.oms.addMarker(marker);
        this.markers.push(marker);
        return marker;
    }

    updateEndpointOffset() {
        const { endpoint } = this.props;

        let url = new URL(endpoint);
        let query_string = url.search;

        let search_params = new URLSearchParams(query_string);

        search_params.set('offset', parseInt(this.currentOffset));

        url.search = search_params.toString();

        var new_url = url.toString();

        return new_url;
    }
}

export default Map;