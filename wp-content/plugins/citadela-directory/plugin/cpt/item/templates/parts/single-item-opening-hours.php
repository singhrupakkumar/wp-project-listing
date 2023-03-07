<?php
/* required template variables should be defined before including this file */
$blockTitle = isset($blockTitle) ? $blockTitle : '';

?>

<?php if($meta->show_opening_hours): ?>

<?php
$days = [
    'monday' => [
        'title' => esc_html__('Monday', 'citadela-directory'),
        'value' => $meta->opening_hours_monday ? $meta->opening_hours_monday : '-',
    ],
    'tuesday' => [
        'title' => esc_html__('Tuesday', 'citadela-directory'),
        'value' => $meta->opening_hours_tuesday ? $meta->opening_hours_tuesday : '-',
    ],
    'wednesday' => [
        'title' => esc_html__('Wednesday', 'citadela-directory'),
        'value' => $meta->opening_hours_wednesday ? $meta->opening_hours_wednesday : '-',
    ],
    'thursday' => [
        'title' => esc_html__('Thursday', 'citadela-directory'),
        'value' => $meta->opening_hours_thursday ? $meta->opening_hours_thursday : '-',
    ],
    'friday' => [
        'title' => esc_html__('Friday', 'citadela-directory'),
        'value' => $meta->opening_hours_friday ? $meta->opening_hours_friday : '-',
    ],
    'saturday' => [
        'title' => esc_html__('Saturday', 'citadela-directory'),
        'value' => $meta->opening_hours_saturday ? $meta->opening_hours_saturday : '-',
    ],
    'sunday' => [
        'title' => esc_html__('Sunday', 'citadela-directory'),
        'value' => $meta->opening_hours_sunday ? $meta->opening_hours_sunday : '-',
    ],
];
?>

<?php if($blockTitle) : ?>
<header class="citadela-block-header">
	<div class="citadela-block-title">
		<h2><?php echo esc_html( $blockTitle ); ?></h2>
	</div>
</header>
<?php endif; ?>

<div class="citadela-block-articles">
    <div class="citadela-block-articles-wrap">
        <?php foreach ($days as $key=>$day) : ?>
        <div class="oh-day <?php echo esc_attr( $day['value'] == '-' ? 'day-closed' : 'day-opened' ); ?>">
            <div class="oh-label"><p><?php echo wp_kses_data( $day['title'] ); ?></p></div>
            <div class="oh-data">
                <p><?php echo wp_kses_data( $day['value'] ); ?></p>
                <?php if($day['value'] != '-'): ?>
                <meta itemprop="openingHours" content="<?php echo esc_attr( ucfirst(substr($key, 0, 2)) . ' ' . $day['value'] ); ?>"/>
                <?php endif; ?>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<?php if ($meta->opening_hours_note) : ?>
<div class="citadela-block-note">
	<p><?php echo wp_kses_data( $meta->opening_hours_note ); ?></p>
</div>
<?php endif; ?>

<?php endif; ?>