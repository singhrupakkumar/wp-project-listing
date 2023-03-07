<nav class="nav-tab-wrapper">
<?php foreach( $tabs as $slug => $tab ): ?>
	<a href="<?php echo esc_url( $tab->url ) ?>" class="nav-tab <?php if ( $slug === $current_tab ): ?>nav-tab-active<?php endif ?>"><?php echo esc_html( $tab->label ) ?></a>
<?php endforeach ?>
</nav>
