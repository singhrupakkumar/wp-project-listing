const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { __ } = wp.i18n;
const { withSelect, withDispatch } = wp.data;
const { CheckboxControl, TextControl, BaseControl } = wp.components;

let PageSettingsPanel = ( props ) => {
    const post_type = wp.data.select("core/editor").getCurrentPostType();
    return (
        <>
            { post_type !== 'post' &&
                <>
                    <CheckboxControl
                        label={ __( 'Hide page title section', 'citadela' ) }
                        checked={ props.hide_page_title }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_hide_page_title' ) } }
                    />
                    { props.hide_page_title && <CheckboxControl
                        label={ __( 'Remove space under header', 'citadela' ) }
                        checked={ props.remove_header_space }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_remove_header_space' ) } }
                    /> }
                </>
            }

            { CitadelaSettings.activeProPlugin &&
                <TextControl
                    label={ __( 'Custom CSS class', 'citadela' ) }
                    help={ __( 'Add more classes separated by space.', 'citadela' ) }
                    value={ props.custom_class }
                    onChange={ ( value ) => { props.onChange( value, '_citadela_custom_class', 'text' ) } }
                />
            }

        </>
        );
    };

PageSettingsPanel = withSelect(
    ( select ) => {
        return {
            hide_page_title: wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['_citadela_hide_page_title'] == '1',
            remove_header_space: wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['_citadela_remove_header_space'] == '1',
            custom_class: wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['_citadela_custom_class']
        }
    }
) (PageSettingsPanel);

// do not forget register meta in CitadelaTheme.php!
PageSettingsPanel = withDispatch(
    ( dispatch ) => {
        return {
            onChange: ( value, metaKey, type = 'checkbox' ) => {
                let meta = {};
                
                if( type == 'checkbox' ){
                    meta[ metaKey ] = value ? '1' : '0';
                }

                if( type == 'text' ){
                    meta[ metaKey ] = value ? value : '';
                }

                dispatch( 'core/editor' ).editPost( {
                    meta: meta
                } );

                if ( metaKey == '_citadela_hide_page_title' && !value ) {
                    dispatch( 'core/editor' ).editPost( {
                        meta: { '_citadela_remove_header_space': '0' }
                    } );
                }
            }
        }
    }
) (PageSettingsPanel);

registerPlugin( 'citadela-page-settings-panel', {
    render: () => {
        const { getCurrentPost, getCurrentPostType, getEditedPostAttribute } = wp.data.select("core/editor");
        if ( ! [ 'page', 'post', 'special_page', 'citadela-item' ].includes( getCurrentPostType() ) ) {
            return null;
        }
        
        //disable panel for posts, there are no settings yet with disabled Citadela Pro plugin
        if( getCurrentPostType() == 'post' && ! CitadelaSettings.activeProPlugin ){
            return null;
        }

        //additionally check if use options for Item page
        const ignore_special_page = getEditedPostAttribute( 'meta' )['_citadela_ignore_special_page'] == '1'
        if( getCurrentPostType() == 'citadela-item' && ! ignore_special_page ){
            return null;
        }
        
        let title = __( 'Citadela Page Settings', 'citadela' );

        if( getCurrentPostType() == 'post' ){
            title = __( 'Citadela Post Settings', 'citadela' );            
        }

        return (
            <PluginDocumentSettingPanel
                name="ctdl-page-settings-panel"
                title={ title }
            >
                <PageSettingsPanel />
            </PluginDocumentSettingPanel>
        )
    },
    icon: ''
} );