const { Component, createRef, Fragment } = wp.element;
import 'leaflet.markercluster'
import 'leaflet-gesture-handling'
import LeafletPopup from './leaflet-popup';
import LeafletPopupTrack from './leaflet-popup-track';

export default class LeafletMap extends Component {
    constructor() {
        super( ...arguments );

        this.state = {
            map: null,
            activeMarker: null,
            activeMarkerData: null,
            popupIsOn: false,

            activeTrack: null,
            activeTrackEdgeMarkers: null,
            activeTrackPopupLatLng: null,
            trackPopupIsOn: false,
        };

        this.blockNode = null;
        this.map = null;
        this.markers = [];
        this.markerClusterer = null;
        this.markersWithoutTrack = []; //store markers of posts which have defined tracks
        this.tracks = [];
        this.tracksGroup = [];
        this.tracksEdgeMarkers = [];
        this.dataType = null;
        this.zoomThreshold = null;
        this.visiblePart = null;
        this.trackColor = null;
        this.trackEndpointsColor = null;
        this.mapSize = null;
        this.hiddenTracks = [];
        this.tracksAndMarkers = [];
        this.singleItem = false;
        this.dynamicTrack = null;
        this.geolocation = false;
        this.onMarkerClick.bind(this);
        this.onTrackClick.bind(this);
        this.checkZoomThreshold.bind(this);
        this.checkFormInsideMap = this.checkFormInsideMap.bind(this);
        this.onScreenResize = this.onScreenResize.bind(this);
        this.setEmptyMap = this.setEmptyMap.bind(this);

        this.mapRef = createRef();

        window.addEventListener("resize", this.onScreenResize); 
    }

    render() {
        const { map, activeMarker, activeMarkerData, popupIsOn, trackPopupIsOn, activeTrack, activeTrackPopupLatLng } = this.state;
        const { mapHeight, noDataBehavior, noDataText } = this.props;
        const styles = {
			...( mapHeight ? { height: mapHeight } : {} ),
		}
        return (
            <Fragment>
                {map && 
                    <LeafletPopup
                        map={ map } on={ popupIsOn }
                        marker={ activeMarker } markerData={ activeMarkerData }
                    />
                }
                { ( this.dataType == "tracks" || this.dataType == "all" ) &&
                    <LeafletPopupTrack
                        map={ map } on={ trackPopupIsOn }
                        track={ activeTrack } markerData={ activeMarkerData } latLng={ activeTrackPopupLatLng }
                    />
                }
                <div class="map-container" ref={ this.mapRef } style={ styles }></div>
                { ( noDataBehavior == 'empty-map' && noDataText ) &&
                    <div class="empty-map-cover"><div class="text-wrapper">{ noDataText }</div></div>
                }
            </Fragment>
        );
    }

    componentDidMount() {
        const { 
            endpoint, 
            dataType,
            dynamicTrack,
            zoomThreshold,
            trackColor,
            trackEndpointsColor,
            singleItem,
            geolocation,
            clusterGridSize,
        } = this.props;

        this.blockNode = this.mapRef.current.parentNode.parentNode.parentNode;
        this.dataType = dataType;
        this.dynamicTrack = dynamicTrack;
        this.zoomThreshold = zoomThreshold;
        this.trackColor = trackColor;
        this.trackEndpointsColor = trackEndpointsColor;
        this.singleItem = singleItem;
        this.geolocation = geolocation ? JSON.parse( geolocation ) : false;

        let map = L.map(this.mapRef.current, {
            center: [51.505, -0.09],
            zoom: 13,
            gestureHandling: true,
			gestureHandlingOptions: {
        		duration: 3000
            },
        });

        this.map = map;
        this.mapSize = this.map._size;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'}).addTo(map);

        this.setState({ map: map });

        this.markerClusterer = L.markerClusterGroup( { 
            chunkedLoading: true,
            maxClusterRadius: parseInt( clusterGridSize ),
        } );
        
        map.on('popupclose', () => {

            if( this.state.activeTrack && ! this.singleItem ){
                this.hide( this.state.activeTrackEdgeMarkers );
            }
            
            this.setState({ popupIsOn: false, activeTrack: false, trackPopupIsOn: false, activeTrackEdgeMarkers: null, });
        });
        
        if( this.dynamicTrack ){
            map.on('zoomend', () => {
               this.checkZoomThreshold();
            });
        }

        this.getPoints(endpoint);
        this.checkFormInsideMap();
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

    fitAndZoom() {
        if( this.dataType == 'markers' || this.dataType === null ){
            if ( this.markers.length == 0 ) {
                this.setEmptyMap();
                return;
            }
            
            if (this.markers.length == 1) {
                this.map.setView(this.markerClusterer.getBounds().getCenter(), 15);
                return;
            }

            if (this.markers.length > 1) {
                let group = new L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds());
                return;
            }

        }else if( this.dataType == 'tracks' ){
            
            if ( !this.tracks ) {
                this.setEmptyMap();
                return;
            }
            //there are maybe markers, if zoom threshold is enabled to show markers
            let markers = this.markers.length > 0 ? this.markers : [];
            let tracks = this.tracks.length > 0 ? this.tracks : [];
            let group = new L.featureGroup( tracks.concat( markers ) );
            this.map.fitBounds(group.getBounds());
            return;

        }else{
            let markers = this.markers.length > 0 ? this.markers : [];
            let tracks = this.tracks.length > 0 ? this.tracks : [];
            const allDataTypes = markers.concat( tracks );
            if( allDataTypes.length == 0 ){
                this.setEmptyMap();
                return;
            }
            let group = new L.featureGroup( markers.concat( tracks ) );
            this.map.fitBounds(group.getBounds());
            return;
        }
    }
    
