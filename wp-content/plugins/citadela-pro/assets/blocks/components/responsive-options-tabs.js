/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Fragment, Component } = wp.element;
const { Button } = wp.components;



export default class ResponsiveOptionsTabs extends Component {

	render() {
		const { activeTab, onChange } = this.props; 
		const icons = {
					desktop: "desktop",
					mobile: "smartphone"
				};	
		let tabLabel = activeTab == "desktop"
			? __( 'General options for desktop.', 'citadela-pro')
			: __( 'Customized options for mobile screen width.', 'citadela-pro');

		return(
			<Fragment>
				<div class="citadela-responsive-options-tabs">
					<div class="responsive-tabs">
						<div className={ classNames( "desktop-tab", { "active": activeTab === "desktop" } ) }>
							<Button
								className={ classNames( "components-toolbar__control", { "is-active": activeTab === "desktop" } ) }
								icon={ icons.desktop }
								label={ __("Desktop size", "citadela-pro") }
								onClick={ () => { onChange("desktop") } }
							/>
						</div>
						<div className={ classNames( "mobile-tab", { "active": activeTab === "mobile" } ) }>
							<Button
								className={ classNames( "components-toolbar__control", { "is-active": activeTab === "mobile" } ) }
								icon={ icons.mobile }
								label={ __("Mobile size", "citadela-pro") }
								onClick={ () => { onChange("mobile") } }
							/>
						</div>
					</div>
					<p class="tab-label">{ tabLabel }</p>
				</div>

			</Fragment>
	    );
	}
}