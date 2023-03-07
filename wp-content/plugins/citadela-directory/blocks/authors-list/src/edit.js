import CarouselPanel from '../../components/panel-carousel';
import CustomColorControl from '../../components/custom-color-control';
import PostsSelection from '../../components/posts-selection';

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

const CATEGORIES_LIST_QUERY = {
	per_page: -1,
};

export default class Edit extends Component {
    constructor() {
        super( ...arguments );
        this.maybeUpdateSelectedAuthors = this.maybeUpdateSelectedAuthors.bind(this);
        this.state = {
            infographicCount: 0,
        };
    }

    componentDidMount() {
        const { selectedAuthors } = this.props.attributes;
        let params = {};
        params['selected_posts'] = selectedAuthors;
        params['fields'] = 'ID';

        let paramsQuery = Object.keys(params)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
            .join('&');

        let path = `/citadela-directory/users?${paramsQuery}`

        this.fetchRequest = apiFetch( {
            path: path,
        } ).then(
            ( result ) => {
                this.setState( { infographicCount: result.length } );
                this.maybeUpdateSelectedAuthors( result );
            }
        ).catch(
            () => {
                this.setState( { infographicCount: 0 } );
            }
        );
    }
    maybeUpdateSelectedAuthors( alreadyAvailableUsersIds ) {
        let updatedSelectedAuthors = [];
        let needUpdate = false;
        this.props.attributes.selectedAuthors.map( ( user_id ) => {
            if( alreadyAvailableUsersIds.includes( user_id.toString() ) ){
                updatedSelectedAuthors.push( user_id );
            }else{
                needUpdate = true;
            }
        } );
        if( needUpdate ){
            this.props.setAttributes( { selectedAuthors: updatedSelectedAuthors } );
        }
    }

