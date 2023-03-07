import CustomInspectorControls from './inspector-controls';
import AlignToolbar from '../../components/toolbar-alignment';
import FontWeightToolbar from '../../components/toolbar-font-weight';
import StateIcons from '../../components/state-icons';

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { RichText, BlockControls, InspectorControls } = wp.blockEditor;
const { ToolbarGroup, ToolbarItem, Icon, Tooltip } = wp.components;

export default class Edit extends Component{

	constructor() {
		super( ...arguments );
		this.setState = this.setState.bind(this);
		this.migrateAlignAttr = this.migrateAlignAttr.bind(this);
		const loadedFamily = this.props.attributes.googleFont['family'];
		this.state = {
			responsiveTab: "desktop",
			loadedFonts: loadedFamily ? [ loadedFamily.replace( /\s+/g, '+' ) ] : [] ,
		}
	}

	componentDidMount(){
		const { googleFont } = this.props.attributes;
		this.loadGoogleFont( googleFont );

		this.migrateAlignAttr();
	}

	migrateAlignAttr() {
		const { align } = this.props.attributes;
		
		if( align === 'align-left' ) {
			this.props.setAttributes( { align: 'left' } );
		}
		if( align === 'align-center' ){
			this.props.setAttributes( { align: 'center' } );
		} 
		if( align === 'align-right' ){
			this.props.setAttributes( { align: 'right' } );
		} 
	}

	loadGoogleFont( googleFont ) {
		const fontFamily = googleFont['family'];
		if( fontFamily == '' ) return;

		const head = document.head;
		const link = document.createElement( 'link' );
		const variants = googleFont.variants ? ':' + googleFont.variants.join( ',' ) : '';

		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = 'https://fonts.googleapis.com/css?family=' + fontFamily.replace( /\s+/g, '+' ) + variants + '&display=swap';

		head.appendChild( link );
	}

