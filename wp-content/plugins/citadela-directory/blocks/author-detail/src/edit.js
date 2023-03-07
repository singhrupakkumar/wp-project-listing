const { apiFetch } = wp;
const { Component, useState } = wp.element;
const { __ } = wp.i18n;
const { BlockControls, InspectorControls, RichText, } = wp.blockEditor;
const { 
    ToolbarGroup, 
    ToolbarItem,
    PanelBody, 
    RangeControl, 
    ToggleControl, 
    Icon, 
    Tooltip, 
    SelectControl, 
    RadioControl, 
    ColorPalette, 
    ColorIndicator, 
    BaseControl,
    TextControl,
} = wp.components;

export default class Edit extends Component {
    constructor() {
        super( ...arguments );
    }

    render() {

        const { attributes, setAttributes, name, isSelected } = this.props;

        const { 
            showCover,
            showDescription,
            showIcon,
            showPostsNumber,
        } = attributes;

        return (
            <>
                <InspectorControls key='inspector'>
                    <PanelBody
                        title={__('Author Details', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <ToggleControl
                            label={__('Show cover image', 'citadela-directory')}
                            checked={ showCover }
                            onChange={ ( checked ) => this.props.setAttributes( { showCover: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show author image', 'citadela-directory')}
                            checked={ showIcon }
                            onChange={ ( checked ) => this.props.setAttributes( { showIcon: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show biographical info', 'citadela-directory')}
                            checked={ showDescription }
                            onChange={ ( checked ) => this.props.setAttributes( { showDescription: checked } ) }
                        />
                        <ToggleControl
                            label={__('Show posts number', 'citadela-directory')}
                            checked={ showPostsNumber }
                            onChange={ ( checked ) => this.props.setAttributes( { showPostsNumber: checked } ) }
                        />

                    </PanelBody>
                </InspectorControls>
                
                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-author-detail",
                        attributes.className,
                        { "show-author-cover": showCover ? true : false },
                        { "show-author-icon": showIcon ? true : false },
                        { "show-author-description": showDescription ? true : false },
                        { "show-posts-number": showPostsNumber ? true : false },
                    )}
                >
                    <article class="citadela-author-detail has-cover has-icon has-description has-posts">
                        <div class="item-content">
                            <div class="item-thumbnail">
                                    { showCover && <div class="author-cover"></div> }
                                    { showPostsNumber && 
                                        <div class="author-posts-number">
                                            <span class="posts-number"></span>
                                            <span class="posts-text"></span>
                                        </div>
                                    }
                                
                                <div class="author-info">
                                    { showIcon && <div class="author-icon"></div> }
                                    <div class="author-name">{ __('Author name', 'citadela-directory' ) }</div>
                                </div>

                            </div>
                            { showDescription &&
                                <div class="item-body">
                                    <div class="item-description">{ __('Author biographical info', 'citadela-directory' ) }</div>
                                </div>
                            }
                        </div>
                    </article>

                </div>
            </>
        );
    }
}
