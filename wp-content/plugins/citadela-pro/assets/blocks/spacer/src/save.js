export default function SpacerSave( { 
	attributes,
	className
} ) {
	const {
		useResponsiveOptions,
		height,
		heightMobile,
		unit,
		unitMobile,
		breakpointMobile,
	} = attributes;

	let style = {};
	let negativeHeight = false;
	const desktopHeightCss = height + unit;
	if(height < 0){
		style={
			...{marginTop: desktopHeightCss }
		}
		negativeHeight = true;
	}else{
		style={
			...{paddingTop: desktopHeightCss }
		}
	}

	//build mobile data
	let attrs = {};
	let useResponsiveAttrs = false;

	if( useResponsiveOptions ){
		const mobileAttributes = {
			height: heightMobile === undefined ? height : heightMobile,
			unit: unitMobile === undefined ? unit : unitMobile,
		}
		
		const mobileHeightCss = mobileAttributes.height + mobileAttributes.unit;

		if( mobileHeightCss != desktopHeightCss ){
			useResponsiveAttrs = true;
			attrs = {
				desktop: {
					height: desktopHeightCss,
					...( height < 0 ? { negative: true } : null )
				},
				mobile: { 
					height: mobileHeightCss,
					...( mobileAttributes.height < 0 ? { negative: true } : null )
				}
			}
		}
	}

	return (
		<div 
			className={ 
				classNames(
					className,
					"citadela-block-spacer",
					{ 'negative-height': negativeHeight },
					{ 'responsive-options loading': useResponsiveAttrs }
				)
			}
			data-block-attr={ useResponsiveAttrs ? JSON.stringify( attrs ) : null }
			data-block-mobile-breakpoint={ useResponsiveAttrs ? breakpointMobile : null }
		>
			<div className="inner-holder" style={style} />
		</div>
	);

}
