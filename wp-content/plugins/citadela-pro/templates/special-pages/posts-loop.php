<?php
use Citadela\Pro\Template;
?>

<div class="citadela-block-articles">
    <div class="citadela-block-articles-wrap">

        <?php   
            // limit query loop because of sticky posts in Posts Block
            $limit_sticky_posts = false;
            if( isset( $query->query_vars['showMaxSticky'] ) ){
                $limit_sticky_posts = true;
                $sticky_posts_counter = 0;
            }

            $styles = [];
            $articleStyle = implode('', [ 
                $template_args['layout'] == 'simple' && $template_args['textColor'] ? "color: " . esc_attr( $template_args['textColor'] ) . ";" : "",
                $template_args['layout'] == 'simple' && $template_args['backgroundColor'] ? "background-color: " . esc_attr( $template_args['backgroundColor'] ) . ";" : "",
                $template_args['layout'] == 'simple' && $template_args['borderColor'] ? "border-color: " . esc_attr( $template_args['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['articleStyle'] = $articleStyle ? "style=\"{$articleStyle}\"" : "";
            
            $itemContentStyle = implode('', [ 
                $template_args['layout'] != 'simple' && $template_args['textColor'] ? "color: " . esc_attr( $template_args['textColor'] ) . ";" : "",
                $template_args['layout'] != 'simple' && $template_args['backgroundColor'] ? "background-color: " . esc_attr( $template_args['backgroundColor'] ) . ";" : "",
                $template_args['layout'] != 'simple' && $template_args['borderColor'] ? "border-color: " . esc_attr( $template_args['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemContentStyle'] = $itemContentStyle ? "style=\"{$itemContentStyle}\"" : "";
            
            $footerStyle = implode('', [ 
                $template_args['layout'] != 'simple' && $template_args['borderColor'] ? "border-color: " . esc_attr( $template_args['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['footerStyle'] = $footerStyle ? "style=\"{$footerStyle}\"" : "";
            
            $itemDataStyle = implode('', [ 
                $template_args['layout'] != 'simple' && $template_args['borderColor'] ? "border-color: " . esc_attr( $template_args['borderColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemDataStyle'] = $itemDataStyle ? "style=\"{$itemDataStyle}\"" : "";
            
            $itemDataLocationStyle = implode('', [ 
                $template_args['decorColor'] ? "color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemDataLocationStyle'] = $itemDataLocationStyle ? "style=\"{$itemDataLocationStyle}\"" : "";
            
            $itemDataCategoryStyle = implode('', [ 
                $template_args['decorColor'] ? "border-color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
            ] ) . '"';
            $styles['itemDataCategoryStyle'] = $itemDataCategoryStyle ? "style=\"{$itemDataCategoryStyle}\"" : "";
            
            $dateStyle= implode('', [ 
                $template_args['layout'] == 'list' && $template_args['decorColor'] ? "color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
                $template_args['layout'] == 'box' && $template_args['decorColor'] ? "background-color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
                $template_args['layout'] == 'box' && $template_args['dateColor'] ? "color: " . esc_attr( $template_args['dateColor'] ) . ";" : "",
            ] );
            $styles['dateStyle'] = $dateStyle ? "style=\"{$dateStyle}\"" : "";
            
            $entryMetaLinksStyle = implode('', [ 
                $template_args['layout'] == 'simple' && $template_args['decorColor'] ? "color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
            ] );
            $styles['entryMetaLinksStyle'] = $entryMetaLinksStyle ? "style=\"{$entryMetaLinksStyle}\"" : "";

            $commentsLinkStyle = implode('', [ 
                $template_args['layout'] == 'simple' && $template_args['borderColor'] ? "border-color: " . esc_attr( $template_args['borderColor'] ) . ";" : "",
                $template_args['layout'] == 'simple' && $template_args['decorColor'] ? "color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
            ] );
            $styles['commentsLinkStyle'] = $commentsLinkStyle ? "style=\"{$commentsLinkStyle}\"" : "";

            $stickyStyle = implode('', [ 
                $template_args['decorColor'] ? "color: " . esc_attr( $template_args['decorColor'] ) . ";" : "",
            ] );
            $styles['stickyStyle'] = $stickyStyle ? "style=\"{$stickyStyle}\"" : "";
            
            while ( $query->have_posts() ) : 
                //break the loop if we need to stop query that shows defined number of sticky posts - prevent displaying of more sticky posts than number of posts defined in Posts Block
                if( $limit_sticky_posts && $sticky_posts_counter === $query->query_vars['showMaxSticky'] ) break;

                $query->the_post();
                global $post;

                if ( $template_args['layout'] == 'simple' ) :
                    Template::load( '/special-pages/post-article-simple', [ 'post' => $post, 'template_args' => $template_args, 'styles' => $styles ] );
                else :
                    Template::load( '/special-pages/post-article', [ 'post' => $post, 'template_args' => $template_args, 'styles' => $styles ] );
                endif; 

                if( $limit_sticky_posts ) $sticky_posts_counter++;
            endwhile; 
        ?>

    </div>
</div>