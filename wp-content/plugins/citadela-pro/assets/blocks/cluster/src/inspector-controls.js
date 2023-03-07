import GradientControl  from '../../components/gradient-control';
import CitadelaRangeControl  from '../../components/range-control';
import BoxShadowControl  from '../../components/box-shadow-control';
import ResponsiveOptionsTabs from '../../components/responsive-options-tabs';
import CustomColorControl from '../../components/custom-color-control';
import defaults from './block.json';

const { __, _x } = wp.i18n;
const { Component, Fragment, useState } = wp.element;
const { TextControl, ColorIndicator, FocalPointPicker, BaseControl, PanelBody, RadioControl, RangeControl, ColorPalette, Button, Placeholder, ToggleControl, SelectControl } = wp.components;
const { MediaUpload, MediaUploadCheck } = wp.blockEditor;

class CustomContainerInspectorControls extends Component {

	render() {
		
		const { attributes, setAttributes, state, setState } = this.props;

		const { 
			backgroundColor,
			backgroundImageFixed,
			focalPoint,
			backgroundType,
			backgroundImage,
			backgroundOverlayType,
			backgroundOverlayColor,
			backgroundOverlayOpacity,
			backgroundOverlayGradient,
			backgroundImageSize,
			backgroundImageRepeat,
			backgroundGradient,
			gradientType,
			gradientDegree,
			borderWidth,
			borderColor,
			borderRadius,
			boxShadow,
			insetShadow,
			mobileVisibility,
			heightUnit,
			heightValue,
			heightDisableOnMobile,
			useBackgroundImageGradient,
			backgroundImageColorType,
			insideSpace,
			
			useResponsiveOptions,
			disableBackgroundImageMobile,
			backgroundImageFixedMobile,
			focalPointMobile,
			backgroundImageMobile,
			backgroundImageSizeMobile,
			breakpointMobile,
			coverHeight,
			inColumn,
			zIndex,
		} = attributes;
		
		const colorsSet = [
			{ color: '#00d1b2' },
			{ color: '#3373dc' },
			{ color: '#209cef' },
			{ color: '#22d25f' },
			{ color: '#ffdd57' },
			{ color: '#ff3860' },
			{ color: '#7941b6' },
			{ color: '#392F43' },
		];


		let responsiveImageSettings = "";
		
		if ( backgroundType == "image" &&  backgroundImage ) {

			// default and desktop responsive options
			if ( ! useResponsiveOptions || ( useResponsiveOptions && state.responsiveTab == "desktop" ) ) {

				const desktopBackgroundImageSizeControl = 
					<SelectControl
						label={ __( 'Background image size', 'citadela-pro' ) }
						value={ backgroundImageSize }
						options={ [
							{ label:  _x( 'Cover', 'label for css property "cover": image in background cover entire place', 'citadela-pro' ), value: 'cover' }, //css "cover"
							{ label:  __( '100% horizontal', 'citadela-pro' ), value: 'full-horizontal' }, //css "100% auto"
							{ label:  __( '100% vertical', 'citadela-pro' ), value: 'full-vertical' }, //css "auto 100%"
							{ label:  _x( 'Default size', 'default size of image in background', 'citadela-pro' ), value: 'auto' }, //css "auto"
						] }
						onChange={ ( value ) => { setAttributes( { backgroundImageSize: value } ) } }
					/>;
				
					
				const desktopFixedBackgroundImageControl = 
					<BaseControl>
						<ToggleControl 
							label={ __( 'Fixed background image', 'citadela-pro' ) }
							checked={ backgroundImageFixed }
							onChange={ (value) => { setAttributes( { backgroundImageFixed: value } ) }}
						/>
					</BaseControl>;

				const desktopBackgroundImageRepeatControl = 
					<SelectControl
						label={ __( 'Background image repeat', 'citadela-pro' ) }
						value={ backgroundImageRepeat }
						options={ [
							{ label:  _x( 'No repeat', 'label for css property "no-repeat": do not repeat image in background', 'citadela-pro' ), value: 'no-repeat' },
							{ label:  _x( 'Repeat', 'label for css property "repeat": repeat image in background', 'citadela-pro' ), value: 'repeat' },
							{ label:  _x( 'Repeat vertically', 'label for css property "repeat-y": repeat vertically image in background', 'citadela-pro' ), value: 'repeat-y' },
							{ label:  _x( 'Repeat horizontally', 'label for css property "repeat-x": repeat horizontally image in background', 'citadela-pro' ), value: 'repeat-x' },
						] }
						onChange={ ( value ) => { setAttributes( { backgroundImageRepeat: value } ) } }
					/>
				
				const desktopBackgroundImageFocalControl = 
					<BaseControl
						label={ __( 'Background position', 'citadela-pro' ) }
						>
						<FocalPointPicker key='desktopfocal'
							url={ backgroundImage.url }
							value={ focalPoint }
							onChange={ ( value ) => setAttributes( { focalPoint: value } ) }
						/>
					</BaseControl>;

					
				responsiveImageSettings = 
					<Fragment>
						{ desktopBackgroundImageSizeControl }
					
						{ backgroundImageSize != 'cover' && 
							<Fragment>{ desktopBackgroundImageRepeatControl }</Fragment>
						}

						{ desktopFixedBackgroundImageControl }

						{! backgroundImageFixed &&
							<Fragment>{ desktopBackgroundImageFocalControl }</Fragment>
						}

						

					</Fragment>;
			}


			// mobile responsive options
			if ( useResponsiveOptions && state.responsiveTab == "mobile" ) {
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage : backgroundImageMobile,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
					disableBackgroundImage: disableBackgroundImageMobile,
				}
				const mobileDisableBackgroundImageControl = 
					<BaseControl
						className="citadela-mobile-control"
						>
						<ToggleControl 
							label={ __( 'Disable image', 'citadela-pro' ) }
							checked={ mobileAttributes.disableBackgroundImage }
							onChange={ (value) => { setAttributes( { disableBackgroundImageMobile: value } ) }}
						/>
					</BaseControl>;
				
				const mobileBackgroundImageSizeControl = 
					<SelectControl
						className="citadela-mobile-control"
						label={ __( 'Background image size', 'citadela-pro' ) }
						value={ mobileAttributes.backgroundImageSize }
						options={ [
							{ label:  _x( 'Cover', 'label for css property "cover": image in background cover entire place', 'citadela-pro' ), value: 'cover' }, //css "cover"
							{ label:  __( '100% horizontal', 'citadela-pro' ), value: 'full-horizontal' }, //css "100% auto"
							{ label:  __( '100% vertical', 'citadela-pro' ), value: 'full-vertical' }, //css "auto 100%"
							{ label:  _x( 'Default size', 'default size of image in background', 'citadela-pro' ), value: 'auto' }, //css "auto"
						] }
						onChange={ ( value ) => { setAttributes( { backgroundImageSizeMobile: value } ) } }
					/>;
				
					
				const mobileFixedBackgroundImageControl = 
					<BaseControl
						className="citadela-mobile-control"
						>
						<ToggleControl 
							label={ __( 'Fixed background image', 'citadela-pro' ) }
							checked={ mobileAttributes.backgroundImageFixed }
							onChange={ (state) => { setAttributes( { backgroundImageFixedMobile: state } ) }}
						/>
					</BaseControl>;

				const mobileBackgroundImageFocalControl = 
					<BaseControl
						label={ __( 'Background position', 'citadela-pro' ) }
						className="citadela-mobile-control"
						>
						<FocalPointPicker key='mobilefocal'
							url={ mobileAttributes.backgroundImage.url }
							value={ mobileAttributes.focalPoint }
							onChange={ ( value ) => setAttributes( { focalPointMobile: value } ) }
						/>
					</BaseControl>;

				responsiveImageSettings = 
					<Fragment>

						<ClusterMobileWidth attributes={ attributes } setAttributes={ setAttributes } />

						{ mobileDisableBackgroundImageControl }

						{ ! mobileAttributes.disableBackgroundImage && 
						<>
							{ mobileBackgroundImageSizeControl }

							{ mobileFixedBackgroundImageControl }

							{ ! mobileAttributes.backgroundImageFixed &&
								<Fragment>{ mobileBackgroundImageFocalControl }</Fragment>
							}
						</>
						}
					</Fragment>;

				
			}

		}
		
