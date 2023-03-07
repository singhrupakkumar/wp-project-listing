const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { __ } = wp.i18n;
const { withSelect, withDispatch } = wp.data;
const { SelectControl, BaseControl } = wp.components;

function updateContentWidthClass( width ){
    const body = document.querySelector("body");
    
    //remove all options to set new one
    body.classList.remove("wide-content-width");
    body.classList.remove("full-content-width");

    if( width != 'default' && width != '' ){
        body.classList.add( `${width}-content-width` );
        body.classList.add( "page-fullwidth" );
    }
}

function clearAssociatedTemplateClasses(){
    const body = document.querySelector("body");
    body.classList.remove("wide-content-width");
    body.classList.remove("full-content-width");
    body.classList.remove("page-fullwidth");
}

let ContentSettingsPanel = ( props ) => {
    const { 
        contentWidth,
     } = props;

     updateContentWidthClass( contentWidth );

     return (
        <>
            <BaseControl>
                <SelectControl
                    label={ __( 'Content width', 'citadela-pro' ) }
                    value={ contentWidth }
                    options={ [
                        { label: __( 'Default', 'citadela-pro' ), value: 'default' },
                        { label: __( 'Wide', 'citadela-pro' ), value: 'wide' },
                        { label: __( 'Fullwidth', 'citadela-pro' ), value: 'full' },
                    ] }
                    onChange={ ( value ) => { props.onChange( value, '_citadela_content_width' ) ; }
                    }
                />
            </BaseControl>
        </>
    );
};

ContentSettingsPanel = withSelect(
    ( select ) => {
        const { getEditedPostAttribute } = wp.data.select( 'core/editor' );
        return {
            contentWidth:  getEditedPostAttribute( 'meta' )['_citadela_content_width'],
        }
    }
) (ContentSettingsPanel);

ContentSettingsPanel = withDispatch(
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
) (ContentSettingsPanel);

registerPlugin( 'citadela-content-settings-panel', {
    render: () => {
        const { getCurrentPost, getCurrentPostType, getEditedPostAttribute } = wp.data.select("core/editor");
        if ( ! [ 'page', 'special_page', 'citadela-item' ].includes( getCurrentPostType() ) ) {
            return null;
        }
        
        //additionally check if use options for Item page
        const ignore_special_page = getEditedPostAttribute( 'meta' )['_citadela_ignore_special_page'] == '1'
        if( getCurrentPostType() == 'citadela-item' && ! ignore_special_page ){
            return null;
        }

        const { template } = getCurrentPost();
        
        const body = document.querySelector("body");
        
        //fullwidth template is default template - in post data is stored as empty string
        if( template != "" ) {
            clearAssociatedTemplateClasses();
            return null;
        }

        body.classList.add("page-fullwidth");
        
        return (
                <PluginDocumentSettingPanel
                    name="citadela-content-settings-panel"
                    title={ __( 'Citadela Content Settings', 'citadela-pro' ) }
                >
                    <ContentSettingsPanel />
                </PluginDocumentSettingPanel>
        )
    },
    icon: ''
} );