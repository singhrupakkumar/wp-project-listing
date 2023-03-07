/**
 * Internal dependencies
 */
import ItemContactFormInspectorControls from './inspector-controls';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { RichText, BlockControls, InspectorControls} = wp.blockEditor;
const { Icon } = wp.components;


export default function ItemContactFormEdit( {
	attributes,
	setAttributes,
	className,
	name
} ) {


	const {
		labelName,
		labelEmail,
		labelSubject,
		labelMessage,
		labelSendButton,
		helpName,
		helpEmail,
		helpSubject,
		helpMessage,
		} = attributes;

    const block = wp.blocks.getBlockType(name);

	return (
		<Fragment>

			<InspectorControls key='inspector'>
				<ItemContactFormInspectorControls attributes={ attributes } setAttributes={ setAttributes } />
			</InspectorControls>

			<div className={classNames(
                    "wp-block-citadela-blocks ctdl-item-contact-form",
                    attributes.className,
                )}
            >
                <div class="ctdl-blockcard-title">
                    <div class="ctdl-blockcard-icon">
                        <Icon icon={block.icon.src} />
                    </div>
                    <div class="ctdl-blockcard-text">
                        <div class="ctdl-blockcard-name">{ block.title }</div>
                        <div class="ctdl-blockcard-desc">{ block.description }</div>
                    </div>
                </div>

                <div class="citadela-block-header">
                    <RichText
                        tagName='h3'
                        value={ attributes.title }
                        onChange={ (title) => setAttributes( { title } ) }
                        placeholder={ block.title }
                        keepPlaceholderOnFocus={true}
                        allowedFormats={ [] }
                    />
                </div>

                <div class="citadela-block-form">
                    <div class="contact-form">

                        <div class="data-type-1">
                            <div class="input-container name">
                                <div class="input-data">
                                    <div class="label">{ labelName }</div>
                                    <div class="input"></div>
                                </div>
                                <div class="input-help">{ helpName }</div>
                            </div>

                            <div class="input-container email">
                                <div class="input-data">
                                    <div class="label">{ labelEmail }</div>
                                    <div class="input"></div>
                                </div>
                                <div class="input-help">{ helpEmail }</div>
                            </div>

                            <div class="input-container subject">
                                <div class="input-data">
                                    <div class="label">{ labelSubject }</div>
                                    <div class="input"></div>
                                </div>
                                <div class="input-help">{ helpSubject }</div>
                            </div>
                        </div>

                        <div class="data-type-2">
                            <div class="input-container message">
                                <div class="input-data">
                                    <div class="label">{ labelMessage }</div>
                                    <div class="input"></div>
                                </div>
                                <div class="input-help">{ helpMessage }</div>
                            </div>

                            <div class="input-container sf-button">
                                <div class="input-data">
                                    <span class="submit">{ labelSendButton }</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


			</div>
		</Fragment>
	);
}