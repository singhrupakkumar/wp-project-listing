import CustomImageUpload from "./image-upload";

/**
 * WordPress dependencies
 */
const { __, _x } = wp.i18n;
const { applyFilters } = wp.hooks;

const { Fragment, Component } = wp.element;
const { ColorPicker, ResponsiveWrapper, withNotices, withFilters, DropZoneProvider, DropZone, CheckboxControl, TextControl, ColorIndicator, FocalPointPicker, BaseControl, PanelBody, RadioControl, RangeControl, ColorPalette, Button, Placeholder, ToggleControl, SelectControl } = wp.components;
const { compose, withState } = wp.compose;

export default class CitadelaBackground extends Component {
	
	constructor() {
		super( ...arguments );
	}

	render() {
		const {
			meta,
			image,
			color,
			repeat,
			size,
			position,
			fixed,
			overlayColor,
			colorsSet = [
				{ color: '#00d1b2' },
				{ color: '#3373dc' },
				{ color: '#209cef' },
				{ color: '#22d25f' },
				{ color: '#ffdd57' },
				{ color: '#ff3860' },
				{ color: '#7941b6' },
				{ color: '#392F43' },
			],
			supportOverlay = false,
		} = this.props;

		return (
			<>
				<div className="citadela-background-component">


					<BaseControl
						label={ __('Background image', 'citadela-pro') }
					>
						<CustomImageUpload
							media={ image }
							meta={ `${meta}_image` }
							onChange={ (value) => { this.props.onChange( value, 'image' ) } }
							mediaPopupLabel={ __('Background image', 'citadela-pro') }
							dropzoneLabel={ __('Set background image', 'citadela-pro') }
						/>
					</BaseControl>

					{ image && image.length != 0 &&
					<>
						<BaseControl
							label={ __( 'Image size', 'citadela-pro' ) }
						>
							<SelectControl
								value={ size }
								options={ [
									{ label:  _x( 'Cover', 'label for css property "cover": image in background cover entire place', 'citadela-pro' ), value: 'cover' }, //css "cover"
									{ label:  __( '100% horizontal', 'citadela-pro' ), value: 'full-horizontal' }, //css "100% auto"
									{ label:  __( '100% vertical', 'citadela-pro' ), value: 'full-vertical' }, //css "auto 100%"
									{ label:  _x( 'Default size', 'default size of image in background', 'citadela-pro' ), value: 'auto' }, //css "auto"
								] }
								onChange={ (value) => { this.props.onChange( value, 'size' ) } }
							/>
						</BaseControl>
						
						{ size !== 'cover' &&
							<BaseControl>
								<SelectControl
									label={ __( 'Image repeat', 'citadela-pro' ) }
									value={ repeat }
									options={ [
										{ label:  _x( 'No repeat', 'label for css property "no-repeat": do not repeat image in background', 'citadela-pro' ), value: 'no-repeat' },
										{ label:  _x( 'Repeat', 'label for css property "repeat": repeat image in background', 'citadela-pro' ), value: 'repeat' },
										{ label:  _x( 'Repeat vertically', 'label for css property "repeat-y": repeat vertically image in background', 'citadela-pro' ), value: 'repeat-y' },
										{ label:  _x( 'Repeat horizontally', 'label for css property "repeat-x": repeat horizontally image in background', 'citadela-pro' ), value: 'repeat-x' },
									] }
									onChange={ (value) => { this.props.onChange( value, 'repeat' ) } }
								/>
							</BaseControl>
						}

						<BaseControl>
							<CheckboxControl
								label={ __( 'Fixed image', 'citadela-pro' ) }
								checked={ fixed }
								onChange={ (value) => { this.props.onChange( value, 'fixed' ) } }
							/>
						</BaseControl>

						{ ! fixed &&
							<BaseControl
								label={ __( 'Image position', 'citadela-pro' ) }
							>
								<FocalPointPicker
									url={ image.url }
									value={ position && position.length != 0 ? position : { x: "0.5", y: "0.5" } }
									onChange={ (value) => { this.props.onChange( value, 'position' ) } }
								/>
							</BaseControl>
						}

						{ supportOverlay && 				
							<BaseControl
								label={ __('Image overlay color', 'citadela-pro') }
								className="block-editor-panel-color-settings"
							>
								{ overlayColor && <ColorIndicator colorValue={ overlayColor } /> }
								<div class="reset-button" style={ {marginBottom: '3px'} }>
									<Button
										disabled={ overlayColor === undefined }
										isSecondary
										isSmall
										onClick={ () => { this.props.onChange( '', 'image_overlay' ) } }
										>
										{ __( 'Reset', 'citadela-pro' ) }
									</Button>
								</div>
								<ColorPicker
									color={ overlayColor }
									onChangeComplete={ (value) => { this.props.onChange( value, 'image_overlay' ) } }
								/>
							</BaseControl>
						}
					</>
					}

					<BaseControl
						label={ __('Background color', 'citadela-pro') }
						className="block-editor-panel-color-settings"
					>
						{ color && <ColorIndicator colorValue={ color } /> }
						<div class="reset-button" style={ {marginBottom: '3px'} }>
							<Button
								disabled={ color === undefined }
								isSecondary
								isSmall
								onClick={ () => { this.props.onChange( '', 'color' ) } }
								>
								{ __( 'Reset', 'citadela-pro' ) }
							</Button>
						</div>
						<ColorPicker
							color={ color }
							onChangeComplete={ (value) => { this.props.onChange( value, 'color' ) } }
						/>
					</BaseControl>
					
				</div>
			</>
		);
	}
}