import ToolbarAlignment from '../../components/toolbar-alignment';
import ToolbarAlignInspector from '../../components/toolbar-alignment-inspector';
import CitadelaRangeControl from '../../components/range-control';
import CustomColorControl from '../../components/custom-color-control';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { ToolbarGroup, ToolbarItem, PanelBody, BaseControl, RadioControl } = wp.components;
const { InspectorControls, BlockControls, RichText } = wp.blockEditor;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( "Item Get Directions", 'citadela-directory' ),
	description: __( "Displays button that open Google Maps navigation to Item Post location.", 'citadela-directory' ),
	edit: ({ className, attributes, setAttributes, name }) => {
        const block = wp.blocks.getBlockType(name);
		const { align, text, style, textColor, bgColor, borderRadius } = attributes;

		const textStyles = textColor ? { color: textColor } : {};
		const linkStyles = {
			...( style != 'text' && bgColor ? {backgroundColor: bgColor } : {} ),
			...( style != 'text' && borderRadius >= 0 ? { borderRadius: `${borderRadius}px` } : {} ),
		} 

		return (
			<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarItem>
						{ ( toggleProps ) => (
							<ToolbarAlignment 
								value={ align } 
								onChange={ ( value ) => ( setAttributes( { align: value } ) ) }
								leftLabel={ __( 'Align Left', 'citadela-directory' ) }    
								centerLabel={ __( 'Align Center', 'citadela-directory' ) }    
								rightLabel={ __( 'Align Right', 'citadela-directory' ) }    
								toggleProps={ toggleProps }
							/>
						)}
					</ToolbarItem>
				</ToolbarGroup>				
			</BlockControls>

			<InspectorControls>
				<PanelBody 
					title={ __('Options', 'citadela-directory') }
					initialOpen={true}
					className="citadela-panel"
				>
					
					<ToolbarAlignInspector
						label={ __('Alignment', 'citadela-directory') }
						value={ align }
						onChange={ ( value ) => { setAttributes( { align: value } ) } }
					/>

					<BaseControl 
						label={ __('Style', 'citadela-directory') }
					>
						<RadioControl
							selected={ style }
							options={ [
								{ label:  __('Small button', 'citadela-directory'), value: 'small-button' },
								{ label:  __('Large button', 'citadela-directory'), value: 'large-button' },
								{ label:  __('Text link', 'citadela-directory'), value: 'text' },
							] }
							onChange={ ( value ) => { setAttributes( { style: value } ) } }
						/>
					</BaseControl>					
					
					<CustomColorControl 
						label={ __('Text color', 'citadela-directory') }
						color={ textColor }
						onChange={ ( value ) => { setAttributes( { textColor: value } ) } }
						allowReset
						disableAlpha
					/>

					{ style != 'text' &&
						<>
						<CustomColorControl 
							label={ __('Button background color', 'citadela-directory') }
							color={ bgColor }
							onChange={ ( value ) => { setAttributes( { bgColor: value } ) } }
							allowReset
						/>
						<CitadelaRangeControl
							label={ __('Border radius', 'citadela-directory') }
							rangeValue={ borderRadius }
							onChange={ ( value ) => { setAttributes( { borderRadius: value } ) } }
							min={ 0 }
							max={ 50 }
							initial={ 0 }
							allowReset
							allowNoValue
						/>
						</>
					}
				</PanelBody>
			</InspectorControls>

			<div className={ classNames(
				"wp-block-citadela-blocks",
				"ctdl-item-get-directions",
                attributes.className,
				`align-${align}`,
				`${style}-style`,
				textColor ? 'custom-text-color' : null,
				style != 'text' && bgColor ? 'custom-background-color' : null,
				style != 'text' && borderRadius ? 'custom-border-radius' : null
			) }>
				<div 
					class="button-wrapper"
					style={ linkStyles }
					>
					<RichText
						tagName='span'
						className="button-text"
						placeholder={ __('Get directions', 'citadela-directory') }
						value={ text }
						onChange={ ( value ) => setAttributes( { text: value } ) }
						keepPlaceholderOnFocus={ true }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
						style={ textStyles }
					/>
				</div>
            </div>
			</>
        );
	},
	save: () => {
		return null;
	}
} );