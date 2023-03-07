const { RichText } = wp.blockEditor;
const deprecated = [
    {
        attributes: {
            useResponsiveOptions: {
				type: "boolean",
				default: false
			},
			breakpointMobile: {
				type: "number",
				default: 600
			},
			height: {
				type: "number",
				default: 20
			},
			heightMobile: {
				type: "number"
			},
			unit: {
				type: "string",
				default: "px"
			},
			unitMobile: {
				type: "string"
			},
			hideInResponsive: {
				type: "boolean",
				default: false
			}		
            
        },
        save({ attributes, className }){
            
            const {
				height,
				unit,
				hideInResponsive
			} = attributes;

			let style = {};
			let negativeHeight = false;
			if(height < 0){
				style={
					...{marginTop: height+unit}
				}
				negativeHeight = true;
			}else{
				style={
					...{paddingTop: height+unit}
				}
			}

			return (
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-spacer",
							{'negative-height': negativeHeight},
							{'hide-in-responsive': hideInResponsive}
							)
					}
				>
					<div className="inner-holder" style={style} />
				</div>
			);
        }
    }
]
export default deprecated;