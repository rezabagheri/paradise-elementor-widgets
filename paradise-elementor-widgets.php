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
        add_action('plugins_loaded', [ $this, 'load_admin' ]);
        add_action('elementor/init', [ $this, 'init' ]);
    }

    public function load_admin(): void
    {
        require_once PARADISE_EW_DIR . 'admin/class-paradise-ew-admin.php';
        Paradise_EW_Admin::init();

        require_once PARADISE_EW_DIR . 'admin/class-paradise-user-profile.php';
        Paradise_User_Profile::init();
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
    }

    public function register_widgets($widgets_manager): void
    {

        // Phone Link
        if (Paradise_EW_Admin::widget_enabled('phone_link')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-phone-link.php';
            $widgets_manager->register(new Paradise_Phone_Link_Widget());
        }

        // Bottom Navigation Bar
        if (Paradise_EW_Admin::widget_enabled('bottom_nav')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-bottom-nav.php';
            $widgets_manager->register(new Paradise_Bottom_Nav_Widget());
        }

        // Author Card
        if (Paradise_EW_Admin::widget_enabled('author_card')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-author-card.php';
            $widgets_manager->register(new Paradise_Author_Card_Widget());
        }

        // Phone Button
        if (Paradise_EW_Admin::widget_enabled('phone_button')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-phone-button.php';
            $widgets_manager->register(new Paradise_Phone_Button_Widget());
        }

        // Floating Call Button
        if (Paradise_EW_Admin::widget_enabled('floating_call_btn')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-floating-call-btn.php';
            $widgets_manager->register(new Paradise_Floating_Call_Btn_Widget());
        }

        // Announcement Bar
        if (Paradise_EW_Admin::widget_enabled('announcement_bar')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-announcement-bar.php';
            $widgets_manager->register(new Paradise_Announcement_Bar_Widget());
        }

        // Cookie Consent Bar
        if (Paradise_EW_Admin::widget_enabled('cookie_consent_bar')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-cookie-consent-bar.php';
            $widgets_manager->register(new Paradise_Cookie_Consent_Bar_Widget());
        }

        // Back to Top
        if (Paradise_EW_Admin::widget_enabled('back_to_top')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-back-to-top.php';
            $widgets_manager->register(new Paradise_Back_To_Top_Widget());
        }

        // Off-Canvas Menu
        if (Paradise_EW_Admin::widget_enabled('off_canvas_menu')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-off-canvas-menu.php';
            $widgets_manager->register(new Paradise_Off_Canvas_Menu_Widget());
        }

        // Sticky Header
        if (Paradise_EW_Admin::widget_enabled('sticky_header')) {
            require_once PARADISE_EW_DIR . 'widgets/class-paradise-sticky-header.php';
            $widgets_manager->register(new Paradise_Sticky_Header_Widget());
        }

        // Add future widgets below:
        // 1. Create: widgets/class-paradise-{name}.php
        // 2. Wrap in: if ( Paradise_EW_Admin::widget_enabled( '{key}' ) ) { ... }
        // 3. Add the key+label to Paradise_EW_Admin::$widget_registry
    }
}

Paradise_Elementor_Widgets::get_instance();
