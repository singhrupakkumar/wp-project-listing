/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { BaseControl, ColorPicker, Dropdown, Button } = wp.components;

export default class CustomColorControl extends Component {

	render() {

		const { label, help, color, disableAlpha = false, returnObject = false, allowReset = true, onChange } = this.props;
		
		return(
			<Fragment>

				<BaseControl
					label={ label ? label : '' }	
					className="block-editor-panel-color-settings"
				>
					<div className={ classNames( 
						"citadela-color-control", 
						{ "alpha": !disableAlpha }
						)}
					>
						<div className="dropdown-component-holder">
							<Dropdown
								position="bottom center"
								className="dropdown-component"
								renderToggle={ ( { isOpen, onToggle } ) => (
									<div 
										class="citadela-color-indicator"
										onClick={ onToggle }
									>
										<div class="inner-color"
											style={ color ? { backgroundColor: color } : null }
										></div>

									</div>
								) }
								renderContent={ () => (
									<ColorPicker
										color={ color }
										onChangeComplete={ (value) => { onChange( 
											disableAlpha 
												? value.hex 
												: returnObject 
													? value.rgb 
													: `rgba(${value.rgb.r}, ${value.rgb.g}, ${value.rgb.b}, ${value.rgb.a})`
										) } }
										disableAlpha={ disableAlpha }
									/>
								) }
							/>
							
							{ allowReset &&
								<Button
									disabled={ color ? false : true }
									isSecondary
									isSmall
									onClick={ () => onChange( undefined ) }
									>
									{ __( 'Clear', 'citadela-pro' ) }
								</Button>
							}
						</div>
						

					</div>
				</BaseControl>
				

			{ help &&
				<p class="components-base-control__help">{ help }</p>
			}
			
			</Fragment>
	    );
	}
}