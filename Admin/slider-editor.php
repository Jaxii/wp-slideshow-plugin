<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CustomImageSlideshow_Admin {
    public static function add_admin_menu(): void {
        add_menu_page(
            'Custom Slideshow',
            'Custom Slideshow',
            'manage_options',
            'custom-slideshow',
            [self::class, 'admin_page'],
            'dashicons-images-alt2'
        );
    }

    public static function admin_page(): void {
        $slideshows = get_option('custom_slideshows', []);

        // Handle delete action
        if (isset($_GET['delete']) && isset($slideshows[$_GET['delete']])) {
            unset($slideshows[$_GET['delete']]);
            update_option('custom_slideshows', $slideshows);
            echo '<div class="updated"><p>Slideshow deleted successfully.</p></div>';
        }

        // Handle edit or create new slideshow
        $editing = false;
        $current_slideshow = [
            'name' => '',
            'images' => [],
            'sizes' => [],
            'type' => 'default'
        ];

        if (isset($_GET['edit']) && isset($slideshows[$_GET['edit']])) {
            $editing = true;
            $current_slideshow = $slideshows[$_GET['edit']];
        }

        if (isset($_POST['submit_slideshow'])) {
            self::save_slideshow($editing ? $_GET['edit'] : null);
            $slideshows = get_option('custom_slideshows', []); // Refresh slideshows after saving
        }

        ?>
        <div class="wrap">
            <h1>Custom Image Slideshow</h1>
            <form method="post" action="">
                <h2><?php echo $editing ? 'Edit' : 'Create New'; ?> Slideshow</h2>
                <p>
                    <label for="slideshow_name">Slideshow Name:</label>
                    <input type="text" name="slideshow_name" id="slideshow_name" value="<?php echo esc_attr($current_slideshow['name']); ?>" required>
                </p>
                <p>
                    <label for="slider_type">Slider Type:</label>
                    <select name="slider_type" id="slider_type">
                        <option value="default" <?php selected($current_slideshow['type'], 'default'); ?>>Default</option>
                        <option value="carousel" <?php selected($current_slideshow['type'], 'carousel'); ?>>Carousel</option>
                    </select>
                </p>
                <div id="image_container">
                    <?php foreach ($current_slideshow['images'] as $index => $image_id): ?>
                        <div class="image-row">
                            <input type="hidden" name="image_ids[]" class="image-id" value="<?php echo esc_attr($image_id); ?>">
                            <input type="text" name="image_sizes[]" placeholder="Image size (e.g., 300x200)" value="<?php echo esc_attr($current_slideshow['sizes'][$index] ?? ''); ?>">
                            <span class="preview-image">
                                <?php echo wp_get_attachment_image($image_id, [100, 100]); ?>
                            </span>
                            <button class="remove-image button">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p>
                    <input type="button" id="upload_images" class="button" value="Upload Images">
                    <input type="submit" name="submit_slideshow" class="button button-primary" value="<?php echo $editing ? 'Update' : 'Save'; ?> Slideshow">
                </p>
            </form>

            <h2>Existing Slideshows</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Shortcode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slideshows as $id => $slideshow): ?>
                        <tr>
                            <td><?php echo esc_html($slideshow['name']); ?></td>
                            <td><code>[custom_slideshow id="<?php echo esc_attr($id); ?>"]</code></td>
                            <td>
                                <a href="?page=custom-slideshow&edit=<?php echo esc_attr($id); ?>">Edit</a> |
                                <a href="?page=custom-slideshow&delete=<?php echo esc_attr($id); ?>" onclick="return confirm('Are you sure you want to delete this slideshow?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function enqueue_admin_scripts($hook): void {
        if ($hook !== 'toplevel_page_custom-slideshow') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('custom-slideshow-admin', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['jquery'], '1.0', true);
    }

    private static function save_slideshow($id = null): void {
        if (!isset($_POST['slideshow_name'], $_POST['image_ids'], $_POST['image_sizes'], $_POST['slider_type'])) {
            return;
        }

        $slideshows = get_option('custom_slideshows', []);
        
        $slideshow = [
            'name' => sanitize_text_field($_POST['slideshow_name']),
            'images' => array_map('intval', $_POST['image_ids']),
            'sizes' => array_map(function($size) {
                return $size ? sanitize_text_field($size) : 'full';
            }, $_POST['image_sizes']),
            'type' => sanitize_text_field($_POST['slider_type']),
        ];

        if ($id === null) {
            $id = uniqid('slideshow_');
        }

        $slideshows[$id] = $slideshow;
        update_option('custom_slideshows', $slideshows);

        echo '<div class="updated"><p>Slideshow ' . ($id ? 'updated' : 'created') . ' successfully.</p></div>';
    }
}
