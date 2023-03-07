import ResponsiveOptionsTabs from '../../components/responsive-options-tabs';

const { __ } = wp.i18n;
const { Fragment, useState } = wp.element;
const { SelectControl, BaseControl, PanelBody, ToggleControl, TextControl } = wp.components;


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

const DesktopHeightOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		height,
		unit,
	} = attributes;

	const [ inputHeightValue, setInputHeightValue ] = useState( height );
		
	let defaults = [];
	defaults["px"] = 20;
	defaults["vw"] = 5;
	defaults["vh"] = 5;
	defaults["rem"] = 5;
	defaults["em"] = 5;
	defaults["%"] = 5;

	let step = 1;
	if( unit != "px" ){
		step = 0.1;
	}

	return (
		<Fragment>
			<BaseControl label={__('Height unit', 'citadela-pro')}>
				<SelectControl
					key="spacer-height-unit"
					value={ unit }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vw', value: 'vw' },
						{ label: 'vh', value: 'vh' },
						{ label: 'rem', value: 'rem' },
						{ label: 'em', value: 'em' },
						{ label: '%', value: '%' },
					] }
					onChange={ ( value ) => {
						setInputHeightValue( defaults[value] );
						setAttributes( { 
							unit: value,
							height: defaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Spacer height in', 'citadela-pro' ) + " " + unit } 
			>
				<input
					className="components-text-control__input"
					type="number"
					key="desktopSpacerHeight"
					value={ inputHeightValue }
					onChange={ ( event ) => {
						let newHeight = event.target.value;
						setInputHeightValue(newHeight);
						if ( newHeight == '' ) {
							// height in input is not defined, input is empty, set empty value for input and 0 for height
							setInputHeightValue( '' );
							newHeight = 0;
						}
						if(unit != "px"){
							setAttributes( { height: parseFloat(newHeight) } );
						}else{
							setAttributes( { height: parseInt(newHeight) } );
						}
					} }
					step={ step }
				/>
			</BaseControl>

		</Fragment>
	)
}

const MobileHeightOptions = ({
	attributes,
	setAttributes,
}) => {
	const { 
		height,
		heightMobile,
		unit,
		unitMobile,
	} = attributes;

	const mobileAttributes = {
		height: heightMobile === undefined ? height : heightMobile,
		unit: unitMobile === undefined ? unit : unitMobile,
	}

	const [ inputHeightValueMobile, setInputHeightValueMobile ] = useState( mobileAttributes.height );
		
	let defaults = [];
	defaults["px"] = 20;
	defaults["vw"] = 5;
	defaults["vh"] = 5;
	defaults["rem"] = 5;
	defaults["em"] = 5;
	defaults["%"] = 5;

	let step = 1;
	if( unit != "px" ){
		step = 0.1;
	}

	return (
		<Fragment>
			<BaseControl label={__('Height unit', 'citadela-pro')} >
				<SelectControl
					key="spacer-height-unit-mobile"
					value={ mobileAttributes.unit }
					options={ [
						{ label: 'px', value: 'px' },
						{ label: 'vw', value: 'vw' },
						{ label: 'vh', value: 'vh' },
						{ label: 'rem', value: 'rem' },
						{ label: 'em', value: 'em' },
						{ label: '%', value: '%' },
					] }
					onChange={ ( value ) => {
						setInputHeightValueMobile( defaults[value] );
						setAttributes( { 
							unitMobile: value,
							heightMobile: defaults[value],
						} );
					} 
					}
				/>
			</BaseControl>
			<BaseControl 
				label={ __( 'Spacer height in', 'citadela-pro' ) + " " + mobileAttributes.unit } 
			>
				<input
					className="components-text-control__input"
					type="number"
					key="mobileSpacerHeight"
					value={ inputHeightValueMobile }
					onChange={ ( event ) => {
						let newHeight = event.target.value;
						setInputHeightValueMobile(newHeight);
						if ( newHeight == '' ) {
							// height in input is not defined, input is empty, set empty value for input and 0 for height
							setInputHeightValueMobile( '' );
							newHeight = 0;
						}
						if(unit != "px"){
							setAttributes( { heightMobile: parseFloat(newHeight) } );
						}else{
							setAttributes( { heightMobile: parseInt(newHeight) } );
						}
					} }
					step={ step }
				/>
			</BaseControl>

		</Fragment>
	)
}

const SpacerInspectorControls = ({
	attributes,
	setAttributes,
	state,
	setState
}) => {
	const { 
		useResponsiveOptions,
		height,
		heightMobile,
		unit,
		unitMobile,
		hideInResponsive
	} = attributes; 
	
	//default values for input after unit change - prevent too large spacer for example after change from from 100px to 100vw etc...
	let defaults = [];
	defaults["px"] = 20;
	defaults["vw"] = 5;
	defaults["vh"] = 5;
	defaults["rem"] = 5;
	defaults["em"] = 5;
	defaults["%"] = 5;

	let step = 1;
	if( unit != "px" ){
		step = 0.1;
	}


	let responsiveSettings = "";
		
	const mobileAttributes = {
		height: heightMobile === undefined ? height : heightMobile,
		unit: unitMobile === undefined ? unit : unitMobile,
	}

	// default and desktop responsive options
	if ( ! useResponsiveOptions || ( useResponsiveOptions && state.responsiveTab == "desktop" ) ) {
		

		responsiveSettings = 
		<Fragment>
			<DesktopHeightOptions attributes={ attributes } setAttributes={ setAttributes } />
		</Fragment>;
	}

	// mobile responsive options
	if ( useResponsiveOptions && state.responsiveTab == "mobile" ) {
		responsiveSettings = 
		<Fragment>
			<MobileWidthBreakpoint attributes={ attributes } setAttributes={ setAttributes } />
			<MobileHeightOptions attributes={ attributes } setAttributes={ setAttributes } />
		</Fragment>
	}
	

	const generalSettings = 
		<>
		{/*
		<BaseControl>
			<ToggleControl
				label={ __( 'Hide in responsive', 'citadela-pro' ) } 
				checked={ hideInResponsive }
				onChange={ ( checked ) => setAttributes( { hideInResponsive: checked } ) }
			/>
		</BaseControl>
		*/}
		</>



	return (
		<Fragment>
			<PanelBody 
				title={__('Spacer settings', 'citadela-pro')}
				initialOpen={true}
				className="citadela-panel"
			>
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
		</Fragment>
	);
}

export default SpacerInspectorControls;