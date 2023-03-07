/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { ToolbarGroup, ToolbarButton, BaseControl } = wp.components;

export default class ToolbarAlignmentInspector extends Component {
	
	render() {
		const {
			value,
			onChange,
			label = __('Select alignment', 'citadela-pro'),
		} = this.props;
		
		const alignmentIcons = {
			left: 'editor-alignleft',
			center: 'editor-aligncenter',
			right: 'editor-alignright',
		};

		return (
			<BaseControl 
				label={ label }
				className="citadela-alignment-control"
			>
				<ToolbarGroup 
					label={ label }
					isCollapsed={ false }
					>
					<ToolbarButton
						icon={ alignmentIcons.left }
						onClick={ () => onChange('left') }
						isPressed={ value === 'left' }
					/>
					<ToolbarButton
						icon={ alignmentIcons.center }
						onClick={ () => onChange('center') }
						isPressed={ value === 'center' }
					/>
					<ToolbarButton
						icon={ alignmentIcons.right }
						onClick={ () => onChange('right') }
						isPressed={ value === 'right' }
					/>
				</ToolbarGroup>
			</BaseControl>
		);
	}
}
