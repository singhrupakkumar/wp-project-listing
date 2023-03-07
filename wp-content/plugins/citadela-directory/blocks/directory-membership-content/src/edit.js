const { Component } = wp.element;
const { __ } = wp.i18n;
const { InspectorControls, RichText, InnerBlocks } = wp.blockEditor;
const { SelectControl, RadioControl, PanelBody, Icon } = wp.components;

export default class Edit extends Component {
    constructor() {
        super( ...arguments );
    }

    render() {
        const { attributes, setAttributes, name, isSelected } = this.props;
        const { 
            contentFor,
            membership
        } = attributes;
        
        const block = wp.blocks.getBlockType(name);
        const subscriptions = CitadelaDirectorySettings.citadelaSubscriptionProducts ? CitadelaDirectorySettings.citadelaSubscriptionProducts : [] ;
        
        let labels = subscriptions.map( ( data ) => {
            return { label: data.post_title, value: data.id };
        });
        
        labels.unshift( { label: __('Any active subscription', 'citadela-directory'), value: '0' } );
        
        return (
            <>
                <InspectorControls key='inspector'>
                    
                    <PanelBody
                        title={__('Options', 'citadela-directory')}
                        initialOpen={true}
                        className="citadela-panel"
                    >

                        <RadioControl
                            selected={ contentFor }
                            label={ __( 'Content visible for:', 'citadela-directory' ) }
                            options={ [
                                { label: __( 'Non-membership visitors', 'citadela-directory' ), value: 'non-membership' },
                                { label: __( 'Users with active membership', 'citadela-directory' ), value: 'active-membership' },
                                
                            ] }
                            onChange={ ( value ) => { setAttributes( { contentFor: value } ) } }
                        />

                        { contentFor == 'active-membership' &&
                            <>
                                { subscriptions.length
                                    ? <SelectControl
                                            label={ __( 'WooCommerce subscriptions', 'citadela-directory' ) }
                                            value={ membership == '' ? subscriptions[0]['id'] : membership }
                                            options={ labels }
                                            onChange={ ( value ) => { setAttributes( { membership: value } ) } }
                                        />
                                    : __( 'There are no available subscriptions. Add some, please.', 'citadela-directory' )
                                }
                            </>
                        }

                    </PanelBody>
                    
                </InspectorControls>

                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-directory-membership-content",
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
                    
                    <InnerBlocks
                        renderAppender={ () => (
                            <InnerBlocks.ButtonBlockAppender />
                        ) }
                    />

                </div>
            </>
        );
    }
}