/**
 * Internal dependencies
 */
import CitadelaRangeControl  from '../../components/range-control';
import BoxShadowControl  from '../../components/box-shadow-control';
import CustomColorControl from '../../components/custom-color-control';
import GradientControl  from '../../components/gradient-control';

/**
 * WordPress dependencies
 */
const { __, _x } = wp.i18n;
const { Component, Fragment } = wp.element;
const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;

export default class CustomInspectorControls extends Component {
	
	constructor() {
        super( ...arguments );
        this.state = {
            childBlock: null,
        };
    }
	
	render() {

		const { attributes, setAttributes, props } = this.props;
		const { 
			withAdvancedFilters,
			geoDistanceLabel,
			geoDistanceSubmitLabel,
			geoDisableLabel,
			useGeolocationInput,
			geoUnit,
            geoMax,
            geoStep,
			buttonBackgroundColor,
			buttonTextColor,
			backgroundBlur,
			blurRadius,
			backgroundType,
			backgroundColorType,
			backgroundColor,
			backgroundGradient,
			borderColor,
			borderRadius, 
			borderWidth,
			boxShadowType,
			boxShadow,
		} = attributes;
		
		const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;
		
		// make sure the child block is loaded to allow us set attributes for it
		if( withAdvancedFilters && ! this.state.childBlock ){
			const childBlock = wp.data.select('core/block-editor').getBlocksByClientId(this.props.props.clientId)[0].innerBlocks[0];
			this.setState( { childBlock: childBlock } );
		}
		
		return (
			<Fragment>
				<PanelBody 
					title={ __('Filters settings', 'citadela-directory') }
					initialOpen={false}
					className="citadela-panel"
				>
					<ToggleControl
						label={__('Show advanced filters', 'citadela-directory')}
						help={__('Advanced Filters block displayed within search form', 'citadela-directory')}
						checked={ withAdvancedFilters }
						onChange={ ( checked ) => setAttributes( { withAdvancedFilters: checked } ) }
					/>
					{ withAdvancedFilters &&
					<>
				
						<TextControl
							label={ __('Submit filters button label', 'citadela-directory') }
							value={ this.state.childBlock ? wp.data.select('core/block-editor').getBlockAttributes(this.state.childBlock.clientId).filters_submit_label : "" }
							onChange={ ( value ) => { 
								//we need to update attribute for child block Advanced Filters
								wp.data.dispatch('core/block-editor').updateBlockAttributes(this.state.childBlock.clientId, { filters_submit_label: value } );
								this.forceUpdate();
							} }
						/>

						<TextControl
							label={ __('Disable filters button label', 'citadela-directory') }
							value={ this.state.childBlock ? wp.data.select('core/block-editor').getBlockAttributes(this.state.childBlock.clientId).filters_disable_label : "" }
							onChange={ ( value ) => { 
								//we need to update attribute for child block Advanced Filters
								wp.data.dispatch('core/block-editor').updateBlockAttributes(this.state.childBlock.clientId, { filters_disable_label: value } );
								this.forceUpdate();						
							} }
						/>


					</>
					}
				</PanelBody>
				<PanelBody 
					title={ __('Geolocation search settings', 'citadela-directory') }
					initialOpen={false}
					className="citadela-panel"
				>
					<ToggleControl
						label={__('Use geolocation search', 'citadela-directory')}
						checked={ useGeolocationInput }
						onChange={ ( checked ) => setAttributes( { useGeolocationInput: checked } ) }
					/>
					{ useGeolocationInput &&
					<>
						<SelectControl
							label={ __( 'Distance unit', 'citadela-directory' ) }
							value={ geoUnit }
							options={ [
								{ label: __( 'Kilometers', 'citadela-directory' ), value: 'km' },
								{ label: __( 'Miles', 'citadela-directory' ), value: 'mi' },
							] }
							onChange={ ( value ) => { setAttributes( { geoUnit: value } ); }
							}
						/>

						<SelectControl
							label={ __( 'Step', 'citadela-directory' ) }
							value={ geoStep }
							options={ [
								{ label: __( '1', 'citadela-directory' ), value: 1 },
								{ label: __( '0.1', 'citadela-directory' ), value: 0.1 },
							] }
							onChange={ ( value ) => { setAttributes( { geoStep: value } ); }
							}
						/>

						<CitadelaRangeControl
							label={ __('Maximum distance', 'citadela-directory') + `: ${geoMax}${geoUnit}`}
							rangeValue={ geoMax }
							onChange={ ( value ) => { setAttributes( { geoMax: value } ); } }
							min={ 1 }
							max={ 1000 }
							initial={ 10 }
							allowReset
						/>
						
						<TextControl
							label={ __('Distance option label', 'citadela-directory') }
							value={ geoDistanceLabel }
							onChange={ ( value ) => { setAttributes( { geoDistanceLabel: value } ); } }
						/>

						<TextControl
							label={ __('Submit distance button label', 'citadela-directory') }
							value={ geoDistanceSubmitLabel }
							onChange={ ( value ) => { setAttributes( { geoDistanceSubmitLabel: value } ); } }
						/>

						<TextControl
							label={ __('Disable geolocation button label', 'citadela-directory') }
							value={ geoDisableLabel }
							onChange={ ( value ) => { setAttributes( { geoDisableLabel: value } ); } }
						/>


					</>
					}
				</PanelBody>

				{ activeProPlugin &&
					<>
					<PanelBody 
						title={ __('Background settings', 'citadela-directory') }
						initialOpen={false}
						className="citadela-panel"
					>
						<SelectControl
							label={ __( 'Background', 'citadela-directory' ) }
							value={ backgroundType }
							options={ [
								{ label: __( 'None', 'citadela-directory' ), value: 'none' },
								{ label: __( 'With background', 'citadela-directory' ), value: 'background' },
								{ label: __( 'Background on collapsed form', 'citadela-directory' ), value: 'background-collapsed' },
							] }
							onChange={ ( value ) => { setAttributes( { backgroundType: value } ); }
							}
						/>

						{ backgroundType != 'none' &&
						<>	
							<ToggleControl
								label={__('Blur background', 'citadela-directory')}
								checked={ backgroundBlur }
								onChange={ ( checked ) => setAttributes( { backgroundBlur: checked } ) }
							/>
							{ backgroundBlur && 
								<CitadelaRangeControl
									label={ __('Blur radius', 'citadela-directory') + `: ${blurRadius}px`}
									rangeValue={ blurRadius }
									onChange={ ( value ) => { setAttributes( { blurRadius: value } ); } }
									min={ 0 }
									max={ 50 }
									initial={ 10 }
									allowReset
								/>
							}

							<SelectControl
								label={ __( 'Background type', 'citadela-directory' ) }
								value={ backgroundColorType }
								options={ [
									{ label: __( 'Solid color', 'citadela-directory' ), value: 'solid' },
									{ label: __( 'Gradient', 'citadela-directory' ), value: 'gradient' },
								] }
								onChange={ ( value ) => { setAttributes( { backgroundColorType: value } ); }
								}
							/>

							{ backgroundColorType == 'solid' &&
								<CustomColorControl 
									label={ __('Background color', 'citadela-directory') }
									color={ backgroundColor }
									onChange={ (value) => { setAttributes( { backgroundColor: value } ); } }
									
								/>
							}
							
							{ backgroundColorType == 'gradient' &&
								<GradientControl 
									label={ __('Background gradient', 'citadela-blocks') }
									gradient={ backgroundGradient }
									onFirstColorChange={ ( value ) => { 
										setAttributes( { 
											backgroundGradient: { 
												first:  value,
												second: backgroundGradient.second,
												degree: backgroundGradient.degree,
												type: backgroundGradient.type,
											} 
										} )
									} }
									onSecondColorChange={ ( value ) => { 
										setAttributes( { 
											backgroundGradient: { 
												first: backgroundGradient.first,
												second:  value,
												degree: backgroundGradient.degree,
												type: backgroundGradient.type,
											} 
										} )
									} }						
									onDegreeChange={ ( value ) => { 
										setAttributes( { 
											backgroundGradient: { 
												first: backgroundGradient.first,
												second: backgroundGradient.second,
												degree: value == undefined ? 90 : value,
												type: backgroundGradient.type,
											} 
										} )
									} }
									onGradientTypeChange={ ( value ) => { 
										setAttributes( { 
											backgroundGradient: { 
												first: backgroundGradient.first,
												second: backgroundGradient.second,
												degree: backgroundGradient.degree,
												type:  value ? "radial" : "linear",
											} 
										} )
									} }
								/>
							}

						</>
						}

					</PanelBody>
				
					<PanelBody 
						title={ __('Border settings', 'citadela-directory') }
						initialOpen={false}
						className="citadela-panel"
					>
						<SelectControl
							label={ __( 'Border width', 'citadela-directory' ) }
							value={ borderWidth }
							options={ [
								{ label: __( 'No border', 'citadela-directory' ), value: 'none' },
								{ label: __( 'Thin border', 'citadela-directory' ), value: 'thin' },
								{ label: __( 'Thick border', 'citadela-directory' ), value: 'thick' },
							] }
							onChange={ ( value ) => { setAttributes( { borderWidth: value } ); }
							}
						/>

						{ borderWidth != 'none' &&
							<CustomColorControl 
								label={ __('Border color', 'citadela-directory') }
								color={ borderColor }
								onChange={ (value) => { setAttributes( { borderColor: value } ); } }
								disableAlpha
							/>
						}

						<CitadelaRangeControl
							label={ __('Border radius', 'citadela-directory') }
							rangeValue={ borderRadius }
							onChange={ ( value ) => { setAttributes( { borderRadius: value } ); } }
							min={ 0 }
							max={ 40 }
							initial={ 40 }
							allowNoValue
							allowReset
						/>
					</PanelBody>

					<PanelBody 
						title={ __('Shadow settings', 'citadela-directory') }
						initialOpen={false}
						className="citadela-panel"
					>
						<SelectControl
							label={ __( 'Shadow', 'citadela-directory' ) }
							value={ boxShadowType }
							options={ [
								{ label: __( 'No shadow', 'citadela-directory' ), value: 'none' },
								{ label: __( 'Default shadow', 'citadela-directory' ), value: 'default' },
								{ label: __( 'Custom shadow', 'citadela-directory' ), value: 'custom' },
							] }
							onChange={ ( value ) => { setAttributes( { boxShadowType: value } ); } }
							/>

						{ boxShadowType === 'custom' &&
							<BoxShadowControl
								value={ boxShadow === undefined ? {} : boxShadow }
								onChange={ (value) => { setAttributes( { boxShadow: value } ) } }
								allowColorReset={ false }
							/>
						}
					</PanelBody>

					<PanelBody 
						title={ __('Button settings', 'citadela-directory') }
						initialOpen={false}
						className="citadela-panel"
					>
						<CustomColorControl 
							label={ __('Background color', 'citadela-directory') }
							color={ buttonBackgroundColor }
							onChange={ (value) => { setAttributes( { buttonBackgroundColor: value } ); } }
						/>
						<CustomColorControl 
							label={ __('Text color', 'citadela-directory') }
							color={ buttonTextColor }
							onChange={ (value) => { setAttributes( { buttonTextColor: value } ); } }
						/>
					</PanelBody>
				</>
				}
				
			</Fragment>
		);
	}
}