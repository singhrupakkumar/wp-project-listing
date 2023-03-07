const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { BlockVerticalAlignmentToolbar } = wp.blockEditor;
const { ToolbarGroup, ToolbarButton } = wp.components;

class CustomContainerBlockControls extends Component {

	render() {
		const { setAttributes } = this.props;
		const { 
			sectionsSize,
			verticalAlignment,
			heightValue,
			coverHeight,
			inColumn,
		} = this.props.attributes; 	
		
		return (
			<Fragment>
				<ToolbarGroup>
					
					<ToolbarButton
						label={ __('Content size', 'citadela-pro') }
						icon="align-center"
						isPressed={ sectionsSize === 'content' }
						onClick={ () => setAttributes( { sectionsSize: 'content' } ) }
					/>
					<ToolbarButton
						label={ __('Wide size', 'citadela-pro') }
						icon="align-wide"
						isPressed={ sectionsSize === 'wide' }
						onClick={ () => setAttributes( { sectionsSize: 'wide' } ) }
					/>
					<ToolbarButton
						label={ __('Fullwidth size', 'citadela-pro') }
						icon="align-full-width"
						isPressed={ sectionsSize === 'fullwidth' }
						onClick={ () => setAttributes( { sectionsSize: 'fullwidth' } ) }
					/>

				</ToolbarGroup>
				
				{ ( heightValue !== undefined || ( inColumn && coverHeight ) ) &&
					<BlockVerticalAlignmentToolbar
						onChange={ ( value ) => { setAttributes( { verticalAlignment: value } ) } }
						value={ verticalAlignment }
					/>
				}
			</Fragment>
		);
	}
}

export default CustomContainerBlockControls;