		const generalBackgroundImageControls = 
		<>
			
			<RadioControl
				selected={ backgroundImageColorType }
				label={ __('Background color type', 'citadela-pro') }
				options={ [
					{ label:  __('Single color', 'citadela-pro'), value: 'color' },
					{ label:  __('Gradient', 'citadela-pro'), value: 'gradient' },
				] }
				onChange={ ( option ) => { setAttributes( { backgroundImageColorType: option } ) } }
			/>

			{ backgroundImageColorType == "color" &&
				<CustomColorControl 
					label={ __('Background color', 'citadela-pro') }
					color={ backgroundColor }
					onChange={ ( value ) => { setAttributes( { backgroundColor: value } ); } }
					allowReset
				/>
			}

			{ backgroundImageColorType == "gradient" &&
				<GradientControl 
					label={ __('Background gradient', 'citadela-pro') }
					help={ __('Gradient under image, useful for .png images in background.', 'citadela-pro') }
					gradient={ backgroundGradient }

					onFirstColorChange={ ( value ) => { 
						setAttributes( { 
							backgroundGradient: { 
								first:  value == undefined ? defaults.attributes.backgroundGradient.default.first : value,
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
								second:  value == undefined ? defaults.attributes.backgroundGradient.default.second : value,
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
								degree:  value == undefined ? defaults.attributes.backgroundGradient.default.degree : value,
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
		
		
		return (
			<Fragment>

				<PanelBody 
					title={ __('Background Settings', 'citadela-pro') }
					initialOpen={true}
					className="citadela-panel"
				>

					
					<BaseControl
							label={ __( 'Select background type', 'citadela-pro' ) }
						>
						<SelectControl
							value={ backgroundType }
							options={ [
								{ label: __( 'None', 'citadela-pro' ), value: 'none' },
								{ label: __( 'Image', 'citadela-pro' ), value: 'image' },
								{ label: __( 'Single color', 'citadela-pro' ), value: 'color' },
								{ label: __( 'Gradient', 'citadela-pro' ), value: 'gradient' },
							] }
							onChange={ ( value ) => { setAttributes( { backgroundType: value } ) } }
						/>
					</BaseControl>
					

					{ ( backgroundType == "image" &&  !backgroundImage ) &&
							<Fragment>
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( media ) => {
											setAttributes( {
												backgroundImage: media,
											} ) }
										}
										allowedTypes={ ['image'] }
										value={ backgroundImage ? backgroundImage.id : '' }
										render={ ( { open } ) => (
											<Placeholder
											icon='format-image'
												label={ __("Background image", 'citadela-pro') }
											>
												<Button
													className='button button-large'
													onClick={ open }
													>
													{ __( 'Select image', 'citadela-pro' ) }
												</Button>
											</Placeholder>
										) }
									/>
								</MediaUploadCheck>
							</Fragment>
					}

					{ ( backgroundType == "image" &&  backgroundImage ) &&	
						<Fragment>
							
							<ToggleControl 
								label={ __( 'Use responsive options', 'citadela-pro' ) }
								checked={ useResponsiveOptions }
								onChange={ (state) => { setAttributes( { useResponsiveOptions: state } ) }}
							/>

							{ useResponsiveOptions 
								? 
									<>
									<div class="citadela-responsive-settings-holder">
										<ResponsiveOptionsTabs activeTab={ state.responsiveTab } onChange={ (value) => { setState( { responsiveTab: value } ) } } />
										{ responsiveImageSettings }
									</div>
									{ generalBackgroundImageControls }
									</>
								:
									<Fragment>
										{ responsiveImageSettings }
										{ generalBackgroundImageControls }
									</Fragment>
							}
							
						</Fragment>
					}


					{ backgroundType == "color" &&	
						<CustomColorControl 
							label={ __('Background color', 'citadela-pro') }
							color={ backgroundColor }
							onChange={ ( value ) => { setAttributes( { backgroundColor: value } ); } }
							allowReset
						/>
					}


					{ backgroundType == "gradient" &&	
						<GradientControl 
							gradient={ backgroundGradient }

							onFirstColorChange={ ( value ) => { 
								setAttributes( { 
									backgroundGradient: { 
										first:  value == undefined ? defaults.attributes.backgroundGradient.default.first : value,
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
										second:  value == undefined ? defaults.attributes.backgroundGradient.default.second : value,
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
										degree:  value == undefined ? defaults.attributes.backgroundGradient.default.degree : value,
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
				</PanelBody>
				
				{/* show background overlay settings for Image background */}
				
				{/*{ ( ( !useResponsiveOptions || ( useResponsiveOptions && state.responsiveTab == "desktop" ) ) &&  backgroundType == "image" && backgroundImage ) &&*/}
				{ backgroundType == "image" && backgroundImage &&
				<PanelBody 
					title={ __('Background Image Overlay', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>
					
					<RadioControl
						selected={ backgroundOverlayType }
						options={ [
							{ label:  __('Single color overlay', 'citadela-pro'), value: 'color' },
							{ label:  __('Gradient overlay', 'citadela-pro'), value: 'gradient' },
						] }
						onChange={ ( option ) => { setAttributes( { backgroundOverlayType: option } ) } }
					/>

					{ backgroundOverlayType == "color" &&
						<CustomColorControl 
							label={ __('Overlay color', 'citadela-pro') }
							color={ backgroundOverlayColor }
							onChange={ ( value ) => { setAttributes( { backgroundOverlayColor: value } ); } }
							disableAlpha
							allowReset
						/>
					}

					{ backgroundOverlayType == "gradient" &&
						<GradientControl 
							label={ __('Overlay gradient', 'citadela-pro') }
							gradient={ backgroundOverlayGradient }

							onFirstColorChange={ ( value ) => { 
								setAttributes( { 
									backgroundOverlayGradient: { 
										first:  value == undefined ? defaults.attributes.backgroundOverlayGradient.default.first : value,
										second: backgroundOverlayGradient.second,
										degree: backgroundOverlayGradient.degree,
										type: backgroundOverlayGradient.type,
									} 
								} )
							} }
							onSecondColorChange={ ( value ) => { 
								setAttributes( { 
									backgroundOverlayGradient: { 
										first: backgroundOverlayGradient.first,
										second:  value == undefined ? defaults.attributes.backgroundOverlayGradient.default.second : value,
										degree: backgroundOverlayGradient.degree,
										type: backgroundOverlayGradient.type,
									} 
								} )
							} }						
							onDegreeChange={ ( value ) => { 
								setAttributes( { 
									backgroundOverlayGradient: { 
										first: backgroundOverlayGradient.first,
										second: backgroundOverlayGradient.second,
										degree:  value == undefined ? defaults.attributes.backgroundOverlayGradient.default.degree : value,
										type: backgroundOverlayGradient.type,
									} 
								} )
							} }
							onGradientTypeChange={ ( value ) => { 
								setAttributes( { 
									backgroundOverlayGradient: { 
										first: backgroundOverlayGradient.first,
										second: backgroundOverlayGradient.second,
										degree: backgroundOverlayGradient.degree,
										type:  value ? "radial" : "linear",
									} 
								} )
							} }
						/>
					}

					<BaseControl
						label={ __('Overlay opacity', 'citadela-pro') }
					>
						<RangeControl
							value={ backgroundOverlayOpacity }
							onChange={ ( value ) => setAttributes( { backgroundOverlayOpacity: value } ) }
							min={ 0 }
							max={ 100 }
							step={ 1 }
							required
						/>
					</BaseControl>
				</PanelBody>
				}


				<PanelBody 
					title={ __('Border Settings', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>

					<CustomColorControl 
						label={ __('Border color', 'citadela-pro') }
						color={ borderColor }
						onChange={ ( value ) => { setAttributes( { borderColor: value } ); } }
						allowReset
					/>
						
					<CitadelaRangeControl
						label={ __('Border width', 'citadela-pro') }
						rangeValue={ borderWidth }
						onChange={ ( value ) => { setAttributes( { borderWidth: value == undefined ? defaults.attributes.borderWidth.default : value } ); } }
						min={ 0 }
						max={ 15 }
						initial={ 0 }
						allowReset
					/>

					<CitadelaRangeControl
						label={ __('Border radius', 'citadela-pro') }
						rangeValue={ borderRadius }
						onChange={ ( value ) => { setAttributes( { borderRadius: value == undefined ? defaults.attributes.borderRadius.default : value } ); } }
						min={ 0 }
						max={ 50 }
						initial={ 0 }
						allowReset
					/>
				</PanelBody>


				<PanelBody 
					title={ __('Shadow Settings', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>
					<BoxShadowControl
						value={ boxShadow === undefined ? {} : boxShadow }
						onChange={ (value) => { setAttributes( { boxShadow: value } ) } }
						allowInsetShadow
					/>
				</PanelBody>
				

				<PanelBody 
					title={ __('Height Settings', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>
					{ inColumn &&
						<ToggleControl 
							label={ __( 'Cover column height', 'citadela-pro' ) }
							help={ __( 'Works when Cluster is the only block in a column.', 'citadela-directory' ) }
							checked={ coverHeight }
							onChange={ (value) => { setAttributes( { coverHeight: value } ) }}
						/>
					}
					<ClusterHeightOptions attributes={ attributes } setAttributes={ setAttributes } />

				</PanelBody>
				
				<ClusterWidthSettingsPanel attributes={ attributes } setAttributes={ setAttributes } />

				<PanelBody 
					title={ __('Mobile Settings', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>
					<BaseControl
						label={ __('Visibility on mobile', 'citadela-pro') }
						help={ __( 'Show or hide Cluster block only on mobile screen size.', 'citadela-pro' ) }
					>
						<RadioControl
							selected={ mobileVisibility }
							options={ [
								{ label: __( 'Show always', 'citadela-pro' ), value: 'always' },
								{ label: __( 'Show only on mobile', 'citadela-pro' ), value: 'show' },
								{ label: __( 'Hide only on mobile', 'citadela-pro' ), value: 'hide' },
							] }
							onChange={ ( option ) => { setAttributes( { mobileVisibility: option } ) } }
						/>
					</BaseControl>
				</PanelBody>
				
				
				<PanelBody 
					title={ __('Spacing Settings', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>
					<RadioControl
						label={ __('Forced inside space', 'citadela-pro') }
						selected={ insideSpace }
						options={ [
							{ label:  __('None', 'citadela-pro'), value: 'none' },
							{ label:  "0", value: 'zero' },
							{ label:  __('Small', 'citadela-pro'), value: 'small' },
							{ label:  __('Large', 'citadela-pro'), value: 'large' },
						] }
						onChange={ ( option ) => { setAttributes( { insideSpace: option } ) } }
					/>
				</PanelBody>

			</Fragment>
		);
	}
}























const ClusterHeightOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		heightUnit,
		heightValue,
	} = attributes;

	const [ inputHeightValue, setInputHeightValue ] = useState( heightValue );
		
	let unitDefaults = [];
	unitDefaults["px"] = 400;
	unitDefaults["vh"] = 100;
	unitDefaults["vw"] = 30;

	let unitStep = 1;
	if( heightUnit != "px" ){
		unitStep = 0.1;
	}

	return (
		<Fragment>
			<BaseControl 
				label={ __('Height unit', 'citadela-pro') } 
				>
				<SelectControl
					value={ heightUnit }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vh', value: 'vh' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setInputHeightValue( unitDefaults[ value ] );
						setAttributes( { 
							heightUnit: value,
							heightValue: unitDefaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Mininum height', 'citadela-pro' ) + ( heightValue !== undefined ? `: ${heightValue}${heightUnit}` : '' ) } 
				id="spacer-height"
			>
				<input
					className="components-text-control__input"
					type="number"
					onChange={ ( event ) => {
						let newHeight = event.target.value;
						setInputHeightValue(newHeight);
						if ( newHeight == '' ) {
							// height in input is not defined, input is empty, set empty value for input and 0 for height
							setInputHeightValue( '' );
							newHeight = undefined;
						}
						if(heightUnit != "px"){
							setAttributes( { heightValue: newHeight ? parseFloat(newHeight) : newHeight } );
						}else{
							setAttributes( { heightValue: newHeight ? parseInt(newHeight) : newHeight } );
						}
					} }
					value={ inputHeightValue }
					step={ unitStep }
				/>
			</BaseControl>

		</Fragment>
	)
}

const ClusterMobileWidth = ({
	attributes,
	setAttributes,
}) => {
	const { 
		breakpointMobile,
	} = attributes;

	const [ inputBreakpointMobileValue, setBreakpointMobileInputValue ] = useState( breakpointMobile );

	return (
		<BaseControl 
			label={ __( 'Mobile width breakpoint', 'citadela-pro' ) }
			help={ __( 'Responsive options applied under screen width', 'citadela-pro' ) + ` ${breakpointMobile}px` }
			id="mobile-width"
		>
			<TextControl
				type="number"
				value={ inputBreakpointMobileValue }
				onChange={ ( value ) => {
					let newValue = value;
					setBreakpointMobileInputValue(newValue);
					if ( value == '' ) {
						newValue = 600;
					}
					setAttributes( { breakpointMobile: newValue ? parseInt( newValue ) : newValue } );
				} }
				step={ 1 }
			/>
		</BaseControl>
	)
}


const ClusterWidthSettingsPanel = ({
	attributes,
	setAttributes,
}) => {
	const { 
		widthContent,
		widthWide,
		widthFull,
		sectionsSize,
		useCustomWidth,
	} = attributes;

	//width settings are available only if Citadela Pro plugin is active
	const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;
	if ( ! activeProPlugin ) return "";

	const data = {
		content: {
			min: 300,
			max: 960,
			default: 768,
		},
		wide: {
			min: 1200,
			max: 1400,
			default: 1200,
		},
		fullwidth: {
			min: 1500,
			max: 1920,
			default: 1920,
		},
	};
	
	return (
		<PanelBody 
				title={ __('Width Settings', 'citadela-pro') }
				initialOpen={false}
				className="citadela-panel"
			>
				
				<BaseControl>
					<ToggleControl 
						label={ __( 'Use custom width settings', 'citadela-pro' ) }
						checked={ useCustomWidth }
						onChange={  (value ) => { setAttributes( { useCustomWidth: value } ) }}
					/>
				</BaseControl>

				{ useCustomWidth &&
					<>
						<CitadelaRangeControl
							label={ __('Content size width', 'citadela-pro') }
							help={ __('Available range', 'citadela-pro') + `: ${data.content.min}px - ${data.content.max}px` }
							className={ sectionsSize === 'content' ? 'citadela-highlight-control' : "" }
							rangeValue={ widthContent }
							onChange={ ( value ) => { setAttributes( { widthContent: value } ) } }
							min={ data.content.min }
							max={ data.content.max }
							initial={ data.content.default }
							allowReset
						/>

						<CitadelaRangeControl
							label={ __('Wide size width', 'citadela-pro') }
							help={ __('Available range', 'citadela-pro') + `: ${data.wide.min}px - ${data.wide.max}px` }
							className={ sectionsSize === 'wide' ? 'citadela-highlight-control' : "" }
							rangeValue={ widthWide }
							onChange={ ( value ) => { setAttributes( { widthWide: value } ) } }
							min={ data.wide.min }
							max={ data.wide.max }
							initial={ data.wide.default }
							allowReset
						/>

						<CitadelaRangeControl
							label={ __('Full size width', 'citadela-pro') }
							help={ __('Available range', 'citadela-pro') + `: ${data.fullwidth.min}px - ${data.fullwidth.max}px` }
							className={ sectionsSize === 'fullwidth' ? 'citadela-highlight-control' : "" }
							rangeValue={ widthFull }
							onChange={ ( value ) => { setAttributes( { widthFull: value } ) } }
							min={ data.fullwidth.min }
							max={ data.fullwidth.max }
							initial={ data.fullwidth.default }
							allowReset
						/>
					</>
				}
		</PanelBody>
	)
}

export default CustomContainerInspectorControls;
