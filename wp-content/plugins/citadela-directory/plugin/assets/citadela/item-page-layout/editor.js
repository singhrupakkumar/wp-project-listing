const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { withSelect, withDispatch } = wp.data;
const { CheckboxControl, SelectControl, BaseControl, ToggleControl, TextareaControl } = wp.components;

let ItemLayoutSettingsPanel = ( props ) => {
    const { 
        ignoreSpecialPageLayout,
        pageTemplate,
    } = props;
    

    return (
        <>
            <BaseControl>
                <ToggleControl 
                    label={ __( 'Do not use Item Detail special page settings', 'citadela-directory' ) }
                    help={ __( 'It will allow you to build a whole item detail page. No blocks will be inherited from Item Detail special page.', 'citadela-directory' ) }
                    checked={ ignoreSpecialPageLayout }
                    onChange={ ( value ) => { props.onChange( value, '_citadela_ignore_special_page' ) } }
                />
                { ignoreSpecialPageLayout &&
                    <SelectControl
                        label={__('Page template', 'citadela-directory')}
                        value={ pageTemplate }
                        options={ [
                            { label: __('Fullwidth page', 'citadela-directory'), value: '' },
                            { label: __('Half layout page', 'citadela-directory'), value: 'half-layout-template' },
                        ] }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_page_template' ) } }
                    />
                }
            </BaseControl>
        </>
    );
};

ItemLayoutSettingsPanel = withSelect(
    ( select ) => {
        const { getEditedPostAttribute } = wp.data.select( 'core/editor' );

        return {
            ignoreSpecialPageLayout:  getEditedPostAttribute( 'meta' )['_citadela_ignore_special_page'],
            pageTemplate:  getEditedPostAttribute( 'meta' )['_citadela_page_template'],
        }
    }
) (ItemLayoutSettingsPanel);

ItemLayoutSettingsPanel = withDispatch(
    ( dispatch ) => {
        return {
            onChange: ( value, metaKey ) => {
                let meta = {};

                //sanitize values before save
                meta[ metaKey ] = value === undefined ? "" : value;

                dispatch( 'core/editor' ).editPost( {
                    meta: meta
                } );
            }
        }
    }
) (ItemLayoutSettingsPanel);

registerPlugin( 'citadela-item-layout-settings-panel', {
    render: () => {
        const { getCurrentPost, getCurrentPostType } = wp.data.select("core/editor");
        if ( ! [ 'citadela-item' ].includes( getCurrentPostType() ) ) {
            return null;
        }

        return (
                <PluginDocumentSettingPanel
                    name="citadela-item-layout-settings-panel"
                    title={ __( 'Citadela Item Page Layout', 'citadela-directory' ) }
                >
                    <ItemLayoutSettingsPanel />
                </PluginDocumentSettingPanel>
        )
    },
    icon: ''
} );