import PriceTableInspectorControls from './inspector-controls';
import PriceTableBlockControls from './block-controls';
import TableRows from './rows-component';

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { RichText, URLInput } = wp.blockEditor;

export default class Edit extends Component {
	
	constructor( ){
		super(...arguments);
	}

	render() {				
		const { 
			attributes, 
			setAttributes, 
			className, 
			isSelected,
		} = this.props;
		
		const {
			title,
			subtitle,
			price,
			oldPrice,
			showButton,
			buttonText,
			buttonUrl,
			featuredTable,
			featuredTableText,
			showOldPrice,
			rows,
			alignment,
			colorHeaderBg,
			colorHeaderText,
			colorButtonBg,
			colorButtonText,
			buttonBorderRadius
		} = attributes;
		
		var colorStyles = {
			colorHeaderBg: colorHeaderBg ? { backgroundColor: colorHeaderBg } : undefined,
			colorHeaderText: colorHeaderText ? { color: colorHeaderText } : undefined,
			colorButtonBg: colorButtonBg ? { backgroundColor: colorButtonBg } : undefined,
			colorButtonText: colorButtonText ? { color: colorButtonText } : undefined,
		}

		const headerStyles = {
			...( colorStyles.colorHeaderBg ),
			...( colorStyles.colorHeaderText ),
		}

		const buttonStyles = {
			...( colorStyles.colorButtonBg ),
			...( colorStyles.colorButtonText ),
			...( { borderRadius: buttonBorderRadius !== undefined ? buttonBorderRadius+"px" : undefined } )
		}
		
		const isButton = ( showButton && buttonText != "" && buttonUrl != "" ) ? true : false;

		return (
			<Fragment>

				<PriceTableBlockControls attributes={ attributes } setAttributes={ setAttributes } />				
				<PriceTableInspectorControls attributes={ attributes } setAttributes={ setAttributes } />

				<div className={ 
						classNames(
							className,
							"citadela-block-price-table",
							{'is-selected' : isSelected},
							{'is-featured' : featuredTable},
							{'with-old-price' : showOldPrice},
							{'with-button' : isButton},
							"align-" + alignment,
						)}
				>
					<div class="price-table-content">

						<div class="price-table-header" style={ headerStyles }>
							<div class="title-part">
								{featuredTable &&
									<RichText
										tagName='div'
										className="featured-text"
										onChange= { ( value ) => { setAttributes( { featuredTableText: value } ) } }
										value= { featuredTableText }
										placeholder={ __('Featured', 'citadela-pro' ) }
										keepPlaceholderOnFocus={ true }
										allowedFormats={[]}
									/>
								}
								<RichText
									tagName='h3'
									onChange= { ( value ) => { setAttributes( { title: value } ) } }
									value= { title }
									placeholder={ __('Table title', 'citadela-pro' ) }
									keepPlaceholderOnFocus={ true }
									allowedFormats={[]}
								/>
								<RichText
									tagName='p'
									className="subtitle-text"
									onChange= { ( value ) => { setAttributes( { subtitle: value } ) } }
									value= { subtitle }
									placeholder={ __('Table subtitle', 'citadela-pro' ) }
									keepPlaceholderOnFocus={ true }
									allowedFormats={[]}
								/>
							</div>
							<div class="price-part">
								<RichText
									tagName='span'
									className="current-price"
									onChange= { ( value ) => { setAttributes( { price: value } ) } }
									value= { price }
									placeholder="$99"
									keepPlaceholderOnFocus={ true }
									allowedFormats={[]}
								/>
								{showOldPrice && 
								<RichText
									tagName='span'
									className="old-price"
									onChange= { ( value ) => { setAttributes( { oldPrice: value } ) } }
									value= { oldPrice }
									placeholder="$199"
									keepPlaceholderOnFocus={ true }
									allowedFormats={[]}
								/>
								}
							</div>
						</div>

						<div class="price-table-body">
							<div class="rows-part">
								<TableRows 
									rows={ rows }
									setAttributes={ setAttributes }
									isSelected={ isSelected }
								/>
							</div>

							{showButton &&
								<div class="button-part">	
									<RichText
										placeholder={ __( 'button text', 'citadela-pro' ) }
										value={ buttonText }
										onChange={ ( value ) => setAttributes( { buttonText: value } ) }
										keepPlaceholderOnFocus={ true }
										allowedFormats={[]}
										className="readmore-button"
										style={ buttonStyles }
									/>
									{ isSelected &&
										<URLInput
											label={ __( 'Button link', 'citadela-pro' ) }
											value={ buttonUrl }
											autoFocus={ false }
											onChange={ ( value ) => setAttributes( { buttonUrl: value } ) }
											disableSuggestions={ ! isSelected }
											isFullWidth
											hasBorder
										/>
									}
								</div>
							}
						</div>

					</div>
				</div>
			</Fragment>
		);
	}
	

}
