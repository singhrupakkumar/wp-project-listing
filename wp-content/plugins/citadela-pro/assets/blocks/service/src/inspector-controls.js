import FontAwesomePicker from "../../components/fontawesome-picker.js";

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { TextControl, SelectControl, BaseControl, PanelBody, ColorPalette, Button, Placeholder, ToggleControl } = wp.components;
const { URLInput, MediaUpload } = wp.blockEditor;

class ServiceInspectorControls extends Component {

	render() {
		const { 
			serviceImageObject,
			serviceBlockBackgroundColor,
			serviceBlockTitleColor,
			serviceBlockTextColor,
			serviceDesignType,
			serviceDesignIconColor,
			serviceDesignIconClass,
			serviceLinkNewTab,
			serviceLink,
			serviceReadMoreText,
		} = this.props.attributes; 	

		const customColors = [
			{ color: '#00d1b2' },
			{ color: '#3373dc' },
			{ color: '#209cef' },
			{ color: '#22d25f' },
			{ color: '#ffdd57' },
			{ color: '#ff3860' },
			{ color: '#7941b6' },
			{ color: '#392F43' },
		];
				
		return (
			<Fragment>
				<PanelBody
						title={__('Colors', 'citadela-pro')}
						initialOpen={false}
						className="citadela-panel"
					>
						{ serviceDesignType == 'icon' &&
							<BaseControl
								label={__('Icon color', 'citadela-pro')}
							>
								<ColorPalette
									value={ serviceDesignIconColor }
									onChange={ (value) => { this.props.setAttributes( { serviceDesignIconColor: value } ); } }
									colors={customColors}
								/>
							</BaseControl>
						}
						<BaseControl
							label={__('Background color', 'citadela-pro')}
							//help={}
						>
							<ColorPalette
								value={ serviceBlockBackgroundColor }
								onChange={ (value) => { this.props.setAttributes( { serviceBlockBackgroundColor: value } ); } }
								colors={customColors}
							/>
						</BaseControl>
						<BaseControl
							label={__('Title color', 'citadela-pro')}
							//help={}
						>
							<ColorPalette
								value={ serviceBlockTitleColor }
								onChange={ (value) => { this.props.setAttributes( { serviceBlockTitleColor: value } ); } }
								colors={customColors}
							/>
						</BaseControl>
						<BaseControl
							label={__('Text color', 'citadela-pro')}
							//help={}
						>
							<ColorPalette
								value={ serviceBlockTextColor }
								onChange={ (value) => { this.props.setAttributes( { serviceBlockTextColor: value } ); } }
								colors={customColors}
							/>
						</BaseControl>
					
				</PanelBody>

				<PanelBody 
					title={__('Link', 'citadela-pro')}
					initialOpen={false}
					className="citadela-panel"
				>
					<BaseControl>
						<ToggleControl
							label={__('Open in new window', 'citadela-pro')}
							checked={ serviceLinkNewTab }
							onChange={ ( checked ) => this.props.setAttributes( { serviceLinkNewTab: checked } ) }
						/>
					</BaseControl>

				</PanelBody>
			</Fragment>
		);
	}
}

export default ServiceInspectorControls;
