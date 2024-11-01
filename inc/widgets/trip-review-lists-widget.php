<?php

/**
 * This file is used to fetch the trip review form.
 */

namespace WTWE\Widgets\Single_Page_Trip_Review_List;

use Elementor\Widget_Base;
use WTWE\Helper\WTWE_Helper;

defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Review_List')) {
    class WTWE_Trip_Review_List extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }

        public function get_name()
        {
            return 'wp-travel-trip-review-list';
        }

        public function get_title()
        {
            return esc_html__('Trip Review Lists', 'wt-widgets-elementor');
        }

        public function get_icon()
        {
            return 'eicon-review';
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
            $settings = wptravel_get_settings();

            if (!empty($trip_id) && get_post_type($trip_id) == 'itineraries') {
?>
                <div id="wptravel-trip-review-lists" class="wp-tab-review-inner-wrapper">

                    <?php
                    $args              = array(
                        'post_id' => get_the_ID()
                    );
                    $the_query = new \WP_Comment_Query($args);

                    if (count($the_query->comments) > 0) :
                    ?>
                    <style>
                        @media screen and (max-width: 600px ) {
                            .elementor-widget-wp-travel-trip-review-list .comment-text .description { 
                            word-break: break-word;
                        }
                        .elementor-widget-wp-travel-trip-review-list .comment-text { 
                            word-break: break-word;
                        }
                        }
                        
                    </style>
                        <ol class="commentlist">
                            <?php foreach ($the_query->comments as $comment) {
                                $rating = intval(get_comment_meta($comment->comment_ID, '_wp_travel_rating', true));
                            ?>

                                <li id="li-comment-<?php echo esc_attr($comment->comment_ID); ?>">

                                    <div id="comment-<?php echo esc_attr($comment->comment_ID); ?>" class="comment_container">

                                        <?php echo get_avatar($comment, apply_filters('wp_travel_review_gravatar_size', '60'), ''); ?>

                                        <div class="comment-text">
                                            <!-- since 6.2 -->
                                            <?php
                                            if ($settings['disable_admin_review'] == 'yes') :

                                                if (get_user_by('login', $comment->comment_author)) {
                                                    if (in_array(get_user_by('login', $comment->comment_author)->roles[0], array('administrator', 'editor', 'author'))) {
                                            ?>
                                                        <div class="wp-travel-admin-review">
                                                            <?php echo esc_html__('Admin', 'wt-widgets-elementor'); ?>
                                                        </div>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <div class="wp-travel-average-review" title="<?php 
                                                        /* translators: %d represents the rating number */ 
                                                        echo sprintf(__('Rated %d out of 5', 'wt-widgets-elementor'), esc_attr( $rating ) ); ?>">
                                                            <a>
                                                                <span style="width:<?php echo esc_attr(($rating / 5) * 100); ?>%"><strong><?php echo esc_attr( $rating ); ?></strong> <?php echo esc_html__('out of 5', 'wt-widgets-elementor'); ?></span>
                                                            </a>
                                                        </div>
                                                    <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="wp-travel-average-review" title="<?php
                                                    /* translators: %d represents the rating number */
                                                    echo sprintf(__('Rated %d out of 5', 'wt-widgets-elementor'), esc_attr( $rating) ); ?>">
                                                        <a>
                                                            <span style="width:<?php echo esc_attr(($rating / 5) * 100); ?>%"><strong><?php echo esc_html( $rating ); ?></strong> <?php echo esc_html__('out of 5', 'wt-widgets-elementor'); ?></span>
                                                        </a>
                                                    </div>
                                                <?php    } ?>

                                            <?php else : ?>
                                                <div class="wp-travel-average-review" title="<?php
                                                    /* translators: %d represents the rating number */
                                                    echo sprintf(__('Rated %d out of 5', 'wt-widgets-elementor'), esc_attr( $rating) ); ?>">
                                                    <a>
                                                        <span style="width:<?php echo esc_attr(($rating / 5) * 100); ?>%"><strong><?php echo esc_html( $rating ); ?></strong> <?php echo esc_html__('out of 5', 'wt-widgets-elementor'); ?></span>
                                                    </a>
                                                </div>
                                            <?php endif ?>

                                            <?php do_action('wp_travel_review_before_comment_meta', $comment); ?>

                                            <?php if ($comment->comment_approved == '0') : ?>

                                                <p class="meta"><em><?php echo esc_html__( 'Your comment is awaiting approval', 'wt-widgets-elementor'); ?></em></p>

                                            <?php else : ?>

                                                <p class="meta">
                                                    <strong><?php echo apply_Filters('wp_travel_single_archive_comment_author', $comment->comment_author); ?></strong>&ndash; <time datetime="<?php echo apply_filters('wp_travel_single_archive_comment_date', get_comment_date('c', $comment->comment_ID)); ?>"><?php echo apply_filters('wp_travel_single_archive_comment_date_format', get_comment_date(get_option('date_format'), $comment->comment_ID)); ?></time>
                                                </p>

                                            <?php endif; ?>

                                            <?php do_action('wp_travel_review_before_comment_text', $comment); ?>

                                            <div class="description"><?php echo apply_filters('wp_travel_single_archive_comment', $comment->comment_content); ?></div>
                                            <div class="reply">
                                                <?php
                                                do_action('wp_travel_single_archive_after_comment_text', $comment, $rating);
                                                // Reply Link.
                                                $post_id = get_the_ID();
                                                if (!comments_open(get_the_ID())) {
                                                    return;
                                                }
                                                global $user_ID;
                                                $login_text = __('please login to review', 'wt-widgets-elementor');
                                                $link       = '';
                                                if (get_option('comment_registration') && !$user_ID) {
                                                    $link = '<a rel="nofollow" href="' . wp_login_url(get_permalink()) . '">' . $login_text . '</a>';
                                                } else {

                                                    $link = "<a class='comment-reply-link' href='" . esc_url(add_query_arg('replytocom', $comment->comment_ID)) . '#respond' . "' onclick='return addComment.moveForm(\"comment-$comment->comment_ID\", \"$comment->comment_ID\", \"respond\", \"$post_id\")'>" . esc_html('Reply', 'wt-widgets-elementor') . '</a>';
                                                }
                                                echo apply_filters('wp_travel_comment_reply_link', $link);
                                                ?>
                                            </div>
                                            <?php do_action('wp_travel_review_after_comment_text', $comment); ?>

                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ol>

                        <?php
                        if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                            echo '<nav class="wp-travel-pagination">';
                            paginate_comments_links(
                                apply_filters(
                                    'wp_travel_comment_pagination_args',
                                    array(
                                        'prev_text' => '&larr;',
                                        'next_text' => '&rarr;',
                                        'type'      => 'list',
                                    )
                                )
                            );
                            echo '</nav>';
                        endif;
                        ?>

                    <?php else : ?>

                        <span class="wp-travel-noreviews">
                            <?php echo esc_html__( 'There are no reviews yet.', 'wt-widgets-elementor'); ?>
                        </span>

                    <?php endif; ?>
                </div>
            <?php
            } else {
                WTWE_Helper::wtwe_get_widget_notice( __( 'This widget is only visible on the frontend for itineraries.','wt-widgets-elementor' ), 'info');
            }
        }
        protected function content_template()
        {
            $elementor_placeholder_image = plugins_url( 'assets/images/elementor-placeholder-image.png', WTWE_PLUGIN_FILE );
            ?>
             <div class="wtwe-trip-review-list">
				<h5 class="wtwe-trip-review-list"><?php echo esc_html__( 'Trip Review List', 'wt-widgets-elementor' ); ?></h5>
			</div>
            <div class="elementor-widget-container">
                <div id="wptravel-trip-review-lists" class="wp-tab-review-inner-wrapper">
                    <ol class="commentlist">
                        <li id="li-comment-3">
                            <div id="comment-3" class="comment_container">
                                <img alt="Elementor Placeholder Image" src="<?php echo esc_url( $elementor_placeholder_image );?>" class="avatar avatar-60 photo" height="60" width="60" decoding="async">
                                <div class="comment-text">
                                    <div class="wp-travel-average-review" title="Rated 4 out of 5">
                                        <a>
                                            <span style="width:80%"><strong><?php echo esc_html__( '4', 'wt-widgets-elementor' ); ?></strong><?php echo esc_html__( 'out of 5', 'wt-widgets-elementor' ); ?> </span>
                                        </a>
                                    </div>
                                    <p class="meta">
                                        <strong><?php echo esc_html__( 'root', 'wt-widgets-elementor' ); ?></strong>– <time datetime="2024-04-25T07:32:45+00:00"><?php echo esc_html__( 'April 25, 2024', 'wt-widgets-elementor' ); ?></time>
                                    </p>
                                    <div class="description"><?php echo esc_html__( 'Review Content', 'wt-widgets-elementor' ); ?></div>
                                    <div class="reply">
                                        <a class="comment-reply-link"><?php echo esc_html__( 'Reply', 'wt-widgets-elementor' ); ?></a>
                                    </div>

                                </div>
                            </div>
                        </li>
                    </ol>


                </div>
            </div>


<?php
        }
    }
}
