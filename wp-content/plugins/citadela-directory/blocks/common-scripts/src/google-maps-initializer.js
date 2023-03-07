import { createElement, render } from '@wordpress/element';
import mapComponent from '../../components/map.js';

const containersSelector = '.citadela-google-map .component-container';

const mapContainers = document.querySelectorAll( containersSelector );
for ( const node of mapContainers ) {
    
    const mapHeight = node.getAttribute( 'data-map-height' );
    const outsideFormBreakpoint = node.getAttribute( 'data-outside-form-breakpoint' );
    const noDataBehavior = node.getAttribute( 'data-no-data-behavior' );
    const noDataText = node.getAttribute( 'data-no-data-text' );
    const isHalfLayoutMap = node.getAttribute( 'data-is-half-layout-map' );
    const clusterGridSize = node.getAttribute( 'data-cluster' );

    const data = {};
    
    data['endpoint'] = (node.getAttribute( 'data-endpoint' ));
    data['theme'] = node.getAttribute( 'data-theme' );
    data['customTheme'] = JSON.parse( node.getAttribute( 'data-custom-theme' ) );
    data['streetview'] = JSON.parse(node.getAttribute( 'data-streetview' ));
    
    const geolocation = node.getAttribute( 'data-geolocation' );
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