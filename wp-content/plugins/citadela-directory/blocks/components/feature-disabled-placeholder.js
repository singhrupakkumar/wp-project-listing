/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { Icon } = wp.components;

export default class FeatureDisabled extends Component {

	render() {
		const { 
			title = __('Feature is disabled', 'citadela-directory'), 
			description = '',
			pathText = '',
			icon = true,
		} = this.props; 

		return(
			<div 
				className={ classNames(
	                "feature-disabled-placeholder",
	                icon ? 'with-icon' : null,
	                description ? 'has-description' : null,
	            )}>
				<div class="inner-wrapper">
					<div class="title">
						<h4>{title}</h4>
					</div>
					{ description &&
						<div class="description"><p>{description}</p></div>
					}
					{ pathText &&
						<div class="path-text"><strong><small>{pathText}</small></strong></div>
					}
				</div>
			</div>
	    );
	}
}