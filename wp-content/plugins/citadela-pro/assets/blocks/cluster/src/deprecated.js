const { InnerBlocks} = wp.blockEditor;
const blockSupports = {};

const blockAttributes = {
	backgroundColor: {
		type: "string"
	},
	backgroundImageUrl: {
		type: "string"
	},
	backgroundImageId: {
		type: "number"
	},
	backgroundImageFixed: {
		type: "boolean",
		default: false
	},
	sectionsSize: {
		type: "string",
		default: "content"
	},
	focalPoint: {
		type: "object",
		default: {
			x: 0.5,
			y: 0.5,
		}
	},
	useBackgroundImage: {
		type: "boolean",
		default: false
	},
	backgroundImage: {
		type: "object"
	},
	backgroundOverlayType: {
		type: "string",
		default: "color"
	},
	backgroundOverlayColor: {
		type: "string"
	},
	backgroundOverlayOpacity: {
		type: "number",
		default: 50
	},
	backgroundOverlayGradient: {
		type: "object",
		default: {
			first: "#dddddd",
			second: "#f6f6f6",
			degree: 90,
			type: "linear",
		}
	},
	backgroundType: {
		type: "string",
		default: "none"
	},
	backgroundImageSize: {
		type: "string",
		default: "cover"
	},
	backgroundImageRepeat: {
		type: "string",
		default: "no-repeat"
	},
	backgroundGradient: {
		type: "object",
		default: {
			first: "#dddddd",
			second: "#f6f6f6",
			degree: 90,
			type: "linear",
		}
	},
	borderWidth: {
		type: "number",
		default: 0
	},
	borderColor: {
		type: "string"
	},
	borderRadius: {
		type: "number",
		default: 0
	},
	boxShadow: {
		type: "object"
	},
	mobileVisibility: {
		type: "string",
		default: "always"			
	},
	heightUnit: {
		type: "string",
		default: "px"
	},
	heightValue: {
		type: "number"
	},
	heightDisableOnMobile: {
		type: "boolean",
		default: false
	},
	verticalAlignment: {
		type: "string",
		default: "top"
	},
	useBackgroundImageGradient: {
		type: "boolean",
		default: false
	},
	backgroundImageColorType: {
		type: "string",
		default: "color"
	},
	insideSpace: {
		type: "string",
		default: "none"
	},

	useResponsiveOptions: {
		type: "boolean",
		default: false
	},
	backgroundImageFixedMobile: {
		type: "boolean"
	},
	focalPointMobile: {
		type: "object"
	},
	backgroundImageMobile: {
		type: "object"
	},
	backgroundImageSizeMobile: {
		type: "string"
	},
	breakpointMobile: {
		type: "number",
		default: 600
	},
	width:{
		type: 'number',
	},
	useCustomWidth: {
		type: "boolean"
	},
	widthContent: {
		type: "number",
		default: 768
	},
	widthWide: {
		type: "number",
		default: 1200
	},
	widthFull: {
		type: "number",
		default: 1920
	},
	inColumn: {
		type: "boolean",
		default: false
	},
	coverHeight: {
		type: "boolean",
		default: false
	},
	disableBackgroundImageMobile: {
		type: "boolean",
		default: false
	}
};



