/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { ToolbarGroup, ToolbarButton } = wp.components;

/**
 * Internal dependencies
 */
import HtmlTagIcon from './html-tag-icon';

class HtmlTagToolbar extends Component {
	createLevelControl( targetLevel, selectedLevel, onChange ) {
		const isActive = targetLevel === selectedLevel;
		return <ToolbarButton
					icon={ <HtmlTagIcon
						level={ targetLevel }
						/> }
					isPressed={ isActive }
					onClick={ () => onChange( targetLevel ) }
				/>
	}
		
	render() {
		const {
			selectedLevel,
			onChange,
		} = this.props;
		
		const levels = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

		return (
			<ToolbarGroup
				label={ __( 'Change HTML tag', 'citadela-pro' ) }
			>
				{
					levels.map( ( index ) => { 
						return this.createLevelControl( index, selectedLevel, onChange ) 
					})
				}
			</ToolbarGroup>

		);
	}
}

export default HtmlTagToolbar;
