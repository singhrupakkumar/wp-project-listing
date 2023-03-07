import CustomContainerBlockControls from './block-controls';
import CustomContainerBackgroundToolbar from './background-toolbar';
import CustomContainerInspectorControls from './inspector-controls';
import StateIcons from '../../components/state-icons';

const { __, _x } = wp.i18n;
const { Component, Fragment, createRef } = wp.element;
const { BlockControls, InspectorControls, InnerBlocks, InspectorAdvancedControls } = wp.blockEditor;
const { TextControl } = wp.components;
const { getBlockName, getBlockParents } = wp.data.select('core/block-editor');

export default class ClusterEdit extends Component {

	constructor() {
		super( ...arguments );
		this.setState = this.setState.bind(this);
		this.isParentColumn = this.isParentColumn.bind(this);
		this.updateMainBlockDivWrapper = this.updateMainBlockDivWrapper.bind(this);
		this.blockRef = createRef();
		this.state = {
			responsiveTab: "desktop",
		}
	}

	componentDidMount(){
		const { attributes, setAttributes } = this.props;
		const {
			useBackgroundImageGradient,
		} = attributes;

		//we need to switch toggle option "Use background gradient" to backgroundImageColorType value "gradient", const useBackgroundImageGradient will be no longer used
		if( useBackgroundImageGradient ) {
			setAttributes( { 
				useBackgroundImageGradient: false, 
				backgroundImageColorType: 'gradient' 
			} );
		}

		if( attributes.inColumn !== this.isParentColumn() ){
			setAttributes( { inColumn: this.isParentColumn() } );
		}
		this.updateMainBlockDivWrapper();
	}

	componentDidUpdate(){
		this.updateMainBlockDivWrapper();
	}

	isParentColumn(){
		const parents = getBlockParents( this.props.clientId );
		return parents.length > 0 && getBlockName( parents[ parents.length - 1] ) === 'core/column';
	}

	updateMainBlockDivWrapper(){
		const { coverHeight, inColumn } = this.props.attributes;
		let blockWrapper = this.blockRef.current.parentNode;
		if( inColumn && coverHeight ){
			blockWrapper.dataset.coverHeight = 'true';
		}else{
			delete blockWrapper.dataset.coverHeight;
		}
	}