const deprecated = [
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				attr = {
						//...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				attr = {
						//...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
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
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
							{ "cover-height": inColumn && coverHeight ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>
	
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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				backgroundImageFixedMobile,
				focalPointMobile,
				backgroundImageMobile,
				backgroundImageSizeMobile,
				breakpointMobile,
				useCustomWidth,
				widthContent,
				widthWide,
				widthFull,
				
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
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
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				attr = {
						//...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
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
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>
	
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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				backgroundImageFixedMobile,
				focalPointMobile,
				backgroundImageMobile,
				backgroundImageSizeMobile,
				breakpointMobile,
				useCustomWidth,
				widthContent,
				widthWide,
				widthFull,
				
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
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
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				attr = {
						//...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
						...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${mobileAttributes.backgroundImage}), ${backgroundGradientCss}` : `url(${mobileAttributes.backgroundImage})` } : null ),
						...( mobileAttributes.backgroundImageSize != backgroundImageSize && mobileAttributes.backgroundImageSize != undefined ? { size: `bg-size-${mobileAttributes.backgroundImageSize}` } : null ),
						...( mobileAttributes.backgroundImageFixed != backgroundImageFixed && mobileAttributes.backgroundImageFixed != undefined ? { fixed: mobileAttributes.backgroundImageFixed } : null ),
						...( ( ! mobileAttributes.backgroundImageFixed && mobileAttributes.focalPoint != undefined ) && ( mobileAttributes.focalPoint.x != focalPoint.x || mobileAttributes.focalPoint.y != focalPoint.y ) ? { position: mobileAttributes.focalPoint } : null ),
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
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>
	
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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				backgroundImageFixedMobile,
				focalPointMobile,
				backgroundImageMobile,
				backgroundImageSizeMobile,
				breakpointMobile,
				useCustomWidth,
				widthContent,
				widthWide,
				widthFull,
				
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
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
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: backgroundImageColorType == 'gradient' ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( ( backgroundType == "image" && backgroundImageColorType == 'color' ) || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				
				attr = {
						...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
						...( mobileAttributes.backgroundImageSize != backgroundImageSize && mobileAttributes.backgroundImageSize != undefined ? { size: `bg-size-${mobileAttributes.backgroundImageSize}` } : null ),
						...( mobileAttributes.backgroundImageFixed != backgroundImageFixed && mobileAttributes.backgroundImageFixed != undefined ? { fixed: mobileAttributes.backgroundImageFixed } : null ),
						...( ( ! mobileAttributes.backgroundImageFixed && mobileAttributes.focalPoint != undefined ) && ( mobileAttributes.focalPoint.x != focalPoint.x || mobileAttributes.focalPoint.y != focalPoint.y ) ? { position: mobileAttributes.focalPoint } : null ),
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
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>

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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				insideSpace,
	
				useResponsiveOptions,
				backgroundImageFixedMobile,
				focalPointMobile,
				backgroundImageMobile,
				backgroundImageSizeMobile,
				breakpointMobile,
				useCustomWidth,
				widthContent,
				widthWide,
				widthFull,
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			const widths = {
				content: widthContent,
				wide: widthWide,
				fullwidth: widthFull,
			};

			const innerHolderStyle = {
				...( activeProPlugin && useCustomWidth && widths[sectionsSize] ? { maxWidth: `${widths[sectionsSize]}px` } : {})
			};
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				
				attr = {
						...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
						...( mobileAttributes.backgroundImageSize != backgroundImageSize && mobileAttributes.backgroundImageSize != undefined ? { size: `bg-size-${mobileAttributes.backgroundImageSize}` } : null ),
						...( mobileAttributes.backgroundImageFixed != backgroundImageFixed && mobileAttributes.backgroundImageFixed != undefined ? { fixed: mobileAttributes.backgroundImageFixed } : null ),
						...( ( ! mobileAttributes.backgroundImageFixed && mobileAttributes.focalPoint != undefined ) && ( mobileAttributes.focalPoint.x != focalPoint.x || mobileAttributes.focalPoint.y != focalPoint.y ) ? { position: mobileAttributes.focalPoint } : null ),
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
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>
	
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder' style={ innerHolderStyle } >
						<InnerBlocks.Content/>
					</div>
	
				</div>
				
			);
		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				insideSpace,
	
				useResponsiveOptions,
				backgroundImageFixedMobile,
				focalPointMobile,
				backgroundImageMobile,
				backgroundImageSizeMobile,
				breakpointMobile,
				width
			} = attributes;
			
			
	
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			const innerHolderStyle = {
				...( width ? { maxWidth: `${width}px` } : {})
			};
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				
				attr = {
						...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
						...( mobileAttributes.backgroundImageSize != backgroundImageSize && mobileAttributes.backgroundImageSize != undefined ? { size: `bg-size-${mobileAttributes.backgroundImageSize}` } : null ),
						...( mobileAttributes.backgroundImageFixed != backgroundImageFixed && mobileAttributes.backgroundImageFixed != undefined ? { fixed: mobileAttributes.backgroundImageFixed } : null ),
						...( ( ! mobileAttributes.backgroundImageFixed && mobileAttributes.focalPoint != undefined ) && ( mobileAttributes.focalPoint.x != focalPoint.x || mobileAttributes.focalPoint.y != focalPoint.y ) ? { position: mobileAttributes.focalPoint } : null ),
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
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>
	
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder' style={ innerHolderStyle } >
						<InnerBlocks.Content/>
					</div>
	
				</div>
				
			);
		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
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
				insideSpace,
	
				useResponsiveOptions,
				backgroundImageFixedMobile,
				focalPointMobile,
				backgroundImageMobile,
				backgroundImageSizeMobile,
				breakpointMobile,
			} = attributes;
			
			
	
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ Math.round( focalPoint.x * 100 ) }% ${ Math.round( focalPoint.y * 100 ) }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			
			let attr = {};
			if( backgroundType == "image" && backgroundImage ) {
				
				const mobileAttributes = {
					backgroundImage: backgroundImageMobile === undefined ? backgroundImage.url : backgroundImageMobile.url,
					backgroundImageSize: backgroundImageSizeMobile === undefined ? backgroundImageSize : backgroundImageSizeMobile,
					backgroundImageFixed: backgroundImageFixedMobile === undefined ? backgroundImageFixed : backgroundImageFixedMobile,
					focalPoint: focalPointMobile === undefined ? focalPoint : focalPointMobile,
				}
				
				attr = {
						...( mobileAttributes.backgroundImage != backgroundImage.url && mobileAttributes.backgroundImage != undefined ? { image: mobileAttributes.backgroundImage } : null ),
						...( mobileAttributes.backgroundImageSize != backgroundImageSize && mobileAttributes.backgroundImageSize != undefined ? { size: `bg-size-${mobileAttributes.backgroundImageSize}` } : null ),
						...( mobileAttributes.backgroundImageFixed != backgroundImageFixed && mobileAttributes.backgroundImageFixed != undefined ? { fixed: mobileAttributes.backgroundImageFixed } : null ),
						...( ( ! mobileAttributes.backgroundImageFixed && mobileAttributes.focalPoint != undefined ) && ( mobileAttributes.focalPoint.x != focalPoint.x || mobileAttributes.focalPoint.y != focalPoint.y ) ? { position: mobileAttributes.focalPoint } : null ),
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
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
					data-block-attr={ useResponsiveAttrs ? JSON.stringify(attr) : null }
					data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
				>
	
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder'>
						<InnerBlocks.Content/>
					</div>
	
				</div>
				
			);
		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,

		save( { attributes,	className } ) {
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
			} = attributes;
			
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							/*{ [ `${mobileVisibility}-on-mobile` ]: mobileVisibility != "always" ? mobileVisibility : false },*/
							/*{ "disable-min-height-on-mobile": heightDisableOnMobile ? true : false },*/
							`size-${sectionsSize}`,
							`bg-type-${backgroundType}`,
							{ [ `bg-size-${backgroundImageSize}` ] : ( ( backgroundType == "image" && backgroundImage ) ? true : false ) },
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
				>
					
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder'>
						<InnerBlocks.Content/>
					</div>
					
	
				</div>
				
			);
		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,

		save( { attributes,	className } ) {
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
			} = attributes;
			
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							/*{ [ `${mobileVisibility}-on-mobile` ]: mobileVisibility != "always" ? mobileVisibility : false },*/
							/*{ "disable-min-height-on-mobile": heightDisableOnMobile ? true : false },*/
							`size-${sectionsSize}`,
							`bg-type-${backgroundType}`,
							{ [ `bg-size-${backgroundImageSize}` ] : ( ( backgroundType == "image" && backgroundImage ) ? true : false ) },
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
							`vertical-align-${verticalAlignment}`,
						)
						
					}
					style={ style }
				>
					
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder'>
						<InnerBlocks.Content/>
					</div>
					
	
				</div>
				
			);
		}
	},



	{
		supports: blockSupports,
		attributes: blockAttributes,

		save( { attributes,	className } ) {
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
			} = attributes;
			
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							/*{ [ `${mobileVisibility}-on-mobile` ]: mobileVisibility != "always" ? mobileVisibility : false },*/
							/*{ "disable-min-height-on-mobile": heightDisableOnMobile ? true : false },*/
							`size-${sectionsSize}`,
							{ [ `vertical-align-${verticalAlignment}` ] : heightValue !== undefined ? true : false },
							`bg-type-${backgroundType}`,
							{ [ `bg-size-${backgroundImageSize}` ] : ( ( backgroundType == "image" && backgroundImage ) ? true : false ) },
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
				>
					
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder'>
						<InnerBlocks.Content/>
					</div>
					
	
				</div>
				
			);
		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,

		save( { attributes,	className } ) {
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
			} = attributes;
			
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
			
			const boxShadowStyle = ( hasShadow ) ? {
				boxShadow: `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
			} : {};
			
			const style = {
				...( heightValue !== undefined ? { minHeight: heightValue + heightUnit } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundImage: useBackgroundImageGradient ? `url(${backgroundImage.url}), ${backgroundGradientCss}` : `url(${backgroundImage.url})` } : {} ),
				...( backgroundType == "image" && backgroundImage ? { backgroundRepeat: backgroundImageRepeat } : {} ),
				...( backgroundColor && ( backgroundType == "image" || backgroundType == "color" ) ? { backgroundColor: backgroundColor } : {} ),
				...( backgroundType == "image" && backgroundImage && focalPoint && !backgroundImageFixed ? { backgroundPosition: `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` } : {} ),
				...( backgroundType == 'gradient' ? gradientStyles : {} ),
				...( hasBorder ? { border: `${borderWidth}px solid ${borderColor}` } : {} ),
				...( borderRadius > 0 ? { borderRadius: `${borderRadius}px` } : {} ),
				...( boxShadowStyle ),
			};
			
			const hasOverlay = backgroundType == "image" && backgroundOverlayOpacity && ( backgroundOverlayColor || backgroundOverlayGradient ) ? true : false;
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							/*{ [ `${mobileVisibility}-on-mobile` ]: mobileVisibility != "always" ? mobileVisibility : false },*/
							/*{ "disable-min-height-on-mobile": heightDisableOnMobile ? true : false },*/
							`size-${sectionsSize}`,
							`vertical-align-${verticalAlignment}`,
							`bg-type-${backgroundType}`,
							{ [ `bg-size-${backgroundImageSize}` ] : ( ( backgroundType == "image" && backgroundImage ) ? true : false ) },
							{ "has-bg": ( ( backgroundType == "color" || backgroundType == "image" ) && ( backgroundColor || useBackgroundImageGradient ) ) || backgroundType == "gradient" ? true : false },
							{ "fixed-bg": backgroundType == "image" && backgroundImageFixed ? true : false },
							{ "has-overlay": hasOverlay ? true : false },
							{ "has-border": borderWidth > 0 && borderColor ? true : false },
							{ "has-border-radius": borderRadius > 0  ? true : false },
							{ "has-shadow": hasShadow ? true : false },
							{ "has-min-height" : heightValue !== undefined ? true : false },
						)
						
					}
					style={ style }
				>
					
					{ hasOverlay &&
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					}
					<div className='inner-holder'>
						<InnerBlocks.Content/>
					</div>
					
	
				</div>
				
			);
		}
	},





	//deprecated hotfix	for missing focal point value
	{
		supports: blockSupports,
		attributes: blockAttributes,
		migrate( attributes ) {
			//do update in version 1.3.0, compatibility because of added option backgroundType, define constant backgroundType on the base of previous image and color settings
		
			if( attributes.useBackgroundImage ){
				attributes.backgroundType = "image";
			}else if( attributes.backgroundColor ){
				attributes.backgroundType = "color";
			}

			return attributes;
		},
		save( { attributes,	className } ) {
			
			const { 
				backgroundColor,
				backgroundImageFixed,
				sectionsSize,
				focalPoint,
				backgroundImage,
				useBackgroundImage,
				backgroundOverlayColor,
				backgroundOverlayOpacity
			} = attributes;
			
			const style = {
				...( ( useBackgroundImage && backgroundImage ) ? { backgroundImage: `url(${backgroundImage.url})` } : {} ),
				...( backgroundColor ? { backgroundColor: backgroundColor } : {} ),
			};
			
			const overlayStyle = {
				...( backgroundOverlayColor ? { backgroundColor: backgroundOverlayColor } : {} ),
				...( backgroundOverlayOpacity ? { opacity: backgroundOverlayOpacity/100 } : {} ),
			}
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							"size-" + sectionsSize,
							{ "has-bg": (backgroundColor ? true : false ) },
							{ "fixed-bg": ( (useBackgroundImage && backgroundImage && backgroundImageFixed ) ? true : false ) },
							{ "has-overlay": ( ( useBackgroundImage && backgroundOverlayColor && backgroundOverlayOpacity ) ? true : false ) },
							)
					}
					style={ style }
				>
					{ ( useBackgroundImage && backgroundOverlayColor && backgroundOverlayOpacity && backgroundOverlayOpacity !== "0") ? (
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					) : "" }
					<div className='inner-holder'>
							<InnerBlocks.Content/>
					</div>
				</div>
			);

		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		migrate( attributes ) {
			//do update in version 1.3.0, compatibility because of added option backgroundType, define constant backgroundType on the base of previous image and color settings
		
			if( attributes.useBackgroundImage ){
				attributes.backgroundType = "image";
			}else if( attributes.backgroundColor ){
				attributes.backgroundType = "color";
			}

			return attributes;
		},
		save( { attributes,	className } ) {
			
			const { 
				backgroundColor,
				backgroundImageFixed,
				sectionsSize,
				focalPoint,
				backgroundImage,
				useBackgroundImage,
				backgroundOverlayColor,
				backgroundOverlayOpacity
			} = attributes;
			
			const style = {
				...( ( useBackgroundImage && backgroundImage ) ? { backgroundImage: `url(${backgroundImage.url})` } : {} ),
				...( backgroundColor ? { backgroundColor: backgroundColor } : {} ),
				...( ( focalPoint && useBackgroundImage && !backgroundImageFixed ) ? { backgroundPosition: `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` } : {} )
			};
			
			const overlayStyle = {
				...( backgroundOverlayColor ? { backgroundColor: backgroundOverlayColor } : {} ),
				...( backgroundOverlayOpacity ? { opacity: backgroundOverlayOpacity/100 } : {} ),
			}
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							"size-" + sectionsSize,
							{ "has-bg": (backgroundColor ? true : false ) },
							{ "fixed-bg": ( (useBackgroundImage && backgroundImage && backgroundImageFixed ) ? true : false ) },
							{ "has-overlay": ( ( useBackgroundImage && backgroundOverlayColor && backgroundOverlayOpacity ) ? true : false ) },
							)
					}
					style={ style }
				>
					{ ( useBackgroundImage && backgroundOverlayColor && backgroundOverlayOpacity && backgroundOverlayOpacity !== "0") ? (
						<div className={ 'bg-image-overlay' }
							style={ overlayStyle }
						></div>
					) : "" }
					<div className='inner-holder'>
							<InnerBlocks.Content/>
					</div>
				</div>
			);

		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
			const { 
				backgroundColor,
				backgroundImageFixed,
				sectionsSize,
				focalPoint,
				backgroundImage,
				useBackgroundImage
			} = attributes;
			
			const style = {
				...( backgroundImage ? { backgroundImage: `url(${backgroundImage.url})` } : {} ),
				...( backgroundColor ? { backgroundColor: backgroundColor } : {} ),
				...( ( focalPoint && useBackgroundImage && !backgroundImageFixed ) ? { backgroundPosition: `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` } : {} )
			};
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							{ "has-bg": (backgroundColor ? true : false ) },
							{ "fixed-bg": (backgroundImageFixed ? true : false ) },
							"size-" + sectionsSize,
							)
					}
					style={	style }
				>
					<div className='inner-holder'>
							<InnerBlocks.Content/>
					</div>
				</div>
			);

		}
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes,	className } ) {
			const { 
				backgroundColor,
				backgroundImageUrl,
				backgroundImageFixed,
				sectionsSize
			} = attributes;
			
			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-custom-container",
							{ "has-bg": (backgroundColor ? true : false ) },
							{ "fixed-bg": (backgroundImageFixed ? true : false ) },
							"size-" + sectionsSize,
							)
					}
					style={ 
							{
								backgroundColor: backgroundColor,
								backgroundImage: backgroundImageUrl? 'url(' + backgroundImageUrl + ')' : "",
							} 
						}
				>
					<div className='inner-holder'>
							<InnerBlocks.Content/>
					</div>
				</div>
			);

		}
	},
];

export default deprecated;