	render() {
		const { attributes, setAttributes, isSelected, className, name } = this.props;
		const isWidgetsPage = _citadela_page_title_block_vars.current_screen && _citadela_page_title_block_vars.current_screen.id && _citadela_page_title_block_vars.current_screen.id == 'widgets';
		const isCustomizePage = _citadela_page_title_block_vars.current_screen && _citadela_page_title_block_vars.current_screen.id && _citadela_page_title_block_vars.current_screen.id == 'customize';
		var isSpecialPage = false;
		var isNothingFoundPage = false;
		var specialTitleText = '';
		var specialSubtitleText = '';
		
		let post = null;
		if( isWidgetsPage || isCustomizePage ){
			specialTitleText = __('Current page title', 'citadela-pro');
		}else{
			const { getCurrentPost, getEditedPostAttribute } = wp.data.select("core/editor");
		

			post = getCurrentPost();

			if ( post.id && typeof CitadelaDirectorySettings !== 'undefined' && CitadelaDirectorySettings.specialPages ) {
				//Directory plugin is active, check special pages
				const singleItemPageId = CitadelaDirectorySettings.specialPages['single-item'];
				const itemCategoryPageId = CitadelaDirectorySettings.specialPages['item-category'];
				const itemLocationPageId = CitadelaDirectorySettings.specialPages['item-location'];
				const searchResultsPageId = CitadelaDirectorySettings.specialPages['search-results'];
				const postsSearchResultsPageId = CitadelaDirectorySettings.specialPages['posts-search-results'];
				const defaultSearchResultsPageId = CitadelaDirectorySettings.specialPages['default-search-results'];
				const postsCategoryPageId = CitadelaDirectorySettings.specialPages['posts-category'];
				const postsTagPageId = CitadelaDirectorySettings.specialPages['posts-tag'];
				const postsDatePageId = CitadelaDirectorySettings.specialPages['posts-date'];
				const postsAuthorPageId = CitadelaDirectorySettings.specialPages['posts-author'];
				const nothingFoundPageId = CitadelaDirectorySettings.specialPages['404-page'];
				const item_detail_options = CitadelaDirectorySettings.options.item_detail;
				const currentPostType = CitadelaDirectorySettings.currentPost.post_type;

				if(singleItemPageId == post.id){
					//we are on single item special page
					isSpecialPage = true;
					specialTitleText = __('Item title', 'citadela-pro');
					specialSubtitleText = __('Item subtitle', 'citadela-pro');
				}
				if(itemCategoryPageId == post.id || postsCategoryPageId == post.id){
					//we are on item or post category special page
					isSpecialPage = true;
					specialTitleText = __('Category title', 'citadela-pro');
					specialSubtitleText = __('Category description', 'citadela-pro');
				}
				if(itemLocationPageId == post.id){
					//we are on location special page
					isSpecialPage = true;
					specialTitleText = __('Location title', 'citadela-pro');
					specialSubtitleText = __('Location description', 'citadela-pro');
				}
				if(searchResultsPageId == post.id || postsSearchResultsPageId == post.id || defaultSearchResultsPageId == post.id){
					//we are on some search results page
					isSpecialPage = true;
					specialTitleText = __('Search results for:', 'citadela-pro');
					specialSubtitleText = "";
				}
				if(postsTagPageId == post.id){
					isSpecialPage = true;
					specialTitleText = __('Tag name', 'citadela-pro');
					specialSubtitleText = __('Tag description', 'citadela-pro');
				}
				if(postsDatePageId == post.id){
					isSpecialPage = true;
					specialTitleText = __('Date', 'citadela-pro');
					specialSubtitleText = "";
				}
				if(postsAuthorPageId == post.id){
					isSpecialPage = true;
					specialTitleText = __('Author name', 'citadela-pro');
					specialSubtitleText = __('Author biographical info', 'citadela-pro');
				}
				if(nothingFoundPageId == post.id){
					isNothingFoundPage = true;
					specialTitleText = __("Oops! That page can't be found.", 'citadela-pro');
				}

				if( currentPostType == 'citadela-item' && item_detail_options && item_detail_options.enable ){
					console.log(post);
					isSpecialPage = true;
					specialTitleText = post.title;
					specialSubtitleText = getEditedPostAttribute( 'meta' )['_citadela_subtitle'];
				}



			}

			if ( post.id && typeof CitadelaProSettings !== 'undefined') {
				const blogPageId = CitadelaProSettings.specialPages['blog'];
				if (blogPageId == post.id){
					//we are on blog special page
					isSpecialPage = true;
					if (typeof CitadelaProSettings.page_for_posts !== 'undefined') {
						specialTitleText = CitadelaProSettings.page_for_posts.title;
					} else {
						specialTitleText = post.title;
					}
					specialSubtitleText = "";
				}
			}
			
		}

		const {
			align,
			alignMobile,
			subtitle,
			useResponsiveOptions,
			fontSizeUnit,
			fontSizeUnitMobile,
			fontSize,
			fontSizeMobile,
			lineHeight,
			lineHeightMobile,
			letterSpacing,
			googleFont,
			titleColor,
			subtitleColor,
			hideSeparator,
			fontWeight,
		} = attributes;

		const mobileView = ( useResponsiveOptions && this.state.responsiveTab == 'mobile' );
		const desktopView = ( useResponsiveOptions && this.state.responsiveTab == 'desktop' );

		const actualValue = mobileView
		? {
			fontSize: fontSizeMobile ? fontSizeMobile : "",
			fontSizeUnit: fontSizeUnitMobile ? fontSizeUnitMobile : fontSizeUnit,
			lineHeight: lineHeightMobile ? lineHeightMobile : "",
			align: alignMobile ? alignMobile : align,
		}
		: {
			fontSize: fontSize ? fontSize : "",
			fontSizeUnit: fontSizeUnit ? fontSizeUnit : "",
			lineHeight: lineHeight ? lineHeight : "",
			align: align ? align : "",
		}

		var styles = {
			...( googleFont['family'] != '' ? { fontFamily: googleFont['family'] } : "" ),
			...( actualValue.fontSize ? { fontSize: `${actualValue.fontSize}${actualValue.fontSizeUnit}` } : "" ),
			...( actualValue.lineHeight ? { lineHeight: actualValue.lineHeight } : "" ),
			...( letterSpacing ? { letterSpacing: `${letterSpacing}em` } : "" ),
			...( fontWeight ? { fontWeight: fontWeight } : "" ),
		}
		var subtitleStyles = {
			...( subtitleColor ? { color: `${subtitleColor}` } : "" ),
		}
		
		var entryHeaderStyles = {
			...( titleColor ? { color: `${titleColor}` } : "" ),
		}
		
		return (
			<Fragment>
				<BlockControls key='controls'>
					
					<ToolbarGroup>
						<ToolbarItem>
							{ ( toggleProps ) => ( 
								<AlignToolbar
									label={ mobileView ? __( 'Select alignment on mobile design', 'citadela-pro') : __( 'Select alignment', 'citadela-pro') }
									value={ actualValue.align } 
									onChange={ ( value ) => { mobileView ? setAttributes( { alignMobile: value } ) : setAttributes( { align: value } ) } } 
									toggleProps={ toggleProps }
								/>
							)}
						</ToolbarItem>

						<ToolbarItem>
							{ ( toggleProps ) => ( 
								<FontWeightToolbar 
									value={ fontWeight } 
									onChange={ ( value ) => setAttributes( { fontWeight: value } ) } 
									toggleProps={ toggleProps }
								/>
							)}
						</ToolbarItem>

					</ToolbarGroup>
					
				</BlockControls>
				{
				<InspectorControls key='inspector'>
					<CustomInspectorControls attributes={ attributes } setAttributes={ setAttributes } state={ this.state } setState={ this.setState } />
				</InspectorControls>
				}
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-page-title",
							{ "special-page-title": (isSpecialPage ? true : false ) },
							`align-${actualValue.align}`,
							{ "has-subtitle": (subtitle ? true : false ) },
							{ "hidden-separator": ( hideSeparator ? true : false ) },
							fontWeight ? `weight-${fontWeight}` : "",
						)
					}
				>
					<StateIcons 
                        useResponsiveOptions= { useResponsiveOptions } 
                        isSelected={ isSelected } 
                        currentView={ this.state.responsiveTab }
                    />

					<div className={ "page-title custom" }>
						<header className={"entry-header"} style={entryHeaderStyles}>
							<div className={"entry-header-wrap"}>
								<h1 
									className={"entry-title"}
									style={styles}
								>
									{ ( isSpecialPage || isWidgetsPage || isCustomizePage || isNothingFoundPage)
										? specialTitleText
										: post.title
									}
								</h1>
								<div className={"entry-subtitle"}>
									{ ( isSpecialPage && ! isNothingFoundPage )
										?
											<p className="ctdl-subtitle" style={subtitleStyles}>{specialSubtitleText}</p>
										: <RichText
											key='richtext'
											tagName='p'
											className="ctdl-subtitle"
											onChange= { (value) => { setAttributes( { subtitle: value } ) } }
											value= {subtitle}
											placeholder={ __('Subtitle', 'citadela-pro' ) }
											keepPlaceholderOnFocus={true}
											style={subtitleStyles}
											/>
									}
								</div>
							</div>
						</header>
					</div>
				</div>
			</Fragment>
		);
	}

}