/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { Toolbar, DropdownMenu, SVG, G, Path } = wp.components;



export default class CitadelaDirectoryToolbarLayout extends Component {

	render() {

        const { toggleProps, value, onChange } = this.props;
        const svgCss = {
			shapeRendering:"geometricPrecision",
			textRendering:"geometricPrecision",
			imageRendering:"optimizeQuality",
			fillRule:"evenodd"
		};
		const icons = {
            simple: <SVG style={svgCss} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <G id="Layer_x0020_1">
                        <Path class="fil0" d="M2 11l16 0 0 1 -16 0 0 -1zm0 2l16 0 0 1 -16 0 0 -1zm0 2l13 0 0 1 -13 0 0 -1zm1 -12l9 0c1,0 1,0 1,1l0 4c0,1 0,1 -1,1l-9 0c-1,0 -1,0 -1,-1l0 -4c0,-1 0,-1 1,-1z"/>
                    </G>
                </SVG>,
            list: <SVG style={svgCss} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <G id="Layer_x0020_1">
                        <Path class="fil0" d="M7 5l8 0 0 1 -8 0 0 -1zm0 -2l11 0 0 1 -11 0 0 -1zm-4 -1l2 0c1,0 1,0 1,1l0 2c0,1 0,1 -1,1l-2 0c-1,0 -1,0 -1,-1l0 -2c0,-1 0,-1 1,-1z"/>
                        <Path class="fil0" d="M7 11l8 0 0 1 -8 0 0 -1zm0 -2l11 0 0 1 -11 0 0 -1zm-4 -1l2 0c1,0 1,0 1,1l0 2c0,1 0,1 -1,1l-2 0c-1,0 -1,0 -1,-1l0 -2c0,-1 0,-1 1,-1z"/>
                        <Path class="fil0" d="M7 17l8 0 0 1 -8 0 0 -1zm0 -2l11 0 0 1 -11 0 0 -1zm-4 -1l2 0c1,0 1,0 1,1l0 2c0,1 0,1 -1,1l-2 0c-1,0 -1,0 -1,-1l0 -2c0,-1 0,-1 1,-1z"/>
                    </G>
                </SVG>,

            box: <SVG style={svgCss} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <G id="Layer_x0020_1">
                        <Path class="fil0" d="M3 11l3 0c0,0 0,0 0,1l0 4c0,0 0,1 0,1l-3 0c-1,0 -1,-1 -1,-1l0 -4c0,-1 0,-1 1,-1zm11 0l3 0c1,0 1,0 1,1l0 4c0,0 0,1 -1,1l-3 0c0,0 0,-1 0,-1l0 -4c0,-1 0,-1 0,-1zm-5 0l2 0c1,0 1,0 1,1l0 4c0,0 0,1 -1,1l-2 0c-1,0 -1,-1 -1,-1l0 -4c0,-1 0,-1 1,-1zm-6 -8l3 0c0,0 0,1 0,1l0 4c0,1 0,1 0,1l-3 0c-1,0 -1,0 -1,-1l0 -4c0,0 0,-1 1,-1zm11 0l3 0c1,0 1,1 1,1l0 4c0,1 0,1 -1,1l-3 0c0,0 0,0 0,-1l0 -4c0,0 0,-1 0,-1zm-5 0l2 0c1,0 1,1 1,1l0 4c0,1 0,1 -1,1l-2 0c-1,0 -1,0 -1,-1l0 -4c0,0 0,-1 1,-1z"/>
                    </G>
                </SVG>,
            text: <SVG xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
            		<Path d="M18 3v2H2V3h16zm-6 4v2H2V7h10zm6 0v2h-4V7h4zM8 11v2H2v-2h6zm10 0v2h-8v-2h8zm-4 4v2H2v-2h12z" />
            	</SVG>
        };

        const availableControls = {
            'text': {
                title: __('Text layout', 'citadela-directory'),
                icon: icons.text,
                isActive: value === 'text',
                onClick: () => onChange( 'text' ),
            },
            'simple': {
                title: __('Simple layout', 'citadela-directory'),
                icon: icons.simple,
                isActive: value === 'simple',
                onClick: () => onChange( 'simple' ),
            },
            'list': {
                title: __('List layout', 'citadela-directory'),
                icon: icons.list,
                isActive: value === 'list',
                onClick: () => onChange( 'list' ),
            },
            'box': {
                title: __('Box layout', 'citadela-directory'),
                icon: icons.box,
                isActive: value === 'box',
                onClick: () => onChange( 'box' ),
            }
        };

        let controls = [];
        for (let key in availableControls) {
            if (this.props.allowedLayouts.includes(key)) {
                controls.push(availableControls[key]);
            }
        }

		return(
			<DropdownMenu
				icon={icons[value]}
				label={__('Select layout', 'citadela-directory')}
				controls={ controls }
                toggleProps={ toggleProps }
			/>
	    );
	}
}

CitadelaDirectoryToolbarLayout.defaultProps = {
    allowedLayouts: ['list', 'box'],
}