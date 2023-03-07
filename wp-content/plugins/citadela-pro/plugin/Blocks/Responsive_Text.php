<?php

namespace Citadela\Pro\Blocks;

use Citadela\Pro\Template;

class Responsive_Text extends Block {

   public $slug = 'responsive-text';

   public function render( $attributes, $content ) {
      $attr = (object) $attributes;
   
      ob_start();
      
      $useResponsive = isset( $attr->useResponsiveOptions ) && $attr->useResponsiveOptions;
      
      $classes = [];
      if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
      if( isset( $attr->backgroundColor ) && $attr->backgroundColor ) $classes[] = "has-bg";
      if( isset( $attr->fontWeight ) && $attr->fontWeight ) $classes[] = "weight-{$attr->fontWeight}";
      if( isset( $attr->align ) && $attr->align ) $classes[] = "align-{$attr->align}";
      if( isset( $attr->removeMargins ) && $attr->removeMargins ) $classes[] = "no-margins";
      
      $styles = [];
      if( isset( $attr->googleFont ) && $attr->googleFont['family'] != '' ) $styles[] = "font-family:'{$attr->googleFont['family']}';";
      if( isset( $attr->fontSize ) && $attr->fontSize ) $styles[] = "font-size: {$attr->fontSize}{$attr->fontSizeUnit};";
      if( isset( $attr->fontWeight ) && $attr->fontWeight ) $styles[] = "font-weight: {$attr->fontWeight};";
      if( isset( $attr->lineHeight ) && $attr->lineHeight ) $styles[] = "line-height: {$attr->lineHeight};";
      if( isset( $attr->letterSpacing ) && $attr->letterSpacing ) $styles[] = "letter-spacing: {$attr->letterSpacing}em;";
      if( isset( $attr->color ) && $attr->color ) $styles[] = "color: {$attr->color};";
      if( isset( $attr->backgroundColor ) && $attr->backgroundColor ) $styles[] = "background-color: {$attr->backgroundColor};";
      if( isset( $attr->underline ) && $attr->underline ) $styles[] = "text-decoration: underline;";
      if( isset( $attr->linethrough ) && $attr->linethrough ) $styles[] = "text-decoration: line-through;";
      if( isset( $attr->italic ) && $attr->italic ) $styles[] = "font-style: italic;";

      if( $useResponsive ) {
         
         $data = [
            "desktop" => [],
            "mobile" => [],
         ];
         
         //$fontSizeUnit is always defined
         $fontSize = isset( $attr->fontSize ) ? "{$attr->fontSize}{$attr->fontSizeUnit}" : '';
         $lineHeight = isset( $attr->lineHeight ) ? $attr->lineHeight : '';
         if( $fontSize ) $data['desktop']["fontSize"] = $fontSize;
         if( $lineHeight ) $data['desktop']["lineHeight"] = $lineHeight;
         
         $fontSizeUnitMobile = isset( $attr->fontSizeUnitMobile ) ? $attr->fontSizeUnitMobile : $attr->fontSizeUnit;
         $fontSizeMobile = isset( $attr->fontSizeMobile ) ? "{$attr->fontSizeMobile}{$fontSizeUnitMobile}" : '';
         $lineHeightMobile = isset( $attr->lineHeightMobile ) ? $attr->lineHeightMobile : '';
         if( $fontSizeMobile ) $data['mobile']["fontSize"] = $fontSizeMobile;
         if( $lineHeightMobile ) $data['mobile']["lineHeight"] = $lineHeightMobile;
         
         $align = $attr->align;
         $alignMobile = isset( $attr->alignMobile ) ? $attr->alignMobile : $attr->align;
         if( $align != $alignMobile){
            $data['desktop']["align"] = $align;
            $data['mobile']["align"] = $alignMobile;
         }
      }
      
      if( empty( $data['mobile'] ) ) $useResponsive = false;
      
      if( $useResponsive ) {
         $classes[] = "responsive-options";
         $classes[] = "loading";
      }

      $stylesText = implode( ' ', $styles);
      $startTag = "<{$attr->htmlTag} class=\"inner-tag\" style=\"{$stylesText}\">";
      $endTag = "</{$attr->htmlTag}>";
      $text = isset( $attr->text ) ? $attr->text : '';
      
      self::google_fonts( $attr->googleFont );
      ?>
         <div 
            class="citadela-block-responsive-text <?php echo implode( ' ' , $classes ); ?>"
            <?php if( $useResponsive ) echo 'data-block-mobile-attr="' . htmlspecialchars( json_encode( $data ) ) . '"' ?>
            <?php if( $useResponsive ) echo 'data-block-mobile-breakpoint="' . $attr->breakpointMobile . '"' ?>
         >
            <?php echo "{$startTag}{$text}{$endTag}"; ?>
         </div>
      <?php
      return ob_get_clean();
   }
   
   protected function google_fonts( $googleFont ){
        if( isset( $googleFont['family'] ) && $googleFont['family'] != '' ) {
            
            $variants = implode( ',', $googleFont['variants'] );

            $url = add_query_arg( [
                'family' => "{$googleFont['family']}:{$variants}",
                'subset' => implode( ',', $googleFont['subsets'] ),
                'display' => 'swap',
            ], 'https://fonts.googleapis.com/css' );
            
            $fontSlug = str_replace(" ", "-", $googleFont['family']);
            wp_enqueue_style( "citadela-{$this->slug}-font-{$fontSlug}", $url );

        }
    }

   protected static function get_classes( $attributes ) {
      $classes = [];
      return $classes;
   }
}
