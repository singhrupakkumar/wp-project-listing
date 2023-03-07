<?php

namespace Citadela\Directory\Blocks;

class DirectorySublocationsList extends Block {

    protected static $slug = 'directory-sublocations-list';
    
    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        if (!is_tax('citadela-item-location')) return '';

        $term = get_queried_object();

        $taxonomy = $term->taxonomy;

        $meta_query_args = [];
        if( $attributes['onlyFeatured'] ){
            $meta_query_args['featured'] = true;
        }

        $terms = \CitadelaItem::citadelaGetTermChilds($term->term_id, $taxonomy, $meta_query_args);

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        if( $attributes['onlyFeatured'] ){ $classes[] = "only-featured"; }

        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-directory-sublocations-list grid-type-2 size-small <?php echo esc_attr( implode(" ", $classes) );?>">

            <div class="citadela-block-articles">
                <div class="citadela-block-articles-wrap">

                    <?php foreach ($terms as $term) : ?>
                    <?php
                        $term_id = $term->term_id;
                        $url = get_term_link($term_id);
                        $description = strip_tags(term_description($term_id));
                    ?>

                    <a href="<?php echo esc_url( $url ); ?>">
                        <article class=folder-card>
                            <div class="folder-header">
                                <div class="folder-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>

                            <div class="folder-content">
                                <div class="folder-content-wrap">
                                    <p class="folder-title"><?php echo esc_html( $term->name ); ?></p>
                                    <?php if($description) : ?>
                                    <p class="folder-description"><?php echo wp_kses_data( $description ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
        <?php

        return ob_get_clean();
    }

}