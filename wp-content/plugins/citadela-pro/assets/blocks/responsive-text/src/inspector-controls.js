import CitadelaRangeControl  from '../../components/range-control';
import HtmlTagControl from '../../components/html-tag-control';
import ResponsiveOptionsTabs from '../../components/responsive-options-tabs';
import GoogleFontsSelect from '../../components/google-fonts-select';
import ToolbarAlignInspector from '../../components/toolbar-alignment-inspector';

const { __, _x } = wp.i18n;
const { Component, Fragment, useState } = wp.element;
const { TextControl, ColorIndicator, BaseControl, PanelBody, ColorPalette, ToggleControl, SelectControl } = wp.components;

const MobileWidthBreakpoint = ( { attributes, setAttributes } ) => {
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

export default class CustomInspectorControls extends Component {

	render() {
		
		const { attributes, setAttributes, state, setState } = this.props;
		
		const { 
			htmlTag,
			useResponsiveOptions,
			fontSize,
			fontSizeMobile,
			fontSizeUnit,
			fontSizeUnitMobile,
			lineHeight,
			lineHeightMobile,
			letterSpacing,
			color, 
			backgroundColor,
			googleFont,
			align,
			alignMobile,
			removeMargins,
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

		let responsiveSettings = "";
		
		const mobileAttributes = {
			fontSize: fontSizeMobile,
			fontSizeUnit: fontSizeUnitMobile === undefined ? fontSizeUnit : fontSizeUnitMobile,
			lineHeight: lineHeightMobile,
			align: alignMobile === undefined ? align : alignMobile,
		}

		const fontSizeData = [];
		fontSizeData["px"] = {
			default: '', //16,
			afterChange: 16,
			max: 100,
			step: 1
		};
		fontSizeData["em"] = {
			default: '',//1.5,
			afterChange: 1.5,
			max: 10,
			step: 0.01
		};
		fontSizeData["vw"] = {
			default: '',//1.5,
			afterChange: 1.5,
			max: 10,
			step: 0.01
		};
		
		// default and desktop responsive options
		if ( ! useResponsiveOptions || ( useResponsiveOptions && state.responsiveTab == "desktop" ) ) {

			const desktopControl_Align =
				<ToolbarAlignInspector
					label={ __('Text alignment', 'citadela-pro') }
					value={ align }
					onChange={ ( value ) => { setAttributes( { align: value } ) } }
				/>
			const desktopControl_FontSizeUnit = 
				<SelectControl
					value={ fontSizeUnit }
					label={ __('Font size unit', 'citadela-pro') }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'em', value: 'em' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setAttributes( { 
							fontSizeUnit: value,
							fontSize: fontSizeData[value].afterChange,
						} );
					} 
					}
				/>
			
			const fontSizeLabelInfo = fontSize ? `: ${fontSize}${fontSizeUnit}` : "";
			const desktopControl_FontSize = 
				<CitadelaRangeControl
					label={ __('Font size', 'citadela-pro') + `${fontSizeLabelInfo}` }
					help={ ! fontSize ? __( 'Using font size from theme design.', 'citadela-pro') : "" }
					rangeValue={ fontSize }
					onChange={ ( value ) => { setAttributes( { fontSize: value == undefined ? '' : value } ); } }
					min={ fontSizeData[fontSizeUnit].step }
					max={ fontSizeData[fontSizeUnit].max }
					step={ fontSizeData[fontSizeUnit].step }
					initial={ fontSizeData[fontSizeUnit].default }
					allowReset
				/>;
			
			const lineHeightLabelInfo = lineHeight ? `: ${lineHeight}` : "";
			const desktopControl_LineHeight = 
				<CitadelaRangeControl
					label={ __('Line height', 'citadela-pro') + `${lineHeightLabelInfo}` }
					help={ ! lineHeight ?__( 'Using line height from theme design.', 'citadela-pro') : "" }
					rangeValue={ lineHeight }
					onChange={ ( value ) => { setAttributes( { lineHeight: value == undefined ? '' : value } ); } }
					min={ 0.5 }
					max={ 5 }
					step={ 0.01 }
					initial={ '' }
					allowReset
				/>;

			
			responsiveSettings = 
				<Fragment>
					{ desktopControl_Align }
					{ desktopControl_FontSizeUnit }
					{ desktopControl_FontSize }
					{ desktopControl_LineHeight }
				</Fragment>;
		}
		
		
		// mobile responsive options
		if ( useResponsiveOptions && state.responsiveTab == "mobile" ) {
			
			const mobileControl_Align =
				<ToolbarAlignInspector
					label={ __('Text alignment', 'citadela-pro') }
					value={ mobileAttributes.align }
					onChange={ ( value ) => { setAttributes( { alignMobile: value } ) } }
				/>

			const mobileControl_FontSizeUnit = 
				<SelectControl
					value={ mobileAttributes.fontSizeUnit }
					label={ __('Font size unit', 'citadela-pro') }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'em', value: 'em' },
						{ label: 'vw', value: 'vw' },
					] }
					onChange={ ( value ) => {
						setAttributes( { 
							fontSizeUnitMobile: value,
							fontSizeMobile: fontSizeData[value].afterChange,
						} );
					} 
					}
				/>
		
			const fontSizeLabelInfoMobile = mobileAttributes.fontSize ? `: ${mobileAttributes.fontSize}${mobileAttributes.fontSizeUnit}` : "";
			const mobileControl_FontSize = 
				<CitadelaRangeControl
					label={ __('Font size', 'citadela-pro') + `${fontSizeLabelInfoMobile}` }
					help={ ! mobileAttributes.fontSize ? __( 'Using font size from theme design.', 'citadela-pro') : "" }
					rangeValue={ mobileAttributes.fontSize }
					onChange={ ( value ) => { setAttributes( { fontSizeMobile: value } ); } }
					min={ fontSizeData[mobileAttributes.fontSizeUnit].step }
					max={ fontSizeData[mobileAttributes.fontSizeUnit].max }
					step={ fontSizeData[mobileAttributes.fontSizeUnit].step }
					initial={ fontSizeData[mobileAttributes.fontSizeUnit].default }
					allowReset
				/>;
			
			const lineHeightLabelInfoMobile = mobileAttributes.lineHeight ? `: ${mobileAttributes.lineHeight}` : "";
			const mobileControl_LineHeight = 
				<CitadelaRangeControl
					label={ __('Line height', 'citadela-pro') + `${lineHeightLabelInfoMobile}` }
					help={ ! mobileAttributes.lineHeight ? __( 'Using line height from theme design.', 'citadela-pro') : "" }
					rangeValue={ mobileAttributes.lineHeight }
					onChange={ ( value ) => { setAttributes( { lineHeightMobile: value } ); } }
					min={ 0.5 }
					max={ 5 }
					step={ 0.01 }
					initial={ '' }
					allowReset
				/>;

			responsiveSettings = 
				<Fragment>
					<MobileWidthBreakpoint attributes={ attributes } setAttributes={ setAttributes } />
					{ mobileControl_Align }
					{ mobileControl_FontSizeUnit }
					{ mobileControl_FontSize }
					{ mobileControl_LineHeight }
				</Fragment>;
	
		}
			
		//general settings

		const letterSpacingLabelInfo = letterSpacing ? `: ${letterSpacing}em` : "";
		const generalControl_LetterSpacing = 
			<CitadelaRangeControl
				label={ __('Letter spacing', 'citadela-pro') + `${letterSpacingLabelInfo}` }
				help={ ! letterSpacing ? __( 'Using letter spacing from theme design.', 'citadela-pro') : ""}
				rangeValue={ letterSpacing }
				onChange={ ( value ) => { setAttributes( { letterSpacing: value == undefined ? '' : value } ); } }
				min={ 0 }
				max={ 2 }
				step={ 0.01 }
				initial={ '' }
				allowReset
			/>;

		const generalSettings = 
			<Fragment>
				{ generalControl_LetterSpacing }

				<GoogleFontsSelect
					label={ __('Google font', 'citadela-pro') }
					googleFont={ googleFont }
					onChange={ ( value ) => { setAttributes( { googleFont: value } ); } }
					state={ this.props.state } 
					setState={ this.props.setState }
				/>

				<ToggleControl 
					label={ __( 'Disable default margins', 'citadela-pro' ) }
					checked={ removeMargins }
					onChange={ (value) => { setAttributes( { removeMargins: value } ) }}
				/>
			</Fragment>;
		
		
		return (
			<Fragment>
				
				<PanelBody 
					title={ __('General settings', 'citadela-pro') }
					initialOpen={true}
					className="citadela-panel"
				>	
					<BaseControl 
						label={ __('HTML tag', 'citadela-pro') }
						className="citadela-html-tag-control" 
					>
						<HtmlTagControl
							isCollapsed={ false }
							selectedLevel={ htmlTag }
							onChange={ ( value ) =>
								setAttributes( { htmlTag: value } )
							}
						/>
					</BaseControl>
					
					<ToggleControl 
						label={ __( 'Use responsive options', 'citadela-pro' ) }
						checked={ useResponsiveOptions }
						onChange={ (value) => { setAttributes( { useResponsiveOptions: value } ) }}
					/>
					{ useResponsiveOptions 
						? 
							<div class="citadela-responsive-settings-holder">
								<ResponsiveOptionsTabs activeTab={ state.responsiveTab } onChange={ (value) => { setState( { responsiveTab: value } ) } } />
								{ responsiveSettings }
							</div>
						:
							<Fragment>
								{ responsiveSettings }
							</Fragment>
					}

					{ generalSettings }

				</PanelBody>
				
				<PanelBody 
					title={ __('Color settings', 'citadela-pro') }
					initialOpen={false}
					className="citadela-panel"
				>
					<BaseControl
						label={ __('Text color', 'citadela-pro') }
						className="block-editor-panel-color-settings"
					>
						{ color && <ColorIndicator colorValue={ color } /> }
						<ColorPalette
							value={ color }
							onChange={ (value) => { setAttributes( { color: value } ); } }
							colors={ colorsSet }
						/>
					</BaseControl>

					<BaseControl
						label={ __('Background color', 'citadela-pro') }
						className="block-editor-panel-color-settings"
					>
						{ backgroundColor && <ColorIndicator colorValue={ backgroundColor } /> }
						<ColorPalette
							value={ backgroundColor }
							onChange={ (value) => { setAttributes( { backgroundColor: value } ); } }
							colors={ colorsSet }
						/>
					</BaseControl>

				</PanelBody>

				

			</Fragment>
		);
	}
}
