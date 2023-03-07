/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { Dashicon, SVG, Path, Icon, Tooltip } = wp.components;


export default class CitadelaStateIcons extends Component {

	render() {
		const { useResponsiveOptions, isSelected, currentView, carouselAutoplay, carouselLoop, mobileVisibility } = this.props;
		const mobileView = ( useResponsiveOptions && currentView == 'mobile' );
		const desktopView = ( useResponsiveOptions && currentView == 'desktop' );
		const smartphoneNoIcon = () => <SVG xmlns="http://www.w3.org/2000/svg" class="dashicon" width="20" height="20" viewBox="0 0 20 20"><Path d="M5,14.7V3c0-0.5,0.5-1,1-1h8c0.3,0,0.6,0.2,0.8,0.4L13,4.6V4H7v8.2L5,14.7z M13,9.1V14H9.1l-2.9,3.6C6.4,17.8,6.7,18,7,18h7c0.5,0,1-0.5,1-1V6.6L13,9.1z M8,10l4-5H8V10z M18.3,1.3l-1.1-0.9L2.5,18.7l1.1,0.9L18.3,1.3z"/></SVG>;	
		const desktopNoIcon = () => <SVG xmlns="http://www.w3.org/2000/svg" class="dashicon" width="20" height="20" viewBox="0 0 20 20"><Path d="M5,5h7.7l-0.7,0.9L5,9V5z M3,14h2.4l2.4-3H4V4h9.5l1.6-2H3C2.5,2,2,2.5,2,3v10C2,13.5,2.5,14,3,14z M16,5.2V11h-4.6L8,15.2V16H7.3l-1.6,2H15v-1c0-0.5-0.5-1-1-1h-2v-2h5c0.5,0,1-0.5,1-1V3c0-0.1,0-0.1,0-0.2L16,5.2z M3.6,19.6l-1.1-0.9L17.2,0.4l1.1,0.9L3.6,19.6z"/></SVG>;	
		
		return(
			<div class="citadela-status-icons">
				{ carouselLoop &&
					<Tooltip text={ __( 'Carousel loop mode enabled', 'citadela-pro' ) }>
						<div className="status-icon carousel-state loop">
							<Icon icon="controls-repeat" />
						</div>
					</Tooltip>
				}
				{ carouselAutoplay &&
					<Tooltip text={ __( 'Carousel autoplay enabled', 'citadela-pro' ) }>
						<div className="status-icon carousel-state autoplay">
							<Icon icon="controls-play" />
						</div>
					</Tooltip>
				}
				{ mobileVisibility == "show" &&
						<Tooltip text={ __( 'Hidden on desktop', 'citadela-pro' ) }>
							<div className="status-icon option-indicator">
								<Icon icon={ desktopNoIcon } />
							</div>
						</Tooltip>
				}
				{ mobileVisibility == "hide" &&
					<Tooltip text={ __( 'Hidden on mobile', 'citadela-pro' ) }>
						<div className="status-icon option-indicator">
							<Icon icon={ smartphoneNoIcon } />
						</div>
					</Tooltip>
				}

				{ ( useResponsiveOptions && isSelected ) &&
					<>
					{ mobileView &&
						<Tooltip text={ __( 'Current block view', 'citadela-pro' ) + ": " + __('Mobile', 'citadela-pro') }>
							<div className="status-icon current-view-indicator">
								<div class="icon"><Icon icon="smartphone" /></div>
							</div>
						</Tooltip>
					}
					{ desktopView &&
						<Tooltip text={ __( 'Current block view', 'citadela-pro' ) + ": " + __('Desktop', 'citadela-pro') }>
							<div className="status-icon current-view-indicator">
								<div class="icon"><Icon icon="desktop" /></div>
							</div>
						</Tooltip>
					}
					</>
				}
			</div>
	    );
	}
}

