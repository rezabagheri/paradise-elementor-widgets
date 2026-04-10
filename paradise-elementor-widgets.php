<?php
/**
 * Plugin Name:       Paradise Elementor Widgets
 * Plugin URI:        https://www.paradisecyber.com/elementor-widgets
 * Description:       Advanced custom Elementor widgets by Paradise. Phone Link, Bottom Navigation Bar, and more.
 * Version:           2.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Reza Bagheri
 * Author URI:        https://www.paradisecyber.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       paradise-elementor-widgets
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PARADISE_EW_VERSION', '2.1.0' );
define( 'PARADISE_EW_DIR',     plugin_dir_path( __FILE__ ) );
define( 'PARADISE_EW_URL',     plugin_dir_url( __FILE__ ) );

final class Paradise_Elementor_Widgets {

    private static $instance = null;

    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'elementor/init', [ $this, 'init' ] );
    }

    public function init(): void {
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
        add_action( 'elementor/widgets/register',               [ $this, 'register_widgets' ] );
        add_action( 'elementor/frontend/after_enqueue_styles',  [ $this, 'enqueue_assets' ] );
        add_action( 'elementor/editor/after_enqueue_styles',    [ $this, 'enqueue_assets' ] );
    }

    public function register_category( $elements_manager ): void {
        $elements_manager->add_category( 'paradise', [
            'title' => 'Paradise Widgets',
            'icon'  => 'fa fa-plug',
        ] );
    }

    public function enqueue_assets(): void {
        wp_register_style(
            'paradise-phone-link',
            PARADISE_EW_URL . 'assets/css/phone-link.css',
            [],
            PARADISE_EW_VERSION
        );

        // ebn-style — handle name preserved for backward compatibility
        wp_register_style(
            'ebn-style',
            PARADISE_EW_URL . 'assets/css/bottom-nav.css',
            [],
            PARADISE_EW_VERSION
        );

        // ebn-script — handle name preserved for backward compatibility
        wp_register_script(
            'ebn-script',
            PARADISE_EW_URL . 'assets/js/bottom-nav.js',
            [],
            PARADISE_EW_VERSION,
            true
        );
    }

    public function register_widgets( $widgets_manager ): void {

        // Phone Link
        require_once PARADISE_EW_DIR . 'widgets/class-paradise-phone-link.php';
        $widgets_manager->register( new Paradise_Phone_Link_Widget() );

        // Bottom Navigation Bar
        require_once PARADISE_EW_DIR . 'widgets/class-paradise-bottom-nav.php';
        $widgets_manager->register( new Paradise_Bottom_Nav_Widget() );

        // Add future widgets below:
        // 1. Create: widgets/class-paradise-{name}.php
        // 2. require_once PARADISE_EW_DIR . 'widgets/class-paradise-{name}.php';
        // 3. $widgets_manager->register( new Paradise_{Name}_Widget() );
    }
}

Paradise_Elementor_Widgets::get_instance();
