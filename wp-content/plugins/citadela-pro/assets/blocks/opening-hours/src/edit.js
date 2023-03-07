import OpeningHoursInspectorControls from './inspector-controls';
import LayoutToolbar from '../../components/toolbar-layout';
import AlignToolbar from '../../components/toolbar-alignment';

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { RichText, BlockControls, InspectorControls} = wp.blockEditor;
const { ToolbarGroup, ToolbarItem } = wp.components;

export default class OpeningHoursEdit extends Component {
	render(){
		const { attributes, setAttributes, className, isSelected } = this.props;
		const {
			mondayTitle, tuesdayTitle, wednesdayTitle, thursdayTitle, fridayTitle, saturdayTitle, sundayTitle,
			mondayValue, tuesdayValue, wednesdayValue, thursdayValue, fridayValue, saturdayValue, sundayValue,
			dayLabelColor, dayDataColor, linesColor,
			hideEmptyDays,
			layout,
			textAlign,
			boxWidth,
		} = attributes;

		const dayLabelStyles = {
			color: dayLabelColor ? dayLabelColor : false,
		};

		const dayDataStyles = {
			color: dayDataColor ? dayDataColor : false,
		};

		const dayHolderStyles = {
			...( boxWidth && layout == 'box' ? { flexBasis: `${boxWidth}px` } : false ),
			borderColor: linesColor ? linesColor : false,
		};

		return (
			<Fragment>
				<BlockControls>
					
					<ToolbarGroup>
						<ToolbarItem as={ ( toggleProps ) => (
							<LayoutToolbar 
								value={ layout } 
								onChange={ ( value ) => setAttributes( { layout: value } ) } 
								toggleProps={ toggleProps }
							/>
						)}/>
					</ToolbarGroup>

					<ToolbarGroup>
						<ToolbarItem as={ ( toggleProps ) => (
							<AlignToolbar 
								value={ textAlign } 
								onChange={ ( value ) => setAttributes( { textAlign: value } ) }
								toggleProps={ toggleProps }
							/>
						)}/>
					</ToolbarGroup>

				</BlockControls>

				<InspectorControls key='inspector'>

					<OpeningHoursInspectorControls 
						attributes={ attributes } 
						setAttributes={ setAttributes }
					/>

				</InspectorControls>
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-opening-hours",
							`layout-${layout}`,
							`align-${textAlign}`,
							{ 'is-selected': isSelected},
							{ 'custom-border-color': linesColor },
							{ 'custom-label-color': dayLabelColor },
							{ 'custom-data-color': dayDataColor }
						)
					}
				>
				
					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && mondayValue == "" ) }							
							) }
						style={ dayHolderStyles ? dayHolderStyles : false }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { mondayTitle: value } ) } }
								value={ mondayTitle }
								placeholder={ __( "Monday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
						<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { mondayValue: value } ) } }
								value={ mondayValue }
								placeholder={ "8:00 - 17:00" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && tuesdayValue == "" ) }
							) }
						style={ dayHolderStyles }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { tuesdayTitle: value } ) } }
								value={ tuesdayTitle}
								placeholder={ __( "Tuesday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { tuesdayValue: value } ) } }
								value={ tuesdayValue }
								placeholder={ "8:00 - 17:00" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && wednesdayValue == "" ) }
							) }
						style={ dayHolderStyles }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { wednesdayTitle: value } ) } }
								value={ wednesdayTitle}
								placeholder={ __( "Wednesday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { wednesdayValue: value } ) } }
								value={ wednesdayValue }
								placeholder={ "8:00 - 17:00" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && thursdayValue == "" ) }
							) }
						style={ dayHolderStyles }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( {thursdayTitle: value } ) } }
								value={ thursdayTitle}
								placeholder={ __( "Thursday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { thursdayValue: value } ) } }
								value={ thursdayValue }
								placeholder={ "8:00 - 17:00" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && fridayValue == "" ) }
							) }
						style={ dayHolderStyles }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( {fridayTitle: value } ) } }
								value={ fridayTitle}
								placeholder={ __( "Friday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { fridayValue: value } ) } }
								value={ fridayValue }
								placeholder={ "8:00 - 17:00" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && saturdayValue == "" ) }
							) }
						style={ dayHolderStyles }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( {saturdayTitle: value } ) } }
								value={ saturdayTitle}
								placeholder={ __( "Saturday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { saturdayValue: value } ) } }
								value={ saturdayValue }
								placeholder={ "-" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

					<div className={ classNames(
							"oh-day",
							{ "hidden-day": ( hideEmptyDays && sundayValue == "" ) }
							) }
						style={ dayHolderStyles }
					>
						<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( {sundayTitle: value } ) } }
								value={ sundayTitle}
								placeholder={ __( "Sunday", 'citadela-pro' ) }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
						<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
							<RichText
								key='richtext'
								tagName='p'
								onChange= { ( value ) => { setAttributes( { sundayValue: value } ) } }
								value={ sundayValue }
								placeholder={ "-" }
								keepPlaceholderOnFocus={ true }
							/>
						</div>
					</div>

				</div>
			</Fragment>
		);
	}

}