    render() {
        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;

        const { attributes, setAttributes, name, isSelected } = this.props;
        const { 
            selectedAuthors,
            useCarousel,
            carouselNavigation,
            carouselPagination,
            carouselAutoplay,
            carouselAutoHeight,
            carouselLoop,
            carouselColor,
            itemsOrderBy,
            itemsOrder,
            showCover,
            showDescription,
            showIcon,
            showLink,
            showPostsNumber,
            linkText,
            borderWidth,
            borderColor,
            backgroundColor,
            textColor,
            decorColor,
            postsNumberColor,
        } = attributes;

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

        const block = wp.blocks.getBlockType(name);

        //set grid type
        var gridType = "grid-type-1";
        const layout = 'box';
        const size = 'small';

        //styles
        let articleStyles = {};
        let itemContentStyles = {};
        let itemPostsLinkStyle = {};
        let authorPostsNumberStyle = {};
        let carouselStyle = {};

        if( activeProPlugin ){
            articleStyles = {
                ...( textColor ? { color: textColor } : {} ),
            };
            itemContentStyles = {
                ...( borderColor ? { borderColor: borderColor } : {} ),
                ...( backgroundColor ? { backgroundColor: backgroundColor } : {} ),
            };
            itemPostsLinkStyle = {
                ...( decorColor ? { color: decorColor } : {} ),
            };
            authorPostsNumberStyle = {
                ...( decorColor ? { backgroundColor: decorColor } : {} ),
                ...( postsNumberColor ? { color: postsNumberColor } : {} ),
            };
            carouselStyle = {
                ...( carouselColor ? { color: carouselColor } : {} ),
            };
        }

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
                        <ToggleControl
                            label={__('Show link', 'citadela-directory')}
                            checked={ showLink }
                            onChange={ ( checked ) => this.props.setAttributes( { showLink: checked } ) }
                        />

                        { showLink && 
                            <TextControl
                                label={ __('View posts label', 'citadela-directory') }
                                value={ linkText }
                                onChange={ ( value ) => { setAttributes( { linkText: value } ); } }
                            />
                        }

                    </PanelBody>

                    <PanelBody
                        title={ __( 'Order Options', 'citadela-directory' ) }
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <SelectControl
                            label={ __( 'Order by', 'citadela-directory' ) }
                            value={ itemsOrderBy }
                            options={ [
                                { label: __( 'User ID', 'citadela-directory' ), value: 'ID' },
                                { label: __( 'Display name', 'citadela-directory' ), value: 'display_name' },
                                { label: __( 'User login', 'citadela-directory' ), value: 'user_login' },
                                { label: __( 'User email', 'citadela-directory' ), value: 'user_email' },
                                { label: __( 'User posts count', 'citadela-directory' ), value: 'post_count' },
                                { label: __( 'User registration date', 'citadela-directory' ), value: 'registered' },
                            ] }
                            onChange={ ( value ) => {
                                    setAttributes( { itemsOrderBy: value } );
                                }
                            }
                        />

                        { itemsOrderBy != 'rand' && 
                            <RadioControl
                                selected={ itemsOrder }
                                options={ [
                                    { label: __( 'Descending', 'citadela-directory' ), value: 'DESC' },
                                    { label: __( 'Ascending', 'citadela-directory' ), value: 'ASC' },
                                ] }
                                onChange={ ( value ) => {
                                        setAttributes( { itemsOrder: value } );
                                    }
                                }
                            />
                        }
                        
                    </PanelBody>
                    
                    { activeProPlugin &&
                    <PanelBody
                        title={__('Design Options', 'citadela-directory')}
                        initialOpen={false}
                        className="citadela-panel"
                    >
                        <BaseControl
                            label={ __('Decoration color', 'citadela-directory') }
                            className="block-editor-panel-color-settings"
                        >
                            { decorColor && <ColorIndicator colorValue={ decorColor } /> }
                            <ColorPalette
                                value={ decorColor }
                                onChange={ (value) => { setAttributes( { decorColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>

                        <BaseControl
                            label={ __('Posts number color', 'citadela-directory') }
                            className="block-editor-panel-color-settings"
                        >
                            { decorColor && <ColorIndicator colorValue={ postsNumberColor } /> }
                            <ColorPalette
                                value={ postsNumberColor }
                                onChange={ (value) => { setAttributes( { postsNumberColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>
                        
                        <BaseControl
                            label={ __('Text color', 'citadela-directory') }
                            className="block-editor-panel-color-settings"
                        >
                            { textColor && <ColorIndicator colorValue={ textColor } /> }
                            <ColorPalette
                                value={ textColor }
                                onChange={ (value) => { setAttributes( { textColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>


                        <CustomColorControl 
                            label={ __('Background color', 'citadela-directory') }
                            color={ backgroundColor }
                            onChange={ (value) => { setAttributes( { backgroundColor: value } ); } }
                        />

                        <CustomColorControl 
                            label={ __('Borders color', 'citadela-directory') }
                            color={ borderColor }
                            onChange={ (value) => { setAttributes( { borderColor: value } ); } }
                        />

                        <SelectControl
                            label={ __( 'Borders width', 'citadela-directory' ) }
                            value={ borderWidth }
                            options={ [
                                { label: __( 'No borders', 'citadela-directory' ), value: 'none' },
                                { label: __( 'Thin borders', 'citadela-directory' ), value: 'thin' },
                                { label: __( 'Thick borders', 'citadela-directory' ), value: 'thick' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { borderWidth: value } ); }
                        }
                        />

                        { ( carouselPagination || carouselNavigation ) &&
                        <BaseControl
                            label={ __('Carousel decoration color', 'citadela-directory') }
                            className="block-editor-panel-color-settings"
                        >
                            { carouselColor && <ColorIndicator colorValue={ carouselColor } /> }
                            <ColorPalette
                                value={ carouselColor }
                                onChange={ (value) => { setAttributes( { carouselColor: value } ); } }
                                colors={ colorsSet }
                            />
                        </BaseControl>
                        }

                    </PanelBody>
                    }
                    
                    <CarouselPanel
                        attributes={ attributes }
                        onChange={ ( values ) => this.props.setAttributes( 
                            { 
                                useCarousel: values.useCarousel,
                                carouselNavigation: values.carouselNavigation,
                                carouselPagination: values.carouselPagination,
                                carouselAutoplay: values.carouselAutoplay,
                                carouselAutoHeight: values.carouselAutoHeight,
                                carouselAutoplayDelay: values.carouselAutoplayDelay,
                                carouselLoop: values.carouselLoop,
                            } 
                            ) }
                    />
                    
                </InspectorControls>
               
                <div className={classNames(
                        "wp-block-citadela-blocks",
                        "ctdl-authors-list",
                        attributes.className,
                        gridType,
                        `layout-${layout}`,
                        `size-${size}`,
                        activeProPlugin ? `border-${borderWidth}` : null,
                        { "custom-text-color": activeProPlugin && textColor ? true : false },
                        { "custom-decor-color": activeProPlugin && decorColor ? true : false },
                        { "custom-posts-number-color": activeProPlugin && postsNumberColor ? true : false },
                        { "custom-background-color": activeProPlugin && backgroundColor ? true : false },
                        { "custom-carousel-color": activeProPlugin && carouselColor ? true : false },
                        { "show-author-cover": showCover ? true : false },
                        { "show-author-icon": showIcon ? true : false },
                        { "show-author-description": showDescription ? true : false },
                        { "show-posts-number": showPostsNumber ? true : false },
                        { "show-posts-link": showLink ? true : false },
                        { "use-carousel": useCarousel ? true : false },
                        { "carousel-navigation": useCarousel && carouselNavigation ? true : false },
                        { "carousel-pagination": useCarousel && carouselPagination ? true : false },
                        { "carousel-autoheight": useCarousel && carouselAutoHeight ? true : false },
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

                    <PostsSelection
                        dataType='users'
                        selectedPostsIds={ selectedAuthors }
                        onChange={ ( value ) => {
                            this.setState( { infographicCount: value.length } )
                            setAttributes( { selectedAuthors: value } );
                            } 
                        }
                        titleLabel={ __('Authors to display', 'citadela-directory' ) }
                        searchLabel={ __('Search for authors', 'citadela-directory' ) }
                        nothingFoundLabel={ __('No users found', 'citadela-directory' ) }
                        {...this.props}
                    />

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

                    <div class="citadela-block-articles">
                        <div class="citadela-block-articles-wrap">
                            {Array.from(Array( this.state.infographicCount ), (e, i) => {
                                return (
                                    <article class="citadela-author-item has-cover has-icon has-description has-posts" style={ articleStyles }>
                                        <div class="item-content" style={ itemContentStyles }>
                                            <div class="item-thumbnail">
                                                <div class="author-cover"></div>
                                                <div class="author-icon"></div>
                                                <div class="author-posts-number" style={ authorPostsNumberStyle }>
                                                    <span class="posts-number"></span>
                                                    <span class="posts-text"></span>
                                                </div>
                                            </div>
                                            <div class="item-body">
                                                <div class="item-title"></div>
                                                <div class="item-description">
                                                    <span class="line"></span>
                                                    <span class="line"></span>
                                                    <span class="line"></span>
                                                    <span class="line"></span>
                                                </div>
                                                { showLink && 
                                                    <div class="item-posts-link" style={ itemPostsLinkStyle }>{ linkText ? linkText : __('View posts', 'citadela-directory') }</div>
                                                }
                                            </div>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                        

                    </div>
                    { this.state.infographicCount > 0 &&
                        <>
                        { useCarousel && carouselNavigation &&
                            <div class="carousel-navigation-wrapper" style={ carouselStyle }>
                                <div class="carousel-button-prev"><i class="fas fa-chevron-left"></i></div>
                                <div class="carousel-button-next"><i class="fas fa-chevron-right"></i></div>
                            </div>
                        }

                        { useCarousel && carouselPagination &&
                            <div class="carousel-pagination-wrapper" style={ carouselStyle }>
                                <span class="carousel-bullet"></span>
                                <span class="carousel-bullet"></span>
                                <span class="carousel-bullet"></span>
                            </div>
                        }
                        </>
                    }
                    
                </div>
            </>
        );
    }
}
