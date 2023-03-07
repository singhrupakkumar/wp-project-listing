
const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { withSelect, withDispatch } = wp.data;
const { CheckboxControl, SelectControl, BaseControl, RadioControl, TextareaControl } = wp.components;

let HalfLayoutSettingsPanel = ( props ) => {
    const { 
        position,
    } = props;
    
    return (
        <BaseControl>
           <RadioControl
                selected={ position ? position : 'right' }
                label={ __('Position', 'citadela-pro') }
                options={ [
                    { label:  __('Left side', 'citadela-pro'), value: 'left' },
                    { label:  __('Right side', 'citadela-pro'), value: 'right' },
                ] }
                onChange={ ( value ) => { props.onChange( value, '_citadela_half_layout_position' ) } }
                />
        </BaseControl>
    );
};

HalfLayoutSettingsPanel = withSelect(
    ( select ) => {
        const { getEditedPostAttribute } = wp.data.select( 'core/editor' );
        return {
            position:  getEditedPostAttribute( 'meta' )['_citadela_half_layout_position'],
        }
    }
) (HalfLayoutSettingsPanel);

HalfLayoutSettingsPanel = withDispatch(
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
) (HalfLayoutSettingsPanel);

registerPlugin( 'citadela-half-layout-settings-panel', {
    render: () => {
        const { getCurrentPost, getCurrentPostType, getEditedPostAttribute } = wp.data.select("core/editor");
        if ( ! [ 'page', 'special_page', 'citadela-item' ].includes( getCurrentPostType() ) ) {
            return null;
        }

        //additionally check if use options for Item page, otherwise are used options from Item Detail Special Page
        const ignore_special_page = getEditedPostAttribute( 'meta' )['_citadela_ignore_special_page'] == '1';
        if( getCurrentPostType() == 'citadela-item' && ! ignore_special_page ){
            return null;
        }
        
        let { template } = getCurrentPost();

        // if it's Item post page, check template from custom post meta
        if( getCurrentPostType() == 'citadela-item' ){
            template = getEditedPostAttribute( 'meta' )['_citadela_page_template'];
        }
        
        const body = document.querySelector("body");

        if( template !== "half-layout-template" ) {
            body.classList.remove("half-layout");
            return null;
        }

        body.classList.add("half-layout");

        return (
                <PluginDocumentSettingPanel
                    name="citadela-half-layout-settings-panel"
                    title={ __( 'Citadela Half Layout Settings', 'citadela-pro' ) }
                >
                    <HalfLayoutSettingsPanel />
                </PluginDocumentSettingPanel>
        )
    },
    icon: ''
} );