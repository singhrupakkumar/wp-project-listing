import LayoutToolbar from '../../components/toolbar-layout';

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { MediaUploadCheck, MediaUpload} = wp.blockEditor;
const { ToolbarGroup, ToolbarButton, ToolbarItem, DropdownMenu, SVG, Path, Rect } = wp.components;

export default class ServiceToolbars extends Component {

	render() {
		const { attributes, setAttributes, state, toggleSwitchIconState } = this.props; 
		const { 
			serviceDesignType,
			serviceImageObject,
			serviceDesignIconClass,
			serviceLayout,
		} = attributes;

		const svgCss = {
			shapeRendering:"geometricPrecision",
			textRendering:"geometricPrecision",
			imageRendering:"optimizeQuality",
			fillRule:"evenodd"
		};

		const icons = {
			headerType: <SVG xmlns="http://www.w3.org/2000/svg" style={ svgCss } width="20" height="20" viewBox="0 0 20 20"><Path d="M3 2l14 0c1,0 1,0 1,1l0 14c0,1 0,1 -1,1l-14 0c-1,0 -1,0 -1,-1l0 -14c0,-1 0,-1 1,-1zm3 2l8 0c1,0 1,0 1,1l0 4c0,1 0,1 -1,1l-8 0c-1,0 -1,0 -1,-1l0 -4c0,-1 0,-1 1,-1zm0 10l4 -3 4 3 -2 0 0 2 -4 0 0 -2 -2 0z"/></SVG>,
			imageAsIcon: <SVG xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><Path d="M4.4,3.5h11.3c0.5,0,0.9,0.4,0.9,0.9v11.3c0,0.5-0.4,0.9-0.9,0.9H4.4c-0.5,0-0.9-0.4-0.9-0.9V4.4C3.5,3.9,3.9,3.5,4.4,3.5zM15.1,15.1V5H5v10.1H15.1z M10.1,7.1c0-0.8-0.6-1.5-1.5-1.5C7.8,5.6,7,6.3,7,7.1s0.6,1.5,1.5,1.5S10.1,7.9,10.1,7.1z M12.2,10.8c0,0,0-4.4,2.2-4.4v7.4c0,0.4-0.4,0.8-0.8,0.8H6.4c-0.4,0-0.8-0.4-0.8-0.8V8.7c1.5,0,2.2,2.9,2.2,2.9s0.8-2.3,2.3-2.3S12.2,10.8,12.2,10.8z"/></SVG>,
			icon: <SVG xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><Path d="M16.5,11.2h-1.7c-0.1,0.5-0.3,1-0.6,1.5l1.2,1.2l-1.6,1.6l-1.2-1.2c-0.4,0.3-0.9,0.5-1.5,0.6v1.7H8.8v-1.7c-0.5-0.1-1-0.3-1.5-0.6l-1.2,1.2l-1.6-1.6l1.2-1.2c-0.3-0.4-0.5-0.9-0.6-1.5H3.5V8.9h1.7c0.1-0.5,0.3-1,0.6-1.5L4.6,6.2l1.6-1.6l1.2,1.2c0.4-0.3,1-0.5,1.5-0.6V3.5h2.3v1.7c0.5,0.1,1,0.3,1.5,0.6l1.2-1.2l1.6,1.6l-1.2,1.2c0.3,0.5,0.5,1,0.6,1.5h1.7V11.2zM10,12.3c1.3,0,2.3-1,2.3-2.3s-1-2.3-2.3-2.3s-2.3,1-2.3,2.3S8.7,12.3,10,12.3z"/></SVG>,
			editImageIcon: <SVG width={ 20 } height={ 20 } viewBox="0 0 20 20"><Rect x={ 11 } y={ 3 } width={ 7 } height={ 5 } rx={ 1 } /><Rect x={ 2 } y={ 12 } width={ 7 } height={ 5 } rx={ 1 } /><Path d="M13,12h1a3,3,0,0,1-3,3v2a5,5,0,0,0,5-5h1L15,9Z" /><Path d="M4,8H3l2,3L7,8H6A3,3,0,0,1,9,5V3A5,5,0,0,0,4,8Z" /></SVG>
		};		
		return(
			<Fragment>
				<ToolbarGroup>
					<ToolbarItem as={ ( toggleProps ) => (
						<LayoutToolbar 
							value={ serviceLayout } 
							onChange={ ( value ) => setAttributes( { serviceLayout: value } ) } 
							toggleProps={ toggleProps }
						/>
					) }/>

					<ToolbarItem as={ ( toggleProps ) => (
						<DropdownMenu
							icon={ icons.headerType }
							label={__('Service header', 'citadela-pro')}
							controls={[
								{
									title: __('Show image', 'citadela-pro'),
									icon: "format-image",
									isActive: serviceDesignType === 'image',
									onClick: () => { setAttributes( { serviceDesignType: 'image' } ) } 
								},
								{
									title: __('Show image as icon', 'citadela-pro'),
									icon: icons.imageAsIcon,
									isActive: serviceDesignType === 'image-as-icon',
									onClick: () => { setAttributes( { serviceDesignType: 'image-as-icon' } ) } 
								},
								{
									title: __('Show icon', 'citadela-pro'),
									icon: icons.icon,
									isActive: serviceDesignType === 'icon',
									onClick: () => { setAttributes( { serviceDesignType: 'icon' } ) } 
								},
								{
									title: __('None', 'citadela-pro'),
									icon: "no",
									isActive: serviceDesignType === 'none',
									onClick: () => { setAttributes( { serviceDesignType: 'none' } ) } 
								}
							]}
							toggleProps={ toggleProps }
						/>
					) }/>
				</ToolbarGroup>

				{ ( serviceImageObject && ( serviceDesignType === "image" || serviceDesignType === "image-as-icon" ) ) &&
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) => { setAttributes( { serviceImageObject: media } ) } }
							allowedTypes="image"
							value={ serviceImageObject.id }
							render={ ( { open } ) => {
									return (
										<ToolbarGroup>
											<ToolbarButton
												icon={ icons.editImageIcon }
												label={ __( 'Change image', 'citadela-pro' ) }
												onClick={ open }
											/>
										</ToolbarGroup>
									);
							}}
						/>
					</MediaUploadCheck>	
				}

				{ serviceDesignType === 'icon'  && 
					<ToolbarGroup>
						<ToolbarButton
							icon={ icons.editImageIcon }
							label={ __( 'Change icon', 'citadela-pro' ) }
							isPressed={ state.switchIcon }
							onClick={ toggleSwitchIconState() }
						/>
					</ToolbarGroup>
				}

			</Fragment>
	    );
	}
}