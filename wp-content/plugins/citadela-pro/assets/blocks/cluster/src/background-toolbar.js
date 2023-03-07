const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { Toolbar, ToolbarGroup, ToolbarButton, SVG, Path, Rect } = wp.components;
const { MediaUpload, MediaUploadCheck } = wp.blockEditor;


class CustomContainerBackgroundToolbar extends Component {

	render() {
		const { attributes, setAttributes, state } = this.props;
		const { 
			useResponsiveOptions,
			backgroundImage,
			backgroundImageMobile,
			backgroundType
		} = attributes;

		const mobileAttributes = {
			backgroundImage: backgroundImageMobile === undefined ? backgroundImage : backgroundImageMobile,
		}
		const icons = {
			editImageIcon: <SVG width={ 20 } height={ 20 } viewBox="0 0 20 20"><Rect x={ 11 } y={ 3 } width={ 7 } height={ 5 } rx={ 1 } /><Rect x={ 2 } y={ 12 } width={ 7 } height={ 5 } rx={ 1 } /><Path d="M13,12h1a3,3,0,0,1-3,3v2a5,5,0,0,0,5-5h1L15,9Z" /><Path d="M4,8H3l2,3L7,8H6A3,3,0,0,1,9,5V3A5,5,0,0,0,4,8Z" /></SVG>
		}
		const mobile = ( useResponsiveOptions && state.responsiveTab == "mobile" ) ? true : false; 
		return (
			<Fragment>
				{ ( ! mobile && backgroundType == "image" && backgroundImage ) && (
					<MediaUploadCheck>
						<ToolbarGroup>
							<MediaUpload
								onSelect={ ( media ) => {
										setAttributes( {
											backgroundImage: media,
										} ) }
									}
								allowedTypes={ ['image'] }
								value={ backgroundImage ? backgroundImage.id : '' }
								render={ ( { open } ) => (
									<ToolbarButton
										label={ ! useResponsiveOptions ? __( 'Change image', 'citadela-pro' ) : __( 'Change image for desktop resolution', 'citadela-pro' ) }
										icon={ icons.editImageIcon }
										onClick={ open }
									/>
								) }
							/>
						</ToolbarGroup>
					</MediaUploadCheck>
				)}
				{ ( state.responsiveTab == "mobile" && backgroundType == "image" && mobileAttributes.backgroundImage ) && (
					<MediaUploadCheck>
						<ToolbarGroup>
							<MediaUpload
								onSelect={ ( media ) => {
										setAttributes( {
											backgroundImageMobile: media,
										} ) }
									}
								allowedTypes={ ['image'] }
								value={ mobileAttributes.backgroundImage ? mobileAttributes.backgroundImage.id : '' }
								render={ ( { open } ) => (
									<ToolbarButton
										label={ __( 'Change image for mobile resolution', 'citadela-pro' ) }
										icon={ icons.editImageIcon }
										onClick={ open }
									/>
								) }
							/>
						</ToolbarGroup>
					</MediaUploadCheck>
				)}
			</Fragment>
		);
	}
}

export default CustomContainerBackgroundToolbar;
