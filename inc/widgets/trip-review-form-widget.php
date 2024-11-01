<?php

/**
 * This file is used to fetch the trip review form.
 */

namespace WTWE\Widgets\Single_Page_Trip_Review_Form;

use Elementor\Widget_Base;
use WTWE\Helper\WTWE_Helper;

defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Review_Form')) {
	class WTWE_Trip_Review_Form extends Widget_Base
	{
		public function __construct($data = [], $args = [])
		{
			parent::__construct($data, $args);
		}

		public function get_name()
		{
			return 'wp-travel-trip-review-form';
		}

		public function get_title()
		{
			return esc_html__('Trip Review Form', 'wt-widgets-elementor');
		}

		public function get_icon()
		{
			return 'eicon-form-horizontal';
		}

		public function get_categories()
		{
			return ['wp-travel-single'];
		}

		protected function render()
		{
			if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				return;
			}
			global $post;

			$trip_id = get_the_ID();
			$args = ['post_id' => $trip_id];
			$the_query = new \WP_Comment_Query($args);

			if (!empty($trip_id) && get_post_type($trip_id) == 'itineraries') {
?>
				<div id="review_form">
					<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						/* translators: %s represents the add a review string */
						'title_reply'          => count($the_query->comments) > 0 ? esc_html__( 'Add a review', 'wt-widgets-elementor') : sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'wt-widgets-elementor'), get_the_title()),
						/* translators: %s represents the leave a reply string */
						'title_reply_to'       => esc_html__('Leave a Reply to %s', 'wt-widgets-elementor'),
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'wt-widgets-elementor') . ' <span class="required">*</span></label> ' .
								'<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" /></p>',
							'email'  => '<p class="comment-form-email"><label for="email">' . __('Email', 'wt-widgets-elementor') . ' <span class="required">*</span></label> ' .
								'<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" /></p>',
						),
						'label_submit'         => esc_html__( 'Submit', 'wt-widgets-elementor'),
						'logged_in_as'         => '',
						'comment_field'        => '',
					);

					/* translators: %s represents the must log in  string */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(__('You must be <a href="%1s">logged in</a> to post a review.', 'wt-widgets-elementor'), esc_url(wp_login_url())) . '</p>';
					$settings                       = wptravel_get_settings();

					if (is_user_logged_in()) {
						global $current_user;

						if ($settings['disable_admin_review'] == 'no') {
							$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . esc_html__( 'Your rating', 'wt-widgets-elementor') . '</label><div id="wp-travel_rate" class="clearfix">
											<a href="#" class="rate_label far fa-star" data-id="1"></a>
											<a href="#" class="rate_label far fa-star" data-id="2"></a>
											<a href="#" class="rate_label far fa-star" data-id="3"></a>
											<a href="#" class="rate_label far fa-star" data-id="4"></a>
											<a href="#" class="rate_label far fa-star" data-id="5"></a>
										</div>
										<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

							$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'wt-widgets-elementor') . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
						} else {
							if (!in_array(get_user_by('login', $current_user->user_login)->roles[0], array('administrator', 'editor', 'author'))) {
								$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . esc_html__( 'Your rating', 'wt-widgets-elementor') . '</label><div id="wp-travel_rate" class="clearfix">
										<a href="#" class="rate_label far fa-star" data-id="1"></a>
										<a href="#" class="rate_label far fa-star" data-id="2"></a>
										<a href="#" class="rate_label far fa-star" data-id="3"></a>
										<a href="#" class="rate_label far fa-star" data-id="4"></a>
										<a href="#" class="rate_label far fa-star" data-id="5"></a>
									</div>
									<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

								$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'wt-widgets-elementor') . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
							} else {
								$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your reply', 'wt-widgets-elementor') . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
							}
						}
					} else {
						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . esc_html__( 'Your rating', 'wt-widgets-elementor') . '</label><div id="wp-travel_rate" class="clearfix">
										<a href="#" class="rate_label far fa-star" data-id="1"></a>
										<a href="#" class="rate_label far fa-star" data-id="2"></a>
										<a href="#" class="rate_label far fa-star" data-id="3"></a>
										<a href="#" class="rate_label far fa-star" data-id="4"></a>
										<a href="#" class="rate_label far fa-star" data-id="5"></a>
									</div>
									<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'wt-widgets-elementor') . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
					}

					apply_filters('wp_travel_single_archive_comment_form', comment_form(apply_filters('wp_travel_product_review_comment_form_args', $comment_form)));
					?>
				</div>
			<?php
			} else {
				WTWE_Helper::wtwe_get_widget_notice( __( 'This widget is only visible on the frontend for itineraries.', 'wt-widgets-elementor' ), 'info');
			}
		}

		protected function content_template()
		{
			?>
			 <div class="wtwe-trip-review-form">
				<h5 class="wtwe-trip-review-form"><?php echo esc_html__( 'Trip Review Form', 'wt-widgets-elementor' ); ?></h5>
			</div>
			<div id="review_form">
				<div id="respond" class="comment-respond">
					<form>
						<p class="comment-form-rating"><label for="wp_travel_rate_val"><?php echo esc_html__( 'Your rating', 'wt-widgets-elementor' ); ?></label></p>
						<div id="wp-travel_rate" class="clearfix">
							<a href="#" class="rate_label far fa-star" data-id="1"></a>
							<a href="#" class="rate_label far fa-star" data-id="2"></a>
							<a href="#" class="rate_label far fa-star" data-id="3"></a>
							<a href="#" class="rate_label far fa-star" data-id="4"></a>
							<a href="#" class="rate_label far fa-star" data-id="5"></a>
						</div>
						<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val">
						<p></p>
						<p class="comment-form-comment"><label for="comment"><?php echo esc_html__( 'Your review', 'wt-widgets-elementor' ); ?></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>
						<p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="Submit"> <input type="hidden" name="comment_post_ID" value="46" id="comment_post_ID">
							<input type="hidden" name="comment_parent" id="comment_parent" value="0">
						</p><input type="hidden" id="_wp_unfiltered_html_comment_disabled" name="_wp_unfiltered_html_comment_disabled" value="6a8d1b011e">
					</form>
				</div>
			</div>

<?php
		}
	}
}
