import CustomInspectorControls from './inspector-controls';
import AlignToolbar from '../../components/toolbar-alignment';
import FontWeightToolbar from '../../components/toolbar-font-weight';
import StateIcons from '../../components/state-icons';

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { RichText, BlockControls, InspectorControls } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, ToolbarButton, Button, Icon, Tooltip, DropdownMenu } = wp.components;

export default class Edit extends Component{
	
	constructor() {
		super( ...arguments );
		this.setState = this.setState.bind(this);
		const loadedFamily = this.props.attributes.googleFont['family'];
		this.state = {
			responsiveTab: "desktop",
			loadedFonts: loadedFamily ? [ loadedFamily.replace( /\s+/g, '+' ) ] : [] ,
		}
	}
	
	componentDidMount(){
		const { googleFont } = this.props.attributes;
		this.loadGoogleFont( googleFont );
	}

	loadGoogleFont( googleFont ) {
		const fontFamily = googleFont['family'];
		if( fontFamily == '' ) return;

		const head = document.head;
		const link = document.createElement( 'link' );
		const variants = googleFont.variants ? ':' + googleFont.variants.join( ',' ) : '';

		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = 'https://fonts.googleapis.com/css?family=' + fontFamily.replace( /\s+/g, '+' ) + variants + '&display=swap';

		head.appendChild( link );
	}

	render() {
		const { attributes, setAttributes, isSelected, className, name } = this.props;
		
		const block = wp.blocks.getBlockType(name);
		const defaults = block.attributes;
		const {
			text,
			useResponsiveOptions,
			htmlTag,
			fontSizeUnit,
			fontSizeUnitMobile,
			fontSize,
			fontSizeMobile,
			lineHeight,
			lineHeightMobile,
			letterSpacing,
			color,
			backgroundColor,
			italic,
			linethrough,
			underline,
			googleFont,
			fontWeight,
			align, 
			alignMobile,
			removeMargins,
		} = attributes;
		
		const mobileView = ( useResponsiveOptions && this.state.responsiveTab == 'mobile' );
		const desktopView = ( useResponsiveOptions && this.state.responsiveTab == 'desktop' );
		
		const actualValue = mobileView
		? {
			fontSize: fontSizeMobile ? fontSizeMobile : "",
			fontSizeUnit: fontSizeUnitMobile ? fontSizeUnitMobile : fontSizeUnit,
			lineHeight: lineHeightMobile ? lineHeightMobile : "",
			align: alignMobile ? alignMobile : align,
		}
		: {
			fontSize: fontSize ? fontSize : "",
			fontSizeUnit: fontSizeUnit ? fontSizeUnit : "",
			lineHeight: lineHeight ? lineHeight : "",
			align: align ? align : "",
		}

		var styles = {
			...( googleFont['family'] != '' ? { fontFamily: googleFont['family'] } : "" ),
			...( actualValue.fontSize ? { fontSize: `${actualValue.fontSize}${actualValue.fontSizeUnit}` } : "" ),
			...( actualValue.lineHeight ? { lineHeight: actualValue.lineHeight } : "" ),
			...( letterSpacing ? { letterSpacing: `${letterSpacing}em` } : "" ),
			...( fontWeight ? { fontWeight: fontWeight } : "" ),
			...( italic ? { fontStyle: 'italic' } : "" ),
			...( linethrough ? { textDecoration: 'line-through' } : "" ),
			...( underline ? { textDecoration: 'underline' } : "" ),
			...( color ? { color: color } : "" ),
			
		}

		const wrapperStyles = {
			...( backgroundColor ? { backgroundColor: backgroundColor } : "" ),
		}

		return (
			<Fragment>
				<BlockControls key='controls'>
					
					<ToolbarGroup>
						<ToolbarItem>
							{ ( toggleProps ) => ( 
								<AlignToolbar
									label={ mobileView ? __( 'Select alignment on mobile design', 'citadela-pro') : __( 'Select alignment', 'citadela-pro') }
									value={ actualValue.align } 
									onChange={ ( value ) => { mobileView ? setAttributes( { alignMobile: value } ) : setAttributes( { align: value } ) } } 
									toggleProps={ toggleProps }
								/>
							)}
						</ToolbarItem>
					</ToolbarGroup>
					<ToolbarGroup>
						<ToolbarItem>
							{ ( toggleProps ) => ( 
								<FontWeightToolbar 
									value={ fontWeight } 
									onChange={ ( value ) => setAttributes( { fontWeight: value } ) } 
									toggleProps={ toggleProps }
								/>
							)}
						</ToolbarItem>
						

						<ToolbarButton
							icon="editor-italic"
							className={classNames(
								"components-toolbar__control",
								{"is-pressed" : italic}
								)}
								aria-pressed={ italic }	
								onClick={ () => setAttributes( { italic: ! italic } ) }
						/>
					
						<ToolbarButton
							icon="editor-underline"
							className={classNames(
								"components-toolbar__control",
								{"is-pressed" : underline}
							)}
							aria-pressed={ underline }	
							onClick={ () => setAttributes( { 
								underline: ! underline ,
								linethrough: linethrough ? !linethrough : linethrough,
							} ) }
						/>

						<ToolbarButton
							icon="editor-strikethrough"
							className={classNames(
								"components-toolbar__control",
								{"is-pressed" : linethrough}
							)}
							aria-pressed={ linethrough }	
							onClick={ () => setAttributes( { 
								linethrough: ! linethrough ,
								underline: underline ? !underline : underline,
							} ) }
						/>

						</ToolbarGroup>
					
				</BlockControls>

				<InspectorControls key='inspector'>
					<CustomInspectorControls attributes={ attributes } setAttributes={ setAttributes } state={ this.state } setState={ this.setState } />
				</InspectorControls>
				
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-responsive-text",
							`align-${actualValue.align}`,
							backgroundColor ? "has-bg" : "",
							fontWeight ? `weight-${fontWeight}` : "",
							removeMargins ? 'no-margins' : ""
							)
					}
					style={ wrapperStyles }
				>
					<StateIcons 
                        useResponsiveOptions= { useResponsiveOptions } 
                        isSelected={ isSelected } 
                        currentView={ this.state.responsiveTab }
                    />

					<RichText
						key='richtext'
						tagName={ htmlTag }
						onChange= { (value) => { setAttributes( { text: value } ) } }
						value= { text }
						placeholder={ __('Insert text', 'citadela-pro' ) }
						keepPlaceholderOnFocus={false}
						//allowedFormats={[]}
						style={ styles }
					/>
				</div>
			</Fragment>
		);
	}

}