<div class="wp-block-citadela-blocks ctdl-item-events<?php if ($attributes['showEventFeaturedImage']) { echo ' show-item-featured-image'; } if ($attributes['showEventDescription']) { echo ' show-item-description'; } if ($attributes['showEventPrice']) { echo ' show-item-price'; }  ?> <?php echo esc_attr( implode( " ", $classes ) );?>">
    <?php if (trim($attributes['title'])) { ?>
    <header class="citadela-block-header">
        <div class="citadela-block-title">
            <h2><?php echo esc_html($attributes['title']); ?></h2>
        </div>
    </header>
    <?php } ?>
	<div class="citadela-block-articles">
		<div class="citadela-block-articles-wrap">
            <?php foreach ($events as $event) { ?>
                <article class="citadela-event">
                    <div class="citadela-event-date">
                        <div class="event-date-label">
                            <span class="month"><?php echo esc_html($event->dates->start_display->format_i18n('M')); ?></span>
                            <span class="day"><?php echo esc_html($event->dates->start_display->format_i18n('j')); ?></span>
                        </div>
                    </div>
                    <div class="citadela-event-body">
                        <?php if ($attributes['showEventFeaturedImage'] && $event->thumbnail->exists) { ?>
                        <div class="citadela-event-thumbnail">
                            <a href="<?php echo esc_url($event->permalink); ?>">
                                <?php if ($attributes['imageSize'] === 'thumbnail') { ?>
                                    <img src="<?php echo esc_url( $event->thumbnail->thumbnail->url ); ?>"
                                <?php } else { ?>
                                    <img src="<?php echo esc_url( $event->thumbnail->full->url ); ?>"
                                    <?php if (!empty($event->thumbnail->srcset)) { ?>
                                    srcset="<?php echo esc_attr( $event->thumbnail->srcset ); ?>"
                                    <?php } ?>
                                <?php } ?>
                                <?php if (!empty($event->thumbnail->alt)) { ?>
                                    alt="<?php echo esc_attr( $event->thumbnail->alt ); ?>"
                                <?php } ?>
                                <?php if (!empty($event->thumbnail->title)) { ?>
                                    title="<?php echo esc_attr( $event->thumbnail->title ); ?>"
                                <?php } ?> />
                            </a>
                        </div>
                        <?php } ?>
                        <div class="citadela-event-data">
                            <div class="citadela-event-datetime"><?php echo wp_kses_post($event->schedule_details->value()); ?></div>
                            <div class="citadela-event-title">
                                <h3>
                                    <a href="<?php echo esc_url($event->permalink); ?>">
                                        <?php echo esc_html($event->title); ?>
                                        <?php if ($attributes['showEventPrice'] && !empty($event->cost)) { ?>
                                        <span class="event-price"><?php echo esc_html($event->cost); ?></span>
                                        <?php } ?>
                                    </a>
                                </h3>
                            </div>
                            <?php if ($attributes['showEventDescription'] && !empty($event->excerpt)) { ?>
                            <div class="citadela-event-description"><?php echo wp_kses_post($event->excerpt); ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </article>
            <?php } ?>
		</div>
	</div>
</div>