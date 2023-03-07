<?php
if( ($meta->latitude && $meta->longitude ) && ($meta->latitude !== "0" && $meta->longitude !== "0" ) ): 
	$latitude = $meta->latitude;
	$longitude = $meta->longitude;
?>


<div class="small-map-container google-map-container" data-latitude="<?php echo esc_attr( $latitude ); ?>" data-longitude="<?php echo esc_attr( $longitude ); ?>">
	<div class="content" style="height: 300px;"></div>
</div>
<?php endif; ?>