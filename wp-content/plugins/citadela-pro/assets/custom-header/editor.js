import CustomImageUpload from "./components/image-upload";
import CustomBackground from "./components/background";

const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { __ } = wp.i18n;
const { withSelect, withDispatch } = wp.data;
const { ColorPicker, CheckboxControl, ColorIndicator, BaseControl, Button } = wp.components;

const colorsSet = [
    { color: '#00d1b2' },
    { color: '#3373dc' },
    { color: '#209cef' },
    { color: '#22d25f' },
    { color: '#ffdd57' },
    { color: '#ff3860' },
    { color: '#7941b6' },
    { color: '#392F43' },
];

/*
*   HEADER SETTINGS PANEL
*/

let HeaderSettingsPanel = ( props ) => {
    const { 
        useHeader,
        overContent,
        textColor,
        logoImage,
        bgImageOverlay,
        bgImage,
        bgPosition,
        bgFixed,
        bgRepeat,
        bgSize,
        bgColor,
        transparentBg,
     } = props;
    
    return (
        <>
        <BaseControl>
            <CheckboxControl
                label={ __( 'Use custom header', 'citadela-pro' ) }
                checked={ useHeader }
                onChange={ ( value ) => { props.onChange( value, '_citadela_header' ) } }
            />
        </BaseControl>

        

        { useHeader && 
            <>
                <BaseControl>
                    <CheckboxControl
                        label={ __( 'Show header over content', 'citadela-pro' ) }
                        help={ __( 'Note: standard page title will be hidden.', 'citadela-pro' ) }
                        checked={ overContent }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_header_over_content' ) } }
                    />
                </BaseControl>

                <BaseControl
                    label={ __('Custom logo image', 'citadela-pro') }
                    help={ __('Logo image in custom header.', 'citadela-pro') }
                >
                    <CustomImageUpload
                        media={ logoImage }
                        meta={ '_citadela_header_logo' }
                        onChange={ (value) => { props.onChange( value, '_citadela_header_logo' ) } }
                        mediaPopupLabel={ __('Custom logo', 'citadela-pro') }
                        dropzoneLabel={ __('Set custom logo image', 'citadela-pro') }
                    />
                </BaseControl>

                <BaseControl>
                    <CheckboxControl
                        label={ __( 'Transparent header', 'citadela-pro' ) }
                        help={ __( 'Show transparent header without additional background settings.', 'citadela-pro' ) }
                        checked={ transparentBg }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_header_transparent_bg' ) } }
                    />
                </BaseControl>

                { ! transparentBg &&
                    <BaseControl>
                        <CustomBackground
                            meta={ '_citadela_header_bg' }
                            image={ bgImage }
                            size={ bgSize }
                            position={ bgPosition }
                            repeat={ bgRepeat }
                            fixed={ bgFixed }
                            color={ bgColor }
                            overlayColor={ bgImageOverlay }
                            onChange={ (value, dataType) => { props.onChange( value, `_citadela_header_bg_${dataType}` ) } }
                            supportOverlay={ true }
                        />
                    </BaseControl>
                }

                <BaseControl
                    label={ __('Text color', 'citadela-pro') }
                    help={ __('Text color in custom header.', 'citadela-pro') }
                    className="block-editor-panel-color-settings"
                >
                    { textColor && <ColorIndicator colorValue={ textColor } /> }
                    <div class="reset-button" style={ {marginBottom: '3px'} }>
                        <Button
                            disabled={ textColor === undefined }
                            isSecondary
                            isSmall
                            onClick={ () => { props.onChange( '', '_citadela_header_text_color' ) } }
                            >
                            { __( 'Reset', 'citadela-pro' ) }
                        </Button>
                    </div>
                    <ColorPicker
                        color={ textColor }
                        onChangeComplete={ (value) => { props.onChange( value, '_citadela_header_text_color' ) } }
                        disableAlpha
                    />
                </BaseControl>

            </>
        }
        </>
    );
};

HeaderSettingsPanel = withSelect(
    ( select ) => {
        const { getEditedPostAttribute } = wp.data.select( 'core/editor' );
        return {
            useHeader:  getEditedPostAttribute( 'meta' )['_citadela_header'] == '1',
            textColor:  getEditedPostAttribute( 'meta' )['_citadela_header_text_color'],
            logoImage:  getEditedPostAttribute( 'meta' )['_citadela_header_logo'],
            overContent: getEditedPostAttribute( 'meta' )['_citadela_header_over_content'] == '1',
            bgImage: getEditedPostAttribute( 'meta' )['_citadela_header_bg_image'],
            bgRepeat: getEditedPostAttribute( 'meta' )['_citadela_header_bg_repeat'],
            bgFixed: getEditedPostAttribute( 'meta' )['_citadela_header_bg_fixed'],
            bgPosition: getEditedPostAttribute( 'meta' )['_citadela_header_bg_position'],
            bgSize: getEditedPostAttribute( 'meta' )['_citadela_header_bg_size'],
            bgColor: getEditedPostAttribute( 'meta' )['_citadela_header_bg_color'],
            bgImageOverlay: getEditedPostAttribute( 'meta' )['_citadela_header_bg_image_overlay'],
            transparentBg: getEditedPostAttribute( 'meta' )['_citadela_header_transparent_bg'],
        }
    }
) (HeaderSettingsPanel);

HeaderSettingsPanel = withDispatch(
    ( dispatch ) => {
        return {
            onChange: ( value, metaKey ) => {
                let meta = {};
                const rgbaMeta = [
                    '_citadela_header_bg_image_overlay',
                    '_citadela_header_bg_color',
                    '_citadela_header_text_color',
                ];

                //sanitize values before save
                if( rgbaMeta.includes(metaKey) ){
                    meta[ metaKey ] = value ? `rgba(${value.rgb.r}, ${value.rgb.g}, ${value.rgb.b}, ${value.rgb.a})` : ''; 
                }else{
                    meta[ metaKey ] = value === undefined ? "" : value; 
                }

                dispatch( 'core/editor' ).editPost( {
                    meta: meta
                } );
            }
        }
    }
) (HeaderSettingsPanel);

registerPlugin( 'citadela-header-settings-panel', {
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

        return (
                <PluginDocumentSettingPanel
                    name="citadela-header-settings-panel"
                    title={ __( 'Citadela Header Settings', 'citadela-pro' ) }
                >
                    <HeaderSettingsPanel />
                </PluginDocumentSettingPanel>
        )
    },
    icon: ''
} );