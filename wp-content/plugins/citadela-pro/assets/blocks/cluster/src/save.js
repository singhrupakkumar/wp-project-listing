const { InnerBlocks } = wp.blockEditor;
const { Component } = wp.element;

export default class ClusterSave extends Component { 

	constructor() {
		super( ...arguments );
	}

	render() {
		const { attributes, className } = this.props;

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
			useCustomWidth,
			widthContent,
			widthWide,
			widthFull,
			coverHeight,
			inColumn,
			zIndex,
		} = attributes;
		
		//width settings are available only if Citadela Pro plugin is active
		const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;

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
		
		const widths = {
			content: widthContent,
			wide: widthWide,
			fullwidth: widthFull,
		};

		const innerHolderStyle = {
			...( activeProPlugin && useCustomWidth && widths[sectionsSize] ? { maxWidth: `${widths[sectionsSize]}px` } : {})
		};
		
		const style = {
			...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
			...( backgroundType == "image" && backgroundImage && backgroundImageColorType == 'gradient' ? { backgroundImage: backgroundGradientCss } : {} ),
			...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
			...( backgroundType == 'gradient' ? gradientStyles : {} ),
			...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
			...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
			...( boxShadowStyle ),
			...( zIndex !== undefined && zIndex !== '' ? { zIndex: zIndex } : {} ),
		};
		
		const useBackgroundImage = backgroundType == "image" && backgroundImage;
		
		const backgroundImageStyle = {
			...( useBackgroundImage ? { backgroundImage: `url(${backgroundImage.url})` } : {} ),
			...( useBackgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
			...( useBackgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
			...( overlayBorderRadius > 0 ? { borderRadius: `${overlayBorderRadius}px` } : {} ),
		}

		const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
		
		
		let attr = {};
		if( useBackgroundImage ) {
			
			const mobileAttributes = {
				backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
				backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
				backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
				focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				disableBackgroundImage: disableBackgroundImageMobile,
			}
			attr = {
					//...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
					...( mobileAttributes.disableBackgroundImage ? { disableBackgroundImage: mobileAttributes.disableBackgroundImage } : null ),
					...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${mobileAttributes.backgroundImage}), ${backgroundGradientCss}` : `url(${mobileAttributes.backgroundImage})` } : null ),
					...( mobileAttributes.backgroundImageSize != backgroundImageSize && mobileAttributes.backgroundImageSize != undefined ? { size: `bg-size-${mobileAttributes.backgroundImageSize}` } : null ),
					...( mobileAttributes.backgroundImageFixed != backgroundImageFixed && mobileAttributes.backgroundImageFixed != undefined ? { fixed: mobileAttributes.backgroundImageFixed } : null ),
					...( ( ! mobileAttributes.backgroundImageFixed && mobileAttributes.focalPoint != undefined ) && ( mobileAttributes.focalPoint.x != focalPoint.x || mobileAttributes.focalPoint.y != focalPoint.y ) ? { position: `${ Math.round( mobileAttributes.focalPoint.x * 100 ) }% ${ Math.round( mobileAttributes.focalPoint.y * 100 ) }%` } : null ),
			}
		}

		let haveAttr = false;
		for(var key in attr) {
			if(attr.hasOwnProperty(key)) haveAttr = true;
		}

		const useResponsiveAttrs = ( useResponsiveOptions && backgroundType == "image" && haveAttr )
			? true
			: false;
		
		return (
			<div 
				className={ 
					classNames(
						className,
						"citadela-block-custom-container",
						/*{ "disable-min-height-on-mobile": heightDisableOnMobile ? true : false },*/
						{ "responsive-options loading-image": useResponsiveAttrs ? true : false },
						`size-${sectionsSize}`,
						`bg-type-${backgroundType}`,
						`inside-space-${insideSpace}`,
						{ [ `${mobileVisibility}-on-mobile` ]: mobileVisibility != "always" ? mobileVisibility : false },
						{ [ `bg-size-${backgroundImageSize}` ] : ( ( backgroundType == "image" && backgroundImage ) ? true : false ) },
						{ "has-bg": ( backgroundType == "color" && backgroundColor ) || ( backgroundType == "image" && ( ( backgroundImageColorType == 'color' && backgroundColor ) || backgroundImageColorType == 'gradient' ) ) || backgroundType == "gradient" ? true : false },
						{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
						{ "has-overlay": hasOverlay ? true : false },
						{ "has-border": borderWidth > 0 && borderColor ? true : false },
						{ "has-border-radius": borderRadius > 0  ? true : false },
						{ "has-shadow": hasShadow ? true : false },
						{ "has-min-height" : heightValue !== undefined ? true : false },
						{ [ `vertical-align-${verticalAlignment}` ] : ( heightValue !== undefined || ( inColumn && coverHeight ) ) ? true : false },
						{ "cover-height": inColumn && coverHeight ? true : false },
					)
					
				}
				style={ style }
				data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
				data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
			>

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
				<div className='inner-holder'
					style={ innerHolderStyle }
				>
					<InnerBlocks.Content/>
				</div>

			</div>
			
		);

	}
}
