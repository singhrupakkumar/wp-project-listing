import ServiceInspectorControls from './inspector-controls';
import ServiceToolbars from './block-toolbars';
import FontAwesomePicker from "../../components/fontawesome-picker.js";

const { compose } = wp.compose;
const { getBlobByURL, isBlobURL, revokeBlobURL } = wp.blob;
const { __ } = wp.i18n;
const { withNotices, Spinner, Placeholder } = wp.components;
const { Fragment, Component, createRef } = wp.element;
const { RichText, BlockControls, InspectorControls, URLInput, MediaPlaceholder, BlockIcon } = wp.blockEditor;

class ServiceEdit extends Component {

	constructor() {
		super( ...arguments );
        // Refs
		this.serviceRef = createRef();
		this.onMediaSelect = this.onMediaSelect.bind( this );
		this.onUploadError = this.onUploadError.bind( this );
		this.toggleSwitchIconState = this.toggleSwitchIconState.bind( this );
		
		this.state = {
			switchIcon: false,
		};
    }
	
	toggleSwitchIconState() {
		this.setState({switchIcon: !this.state.switchIcon });
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

	onMediaSelect( media ){
		this.props.setAttributes({ serviceImageObject: media});
	}

	onUploadError( message ) {
		const { noticeOperations } = this.props;
		noticeOperations.removeAllNotices();
		noticeOperations.createErrorNotice( message );
	}

	componentDidMount(){
		this.checkServiceWidth();
	}

	componentDidUpdate(){
		this.checkServiceWidth();
	}

	render() {
		const { attributes, setAttributes, isSelected, className, noticeUI } = this.props;
		const {
			serviceTitle,
			serviceDescription,
			serviceBlockBackgroundColor,
			serviceBlockTitleColor,
			serviceBlockTextColor,
			serviceDesignType,
			serviceLayout,
			serviceImageObject,
			serviceLink,
			serviceReadMoreText,
			serviceDesignIconClass,
			serviceDesignIconColor
		} = attributes;
		
		var serviceHeader = null;

		if( serviceDesignType === 'image-as-icon' || serviceDesignType === 'image' ){
			
			
			if( serviceImageObject !== undefined ){
				//image was defined, show header with image
				
				var imageUrl = serviceImageObject.url; //use full image size by default
				var imageStyle = null; //do not use image style if will not be defined later
				var imageTag = <img src={imageUrl} />;
				const isBlobUrl = isBlobURL( imageUrl ); // blob url is used when image is still in upload process

				//try to select citadela image size for special custom cases
				if( serviceLayout === "list" && serviceDesignType === "image" ){
					
					//image already uploaded via Upload button, object structure is different than object of image selected from media, include sizes of image under media_details
					if( !isBlobUrl && serviceImageObject.media_details !== undefined && serviceImageObject.media_details.sizes.citadela_service !== undefined ){
						imageUrl = serviceImageObject.media_details.sizes.citadela_service.source_url;
					}
					//image selected from media, include sizes of image directly
					if( !isBlobUrl && serviceImageObject.sizes !== undefined && serviceImageObject.sizes.citadela_service !== undefined ){
						imageUrl = serviceImageObject.sizes.citadela_service.url;
					}
					
					imageStyle = {backgroundImage: 'url("' + imageUrl + '")' };
					imageTag = null;  //do not render image tag
				}
				const imageDeleteButton = isSelected
					? <div class="delete-image-button" onClick={ () => { setAttributes({serviceImageObject: undefined}) } }><i class="fas fa-times"></i></div>
					: null;

				serviceHeader = ( isBlobUrl )
					? 
					<div className='service-header'>
						<div class='is-transient-image'>
							<Spinner/>
							<div 
								className='service-image'
								style={imageStyle}
							>
								{imageTag}
							</div>
						</div>
					</div>
					: 
					<div className='service-header'>
						<div 
							className='service-image'
							style={imageStyle}
						>
							{imageTag}
							{imageDeleteButton}
						</div>
					</div>;		

			}else{
				//show placeholder to upload image if block is selected
				if( isSelected){
					serviceHeader = 
						<div className='service-header'>
							<MediaPlaceholder
								icon={ <BlockIcon icon={ "format-image" } /> }
								labels={{
									title: __( 'Service Image', 'citadela-pro' ),
									instructions: __( 'Upload an image file or pick one from your media library.', 'citadela-pro' ),
								}}
								onSelect={ ( media ) => { this.onMediaSelect( media ) } }
								notices={ noticeUI }
								onError={ this.onUploadError }
								accept="image/*"
								allowedTypes={ ["image"] }
							/>
						</div>;
				}
			}
		}

		if( serviceDesignType === 'icon' && serviceDesignIconClass ){
			serviceHeader= 
				<div className='service-header'>
					<div className='service-icon'>
						<i className={ serviceDesignIconClass } style={ {color: serviceDesignIconColor } }></i>
					</div>
					{ ( this.state.switchIcon && isSelected ) &&
						<FontAwesomePicker 
							selectedIcon={ serviceDesignIconClass } 
							inlinePicker={ true }
							onChange={ (data) => { setAttributes( { serviceDesignIconClass: data } ); } }
						/>
					}
				</div>;
		}

		return (
			<Fragment>
				<BlockControls key='controls'>
					<ServiceToolbars attributes={attributes} setAttributes={setAttributes} isSelected={isSelected} state={this.state} toggleSwitchIconState={ () => this.toggleSwitchIconState } />
				</BlockControls>

				<InspectorControls key='inspector'>
					<ServiceInspectorControls attributes={ attributes } setAttributes={ setAttributes } />
				</InspectorControls>

				<div 
					className={classNames(
									className,
									'citadela-block-service',
									'main-holder',
									'layout-' + serviceLayout,
									'header-type-' + this.getDesignTypeClass(serviceDesignType),
									{ 'has-readmore': ( (serviceLink != '' && serviceReadMoreText != '') ? true : false ) },
									{ 'has-bg': (serviceBlockBackgroundColor ? true : false ) },
									{ 'is-transient': isBlobURL( imageUrl )},
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
							<RichText
								key='richtext'
								tagName='h3'
								style={ { color: serviceBlockTitleColor } }
								className="service-title"
								onChange= { (value) => { setAttributes( { serviceTitle: value } ) } }
								value= {serviceTitle}
								placeholder={ __('Title', 'citadela-pro' ) }
								keepPlaceholderOnFocus={true}
								allowedFormats={[]}
							/>						
							<RichText
								key='richtext'
								tagName='p'
								style={ { color: serviceBlockTextColor } }
								className="service-description"
								onChange= { (value) => { setAttributes( { serviceDescription: value } ) } }
								value= {serviceDescription}
								placeholder={ __('Service description text', 'citadela-pro' ) }
								keepPlaceholderOnFocus={true}
							/>

							{ ( isSelected || (!isSelected && ( serviceReadMoreText !== '' && serviceLink !== '' ) ) ) &&
								<div className='service-readmore'>
									<RichText
										key='richtext'
										tagName='div'
										className="service-readmore-text"
										onChange= { ( value ) => { setAttributes( { serviceReadMoreText: value } ) } }
										value= { serviceReadMoreText }
										placeholder={ __('read more text', 'citadela-pro' ) }
										keepPlaceholderOnFocus={ true }
										multiline={ false }
										allowedFormats={[]}
									/>
								</div>
							}

							{ isSelected &&
								<URLInput
									label={ __( 'Read more link', 'citadela-pro' ) }
									className=""
									value={ serviceLink }
									autoFocus={ false }
									onChange={ ( value ) => setAttributes( { serviceLink: value } ) }
									//disableSuggestions={ ! isSelected }
									//isFullWidth
									hasBorder
								/>
							}

						</div>
					</div>
				</div>
			</Fragment>
		);
	}

}

export default compose( [
	withNotices,
] )( ServiceEdit );