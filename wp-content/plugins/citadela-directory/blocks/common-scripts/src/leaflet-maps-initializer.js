import { createElement, render } from '@wordpress/element';
import mapComponent from '../../components/leaflet-map.js';

const containersSelector = '.citadela-openstreetmap .component-container';

const mapContainers = document.querySelectorAll( containersSelector );

for ( const node of mapContainers ) {
    
    const dataType = node.getAttribute( 'data-type' );
    const dynamicTrack = node.getAttribute( 'data-dynamic-track' );
    const trackColor = node.getAttribute( 'data-track-color' );
    const trackEndpointsColor = node.getAttribute( 'data-track-endpoints-color' );
    const singleItem = node.getAttribute( 'data-single-item' );
    const geolocation = node.getAttribute( 'data-geolocation' );
    const mapHeight = node.getAttribute( 'data-map-height' );
    const outsideFormBreakpoint = node.getAttribute( 'data-outside-form-breakpoint' );
    const noDataBehavior = node.getAttribute( 'data-no-data-behavior' );
    const noDataText = node.getAttribute( 'data-no-data-text' );
    const isHalfLayoutMap = node.getAttribute( 'data-is-half-layout-map' );
    const clusterGridSize = node.getAttribute( 'data-cluster' );
    
    const data = {};
    
    data['endpoint'] = node.getAttribute( 'data-endpoint' );

    if( dataType ) data['dataType'] = dataType;
    if( dataType != 'markers' ) data['dynamicTrack'] = dynamicTrack ? true : false; 
    if( trackColor ) data['trackColor'] = trackColor;
    if( trackEndpointsColor ) data['trackEndpointsColor'] = trackEndpointsColor;
    data['singleItem'] = singleItem ? true : false;
    data['geolocation'] = geolocation ? geolocation : false;

    if( mapHeight ) data['mapHeight'] = mapHeight;
    if( outsideFormBreakpoint ) data['outsideFormBreakpoint'] = outsideFormBreakpoint;

    data['noDataBehavior'] = noDataBehavior;
    if( noDataBehavior == 'empty-map' ) data['noDataText'] = noDataText;
    
    data['isHalfLayoutMap'] = isHalfLayoutMap;
    data['clusterGridSize'] = clusterGridSize;

    const el = createElement( mapComponent, data );
    render( el, node );
}