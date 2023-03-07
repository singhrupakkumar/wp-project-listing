const { __ } = wp.i18n;
const { Component } = wp.element;
const { DropdownMenu } = wp.components;

export default class PageTitleAlignToolbar extends Component {

	render() {
		const { 
			toggleProps,
			align, 
			onChange,
			label = __('Select alignment', 'citadela-pro'),
		} = this.props;

		let alignmentIcons = [];
		alignmentIcons['align-left'] = 'editor-alignleft';
		alignmentIcons['align-center'] = 'editor-aligncenter';
		alignmentIcons['align-right'] = 'editor-alignright';

		return (
			<DropdownMenu
				icon={ alignmentIcons[align] }
				label={ label }
				controls={[
					{
						title: __('Align Text Left', 'citadela-pro'),
						icon: 'editor-alignleft',
						isActive: align === 'align-left',
						onClick: () => onChange( 'align-left' ),
					},
					{
						title: __('Align Text Center', 'citadela-pro'),
						icon: 'editor-aligncenter',
						isActive: align === 'align-center',
						onClick: () => onChange( 'align-center' ),
					},
					{
						title: __('Align Text Right', 'citadela-pro'),
						icon: 'editor-alignright',
						isActive: align === 'align-right',
						onClick: () => onChange( 'align-right' ),
					}
				]}
				toggleProps={ toggleProps }
			/>
		);
	}
}