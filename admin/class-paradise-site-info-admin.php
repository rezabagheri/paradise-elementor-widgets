<?php
/**
 * Paradise Site Info — Admin
 *
 * Registers a "Site Info" submenu under the Paradise admin menu
 * and handles saving phone numbers, emails, addresses, and social links.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Paradise_Site_Info_Admin {

    const PAGE_SLUG = 'paradise-site-info';
    const NONCE     = 'paradise_site_info_save';

    public static function init(): void {
        add_action( 'admin_menu',                  [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_post_paradise_save_site_info', [ __CLASS__, 'handle_save' ] );
        add_action( 'admin_enqueue_scripts',       [ __CLASS__, 'enqueue_assets' ] );
    }

    // ── Menu ──────────────────────────────────────────────────────────────────

    public static function register_menu(): void {
        add_submenu_page(
            'paradise-widgets',
            esc_html__( 'Site Info', 'paradise-elementor-widgets' ),
            esc_html__( 'Site Info', 'paradise-elementor-widgets' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_page' ]
        );
    }

    // ── Assets ────────────────────────────────────────────────────────────────

    public static function enqueue_assets( string $hook ): void {
        if ( strpos( $hook, self::PAGE_SLUG ) === false ) {
            return;
        }

        wp_enqueue_style(
            'paradise-site-info-admin',
            PARADISE_EW_URL . 'assets/css/site-info-admin.css',
            [],
            PARADISE_EW_VERSION
        );

        wp_enqueue_script(
            'paradise-site-info-admin',
            PARADISE_EW_URL . 'assets/js/site-info-admin.js',
            [],
            PARADISE_EW_VERSION,
            true
        );
    }

    // ── Save handler ──────────────────────────────────────────────────────────

    public static function handle_save(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized', 'paradise-elementor-widgets' ) );
        }

        check_admin_referer( self::NONCE );

        $raw = isset( $_POST['paradise_site_info'] ) && is_array( $_POST['paradise_site_info'] )
            ? wp_unslash( $_POST['paradise_site_info'] )
            : [];

        Paradise_Site_Info::save( $raw );

        wp_safe_redirect( add_query_arg( 'saved', '1',
            admin_url( 'admin.php?page=' . self::PAGE_SLUG )
        ) );
        exit;
    }

    // ── Page render ───────────────────────────────────────────────────────────

    public static function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        require_once PARADISE_EW_DIR . 'admin/views/page-site-info.php';
    }
}
