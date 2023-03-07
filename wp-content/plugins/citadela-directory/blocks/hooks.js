const { addFilter } = wp.hooks;

addFilter( 'blocks.registerBlockType', 'citadela-directory/disable-reusable-setting', function filterBlockSupports( settings, name ) {
	if ( settings.supports === undefined ) {
		settings.supports = {}
	}
	/* Disabled reusable block option for Advanced Filters block
	if( name === 'citadela-directory/directory-advanced-filters' ) {
		settings.supports.reusable = false;
	}
	*/
	return settings;
} );

//not used yet
addFilter( 'citadela.JSstripHtml', 'citadela-directory/directory-hooks', ( string ) => {
	var helperTag = document.createElement('div');
	helperTag.innerHTML = string;
	return helperTag.textContent;

});
