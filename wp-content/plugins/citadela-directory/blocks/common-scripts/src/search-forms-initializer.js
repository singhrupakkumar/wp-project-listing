const { createElement, render } = wp.element;
import searchFormComponent from '../../components/search-form.js';

const containersSelector = '.ctdl-posts-search-form .search-form-component-container, .ctdl-directory-search-form .search-form-component-container';
const componentContainers = document.querySelectorAll( containersSelector );
const activeProPlugin = document.querySelector('body').classList.contains( 'pro-plugin-active' );

for ( const node of componentContainers ) {
    const data = {};
    
    data['action'] = JSON.parse(node.getAttribute( 'data-action' ));
    data['attributes'] = node.getAttribute( 'data-attributes' ) ? JSON.parse(node.getAttribute( 'data-attributes' )) : [];
    data['postType'] = node.getAttribute( 'data-post-type' );
    data['categoryTaxonomy'] = node.getAttribute( 'data-category-taxonomy' );
    data['locationTaxonomy'] = node.getAttribute( 'data-location-taxonomy' );

    const el = createElement( searchFormComponent, data );
    render( el, node );
}