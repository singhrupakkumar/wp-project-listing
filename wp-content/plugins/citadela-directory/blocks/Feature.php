<?php

namespace Citadela\Directory\Blocks;

class Feature {

    protected static $blocks = [];

    function __construct() {
        
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );

        foreach ( [

            new ItemContent(),
            new ItemFeaturedImage(),
            new ItemOpeningHours(),
            new ItemContactDetails(),
            new ItemContactForm(),
            new ItemGpxDownload(),
            new ItemGetDirections(),
            new ItemClaimListing(),
            new ItemExtension(),
            new ItemGallery(),
            new ItemEvents(),
            new PostsMap(),
            new DirectoryGoogleMap(),
            new AutomaticDirectoryGoogleMap(),
            new AutomaticPostsMap(),
            new DirectorySearchForm(),
            new DirectorySearchResults(),
            new PostsSearchForm(),
            new PostsSearchResults(),
            new DefaultSearchResults(),
            new DirectoryCategoriesList(),
            new DirectorySubcategoriesList(),
            new DirectoryLocationsList(),
            new DirectorySublocationsList(),
            new DirectoryItemsList(),
            new DirectoryAdvancedFilters(),
            new DirectorySimilarItems(),
            new DirectoryMembershipContent(),
            new AuthorsList(),
            new AuthorDetail(),

        ] as $block ) {

            self::$blocks[] = $block;
           
        }

        
    }

    function enqueue_block_editor_assets() {
        $dir = dirname( __FILE__ );

        $index_js = "build/index.js";
        $current_screen = get_current_screen();

        $deps = [
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'wp-editor',
            'wp-api-fetch',
        ];

        if( $current_screen && $current_screen->id == 'widgets' ){
            // "wp-editor" script should not be enqueued together with the new widgets editor (wp-edit-widgets or wp-customize-widgets)
            unset( $deps[array_search('wp-editor', $deps)] );
            $deps[] = 'wp-edit-widgets';
        }

        wp_register_script(
            "citadela-directory-blocks",
            plugins_url( $index_js, __FILE__  ),
            $deps,
            filemtime( "$dir/$index_js" )
        );

        // hotfix to enqueue scripts in WP Customizer
        if( $current_screen && $current_screen->id == 'customize' ){
            wp_enqueue_script( "citadela-directory-blocks" );
            wp_localize_script( "citadela-directory-blocks",  'CitadelaDirectorySettings', \CitadelaDirectory::getInstance()->getGlobalJsSettings() );
        }

        wp_set_script_translations( "citadela-directory-blocks", 'citadela-directory', \CitadelaDirectory::getInstance()->paths->dir->languages );

        // registered script is enqueued via block.json files
    }
}