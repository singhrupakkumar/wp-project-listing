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
const { PanelBody, SelectControl, ToggleControl } = wp.components;

export default class CustomInspectorControls extends Component {

	render() {
		
		const { attributes, setAttributes } = this.props;

		const { 
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
			hideInputLocation,
		} = attributes;

		const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;

		return (
			<Fragment>
				<PanelBody 
					title={ __('Form settings', 'citadela-directory') }
					initialOpen={true}
					className="citadela-panel"
				>
					<ToggleControl
						label={__('Hide Location input', 'citadela-directory')}
						checked={ hideInputLocation }
						onChange={ ( checked ) => setAttributes( { hideInputLocation: checked } ) }
					/>

				</PanelBody>

				{ activeProPlugin &&
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
				}


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
				
				{ activeProPlugin &&
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
				}

			</Fragment>
		);
	}
}