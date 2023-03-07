import CitadelaRangeControl  from '../../components/range-control';
import CustomColorControl from '../../components/custom-color-control';

const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { BaseControl, PanelBody, ToggleControl, ColorPalette, ColorIndicator, ColorPicker, Button } = wp.components;

const OpeningHoursInspectorControls = ({
	attributes,
	setAttributes,
}) => {
	const { 
		hideEmptyDays, dayDataColor, dayLabelColor, linesColor, layout, boxWidth
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

	return (
		<Fragment>
			<PanelBody 
				title={__('Opening Hours settings', 'citadela-pro')}
				initialOpen={true}
				className="citadela-panel"
			>
				<BaseControl>
					<ToggleControl
						label={ __( 'Hide empty days', 'citadela-pro' ) } 
						help={ __( 'Days without defined time will be hidden.', 'citadela-pro' ) } 
						checked={ hideEmptyDays }
						onChange={ ( checked ) => setAttributes( { hideEmptyDays: checked } ) }
					/>
				</BaseControl>

				{ layout == 'box' && 
					<CitadelaRangeControl
						label={ __('Box width', 'citadela-pro') }
						rangeValue={ boxWidth }
						onChange={ ( value ) => { setAttributes( { boxWidth: value == undefined ? 200 : value } ); } }
						min={ 150 }
						max={ 500 }
						initial={ 200 }
						allowReset
					/>
				}
			</PanelBody>

			<PanelBody 
				title={__('Color settings', 'citadela-pro')}
				initialOpen={true}
				className="citadela-panel"
			>
				<BaseControl
					label={ __('Day title color', 'citadela-pro') }
					className="block-editor-panel-color-settings"
				>
					{ dayLabelColor && <ColorIndicator colorValue={ dayLabelColor } /> }
					<ColorPalette
						value={ dayLabelColor }
						onChange={ (value) => { setAttributes( { dayLabelColor: value } ); } }
						colors={ colorsSet }
					/>
				</BaseControl>

				<BaseControl
					label={ __('Day hours color', 'citadela-pro') }
					className="block-editor-panel-color-settings"
				>
					{ dayDataColor && <ColorIndicator colorValue={ dayDataColor } /> }
					<ColorPalette
						value={ dayDataColor }
						onChange={ (value) => { setAttributes( { dayDataColor: value } ); } }
						colors={ colorsSet }
					/>
				</BaseControl>

				<CustomColorControl 
					label={ __('Lines color', 'citadela-pro') }
					color={ linesColor }
					onChange={ (value) => { setAttributes( { linesColor: value } ); } }
				/>

			</PanelBody>
		</Fragment>
	);
}

export default OpeningHoursInspectorControls;