    setEmptyMap(){
        const { noDataBehavior, isHalfLayoutMap } = this.props;
        if( noDataBehavior == 'empty-map' ){
            this.map.setView([0, 0]);
            this.map.setZoom(2);
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

    checkZoomThreshold(){
        const wThreshold = this.mapSize.x * 0.1;
        const hThreshold = this.mapSize.y * 0.1;
        this.tracksAndMarkers.forEach( data => {
            const track = data['track']['polyline'];
            const edgeMarkers = data['track']['edgemarkers'];
            const marker = data['marker'];
            const b = track.getBounds();
            const sw = b._southWest; 
            const ne = b._northEast; 
            const swPoint = this.map.latLngToContainerPoint( sw );
            const nePoint = this.map.latLngToContainerPoint( ne );
            const w = nePoint.x - swPoint.x;
            const h = swPoint.y - nePoint.y;
            if( w > wThreshold || h > hThreshold ){
                if( this.singleItem ){
                    this.show( [ track, edgeMarkers ] );
                }else{
                    this.show( track );
                }
                this.markerClusterer.removeLayer( marker );
            }else{
                //track is too small, show marker
                if( track === this.state.activeTrack ){
                    this.map.closePopup();
                }
                this.hide( [ track, edgeMarkers ] );
                this.markerClusterer.addLayer( marker );
            }
        });
        
        this.map.addLayer( this.markerClusterer );
    }
   
    hide( what ){
        if( ! what ) return;
        if( Array.isArray( what ) ){
            what.forEach( elm => {
                if( elm ) this.map.removeLayer( elm );
            });
        }else{
            if( what ) this.map.removeLayer( what );
        }
    }

    show( what ){
        if( Array.isArray( what ) ){
            what.forEach( elm => {
                if( elm ) this.map.addLayer( elm );
            });
        }else{
            if( what ) this.map.addLayer( what );
        }
    }

    getPoints(url) {
        let json = fetch(url)
        .then(response => { return response.json() })
        .then(data => {
            if(data.points.length > 0){
                
                data.points.forEach(point => {
                    //do action according to data type displayed on map
                    if( this.dataType == 'markers' || this.dataType === null ){
                        //add only markers
                        if (this.isValidPointLocation(point.coordinates)) {
                            this.addMarker(point);
                        }
                        
                    }else if( this.dataType == 'tracks' ){
                        //add only tracks
                        this.addTrack(point);

                    }else{
                        // add markers and tracks
                        if( point.track.length == 0 ){
                            //item doesn't have track, show standard marker
                            if (this.isValidPointLocation(point.coordinates)) {
                                this.addMarker(point);
                            }
                        }else{
                            //item has track
                            this.addTrack(point);
                            
                        }
                    }
                    
                    
                });
                
                // check if can load more points
                if (data.total > this.currentOffset + data.points.length) {
                    this.currentOffset += data.points.length;
                    this.getPoints(this.updateEndpointOffset());
                } else {
                    // set geolocation layer first, fix gpx track hidden under geolocation circle
                    if( this.geolocation ){
                        this.setGeolocation();
                    }
                    this.tracksGroup = new L.featureGroup( this.tracks );
                    this.map.addLayer( this.tracksGroup );
                    this.map.addLayer( this.markerClusterer );
                    
                    // if no geolocation, zoom and fit to all available markers
                    if( ! this.geolocation ){
                        this.fitAndZoom()
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
        const lat = this.geolocation['lat'];
        const lon = this.geolocation['lon'];
        const radius = this.geolocation['rad'];
        const unit = this.geolocation['unit'];

        var pos = L.latLng(lat, lon);
        const markerIconHtml = `<div class="fa-map-label"><div class="fa-map-label-marker geolocation-marker"></div><i class="fas fa-street-view"></i></div>`;
        const markerIcon = L.divIcon( {
            className: 'citadela-marker-icon geolocation',
            html: markerIconHtml,
            iconSize: [ 50, 50 ],
            iconAnchor: [ 0, 0 ] 
        } );
        const marker = L.marker(L.latLng(lat, lon), { icon: markerIcon } );

        this.map.setView(pos);
        
        this.map.setZoom(Math.round(14-Math.log(unit == 'mi' ? radius * 1.609 : radius * 1 )/Math.LN2));

        var radiusOptions = {
            color: '#005BB7',
            opacity: 0.8,
            weight: 2,
            fillColor: '#008BB2',
            fillOpacity: 0.35,
            radius: unit == 'mi' ? radius * 1609 : radius * 1000,
        };
        const circle = L.circle(pos, radiusOptions);
        let geoData = L.layerGroup( [ circle, marker ] );
        geoData.addTo( this.map );
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


    addMarker( point ) {
        const backgroundStyle = point.color && (`background-color: ${point.color}`);

        const markerIconHtml = `<div class="fa-map-label"><div style="${backgroundStyle}" class="fa-map-label-marker ${point.postType}"></div><i class="${point.faIcon}"></i></div>`;
        const markerIcon = L.divIcon( {
            className: 'citadela-marker-icon',
            html: markerIconHtml,
            iconSize: [ 50, 50 ],
            iconAnchor: [ 0, 0 ] 
        } );

        const marker = L.marker(L.latLng(point.coordinates.latitude, point.coordinates.longitude), { icon: markerIcon } );
        
        marker.on('click', () => {this.onMarkerClick(marker, point)});
       
        this.markers.push(marker);
        this.markerClusterer.addLayer( marker );
    }

    addTrackMarker( point, polyline ) {
        const backgroundStyle = point.color && (`background-color: ${point.color}`);

        const markerIconHtml = `<div class="fa-map-label"><div style="${backgroundStyle}" class="fa-map-label-marker ${point.postType}"></div><i class="${point.faIcon}"></i></div>`;
        const markerIcon = L.divIcon( {
            className: 'citadela-marker-icon',
            html: markerIconHtml,
            iconSize: [ 50, 50 ],
            iconAnchor: [ 0, 0 ] 
        } );

        const trackBounds = polyline.getBounds();
        const trackCenter = trackBounds.getCenter();
        const marker = L.marker( trackCenter , { icon: markerIcon } );
        
        marker.on('click', () => {
            this.map.fitBounds(trackBounds);
        });
        
        //group markers related to track
        this.markers.push(marker);

        return marker;
        
    }

    addTrack( point ) {
        const gpxData = point.track;
        gpxData.forEach( track => {

            // compatibility with track.points data where were stored all points from one gpx file one by one, 
            // now are points stored separately to segments in case the one track consists from more parts, thus store also points of one single track as one segment 
            if( track.points && ! track.data ){
                track.data = [];
                track.data.push( track.points );
            }
            
            let segments = [];
            
            if( track.data ){

                const edgeMarkers = this.addTrackEdgeMarkers( track, this.trackEndpointsColor ? this.trackEndpointsColor : point.color );
                
                if( this.singleItem && edgeMarkers ) this.show( edgeMarkers );
                
                track.data.forEach( segment => {
                    let segment_points = [];        
                    segment.forEach( trackPoint => {
                        segment_points.push( [ trackPoint.lat, trackPoint.lng ] )
                    });
                    segments.push( segment_points );
                });             
                
                let segments_group = [];
                segments.forEach( segment => {
                    let points = [];
                    segment.forEach( point_coords => {
                        points.push( [ point_coords[0], point_coords[1] ] );
                    });

                    const polyline = L.polyline(points, { 
                        color: this.trackColor ? this.trackColor : point.color ? point.color : "#e65656",
                        weight: 6,
                        smoothFactor: 2,
                        className: this.trackColor || point.color ? "citadela-track" : "citadela-track default-color",
                    } );
                    
                    segments_group.push( polyline );
                });

                const segments_group_L_feature =  new L.featureGroup( segments_group );
                segments_group_L_feature.on('click', ( event ) => { 
                    this.onTrackClick( event.target, edgeMarkers, point, event )
                });
                segments_group_L_feature.on('mouseover', ( event ) => { 
                    if( this.singleItem || this.state.trackPopupIsOn ) return;
                    this.show( edgeMarkers );
                    event.target.bringToFront();
                });
                segments_group_L_feature.on('mouseout', ( event ) => { 
                    if( this.singleItem || this.state.trackPopupIsOn ) return;
                    this.hide( edgeMarkers );
                });

                this.tracks.push( segments_group_L_feature );

                if( this.dynamicTrack ) {
                    const trackMarker = this.addTrackMarker( point, segments_group_L_feature );
                    let data = [];
                    data['track'] = [];
                    data['track']['polyline'] = segments_group_L_feature;
                    data['track']['edgemarkers'] = edgeMarkers;
                    data['marker'] = trackMarker;
                    this.tracksAndMarkers.push( data );
                }
            }
        });
    }

    addTrackEdgeMarkers( track, color ){
        const track_data = track.data;
        const endpoints_type = track.endpoints_type ? track.endpoints_type : 'track';
        let edge_markers = [];
        let start = [];
        let end = [];
        if( endpoints_type == 'track' ){
            start = track_data[0][0];
            end = track_data[track_data.length - 1][track_data[track_data.length - 1].length - 1];
            edge_markers.push( [ 
                [ start['lat'], start['lng'] ],
                [ end['lat'], end['lng'] ]
            ] );
        }
        
        if( endpoints_type == 'segments' ){
            track_data.forEach( track => {
                start = track[0];
                end = track[track.length - 1];      
                edge_markers.push( [ 
                    [ start['lat'], start['lng'] ],
                    [ end['lat'], end['lng'] ]
                ] );
            });
        }

        if( endpoints_type == 'none' || ! edge_markers ){
            return false;
        }
                        
        const colorStyle = `background-color: ${color}`;
        const startMarkerIconHtml = `<div class="fa-map-label"><div style="${colorStyle}" class="fa-map-label-marker"></div><i class="fas fa-flag"></i></div>`;
        const endMarkerIconHtml = `<div class="fa-map-label"><div style="${colorStyle}" class="fa-map-label-marker"></div><i class="fas fa-flag-checkered"></i></div>`;
        const startEndMarkerIconHtml = `<div class="fa-map-label"><div style="${colorStyle}" class="fa-map-label-marker"></div><i class="far fa-flag"></i></div>`;
        
        let markers_group = [];
        edge_markers.forEach( edge_points => {
            const start_coords = edge_points[0];
            const end_coords = edge_points[1];

            // check if start and end coordinates are different
            if( ( start_coords[0] !== end_coords[0] ) || ( start_coords[1] !== end_coords[1] ) ){
                let startMarkerIcon = L.divIcon( {
                    className: 'citadela-marker-icon track-endpoint start-point',
                    html: startMarkerIconHtml,
                    iconSize: [ 50, 50 ],
                    iconAnchor: [ 0, 0 ] 
                } );
                const startMarker = L.marker( start_coords, { icon: startMarkerIcon });
                let endMarkerIcon = L.divIcon( {
                    className: 'citadela-marker-icon track-endpoint end-point',
                    html: endMarkerIconHtml,
                    iconSize: [ 50, 50 ],
                    iconAnchor: [ 0, 0 ] 
                } );				
                const endMarker = L.marker( end_coords, { icon: endMarkerIcon });
                //add markers to tracks featureGroup
                this.tracksEdgeMarkers.push( startMarker, endMarker );
                markers_group.push( startMarker, endMarker );
            }else{
                let startMarkerIcon = L.divIcon( {
                    className: 'citadela-marker-icon track-endpoint start-end-icon',
                    html: startEndMarkerIconHtml,
                    iconSize: [ 50, 50 ],
                    iconAnchor: [ 0, 0 ] 
                } );
                const startMarker = L.marker( start_coords, { icon: startMarkerIcon });
                //add markers to tracks featureGroup
                this.tracksEdgeMarkers.push( startMarker );
                markers_group.push( startMarker );
            }


        } );

        return new L.featureGroup( markers_group );

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

    onMarkerClick(marker, point) {
        
        // it is not necessary to add listener to all markers but only to those which were clicked
        marker.once('remove', () => {

            // if marker which was removed (eg was clustered) is activeMarker (has popup)
            if (marker == this.state.activeMarker) {
                this.map.closePopup();
            }

        });

        this.setState({ activeMarker: marker, activeMarkerData: point, popupIsOn: true });
    }

    onTrackClick(polyline, edgeMarkers, point, event) {
        if( this.state.activeTrack ) return;

        const clickLatLng = this.map.mouseEventToLatLng( event.originalEvent );
        
        this.show( edgeMarkers );

        this.setState({ 
            activeTrack: polyline, 
            activeTrackEdgeMarkers: edgeMarkers, 
            activeMarkerData: point,
            activeTrackPopupLatLng: clickLatLng, 
            trackPopupIsOn: true 
        });
    }

    toggleEdgeMarkers( edgeMarkers, opacity ){
        if( ! edgeMarkers ) return;
    }

}