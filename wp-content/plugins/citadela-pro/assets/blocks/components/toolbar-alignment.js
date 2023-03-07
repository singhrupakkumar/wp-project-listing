/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { DropdownMenu } = wp.components;



export default class ToolbarAlignment extends Component {

	render() {
		const { toggleProps, value, onChange, label = __('Select alignment', 'citadela-pro') } = this.props; 

		const alignmentIcons = {
			left: 'editor-alignleft',
			center: 'editor-aligncenter',
			right: 'editor-alignright',
		};

		return(
			<DropdownMenu
				icon={ alignmentIcons[value] }
				label={ label }
				toggleProps={ toggleProps }
				controls={[
					{
						title: __('Align Text Left', 'citadela-pro'),
						icon: alignmentIcons.left,
						isActive: value === 'left',
						onClick: () => { onChange('left'); }
					},
					{
						title: __('Align Text Center', 'citadela-pro'),
						icon: alignmentIcons.center,
						isActive: value === 'center',
						onClick: () => { onChange('center'); }
					},
					{
						title: __('Align Text Right', 'citadela-pro'),
						icon: alignmentIcons.right,
						isActive: value === 'right',
						onClick: () => { onChange('right'); }
					},	
				]}
			/>
	    );
	}
}