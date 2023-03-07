wp.domReady(function () {

    if (typeof CitadelaDirectorySettings !== 'undefined' && CitadelaDirectorySettings.currentPost) {
        const currentPage = CitadelaDirectorySettings.currentPost.post_id;
        const currentPostType = CitadelaDirectorySettings.currentPost.post_type;
        const item_detail_options = CitadelaDirectorySettings.options.item_detail;

        if (currentPostType == 'citadela-item' && item_detail_options && item_detail_options.enable) {
            // disallow blocks available for Citadela Special pages, except these for Item Detail special page
            const blacklist = [
                'citadela-directory/item-content',
                'citadela-directory/automatic-posts-map',
                'citadela-directory/directory-search-results',
                'citadela-directory/posts-search-results',
                'citadela-directory/directory-subcategories-list',
                'citadela-directory/directory-sublocations-list',
            ];

            for (const i in blacklist) {
                const block = blacklist[i];
                wp.blocks.unregisterBlockType(block);
            }

        } else {
            // default behavior for blocks on all other post types or pages
            const availableBlocks = {
                'citadela-directory/item-content': ['single-item'],
                'citadela-directory/item-opening-hours': ['single-item'],
                'citadela-directory/item-featured-image': ['single-item'],
                'citadela-directory/item-contact-form': ['single-item'],
                'citadela-directory/item-contact-details': ['single-item'],
                'citadela-directory/item-gpx-download': ['single-item'],
                'citadela-directory/item-get-directions': ['single-item'],
                'citadela-directory/item-claim-listing': ['single-item'],
                'citadela-directory/item-extension': ['single-item'],
                'citadela-directory/item-gallery': ['single-item'],
                'citadela-directory/item-events': ['single-item'],

                'citadela-directory/automatic-directory-google-map': ['single-item', 'item-category', 'item-location', 'search-results'],
                'citadela-directory/automatic-posts-map': ['posts-search-results', 'post', /*'posts-date', 'posts-tag', 'posts-author', 'posts-category' */],

                'citadela-directory/directory-search-results': ['item-category', 'item-location', 'search-results'],
                'citadela-directory/posts-search-results': ['posts-search-results', 'posts-category', 'posts-tag', 'posts-date', 'posts-author'],

                'citadela-directory/default-search-results': ['default-search-results'],

                'citadela-directory/directory-subcategories-list': ['item-category'],
                'citadela-directory/directory-sublocations-list': ['item-location'],

                'citadela-directory/author-detail': ['posts-author'],
            };

            for (const block in availableBlocks) {
                const enabledOnPages = availableBlocks[block];
                let shouldUnregister = true;

                for (let i = 0; i < enabledOnPages.length; i++) {
                    if (CitadelaDirectorySpecialPages[enabledOnPages[i]] == currentPage || enabledOnPages[i] == currentPostType) {
                        shouldUnregister = false;
                        break;
                    }
                }



                if (shouldUnregister) {
                    wp.blocks.unregisterBlockType(block);
                }
            }
        }
    } else if (typeof CitadelaDirectorySettings !== 'undefined' && CitadelaDirectorySettings.current_screen && CitadelaDirectorySettings.current_screen.id) {
        if (CitadelaDirectorySettings.current_screen.id == 'widgets' || CitadelaDirectorySettings.current_screen.id == 'customize') {
            // blocks disallowed on Widgets or Customize screen
            const blocks = [
                'citadela-directory/item-content',
                'citadela-directory/item-opening-hours',
                'citadela-directory/item-featured-image',
                'citadela-directory/item-contact-form',
                'citadela-directory/item-contact-details',
                'citadela-directory/item-gpx-download',
                'citadela-directory/item-get-directions',
                'citadela-directory/item-claim-listing',
                'citadela-directory/item-extension',
                'citadela-directory/item-gallery',
                'citadela-directory/item-events',

                'citadela-directory/automatic-directory-google-map',
                'citadela-directory/automatic-posts-map',

                'citadela-directory/directory-search-results',
                'citadela-directory/posts-search-results',

                'citadela-directory/directory-subcategories-list',
                'citadela-directory/directory-sublocations-list',
            ];
            for (const i in blocks) {
                wp.blocks.unregisterBlockType(blocks[i]);
            }
        }
    }

});