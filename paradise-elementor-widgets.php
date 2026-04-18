<?php

/**
 * Plugin Name:       Paradise Elementor Widgets
 * Plugin URI:        https://www.paradisecyber.com/elementor-widgets
 * Description:       Advanced custom Elementor widgets by Paradise. Phone Link, Bottom Navigation Bar, and more.
 * Version:           2.2.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            Reza Bagheri
 * Author URI:        https://www.paradisecyber.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       paradise-elementor-widgets
 * Domain Path:       /languages
 */

if (! defined('ABSPATH')) {
    exit;
}

define('PARADISE_EW_VERSION', '2.2.0');
define('PARADISE_EW_DIR', plugin_dir_path(__FILE__));
define('PARADISE_EW_URL', plugin_dir_url(__FILE__));

final class Paradise_Elementor_Widgets
{
    private static $instance = null;

    public static function get_instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('plugins_loaded', [ $this, 'load_textdomain' ]);
        add_action('plugins_loaded', [ $this, 'load_site_info' ]);
        add_action('plugins_loaded', [ $this, 'load_admin' ]);
        add_action('elementor/init', [ $this, 'init' ]);
    }

    public function load_site_info(): void
    {
        require_once PARADISE_EW_DIR . 'includes/class-paradise-site-info.php';
        add_action('init', [ 'Paradise_Site_Info', 'register_shortcode' ]);
    }

    public function load_admin(): void
    {
        require_once PARADISE_EW_DIR . 'admin/class-paradise-ew-admin.php';
        Paradise_EW_Admin::init();

        require_once PARADISE_EW_DIR . 'admin/class-paradise-user-profile.php';
        Paradise_User_Profile::init();

        require_once PARADISE_EW_DIR . 'admin/class-paradise-site-info-admin.php';
        Paradise_Site_Info_Admin::init();
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain(
            'paradise-elementor-widgets',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    public function init(): void
    {
        add_action('elementor/elements/categories_registered', [ $this, 'register_category' ]);
        add_action('elementor/widgets/register', [ $this, 'register_widgets' ]);
        add_action('elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_assets' ]);
        add_action('elementor/editor/after_enqueue_styles', [ $this, 'enqueue_assets' ]);
        add_action('elementor/dynamic_tags/register', [ $this, 'register_dynamic_tags' ]);
    }

    public function register_dynamic_tags( $dynamic_tags_manager ): void
    {
        require_once PARADISE_EW_DIR . 'includes/class-paradise-dynamic-tags.php';
        Paradise_Dynamic_Tags::register( $dynamic_tags_manager );
    }

    public function register_category($elements_manager): void
    {
        $elements_manager->add_category('paradise', [
            'title' => esc_html__('Paradise Widgets', 'paradise-elementor-widgets'),
            'icon'  => 'fa fa-plug',
        ]);
    }

    public function enqueue_assets(): void
    {
        wp_register_style(
            'paradise-phone-link',
            PARADISE_EW_URL . 'assets/css/phone-link.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_style(
            'paradise-author-card',
            PARADISE_EW_URL . 'assets/css/author-card.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_style(
            'paradise-phone-button',
            PARADISE_EW_URL . 'assets/css/phone-button.css',
            [],
            PARADISE_EW_VERSION
        );

        // paradise-bottom-nav-style — updated for consistency
        wp_register_style(
            'paradise-bottom-nav-style',
            PARADISE_EW_URL . 'assets/css/bottom-nav.css',
            [],
            PARADISE_EW_VERSION
        );

        // paradise-bottom-nav-script — updated for consistency
        wp_register_script(
            'paradise-bottom-nav-script',
            PARADISE_EW_URL . 'assets/js/bottom-nav.js',
            [],
            PARADISE_EW_VERSION,
            true
        );

        wp_register_style(
            'paradise-floating-call-btn',
            PARADISE_EW_URL . 'assets/css/floating-call-btn.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_style(
            'paradise-announcement-bar',
            PARADISE_EW_URL . 'assets/css/announcement-bar.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_script(
            'paradise-announcement-bar',
            PARADISE_EW_URL . 'assets/js/announcement-bar.js',
            [],
            PARADISE_EW_VERSION,
            true
        );

        wp_register_style(
            'paradise-cookie-consent-bar',
            PARADISE_EW_URL . 'assets/css/cookie-consent-bar.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_script(
            'paradise-cookie-consent-bar',
            PARADISE_EW_URL . 'assets/js/cookie-consent-bar.js',
            [],
            PARADISE_EW_VERSION,
            true
        );

        wp_register_style(
            'paradise-back-to-top',
            PARADISE_EW_URL . 'assets/css/back-to-top.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_script(
            'paradise-back-to-top',
            PARADISE_EW_URL . 'assets/js/back-to-top.js',
            [],
            PARADISE_EW_VERSION,
            true
        );

        wp_register_style(
            'paradise-off-canvas-menu',
            PARADISE_EW_URL . 'assets/css/off-canvas-menu.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_script(
            'paradise-off-canvas-menu',
            PARADISE_EW_URL . 'assets/js/off-canvas-menu.js',
            [],
            PARADISE_EW_VERSION,
            true
        );

        wp_register_style(
            'paradise-sticky-header',
            PARADISE_EW_URL . 'assets/css/sticky-header.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_script(
            'paradise-sticky-header',
            PARADISE_EW_URL . 'assets/js/sticky-header.js',
            [],
            PARADISE_EW_VERSION,
            true
        );

        wp_register_style(
            'paradise-google-map',
            PARADISE_EW_URL . 'assets/css/google-map.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_register_style(
            'paradise-social-links',
            PARADISE_EW_URL . 'assets/css/social-links.css',
            [],
            PARADISE_EW_VERSION
        );
    }

    public function register_widgets($widgets_manager): void
    {
        foreach (Paradise_EW_Admin::get_widget_registry() as $key => $config) {
            if (!Paradise_EW_Admin::widget_enabled($key)) {
                continue;
            }
            require_once PARADISE_EW_DIR . $config['file'];
            $widgets_manager->register(new $config['class']());
        }

        // To add a new widget:
        // 1. Create: widgets/class-paradise-{name}.php
        // 2. Add one entry to Paradise_EW_Admin::$widget_registry with file + class keys
    }
}

Paradise_Elementor_Widgets::get_instance();
