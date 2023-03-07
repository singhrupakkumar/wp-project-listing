import mapStyles from '../../../../blocks/components/map-styles.js';
import CategorySelect from '../../../../blocks/components/category-select';
import CustomColorControl from '../../../../blocks/components/custom-color-control';
import CitadelaRangeControl from '../../../../blocks/components/range-control';

const { apiFetch } = wp;
const { __, sprintf } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { withSelect, withDispatch } = wp.data;
const { CheckboxControl, SelectControl, BaseControl, RadioControl, TextareaControl, TextControl } = wp.components;

let HalfMapSettingsPanel = ( props ) => {
    const { 
        theme,
        themeOSM,
        customTheme, 
        provider, 
        dataType,
        dynamicTrack,
        trackColor, 
        trackEndpointsColor,
        filterCategory,
        filterLocation,
        filterFeatured,
        categoriesList,
        locationsList,
        noDataBehavior,
        noDataText,
        clusterGridSize,
    } = props;
    
    const editPageId = wp.data.select( "core/editor" ).getCurrentPostId();
    const itemDetailPageId = parseInt( CitadelaDirectorySettings.specialPages['single-item'] );
    let isItemDetail = editPageId === itemDetailPageId;
    const item_detail_options = CitadelaDirectorySettings.options.item_detail;
    const currentPostType = CitadelaDirectorySettings.currentPost.post_type;

    let showFilters = true;
    if( currentPostType == 'citadela-item' && item_detail_options && item_detail_options.enable ){
        showFilters = false;
        isItemDetail = true;
    }else{
        const automaticMapPages = [ "single-item", "item-category", "item-location", "search-results" ]
        for (const [key, value] of Object.entries(CitadelaDirectorySettings.specialPages)) {
            if( value == editPageId && automaticMapPages.includes(key) ){
                showFilters = false;
                break;
            }
        }
    }

    let styleOptions = mapStyles.map((styleObject) => {
        return { label: styleObject.name, value: styleObject.codeName }
    });
    styleOptions.push( {label: __('Custom', 'citadela-directory'), value: 'custom'} );

    return (
        <>
            <BaseControl>
                <SelectControl
                    label={__('Map provider', 'citadela-directory')}
                    value={ provider ? provider : 'openstreetmap' }
                    options={ [
                        { label: 'Google Maps', value: 'google-map' },
                        { label: 'OpenStreetMap', value: 'openstreetmap' },
                    ] }
                    onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_provider' ) } }
                />
            </BaseControl>
            
            { showFilters &&
                <>
                    { typeof categoriesList == 'object' &&
                        <CategorySelect
                            categoriesList={ categoriesList }
                            label={ __( 'Category', 'citadela-directory' ) }
                            noOptionLabel={ __( 'All', 'citadela-directory' ) }
                            selectedCategoryId={ filterCategory }
                            onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_filterCategory' ) } }
                        />
                    }
                    { typeof locationsList == 'object' &&
                        <CategorySelect
                            categoriesList={ locationsList }
                            label={ __( 'Location', 'citadela-directory' ) }
                            noOptionLabel={ __( 'All', 'citadela-directory' ) }
                            selectedCategoryId={ filterLocation }
                            onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_filterLocation' ) } }
                        />
                    }
                    <CheckboxControl
                        label={__('Only featured items', 'citadela-directory')}
                        checked={ filterFeatured }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_filterFeatured' ) } }
                    />
                </>
            }

            { ! showFilters &&
                <>
                <BaseControl>
                    <SelectControl
                        label={__('Empty map behavior', 'citadela-directory')}
                        value={ noDataBehavior }
                        options={ [
                            { label: __('Show empty map', 'citadela-directory'), value: 'empty-map' },
                            { label: __('Hide map', 'citadela-directory'), value: 'hidden-map' },
                        ] }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_noDataBehavior' ) } }
                    />
                </BaseControl>

                { ( ! noDataBehavior || noDataBehavior == 'empty-map' ) &&
                    <BaseControl>
                        <TextControl
                            label={ __('Text displayed on empty map', 'citadela-directory') }
                            value={ noDataText }
                            onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_noDataText' ) } }
                        />
                    </BaseControl>
                }
                </>
            }

            { ( provider == '' || provider == 'openstreetmap' ) &&
                <>
                    { ! isItemDetail ?
                        ( 
                            <>
                            <BaseControl>
                                <RadioControl
                                    label={__('Show on map', 'citadela-directory')}
                                    selected={ dataType ? dataType : 'markers' }
                                    options={ [
                                        { label:  __('Markers', 'citadela-directory'), value: 'markers' },
                                        { label:  __('GPX Tracks', 'citadela-directory'), value: 'tracks' },
                                        { label:  __('Markers & GPX Tracks', 'citadela-directory'), value: 'all' },
                                    ] }
                                    onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_dataType' ) } }
                                />
                            </BaseControl>

                            { ( dataType == "all" || dataType == "tracks" ) &&
                                <>
                                <BaseControl>
                                    <CheckboxControl
                                        label={ __('Dynamic track visibility', 'citadela-directory') }
                                        help={ __('Tracks are hidden and replaced with map marker according to zoom level.', 'citadela-directory') }
                                        checked={ dynamicTrack }
                                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_dynamicTrack' ) } }
                                    />
                                </BaseControl>
                                
                                <BaseControl>
                                    <CustomColorControl 
                                        label={ __('Track color', 'citadela-directory') }
                                        color={ trackColor }
                                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_trackColor' ) } }
                                        allowReset
                                    />
                                </BaseControl>

                                <BaseControl>
                                    <CustomColorControl 
                                        label={ __('Track endpoints color', 'citadela-directory') }
                                        color={ trackEndpointsColor }
                                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_trackEndpointsColor' ) } }
                                        allowReset
                                        disableAlpha
                                    />
                                </BaseControl>
                                </>
                            }
                            </>

                        ) : (
                            <>
                            <BaseControl>
                                <CustomColorControl 
                                    label={ __('Track color', 'citadela-directory') }
                                    color={ trackColor }
                                    onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_trackColor' ) } }
                                    allowReset
                                />
                            </BaseControl>

                            <BaseControl>
                                <CustomColorControl 
                                    label={ __('Track endpoints color', 'citadela-directory') }
                                    color={ trackEndpointsColor }
                                    onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_trackEndpointsColor' ) } }
                                    allowReset
                                    disableAlpha
                                />
                            </BaseControl>
                            </>
                        )
                    }
                </>
            }

            <CitadelaRangeControl
                label={__('Cluster radius in px', 'citadela-directory')}
                help={ clusterGridSize == 0 ? __("Clusters are disabled.", 'citadela-directory') : sprintf( __( "Markers in distance less than %spx radius are grouped into clusters. Set 0 to disable clusters.", 'citadela-directory' ), clusterGridSize ) }
                rangeValue={clusterGridSize}
                onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_clusterGridSize' ) } }
                min={0}
                max={200}
                initial={80}
                allowReset
            />
                    
            { provider == 'google-map' && 
                <>
                <BaseControl>
                    <RadioControl
                        label={__('Theme', 'citadela-directory')}
                        selected={ theme ? theme : 'citadela' }
                        options={ styleOptions }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_theme' ) } }
                    />
                </BaseControl>

                { theme == 'custom' && 
                    <BaseControl>
                        <TextareaControl
                            label={ __('Style JSON', 'citadela-directory') }
                            help={ <>{__('Copy and paste the JSON from this website:', 'citadela-directory')} <a target="_blank" href={'https://mapstyle.withgoogle.com/'}>https://mapstyle.withgoogle.com/</a></> }
                            value={ customTheme }
                            onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_customTheme' ) } }
                        />
                    </BaseControl>
                }

                </>
            }

            { ( provider == '' || provider == 'openstreetmap' ) && 
                <BaseControl>
                    <RadioControl
                        label={__('Theme', 'citadela-directory')}
                        selected={ themeOSM ? themeOSM : 'default' }
                        options={ [
                            { label:  __('Default', 'citadela-directory'), value: 'default' },
                            { label:  __('Silver', 'citadela-directory'), value: 'silver' },
                            { label:  __('Retro', 'citadela-directory'), value: 'retro' },
                            { label:  __('Dark', 'citadela-directory'), value: 'dark' },
                            { label:  __('Night', 'citadela-directory'), value: 'night' },
                            
                        ] }
                        onChange={ ( value ) => { props.onChange( value, '_citadela_half_map_themeOSM' ) } }
                    />                    
                </BaseControl>
            }

        </>
    );
};

HalfMapSettingsPanel = withSelect(
    ( select ) => {
        const { getEditedPostAttribute } = wp.data.select( 'core/editor' );

        const categoriesList = wp.data.select('core').getEntityRecords('taxonomy', 'citadela-item-category', { per_page: -1 } )
        const locationsList = wp.data.select('core').getEntityRecords('taxonomy', 'citadela-item-location', { per_page: -1 } )

        return {
            theme:  getEditedPostAttribute( 'meta' )['_citadela_half_map_theme'],
            themeOSM:  getEditedPostAttribute( 'meta' )['_citadela_half_map_themeOSM'],
            customTheme:  getEditedPostAttribute( 'meta' )['_citadela_half_map_customTheme'],
            provider:  getEditedPostAttribute( 'meta' )['_citadela_half_map_provider'],
            dataType:  getEditedPostAttribute( 'meta' )['_citadela_half_map_dataType'],
            dynamicTrack:  getEditedPostAttribute( 'meta' )['_citadela_half_map_dynamicTrack'],
            trackColor:  getEditedPostAttribute( 'meta' )['_citadela_half_map_trackColor'],
            trackEndpointsColor:  getEditedPostAttribute( 'meta' )['_citadela_half_map_trackEndpointsColor'],
            filterCategory:  getEditedPostAttribute( 'meta' )['_citadela_half_map_filterCategory'],
            filterLocation:  getEditedPostAttribute( 'meta' )['_citadela_half_map_filterLocation'],
            filterFeatured:  getEditedPostAttribute( 'meta' )['_citadela_half_map_filterFeatured'],
            noDataBehavior:  getEditedPostAttribute( 'meta' )['_citadela_half_map_noDataBehavior'],
            noDataText:  getEditedPostAttribute( 'meta' )['_citadela_half_map_noDataText'],
            clusterGridSize:  getEditedPostAttribute( 'meta' )['_citadela_half_map_clusterGridSize'],
            categoriesList: categoriesList,
            locationsList: locationsList,
        }
    }
) (HalfMapSettingsPanel);

HalfMapSettingsPanel = withDispatch(
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
) (HalfMapSettingsPanel);

registerPlugin( 'citadela-half-layout-map-settings-panel', {
    render: () => {
        const { getCurrentPost, getCurrentPostType, getEditedPostAttribute } = wp.data.select("core/editor");
        if ( ! [ 'page', 'special_page', 'citadela-item' ].includes( getCurrentPostType() ) ) {
            return null;
        }
        
        let { template } = getCurrentPost();
        
        // if it's Item post page, check template from custom post meta
        if( getCurrentPostType() == 'citadela-item' ){
            template = getEditedPostAttribute( 'meta' )['_citadela_page_template'];
        }
        
        if( template !== "half-layout-template" ) return null;

        return (
                <PluginDocumentSettingPanel
                    name="citadela-half-layout-map-settings-panel"
                    title={ __( 'Citadela Half Layout Map Settings', 'citadela-directory' ) }
                >
                    <HalfMapSettingsPanel />
                </PluginDocumentSettingPanel>
        )
    },
    icon: ''
} );