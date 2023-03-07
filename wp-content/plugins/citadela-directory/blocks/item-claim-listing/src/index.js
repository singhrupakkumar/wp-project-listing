import metadata from './block.json';

import ToolbarAlignment from '../../components/toolbar-alignment';
import ToolbarAlignInspector from '../../components/toolbar-alignment-inspector';
import CitadelaRangeControl from '../../components/range-control';
import CustomColorControl from '../../components/custom-color-control';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, BlockControls, RichText } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, PanelBody, BaseControl, RadioControl, TextControl, TextareaControl } = wp.components;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __('Item Claim Listing', 'citadela-directory'),
	description: __('Displays the button and the form for claim listing.', 'citadela-directory'),
	edit: ({ className, attributes, setAttributes, name }) => {
		const block = wp.blocks.getBlockType(name);
		const { align, text, style, textColor, bgColor, borderRadius, formTitle, formDescription, formUsername, formEmail, formSubmit, formLoggedIn, formTerms, notificationAlready, notificationPending } = attributes;

		const textStyles = textColor ? { color: textColor } : {};
		const linkStyles = {
			...(style != 'text' && bgColor ? { backgroundColor: bgColor } : {}),
			...(style != 'text' && borderRadius >= 0 ? { borderRadius: `${borderRadius}px` } : {}),
		}

		return (
			<>
				<BlockControls>
					<ToolbarGroup>
						<ToolbarItem>
							{ ( toggleProps ) => (
								<ToolbarAlignment 
									value={ align } 
									onChange={ ( value ) => ( setAttributes( { align: value } ) ) }
									leftLabel={ __( 'Align Left', 'citadela-directory' ) }    
									centerLabel={ __( 'Align Center', 'citadela-directory' ) }    
									rightLabel={ __( 'Align Right', 'citadela-directory' ) }    
									toggleProps={ toggleProps }
								/>
							)}
						</ToolbarItem>
					</ToolbarGroup>
				</BlockControls>

				<InspectorControls>
					<PanelBody
						title={__('Buttons', 'citadela-directory')}
						initialOpen={true}
						className="citadela-panel"
					>
						<ToolbarAlignInspector
							label={__('Alignment', 'citadela-directory')}
							value={align}
							onChange={(value) => { setAttributes({ align: value }) }}
						/>

						<BaseControl
							label={__('Style', 'citadela-directory')}
						>
							<RadioControl
								selected={style}
								options={[
									{ label: __('Small button', 'citadela-directory'), value: 'small-button' },
									{ label: __('Large button', 'citadela-directory'), value: 'large-button' }
								]}
								onChange={(value) => { setAttributes({ style: value }) }}
							/>
						</BaseControl>

						<CustomColorControl
							label={__('Text color', 'citadela-directory')}
							color={textColor}
							onChange={(value) => { setAttributes({ textColor: value }) }}
							allowReset
							disableAlpha
						/>

						{style != 'text' &&
							<>
								<CustomColorControl
									label={__('Button background color', 'citadela-directory')}
									color={bgColor}
									onChange={(value) => { setAttributes({ bgColor: value }) }}
									allowReset
								/>
								<CitadelaRangeControl
									label={__('Border radius', 'citadela-directory')}
									rangeValue={borderRadius}
									onChange={(value) => { setAttributes({ borderRadius: value }) }}
									min={0}
									max={50}
									initial={0}
									allowReset
									allowNoValue
								/>
							</>
						}
					</PanelBody>
					<PanelBody
						title={__('Form', 'citadela-directory')}
						initialOpen={false}
						className="citadela-panel"
					>
						<BaseControl
							id={'formTitle'}
							label={__('Title', 'citadela-directory')}
						>
							<TextControl
								id={'formTitle'}
								type={'text'}
								value={formTitle}
								onChange={(value) => { setAttributes({ formTitle: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'formDescription'}
							label={__('Description', 'citadela-directory')}
						>
							<TextareaControl
								id={'formDescription'}
								type={'text'}
								value={formDescription}
								onChange={(value) => { setAttributes({ formDescription: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'formUsername'}
							label={__('Username Label', 'citadela-directory')}
							help={__('Label for username field', 'citadela-directory')}
						>
							<TextControl
								id={'formUsername'}
								type={'text'}
								value={formUsername}
								onChange={(value) => { setAttributes({ formUsername: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'formEmail'}
							label={__('Email Label', 'citadela-directory')}
							help={__('Label for email field', 'citadela-directory')}
						>
							<TextControl
								id={'formEmail'}
								type={'text'}
								value={formEmail}
								onChange={(value) => { setAttributes({ formEmail: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'formSubmit'}
							label={__('Submit Label', 'citadela-directory')}
							help={__('Label for submit button', 'citadela-directory')}
						>
							<TextControl
								id={'formSubmit'}
								type={'text'}
								value={formSubmit}
								onChange={(value) => { setAttributes({ formSubmit: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'formLoggedIn'}
							label={__('Logged in text', 'citadela-directory')}
							help={__('Text displayed in the form when user is logged in', 'citadela-directory')}
						>
							<TextareaControl
								id={'formLoggedIn'}
								type={'text'}
								value={formLoggedIn}
								onChange={(value) => { setAttributes({ formLoggedIn: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'formTerms'}
							label={__('Terms & Conditions label', 'citadela-directory')}
							help={__('Text for Terms & Conditions form input. Leave empty to disable the checkbox.', 'citadela-directory')}
						>
							<TextareaControl
								id={'formTerms'}
								type={'text'}
								value={formTerms}
								onChange={(value) => { setAttributes({ formTerms: value }) }}
							/>
						</BaseControl>
					</PanelBody>
					<PanelBody
						title={__('Notifications', 'citadela-directory')}
						initialOpen={false}
						className="citadela-panel"
					>
						<BaseControl
							id={'notificationAlready'}
							label={__('Already claimed', 'citadela-directory')}
							help={__('Notification when the item is already claimed. Leave empty to hide the notification.', 'citadela-directory')}
						>
							<TextareaControl
								id={'notificationAlready'}
								type={'text'}
								value={notificationAlready}
								onChange={(value) => { setAttributes({ notificationAlready: value }) }}
							/>
						</BaseControl>
						<BaseControl
							id={'notificationPending'}
							label={__('Pending', 'citadela-directory')}
							help={__('Notification when the item is pending moderation from admin. Leave empty to hide the notification.', 'citadela-directory')}
						>
							<TextareaControl
								id={'notificationPending'}
								type={'text'}
								value={notificationPending}
								onChange={(value) => { setAttributes({ notificationPending: value }) }}
							/>
						</BaseControl>
					</PanelBody>
				</InspectorControls>

				<div className={classNames(
					"wp-block-citadela-blocks",
					"ctdl-item-claim-listing",
                    attributes.className,
					`align-${align}`,
					`${style}-style`,
					textColor ? 'custom-text-color' : null,
					style != 'text' && bgColor ? 'custom-background-color' : null,
					style != 'text' && borderRadius ? 'custom-border-radius' : null
				)}>
					<div
						class="claim-listing-button"
						style={linkStyles}
					>
						<RichText
							tagName='span'
							className="button-text"
							placeholder={__('Claim Listing', 'citadela-directory')}
							value={text}
							onChange={(value) => setAttributes({ text: value })}
							keepPlaceholderOnFocus={true}
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
							style={textStyles}
						/>
					</div>
				</div>
			</>
		);
	},
	save: () => {
		return null;
	}
});