import defaults from './block.json'

const { RichText } = wp.blockEditor;
const { Component, createRef } = wp.element;

export default class ServiceSave extends Component { 
	constructor() {
		super( ...arguments );
        // Refs
        this.serviceRef = createRef();
	}
	
	getDesignTypeClass( type ){
		//correct header design class
		const { attributes } = this.props;
		const { 
			serviceImageObject,
			serviceDesignIconClass,
			} = attributes;

		let c = type;
		
		switch (type) {
			case 'image':
			case 'image-as-icon':
				if( ! serviceImageObject || serviceImageObject.url == 'undefined' || serviceImageObject.url == ''  ){
					c = 'none';
				}
				break;
			
			case 'icon':
				c = ( typeof serviceDesignIconClass == 'undefined' || serviceDesignIconClass == '' ) ? 'none' : type;
				break;
			default:
				c = type;
				break;
		}

		return c;
	}

	checkServiceWidth(){
		const $serviceDiv = jQuery(this.serviceRef.current);
		const width = $serviceDiv.width();
		const widthBreakpoint = 400;
		if(width >= widthBreakpoint ){
			$serviceDiv.removeClass('narrow').addClass('standard');
		}else{
			$serviceDiv.removeClass('standard').addClass('narrow');
		}
	}

	componentDidMount(){
		this.checkServiceWidth();
	}

	componentDidUpdate(){
		this.checkServiceWidth();
	}

	render(){

		const { attributes, setAttributes, className } = this.props;
		const { 
			serviceTitle,
			serviceDescription,
			serviceBlockBackgroundColor,
			serviceBlockTitleColor,
			serviceBlockTextColor,
			serviceImageObject,
			serviceDesignType,
			serviceLayout,
			serviceLinkNewTab,
			serviceLink,
			serviceReadMoreText,
			serviceDesignIconClass,
			serviceDesignIconColor
		} = attributes;
		
		const serviceLinkAnchorTarget = (serviceLinkNewTab === false || serviceLinkNewTab === "false") ? false : '_blank';
		const serviceLinkAnchorRel = (serviceLinkNewTab === false || serviceLinkNewTab === "false") ? false: 'noopener noreferrer'; //fixing validation error because of rel tag automatically added in gutenberg editor for target=_blank anchors

		let serviceTitleContentComponent = null;
		if(serviceTitle != ''){
			serviceTitleContentComponent = <RichText.Content
													tagName='h3'
													className="service-title"
													value= {serviceTitle}
													style={ { color: serviceBlockTitleColor } }
													/>
			if( serviceLink != '' ){
				serviceTitleContentComponent = (
					<a href={serviceLink} target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}>
						{serviceTitleContentComponent}
					</a>
				);
			}
		}

		var serviceHeader = null;
		if( serviceDesignType === 'image-as-icon' || serviceDesignType === 'image'){
			if(serviceImageObject && serviceImageObject.id !== undefined){
				var serviceImageUrl = serviceImageObject.url;
				if( serviceImageObject.media_details !== undefined ){
					//image already uploaded, different object than object of image from media
					if(serviceLayout === "list" && serviceDesignType === "image" && serviceImageObject.media_details.sizes.citadela_service !== undefined){
						serviceImageUrl = serviceImageObject.media_details.sizes.citadela_service.source_url;
					}
				}else{
					//image selected from media
					if(serviceLayout === "list" && serviceDesignType === "image" && serviceImageObject.sizes.citadela_service !== undefined){
						serviceImageUrl = serviceImageObject.sizes.citadela_service.url;
					}
				}
				const serviceImageStyle = (serviceLayout === "list" && serviceDesignType === "image") ? {backgroundImage: 'url("' + serviceImageUrl + '")' } : null;
				var serviceImageTag = (serviceLayout === "list" && serviceDesignType === "image") 
					? null 
					: ( <img 
							src={serviceImageUrl} 
							alt={serviceImageObject.alt} 
							width={ serviceImageObject.width }
							height={ serviceImageObject.height }
							data-id={ serviceImageObject.id }
							data-full-url={ serviceImageObject.url }
							data-link={ serviceImageObject.link }
							className={ serviceImageObject.id ? `wp-image-${ serviceImageObject.id }` : null }
						/> );

				if(serviceLink != ''){
					serviceImageTag = 
						<a href={serviceLink} target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}>
							{serviceImageTag}
						</a>
				}
				serviceHeader = 
					<div className='service-header'>
						<div className='service-image' style={serviceImageStyle}>
							{serviceImageTag}
						</div>
					</div>;
			}
		}

		if( serviceDesignType === 'icon' && serviceDesignIconClass ){
			var iconStyle = serviceDesignIconColor === undefined ? { color: defaults.attributes.serviceDesignIconColor.default } : { color: serviceDesignIconColor };
			var iconTag = <i className={ serviceDesignIconClass } style={ iconStyle }></i>;
			if(serviceLink != ''){
				iconTag = 
						<a href={serviceLink} target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}>
							{iconTag}
						</a>
			}
			serviceHeader= 
				<div className='service-header'>
					<div className='service-icon'>
						{iconTag}
					</div>
				</div>;
		}


		return (
			<div 
				className={classNames(
								className,
								"citadela-block-service",
								"main-holder",
								'layout-' + serviceLayout,
								'header-type-' + this.getDesignTypeClass(serviceDesignType),
								{ 'has-readmore': ( (serviceLink != '' && serviceReadMoreText != '') ? true : false ) },
								{ "has-bg": (serviceBlockBackgroundColor ? true : false ) },
								'standard'
							)
				}
				style={
					{ backgroundColor: serviceBlockBackgroundColor, }
				}
				ref={ this.serviceRef }
			>
				
				{serviceHeader}
				
				<div className='service-content'>
					<div className='service-content-wrap'>
				
						{serviceTitleContentComponent}
						

						{serviceDescription != '' &&
							<RichText.Content
								tagName='p'
								className="service-description"
								value= {serviceDescription}
								style={ { color: serviceBlockTextColor } }
							/>
						}
						
						{serviceReadMoreText != '' && serviceLink != '' && 
							<a href={serviceLink} className='service-readmore' target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}>
								<span className='service-readmore-text'>
									{serviceReadMoreText}
								</span>
							</a>
						}
					</div>
				</div>
				
			</div>
		);
	}
}