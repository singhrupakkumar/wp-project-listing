/**
 * Internal dependencies
 */

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { RichText } = wp.blockEditor;


export default class Save extends Component {
	render() {
		const { 
			attributes,  
			className, 
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
			buttonLinkNewTab,
			colorHeaderBg,
			colorHeaderText,
			colorButtonBg,
			colorButtonText,
			buttonBorderRadius,
		} = attributes;
		
		var colorStyles = {
			colorHeaderBg: colorHeaderBg ? { backgroundColor: colorHeaderBg } : null,
			colorHeaderText: colorHeaderText ? { color: colorHeaderText } : null,
			colorButtonBg: colorButtonBg ? { backgroundColor: colorButtonBg } : null,
			colorButtonText: colorButtonText ? { color: colorButtonText } : null,
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

		const NEW_TAB_REL = 'noopener noreferrer';
		
		const rowsOutput = rows.map((rowData, index) => 
				<RichText.Content
					tagName='div'
					className={classNames(
						"row-text",
						{"empty-row" : rowData.text == "" }
					)}
					value= { rowData.text }
				/>
			);
		
		const linkTarget = buttonLinkNewTab ? "_blank" : undefined;
		const linkRel = buttonLinkNewTab ? NEW_TAB_REL : undefined;

		const isOldPrice = ( showOldPrice && oldPrice != "" ) ? true : false;
		const isButton = ( showButton && buttonText != "" && buttonUrl != "" ) ? true : false;

		return (
			<div className={ classNames(
						className,
						"citadela-block-price-table",
						{'is-featured' : featuredTable},
						{'with-old-price' : showOldPrice},
						{'with-button' : isButton},
						"align-" + alignment,
					)}
			>
				<div class="price-table-content">

					<div class="price-table-header" style={ headerStyles }>
						{ ( title || subtitle || (featuredTable && featuredTableText) ) && (
							<div class="title-part">
								{ ( featuredTable && featuredTableText !== '' ) && 
									<RichText.Content
										tagName='div'
										className="featured-text"
										value= { featuredTableText }
									/>
								}
								{ title && 
									<RichText.Content
										tagName='h3'
										value= { title }
									/>
								}
								{ subtitle &&
									<RichText.Content
										tagName='p'
										className="subtitle-text"
										value= {subtitle}
									/>
								}
							</div>
						)}

						{ ( price || isOldPrice ) && (
							<div class="price-part">
								{ price && 
									<RichText.Content
										tagName='span'
										className="current-price"
										value= {price}
									/>
								}
								{ isOldPrice &&
									<RichText.Content
										tagName='span'
										className="old-price"
										value= {oldPrice}
									/>
								}
							</div>
						)}
					</div>
					
					<div class="price-table-body">
						<div class="rows-part">
							{ rowsOutput }
						</div>

						{ isButton && (
							<div class="button-part">
								<RichText.Content
									tagName="a"
									className={ "readmore-button" }
									href={ buttonUrl }
									value={ buttonText }
									target={ linkTarget }
									rel={ linkRel }
									style={ buttonStyles }
								/>
							</div>
						)}
					</div>

				</div>
			</div>
		);
	
	}
}