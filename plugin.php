<?php
/**
 * Plugin Name: Custom Image Slideshow
 * Description: Create image slideshows using shortcodes with customizable options.
 * Version: 1.0
 * Author: Jaxii @ Github
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . './Admin/slider-editor.php';
require_once plugin_dir_path(__FILE__) . './Shortcodes/slideshow.php';

class CustomImageSlideshow {
    public function __construct() {
        add_action('admin_menu', ['CustomImageSlideshow_Admin', 'add_admin_menu']);
        add_action('admin_enqueue_scripts', ['CustomImageSlideshow_Admin', 'enqueue_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_shortcode('custom_slideshow', ['CustomImageSlideshow_Shortcodes', 'render_slideshow']);
    }

    public function enqueue_frontend_scripts(): void {
        wp_enqueue_style('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        wp_enqueue_style('custom-slideshow-frontend', plugin_dir_url(__FILE__) . 'assets/css/frontend.css');
        wp_enqueue_script('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);
        wp_enqueue_script('custom-slideshow-frontend', plugin_dir_url(__FILE__) . 'assets/js/frontend.js', ['jquery', 'slick'], '1.0', true);
    }
}

new CustomImageSlideshow();
