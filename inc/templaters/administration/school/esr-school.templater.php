<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Templater_School
{

	public static function print_page()
	{

		?>
		<div class="wrap esr-settings">
			<h1 class="wp-heading-inline"><?php echo __('Easy School Registration', 'easy-school-registration') . ' ' . __('(ESR)', 'easy-school-registration'); ?></h1>
			<h2><?php _e('All-in-One Registration Management Tool', 'easy-school-registration'); ?></h2>

			<p><?php _e('ESR is here to help you run your school effectively and with minimum time.', 'easy-school-registration'); ?></p>

			<p><?php echo sprintf(__('How to get started? Follow the <strong><a href="%s" target="_blank">Quick-Start Guide</a></strong> to get up and running in no time!', 'easy-school-registration'), 'https://easyschoolregistration.com/docs/general/quick-start-guide/'); ?></p>

			<p><?php _e('ESR provides a variety of additional modules to make your life even easier. Discounts, attendance tracking, promo codes, and much more - all available for free with your valid ESR license.', 'easy-school-registration'); ?></p>

			<p><?php echo sprintf(__('Visit our <strong><a href="%s" target="_blank">website</a></strong> or get in touch with our team to learn more!', 'easy-school-registration'), 'https://easyschoolregistration.com'); ?></p>
			<br>
			<p><?php _e('Questions or ideas? Need help with the setup?', 'easy-school-registration'); ?></p>

			<p><?php echo sprintf(__('Check the <strong><a href="%s" target="_blank">Documentation</a></strong> or let us know via <strong><a href="%s" target="_blank">Contact form</a></strong>.', 'easy-school-registration'), 'https://easyschoolregistration.com/support/documentation/', 'https://easyschoolregistration.com/contact/'); ?></p>

			<p class="esr-socials">
				<a href="https://www.facebook.com/easyschoolregistration/" target="_blank"><span class="icon-facebook"></span></a>
				<a href="https://twitter.com/esrwp" target="_blank"><span class="icon-twitter"></span></a>
				<a href="https://www.youtube.com/channel/UC1Z1iogssQy7FXzlCUmCqpA" target="_blank"><span class="icon-youtube"></span></a>
			</p>
		</div>
		<?php
	}

}
