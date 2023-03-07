const { Component, useState } = wp.element;
const { __ } = wp.i18n;
const { BlockControls, InspectorControls, RichText, } = wp.blockEditor;
const {
    PanelBody,
    ToggleControl,
    SelectControl,
    Icon
} = wp.components;

export default class Edit extends Component {
    constructor() {
        super(...arguments);
    }
    render() {

        const { attributes, setAttributes, name, isSelected } = this.props;
        const {
            title,
            showEventFeaturedImage,
            showEventDescription,
            showEventPrice,
            imageSize
        } = attributes;

        const block = wp.blocks.getBlockType(name);

        return (
            <>
                <InspectorControls key='inspector'>
                    <PanelBody
                        title={__('Event Details', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <ToggleControl
                            label={__('Show featured image', 'citadela-directory')}
                            checked={showEventFeaturedImage}
                            onChange={(checked) => this.props.setAttributes({ showEventFeaturedImage: checked })}
                        />
                        <ToggleControl
                            label={__('Show description', 'citadela-directory')}
                            checked={showEventDescription}
                            onChange={(checked) => this.props.setAttributes({ showEventDescription: checked })}
                        />
                        <ToggleControl
                            label={__('Show price', 'citadela-directory')}
                            checked={showEventPrice}
                            onChange={(checked) => this.props.setAttributes({ showEventPrice: checked })}
                        />
                    </PanelBody>
                    <PanelBody
                        title={__('Event Image Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={__('Image size', 'citadela-directory')}
                            value={imageSize}
                            options={[
                                { label: __('Thumbnail', 'citadela-directory'), value: 'thumbnail' },
                                { label: __('Medium', 'citadela-directory'), value: 'medium' },
                            ]}
                            onChange={(value) => { setAttributes({ imageSize: value }); }}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className={classNames(
                    "wp-block-citadela-blocks",
                    "ctdl-item-events",
                    attributes.className,
                    { "show-item-featured-image": showEventFeaturedImage ? true : false },
                    { "show-item-description": showEventDescription ? true : false },
                    { "show-item-price": showEventPrice ? true : false },
                )}
                >
                    <div class="ctdl-blockcard-title">
                        <div class="ctdl-blockcard-icon">
                            <Icon icon={block.icon.src} />
                        </div>
                        <div class="ctdl-blockcard-text">
                            <div class="ctdl-blockcard-name">{block.title}</div>
                            <div class="ctdl-blockcard-desc">{block.description}</div>
                        </div>
                    </div>
                    <div class="citadela-block-header">
                        <RichText
                            tagName='h3'
                            value={attributes.title}
                            onChange={(title) => setAttributes({ title })}
                            placeholder={block.title}
                            keepPlaceholderOnFocus={true}
                            allowedFormats={[]}
                        />
                    </div>
                    <div class="citadela-block-articles">
                        <div class="citadela-block-articles-wrap">
                            <article class="citadela-event">
                                <div class="citadela-event-date"><span class="event-date-label"></span></div>
                                <div class="citadela-event-body">
                                    <div class="citadela-event-thumbnail"></div>
                                    <div class="citadela-event-data">
                                        <div class="citadela-event-datetime"></div>
                                        <div class="citadela-event-title"><span class="event-title"></span><span class="event-price"></span></div>
                                        <div class="citadela-event-description"><span class="line"></span><span class="line"></span><span class="line"></span></div>
                                    </div>
                                </div>
                            </article>
                            <article class="citadela-event">
                                <div class="citadela-event-date"><span class="event-date-label"></span></div>
                                <div class="citadela-event-body">
                                    <div class="citadela-event-thumbnail"></div>
                                    <div class="citadela-event-data">
                                        <div class="citadela-event-datetime"></div>
                                        <div class="citadela-event-title"><span class="event-title"></span><span class="event-price"></span></div>
                                        <div class="citadela-event-description"><span class="line"></span><span class="line"></span><span class="line"></span></div>
                                    </div>
                                </div>
                            </article>
                            <article class="citadela-event">
                                <div class="citadela-event-date"><span class="event-date-label"></span></div>
                                <div class="citadela-event-body">
                                    <div class="citadela-event-thumbnail"></div>
                                    <div class="citadela-event-data">
                                        <div class="citadela-event-datetime"></div>
                                        <div class="citadela-event-title"><span class="event-title"></span><span class="event-price"></span></div>
                                        <div class="citadela-event-description"><span class="line"></span><span class="line"></span><span class="line"></span></div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </>
        );
    }
}