	render() {
		

		const { attributes, setAttributes, isSelected, className } = this.props;
		const { 
			backgroundType,
			backgroundColor,
			backgroundImageFixed,
			sectionsSize,
			focalPoint,
			backgroundImage,
			backgroundOverlayType,
			backgroundOverlayColor,
			backgroundOverlayOpacity,
			backgroundOverlayGradient,
			backgroundImageSize,
			backgroundImageRepeat,
			backgroundGradient,
			borderWidth,
			borderColor,
			borderRadius,
			boxShadow,
			mobileVisibility,
			heightValue,
			heightUnit,
			heightDisableOnMobile,
			verticalAlignment,
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

		const availableResponsiveOptions = (
			backgroundType == 'image'
		) ? true : false;
		
		
		let mobileView = false;
		let desktopView = false;

		if( useResponsiveOptions && availableResponsiveOptions ) {
			mobileView = this.state.responsiveTab == 'mobile';
			desktopView = this.state.responsiveTab == 'desktop';
		}

		const mobileAttributes = {
			backgroundImage: backgroundImageMobile === undefined ? backgroundImage : backgroundImageMobile,
			backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
			backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
			focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
			disableBackgroundImage: disableBackgroundImageMobile,
		}

		const hasBorder = borderWidth > 0 && borderColor ? true : false;
		const hasShadow = boxShadow && boxShadow.color ? true : false;
		
		const overlayBorderRadius = hasBorder ? borderRadius - borderWidth : borderRadius;
		const overlayGradient = {
			...( { backgroundImage: (backgroundOverlayGradient.type == "linear" ) ? `linear-gradient(${backgroundOverlayGradient.degree}deg, ${backgroundOverlayGradient.first} 0%, ${backgroundOverlayGradient.second} 100%)` : `radial-gradient(${backgroundOverlayGradient.first} 0%, ${backgroundOverlayGradient.second} 100%)` } ),
		}
		const overlayStyle = {
			...( backgroundOverlayType == "color" && backgroundOverlayColor ? { backgroundColor: backgroundOverlayColor } : {} ),
			...( backgroundOverlayOpacity ? { opacity: backgroundOverlayOpacity/100 } : {} ),
			...( overlayBorderRadius > 0 ? { borderRadius: `${overlayBorderRadius}px` } : {} ),
			...( backgroundOverlayType == "gradient" ? overlayGradient : {} )
		}
		
		const backgroundGradientCss = (backgroundGradient.type == "linear" ) ? `linear-gradient(${backgroundGradient.degree}deg, ${backgroundGradient.first} 0%, ${backgroundGradient.second} 100%)` : `radial-gradient(${backgroundGradient.first} 0%, ${backgroundGradient.second} 100%)`;
		const gradientStyles = {
			...( { backgroundImage: backgroundGradientCss } ),
		}
		
		let boxShadowStyle = {};
		if( hasShadow ){
			const inset = boxShadow.inset ? 'inset' : '';
			boxShadowStyle = {
				boxShadow: `${inset} ${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			};
		}
		

		const currentResponsiveOptions = ( useResponsiveOptions && this.state.responsiveTab == 'mobile' )
		? {
			backgroundImage: mobileAttributes.backgroundImage,
			focalPoint: mobileAttributes.focalPoint,
			backgroundImageFixed: mobileAttributes.backgroundImageFixed,
			backgroundImageSize: mobileAttributes.backgroundImageSize,
			disableBackgroundImage: mobileAttributes.disableBackgroundImage,
			
		}
		: {
			backgroundImage: backgroundImage,
			focalPoint: focalPoint,
			backgroundImageFixed: backgroundImageFixed,
			backgroundImageSize: backgroundImageSize,
			disableBackgroundImage: false, // options not available on desktop yet
		}

		const style = {
			...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
			...( backgroundType == "image" && currentResponsiveOptions.backgroundImage && backgroundImageColorType == 'gradient' ? { backgroundImage: backgroundGradientCss } : {} ),
			...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
			...( backgroundType == 'gradient' ? gradientStyles : {} ),
			...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
			...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
			...( boxShadowStyle ),
			/*...( zIndex !== undefined && zIndex !== '' ? { zIndex: zIndex } : {} ),*/
		};

		const useBackgroundImage = backgroundType == "image" && currentResponsiveOptions.backgroundImage && currentResponsiveOptions.disableBackgroundImage !== true;
		
		const backgroundImageStyle = {
			...( useBackgroundImage ? { backgroundImage: `url(${currentResponsiveOptions.backgroundImage.url})` } : {} ),
			...( useBackgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
			...( useBackgroundImage && currentResponsiveOptions.focalPoint && !currentResponsiveOptions.backgroundImageFixed ? { backgroundPosition: `${ currentResponsiveOptions.focalPoint.x * 100 }% ${ currentResponsiveOptions.focalPoint.y * 100 }%` } : {} ),
			...( overlayBorderRadius > 0 ? { borderRadius: `${overlayBorderRadius}px` } : {} ),
		}

		const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;

		return (
			<Fragment>
				<BlockControls key='controls'>
					<CustomContainerBlockControls attributes={ attributes } setAttributes={ setAttributes }/>
					<CustomContainerBackgroundToolbar attributes={ attributes } setAttributes={ setAttributes } state={ this.state } />
				</BlockControls>
				<InspectorControls key='inspector'>
					<CustomContainerInspectorControls attributes={ attributes } setAttributes={ setAttributes } state={ this.state } setState={ this.setState } />
				</InspectorControls>
				<InspectorAdvancedControls>
					<TextControl
						type="number"
						label={ 'z-index' }
						value={ zIndex }
						onChange={ ( value ) => {
							setAttributes( { zIndex: value } );
						} }
						step={ 1 }
					/>
				</InspectorAdvancedControls>
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							/*{ "disable-min-height-on-mobile": heightDisableOnMobile ? true : false },*/
							`size-${sectionsSize}`,
							`bg-type-${backgroundType}`,
							`inside-space-${insideSpace}`,
							//{ [ `${mobileVisibility}-on-mobile` ]: mobileVisibility != "always" ? mobileVisibility : false },
							{ [ `bg-size-${currentResponsiveOptions.backgroundImageSize}` ] : ( ( backgroundType == "image" && currentResponsiveOptions.backgroundImage ) ? true : false ) },
							{ "has-bg": ( backgroundType == "color" && backgroundColor ) || ( backgroundType == "image" && ( ( backgroundImageColorType == 'color' && backgroundColor ) || backgroundImageColorType == 'gradient' ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && currentResponsiveOptions.backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
							{ [ `vertical-align-${verticalAlignment}` ] : ( heightValue !== undefined || ( inColumn && coverHeight ) ) ? true : false },
							{ "cover-height": inColumn && coverHeight ? true : false },
							
						)
					}
					ref={ this.blockRef }
					style={ style }
				>
					<StateIcons 
                        useResponsiveOptions= { useResponsiveOptions } 
                        isSelected={ isSelected } 
                        currentView={ this.state.responsiveTab }
						mobileVisibility={ mobileVisibility }
                    />			
					{ useBackgroundImage &&
						<div className={ 'bg-image-wrapper' }
							style={ backgroundImageStyle }
						></div>
					}
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder'>
						<InnerBlocks
							renderAppender={ () => (
								<InnerBlocks.ButtonBlockAppender />
							) }
						/>
					</div>
					

				</div>
			</Fragment>
		);
	}
	

}