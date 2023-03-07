const { __ } = wp.i18n;
const { Component, useCallback } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, BaseControl, ColorPalette, ToggleControl, RangeControl } = wp.components;

const BorderRadius = ( { borderRadius = '', setAttributes } ) => {
	const setBorderRadius = useCallback(
		( value ) => {
			setAttributes( { buttonBorderRadius: value } );
		},
		[ setAttributes ]
	);

	return (
		<RangeControl
			value={ borderRadius }
			label={ __( 'Border radius', 'citadela-pro' ) }
			min={ 0 }
			max={ 20 }
			initialPosition={ 20 }
			allowReset
			onChange={ setBorderRadius }
		/>
	);
}

export default class PriceTableInspectorControls extends Component {

	render() {
		const { attributes, setAttributes } = this.props;
		const {
			buttonLinkNewTab,
			colorHeaderBg,
			colorHeaderText,
			colorButtonBg,
			colorButtonText,
			buttonBorderRadius,
		} = attributes;
		
		const customColors = [
			{ color: '#f78da7' },
			{ color: '#cf2e2e' },
			{ color: '#ff6900' },
			{ color: '#fcb900' },
			{ color: '#7bdcb5' },
			{ color: '#00d084' },
			{ color: '#8ed1fc' },
			{ color: '#0693e3' },
			{ color: '#9b51e0' },
			{ color: '#eeeeee' },
			{ color: '#abb8c3' },
			{ color: '#313131' },
			
		];

		return (
			<InspectorControls key='inspector'>
				
				<PanelBody 
					title={__('Colors', 'citadela-pro')}
					initialOpen={false}
					className="citadela-panel"
				>
					<BaseControl
						label={__('Header background', 'citadela-pro')}
					>
						<ColorPalette
							value={ colorHeaderBg }
							className="block-editor-color-palette-control__color-palette"
							onChange={ (value) => { setAttributes( { colorHeaderBg: value } ); } }
							colors={customColors}
						/>
					</BaseControl>
					<BaseControl
						label={__('Header text', 'citadela-pro')}
					>
						<ColorPalette
							value={ colorHeaderText }
							className="block-editor-color-palette-control__color-palette"
							onChange={ (value) => { setAttributes( { colorHeaderText: value } ); } }
							colors={customColors}
						/>
					</BaseControl>
					<BaseControl
						label={__('Button background', 'citadela-pro')}
					>
						<ColorPalette
							value={ colorButtonBg }
							className="block-editor-color-palette-control__color-palette"
							onChange={ (value) => { setAttributes( { colorButtonBg: value } ); } }
							colors={customColors}
						/>
					</BaseControl>
					<BaseControl
						label={__('Button text', 'citadela-pro')}
					>
						<ColorPalette
							value={ colorButtonText }
							className="block-editor-color-palette-control__color-palette"
							onChange={ (value) => { setAttributes( { colorButtonText: value } ); } }
							colors={customColors}
						/>
					</BaseControl>
				</PanelBody>
				<PanelBody 
					title={__('Button settings', 'citadela-pro')}
					initialOpen={false}
					className="citadela-panel"
				>
					<ToggleControl
						label={ __( 'Open link in new tab', 'citadela-pro' ) } 
						checked={ buttonLinkNewTab }
						onChange={ ( checked ) => setAttributes( { buttonLinkNewTab: checked } ) }
					/>

					<BorderRadius
						borderRadius={ buttonBorderRadius }
						setAttributes={ setAttributes }
					/>
				</PanelBody>
				
			</InspectorControls>
		);
	}

}