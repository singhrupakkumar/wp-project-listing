import AlignToolbar from '../../components/toolbar-alignment';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { ToolbarGroup, ToolbarItem, ToolbarButton } = wp.components;
const { BlockControls } = wp.blockEditor;

export default class PriceTableBlockControls extends Component {

	render() {
		const { attributes, setAttributes } = this.props;
		const { 
			featuredTable,
			showOldPrice,
			showButton,
			alignment
		} = attributes; 	
		
		return (
			<BlockControls key='controls'>
				
				<ToolbarGroup>
					<ToolbarItem as={ ( toggleProps ) => ( 
						<AlignToolbar 
							label={__('Price table alignment', 'citadela-pro')} 
							value={alignment} 
							onChange={ ( value ) => setAttributes( { alignment: value } ) } 
							toggleProps={ toggleProps }
						/>
					)}/>
				</ToolbarGroup>

				
				
				<ToolbarGroup>
					<ToolbarButton
						icon="star-filled"
						label={ featuredTable ? __("Disable featured table", 'citadela-pro') : __("Enable featured table", 'citadela-pro') }
						isPressed={ featuredTable }	
						onClick={ () => setAttributes( { featuredTable: !featuredTable } ) }
					/>
					<ToolbarButton
						icon="tag"
						label={ showOldPrice ? __("Hide discount price", 'citadela-pro') : __("Show discount price", 'citadela-pro') }
						isPressed={ showOldPrice }		
						onClick={ () => setAttributes( { showOldPrice: !showOldPrice } ) }
					/>
					<ToolbarButton
						icon="admin-links"
						label={ showButton ? __("Hide button with link", 'citadela-pro') : __("Show button with link", 'citadela-pro') }
						isPressed={ showButton }	
						onClick={ () => setAttributes( { showButton: !showButton } ) }
					/>
					
				</ToolbarGroup>
				
			</BlockControls>
		);
	}
}