/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, useCallback, Fragment } = wp.element;
const { BaseControl, ColorPicker, Dropdown, ToggleControl, RangeControl } = wp.components;

const GradientDegree = ( { gradientDegree = '', onChange } ) => {
	const setGradientDegree = useCallback(
		( value ) => {
			onChange(value);
		},
		[ onChange ]
	);

	return (
		<RangeControl
			value={ gradientDegree }
			min={ 0 }
			max={ 359 }
			initialPosition={ 90 }
			allowReset
			onChange={ setGradientDegree }
		/>
	);
}

export default class GradientControl extends Component {

	render() {

		const { label = '', help = '', gradient, disableAlpha = false, onFirstColorChange, onSecondColorChange, onDegreeChange, onGradientTypeChange } = this.props;

		const gradientPreviewStyles = {
			...( { backgroundImage: (gradient.type == "linear" ) ? `linear-gradient(${gradient.degree}deg, ${gradient.first} 0%, ${gradient.second} 100%)` : `radial-gradient(${gradient.first} 0%, ${gradient.second} 100%)` } ),
		}

		return(
			<Fragment>
			<div class="citadela-gradient-control">

				<BaseControl
					label={ label ? label : '' }	
				>
					<div className={ classNames( "gradient-preview", disableAlpha ? "" : "alpha" ) } >
						<div class="inner-preview" style={ gradientPreviewStyles }></div>
					</div>	

					<div class="colors-preview">
						<Dropdown
							className={ classNames( 'left-picker', disableAlpha ? "" : "alpha" ) }
							position="bottom right"
							renderToggle={ ( { isOpen, onToggle } ) => (
								<div class="color"
									aria-expanded={ isOpen }
									onClick={ onToggle }
									style={ { backgroundColor: gradient.first } }
								/>
							) }
							renderContent={ () => (
								<ColorPicker
									color={ gradient.first }
									onChangeComplete={ (value) => onFirstColorChange( disableAlpha ? value.hex : `rgba(${value.rgb.r}, ${value.rgb.g}, ${value.rgb.b}, ${value.rgb.a})`) }
									disableAlpha={ disableAlpha }
								/>
							) }
						/>

						<Dropdown
							className={ classNames( 'right-picker', disableAlpha ? "" : "alpha" ) }
							position="bottom right"
							renderToggle={ ( { isOpen, onToggle } ) => (
								<div class="color"
									aria-expanded={ isOpen }
									onClick={ onToggle }
									style={ { backgroundColor: gradient.second } }
								/>
							) }
							renderContent={ () => (
								<ColorPicker
									color={ gradient.second }
									onChangeComplete={ (value) => onSecondColorChange( disableAlpha ? value.hex : `rgba(${value.rgb.r}, ${value.rgb.g}, ${value.rgb.b}, ${value.rgb.a})`) }
									disableAlpha={ disableAlpha }
								/>
							) }
						/>

					</div>
				
				{ ( gradient.type == "linear" ) &&
					<BaseControl
						label={__('Gradient degree', 'citadela-pro') + `: ${gradient.degree}°`}
					>
						<GradientDegree
							gradientDegree={ gradient.degree }
							onChange={ onDegreeChange }
						/>
					</BaseControl>
				}
				</BaseControl>

				<ToggleControl 
					label={ __( 'Radial gradient', 'citadela-pro' ) }
					checked={ gradient.type == "radial" ? true : false }
					onChange={ ( value ) => { onGradientTypeChange( value ) } }
				/>
				
			</div>

			{ help != '' &&
				<p class="components-base-control__help">{ help }</p>
			}
			
			</Fragment>
	    );
	}
}