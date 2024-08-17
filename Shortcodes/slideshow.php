<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CustomImageSlideshow_Shortcodes {
    public static function render_slideshow($atts): string {
        $atts = shortcode_atts(['id' => ''], $atts);
        $slideshows = get_option('custom_slideshows', []);

        if (!isset($slideshows[$atts['id']])) {
            return '';
        }

        $slideshow = $slideshows[$atts['id']];
        
        $output = '<div class="custom-slideshow-container">';
        $output .= '<div class="custom-slideshow" data-type="' . esc_attr($slideshow['type']) . '">';

        foreach ($slideshow['images'] as $index => $image_id) {
            $image_meta = wp_get_attachment_metadata($image_id);
            $is_horizontal = $image_meta['width'] > $image_meta['height'];
            if($is_horizontal) {
                $image_meta['width'] = $image_meta['width'] / 1.5;
                $image_meta['height'] = $image_meta['height'] / 1.5;
            }
            
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            
            $output .= '<div class="slide-item ' . ($is_horizontal ? 'horizontal' : 'vertical') . '">';
            $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" ';
            $output .= 'data-original-width="' . esc_attr($image_meta['width']) . '" ';
            $output .= 'data-original-height="' . esc_attr($image_meta['height']) . '" ';
            $output .= 'data-is-horizontal="' . ($is_horizontal ? 'true' : 'false') . '">';
            $output .= '</div>';
        }

        $output .= '</div></div>';
        return $output;
    }
}
