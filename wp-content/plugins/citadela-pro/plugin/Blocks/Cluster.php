<?php

namespace Citadela\Pro\Blocks;

use Citadela\Pro\Template;

class Cluster extends Block {

   public $slug = 'cluster';

    protected static function get_classes( $attributes ) {
        $classes = [];
        return $classes;
    }

    function block_vars(){
      return [
         'blockUrl' => \ctdl\pro\url( '/assets/blocks/cluster' ),
      ];
    }
}
