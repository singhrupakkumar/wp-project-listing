/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { DropdownMenu, SVG, Path } = wp.components;



export default class extends Component {

	render() {
		const { toggleProps, value, onChange } = this.props;
		const svgCss = {
			shapeRendering:"geometricPrecision",
			textRendering:"geometricPrecision",
			imageRendering:"optimizeQuality",
			fillRule:"evenodd"
		};
		const icons = {
					small: <SVG style={svgCss} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><Path d="M8 8l4 0 0 4 -4 0 0 -4zm-5 -6l14 0c0,0 1,1 1,1l0 14c0,0 -1,1 -1,1l-14 0c0,0 -1,-1 -1,-1l0 -14c0,0 1,-1 1,-1zm1 2l12 0 0 12 -12 0 0 -12z"/></SVG>,
					medium: <SVG style={svgCss} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><Path d="M6 6l8 0 0 8 -8 0 0 -8zm-3 -4l14 0c0,0 1,1 1,1l0 14c0,0 -1,1 -1,1l-14 0c0,0 -1,-1 -1,-1l0 -14c0,0 1,-1 1,-1zm1 2l12 0 0 12 -12 0 0 -12z"/></SVG>,
					large: <SVG style={svgCss} xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><Path d="M4 4l12 0 0 12 -12 0 0 -12zm-1 -2l14 0c0,0 1,1 1,1l0 14c0,0 -1,1 -1,1l-14 0c0,0 -1,-1 -1,-1l0 -14c0,0 1,-1 1,-1zm1 2l12 0 0 12 -12 0 0 -12z"/></SVG>,
				};

		return(
			<DropdownMenu
				icon={icons[value]}
				label={__('Select size', 'citadela-pro')}
				controls={[
					{
						title: __('Small size', 'citadela-pro'),
						icon: icons.small,
						isActive: value === 'small',
						onClick: () => onChange( 'small' ),
					},
					{
						title: __('Medium size', 'citadela-pro'),
						icon: icons.medium,
						isActive: value === 'medium',
						onClick: () => onChange( 'medium' ),
					},
					{
						title: __('Large size', 'citadela-pro'),
						icon: icons.large,
						isActive: value === 'large',
						onClick: () => onChange( 'large' ),
					}
				]}
				toggleProps={ toggleProps }
			/>
	    );
	}
}

