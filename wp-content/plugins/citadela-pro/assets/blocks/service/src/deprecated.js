const { RichText } = wp.blockEditor;
const { Component } = wp.element;
const blockSupports = {};

const blockAttributes = {
	serviceTitle: {
		type: 'array',
		source: 'children',
		selector: '.service-title',
	},
	serviceDescription: {
		type: 'array',
		source: 'children',
		selector: '.service-description',
	},
	serviceImageObject: {
		type: 'object',
	},
	serviceBlockBackgroundColor: {
		type: 'string',
	},
	serviceBlockTitleColor: {
		type: 'string',
	},
	serviceBlockTextColor: {
		type: 'string',
	},
	serviceDesignType: {
		type: 'string',
		default: 'icon',
	},
	serviceDesignIconClass: { 
		type: 'text', 
		default: 'fas fa-cog'
	},
	serviceDesignIconColor: { 
		type: 'string', 
		default: '#b9b9b9'
	},
	serviceLayout: {
		type: 'string',
		default: 'box'
	},
	serviceLinkNewTab: {
		type: 'boolean',
		default: "false"
	},
	serviceLink: {
		type: 'text',
		default: ''
	},
	serviceReadMoreText: {
		type: 'string',
		default: '',
	},
};

const deprecated = [
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save({ attributes, className }){
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

			function getDesignTypeClass( type ){
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
					var serviceImageTag = (serviceLayout === "list" && serviceDesignType === "image") ? null : <img src={serviceImageUrl} alt={serviceImageObject.alt} />;
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
									'header-type-' + getDesignTypeClass(serviceDesignType),
									{ 'has-readmore': ( (serviceLink != '' && serviceReadMoreText != '') ? true : false ) },
									{ "has-bg": (serviceBlockBackgroundColor ? true : false ) },
									'standard'
								)
					}
					style={
						{ backgroundColor: serviceBlockBackgroundColor, }
					}
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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save({ attributes, className }){
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
			
			function getDesignTypeClass( type ){
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
					var serviceImageTag = (serviceLayout === "list" && serviceDesignType === "image") ? null : <img src={serviceImageUrl} alt={serviceImageObject.alt} />;
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
									'header-type-' + getDesignTypeClass(serviceDesignType),
									{ 'has-readmore': ( (serviceLink != '' && serviceReadMoreText != '') ? true : false ) },
									{ "has-bg": (serviceBlockBackgroundColor ? true : false ) }
								)
					}
					style={
						{ backgroundColor: serviceBlockBackgroundColor, }
					}
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
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes, className } ) {
			const {
				serviceTitle,
				serviceDescription,
				serviceImageObject,
				serviceBlockBackgroundColor,
				serviceBlockTitleColor,
				serviceBlockTextColor,
				serviceDesignType,
				serviceDesignIconColor,
				serviceDesignIconClass,
				serviceLayout,
				serviceLinkNewTab,
				serviceLink,
				serviceReadMoreText,
			} = attributes;


			const serviceLinkAnchorTarget = (serviceLinkNewTab) ? '_blank' : false;
			const serviceLinkAnchorRel = (serviceLinkNewTab) ? 'noopener noreferrer' : false; //fixing validation error because of rel tag automatically added in gutenberg editor for target=_blank anchors


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


			function getDesignTypeClass( type ){
				//correct header design class
				let c = type;
				
				switch (type) {
					case 'image':
					case 'image-as-icon':
						if( ! serviceImageObject || serviceImageObject.url == 'undefined' || serviceImageObject.url == ''  ){
							c = 'none';
						}
						//c = ( typeof serviceImageURL == 'undefined' || serviceImageURL == '' ) ? 'none' : type;
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
			const serviceDesignTypeClass = getDesignTypeClass(serviceDesignType);




			return (
				<div 
					className={classNames(
									className,
									"citadela-block-service",
									"main-holder",
									'layout-' + serviceLayout,
									'header-type-' + serviceDesignTypeClass,
									{ 'has-readmore': ( (serviceLink != '' && serviceReadMoreText != '') ? true : false ) },
									{ "has-bg": (serviceBlockBackgroundColor ? true : false ) }
								)
					}
					style={
						{ backgroundColor: serviceBlockBackgroundColor, }
					}
				>
					
					<ServiceHeader2 attributes={attributes} action="save" />
					
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
		},
	},
	{
		supports: blockSupports,
		attributes: blockAttributes,
		save( { attributes, className } ) {
			const {
				serviceTitle,
				serviceDescription,
				serviceImageObject,
				serviceBlockBackgroundColor,
				serviceBlockTitleColor,
				serviceBlockTextColor,
				serviceDesignType,
				serviceDesignIconColor,
				serviceDesignIconClass,
				serviceLayout,
				serviceLinkNewTab,
				serviceLink,
				serviceReadMoreText,
			} = attributes;


			const serviceLinkAnchorTarget = (serviceLinkNewTab) ? '_blank' : false;
			const serviceLinkAnchorRel = (serviceLinkNewTab) ? 'noopener noreferrer' : false; //fixing validation error because of rel tag automatically added in gutenberg editor for target=_blank anchors


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


			function getDesignTypeClass( type ){
				//correct header design class
				let c = type;
				
				switch (type) {
					case 'image':
					case 'image-as-icon':
						if( ! serviceImageObject || serviceImageObject.url == 'undefined' || serviceImageObject.url == ''  ){
							c = 'none';
						}
						//c = ( typeof serviceImageURL == 'undefined' || serviceImageURL == '' ) ? 'none' : type;
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
			const serviceDesignTypeClass = getDesignTypeClass(serviceDesignType);




			return (
				<div 
					className={classNames(
									className,
									"citadela-block-service",
									"main-holder",
									'layout-' + serviceLayout,
									'header-type-' + serviceDesignTypeClass,
									{ 'has-readmore': ( (serviceLink != '' && serviceReadMoreText != '') ? true : false ) },
									{ "has-bg": (serviceBlockBackgroundColor ? true : false ) }
								)
					}
					style={
						{ backgroundColor: serviceBlockBackgroundColor, }
					}
				>
					
					<ServiceHeader1 attributes={attributes} />
					
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
		},
	},
];

export default deprecated;

class ServiceHeader2 extends Component {
	render() {
		const { 
			serviceDesignType,
			serviceLayout,
			serviceImageObject,
			serviceDesignIconColor,
			serviceDesignIconClass,
			serviceLink,
			serviceLinkNewTab
		} = this.props.attributes;
		const action = this.props.action;
		const serviceLinkAnchorTarget = (serviceLinkNewTab) ? '_blank' : false;
		const serviceLinkAnchorRel = (serviceLinkNewTab) ? 'noopener noreferrer' : false; //fixing validation error because of rel tag automatically added in gutenberg editor for target=_blank anchors

		let result;
		switch (serviceDesignType) {
			case 'image-as-icon':
			case 'image':
				if(serviceImageObject){
					let serviceImageUrl = serviceImageObject.sizes.full.url;
					let serviceImageImg = <img src={serviceImageUrl} alt={serviceImageObject.alt} />;
					if( serviceLink != '' && action != "edit"){
						serviceImageImg = (
							<a href={serviceLink} target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}>
								{serviceImageImg}
							</a>
						);
					}

					let serviceImageDiv = (<div className='service-image'>
												{serviceImageImg}
											</div>);

					if(serviceLayout === "list" && serviceDesignType === "image"){
						//serviceImageImg = <img src={serviceImageObject.sizes.full.url} alt={serviceImageObject.alt} />;
						serviceImageUrl = serviceImageObject.sizes.citadela_service !== undefined ? serviceImageObject.sizes.citadela_service.url : serviceImageObject.sizes.full.url;
						const anchorTag = ( serviceLink != '' && action != "edit") ? (<a href={serviceLink} target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}></a>) : '';
						serviceImageDiv = <div className='service-image' style={ {backgroundImage: 'url("' + serviceImageUrl + '")' } }>{anchorTag}</div>;
					}

					result = (
							<div className='service-header'>
								{serviceImageDiv}
							</div>
							);
				}else{
					result = null;
				}
				break;
			case 'icon':
				if(serviceDesignIconClass){
					var iconTag = <i className={ serviceDesignIconClass } style={ {color: serviceDesignIconColor } }></i>;
					if( serviceLink != '' && action != "edit"){
						iconTag = (
							<a href={serviceLink} target={serviceLinkAnchorTarget} rel={serviceLinkAnchorRel}>
								{iconTag}
							</a>
						);
					}
					result = (
							<div className='service-header'>
								<div className='service-icon'>
									{iconTag}
								</div>
							</div>
							);
				}else{
					return null;
				}

				break;
			default:
				return null;
		}
		return result;
	}
}

class ServiceHeader1 extends Component {
	render() {
		const { 
			serviceDesignType,
			serviceLayout,
			serviceImageObject,
			serviceDesignIconColor,
			serviceDesignIconClass,
			serviceLink,
			serviceLinkNewTab
		} = this.props.attributes;
		let result;
		switch (serviceDesignType) {
			case 'image-as-icon':
			case 'image':
				if(serviceImageObject){
					let serviceImageUrl = serviceImageObject.sizes.full.url;
					let serviceImageImg = <img src={serviceImageUrl} alt={serviceImageObject.alt} />;
					let serviceImageDiv = (<div className='service-image'>
												{serviceImageImg}
											</div>);

					if(serviceLayout === "list" && serviceDesignType === "image"){
						//serviceImageImg = <img src={serviceImageObject.sizes.full.url} alt={serviceImageObject.alt} />;
						serviceImageUrl = serviceImageObject.sizes.citadela_service !== undefined ? serviceImageObject.sizes.citadela_service.url : serviceImageObject.sizes.full.url;
						serviceImageDiv = <div className='service-image' style={ {backgroundImage: 'url("' + serviceImageUrl + '")' } }></div>;
					}

					result = (
							<div className='service-header'>
								{serviceImageDiv}
							</div>
							);
				}else{
					result = null;
				}
				break;
			case 'icon':
				if(serviceDesignIconClass){
					result = (
							<div className='service-header'>
								<div className='service-icon'>
									<i className={ serviceDesignIconClass } style={ {color: serviceDesignIconColor } }></i>
								</div>
							</div>
							);
				}else{
					return null;
				}

				break;
			default:
				return null;
		}
		return result;
	}
}