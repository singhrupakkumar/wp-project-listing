/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { DropdownMenu } = wp.components;



export default class ToolbarAlignment extends Component {

	render() {
		const { 
			toggleProps,
			value,
			onChange, 
			label = __('Select alignment', 'citadela-directory'), 
			leftLabel = __('Align Text Left', 'citadela-directory'),
			centerLabel = __('Align Text Center', 'citadela-directory'),
			rightLabel = __('Align Text Right', 'citadela-directory'),
			justifyLabel = __('Align Text Justify', 'citadela-directory'),
			allowJustify
		} = this.props; 
		
		const alignmentIcons = {
			left: 'editor-alignleft',
			center: 'editor-aligncenter',
			right: 'editor-alignright',
			justify: 'editor-justify'
		};

		let availableControls = {
            'left': {
				title: leftLabel,
				icon: alignmentIcons.left,
				isActive: value === 'left',
				onClick: () => { onChange('left'); }
			},
            'center': {
				title: centerLabel,
				icon: alignmentIcons.center,
				isActive: value === 'center',
				onClick: () => { onChange('center'); }
			},
            'right': {
				title: rightLabel,
				icon: alignmentIcons.right,
				isActive: value === 'right',
				onClick: () => { onChange('right'); }
			}           
        };

		if( allowJustify ){
			availableControls['justify'] = {
				title: justifyLabel,
				icon: alignmentIcons.justify,
				isActive: value === 'justify',
				onClick: () => { onChange('justify'); }
			};
		}

		let controls = [];
        for (let key in availableControls) {
            controls.push(availableControls[key]);
        }

		return(
			<DropdownMenu
				icon={ alignmentIcons[value] }
				label={ label }
				controls={[controls]}
				toggleProps={ toggleProps }
			/>
	    );
	}
}