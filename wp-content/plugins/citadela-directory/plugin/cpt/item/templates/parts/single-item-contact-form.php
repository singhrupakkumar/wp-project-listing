<?php

$blockTitle = isset($blockTitle) ? $blockTitle : '';


$enabled = ( $meta->use_contact_form && $meta->email ) ? true : false;

$atts = (object) $attributes;

$activeCaptcha = CitadelaDirectoryRecaptcha::$activeCaptcha;
$activeCaptchaClass = $activeCaptcha ? 'active-captcha' : '';

if($enabled): ?>

<div class="wp-block-citadela-blocks ctdl-item-contact-form <?php echo esc_attr( implode( " ", $classes ) );?>">

    <?php if($blockTitle) : ?>
    <header class="citadela-block-header">
        <div class="citadela-block-title">
            <h2><?php echo esc_html( $blockTitle ); ?></h2>
        </div>
    </header>
    <?php endif; ?>

	<div class="citadela-block-form">
		<form id="item-detail-contact-form" class="contact-form <?php echo esc_attr( $activeCaptchaClass ); ?>" onSubmit="javascript:submitItemContactForm(event);">

        <?php if($activeCaptcha): ?>
            <input type="hidden" class="citadela-recaptcha-token" name="g-recaptcha-response" value="">
            <?php endif; ?>

			<input type="hidden" name="response-email-address" value="<?php echo esc_attr( $meta->email ); ?>">
			<input type="hidden" name="response-email-content" value="<?php echo esc_html( $atts->emailMessage); ?>">

			<?php if($atts->emailFromName): ?>
			<input type="hidden" name="response-email-sender-name" value="<?php echo esc_html( $atts->emailFromName ); ?>">
			<?php endif; ?>

			<?php if($atts->emailFromAddress): ?>
			<input type="hidden" name="response-email-sender-address" value="<?php echo esc_html( $atts->emailFromAddress ); ?>">
			<?php else: ?>
			<input type="hidden" name="response-email-sender-address" value="<?php echo esc_html( get_option('admin_email') ); ?>">
			<?php endif; ?>


			<div class="data-type-1">
				<div class="input-container name">
					<label><?php echo esc_html( strip_tags($atts->labelName) ); ?></label>
					<input type="text" name="user-name" placeholder="<?php echo esc_html( strip_tags($atts->labelName) ); ?>">
					<?php if($atts->helpName): ?>
						<div class="input-help"><?php echo wp_kses_post( $atts->helpName ); ?></div>
					<?php endif; ?>
				</div>

				<div class="input-container email">
					<label><?php echo esc_html( strip_tags($atts->labelEmail) ); ?></label>
					<input type="text" name="user-email" placeholder="<?php echo esc_html( strip_tags($atts->labelEmail) ); ?>">
					<?php if($atts->helpEmail): ?>
						<div class="input-help"><?php echo wp_kses_post( $atts->helpEmail ); ?></div>
					<?php endif; ?>
				</div>

				<div class="input-container subject">
					<label><?php echo esc_html( strip_tags($atts->labelSubject) ); ?></label>
					<input type="text" name="user-subject" placeholder="<?php echo esc_html( strip_tags($atts->labelSubject) ); ?>">
					<?php if($atts->helpSubject): ?>
						<div class="input-help"><?php echo wp_kses_post( $atts->helpSubject ); ?></div>
					<?php endif; ?>
				</div>
			</div>

			<div class="data-type-2">
				<div class="input-container message">
					<label><?php echo esc_html( strip_tags($atts->labelMessage) ); ?></label>
					<textarea type="text" name="user-message" placeholder="<?php echo esc_html( strip_tags($atts->labelMessage) ); ?>"></textarea>
					<?php if($atts->helpMessage): ?>
						<div class="input-help"><?php echo wp_kses_post( $atts->helpMessage ); ?></div>
					<?php endif; ?>
				</div>

				<div class="input-container sf-button">
					<button class="item-detail-submit-form" type="submit"><?php echo esc_html( strip_tags($atts->labelSendButton) ); ?></button>
					<i class="fa fa-sync fa-spin" style="display: none;"></i>
				</div>
			</div>

			<div class="data-messages">
				<div class="msg msg-success" style="display: none;">
					<p><?php echo esc_html( strip_tags($atts->notificationSuccess) ); ?></p>
				</div>
				<div class="msg msg-error-user" style="display: none;">
					<p><?php echo esc_html( strip_tags($atts->notificationValidationError) ); ?></p>
				</div>
				<div class="msg msg-error-server" style="display: none;">
					<p><?php echo esc_html( strip_tags($atts->notificationServerError) ); ?></p>
				</div>
			</div>

		</form>
	</div>


</div>

<?php endif; ?>