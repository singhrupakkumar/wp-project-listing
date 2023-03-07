<?php
use Citadela\Pro\Template;
?>

<div class="wrap citadela-settings-wrap">
	<?php Template::load( '/_settings-header' ) ?>
	<div class="citadela-settings-content">
		<?php Template::load( '/_settings-navigation' ) ?>
		<?php $settings->do_form(); ?>
	</div>
</div>

<?php
add_action( 'admin_print_footer_scripts', function(){
	ob_start();
	?>
		<script>
			( function( $ ) {
				var $switches = $( '.section-switch' )
				var $sections = $( '.citadela-section:not(.citadela-section-default' )
				var activeSection = $switches.filter( ':checked' ).val()

				$sections.filter( ':not(.citadela-section-' + activeSection +')' ).hide()

				$switches.on( 'change', function( e ) {
					$sections.filter( ':not(.citadela-section-' + e.target.value +')' ).hide()
					$sections.filter( '.citadela-section-' + e.target.value ).fadeIn()
					if ( e.target.value === 'advanced' ) {
						$( '.field-type-code-editor' ).each( function () {
							$(this).data('codemirror').refresh();
						} )
					}
				} )
			} )( jQuery )
		</script>
	<?php
	echo ob_get_clean();
} );
