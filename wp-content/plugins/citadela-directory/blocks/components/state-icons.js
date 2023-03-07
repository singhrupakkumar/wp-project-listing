/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { Tooltip, Icon } = wp.components;



export default class CitadelaResponsiveState extends Component {

	render() {
		const { useResponsiveOptions, isSelected, currentView, carouselAutoplay, carouselLoop } = this.props;
		const mobileView = ( useResponsiveOptions && currentView == 'mobile' );
		const desktopView = ( useResponsiveOptions && currentView == 'desktop' );
		
		return(
			<div class="citadela-status-icons">
				{ carouselLoop &&
					<Tooltip text={ __( 'Carousel loop mode enabled', 'citadela-directory' ) }>
						<div className="status-icon carousel-state loop">
							<Icon icon="controls-repeat" />
						</div>
					</Tooltip>
				}
				{ carouselAutoplay &&
					<Tooltip text={ __( 'Carousel autoplay enabled', 'citadela-directory' ) }>
						<div className="status-icon carousel-state autoplay">
							<Icon icon="controls-play" />
						</div>
					</Tooltip>
				}
				{ ( useResponsiveOptions && isSelected ) &&
					<>
					{ mobileView &&
						<Tooltip text={ __( 'Current block view', 'citadela-directory' ) + ": " + __('Mobile', 'citadela-directory') }>
							<div className="status-icon current-view-indicator">
								<div class="icon"><Icon icon="smartphone" /></div>
							</div>
						</Tooltip>
					}
					{ desktopView &&
						<Tooltip text={ __( 'Current block view', 'citadela-directory' ) + ": " + __('Desktop', 'citadela-directory') }>
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

