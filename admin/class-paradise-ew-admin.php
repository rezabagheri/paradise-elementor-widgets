<?php
/**
 * Paradise Elementor Widgets — Admin Settings
 *
 * Registers a top-level "Paradise" admin menu shared by all Paradise plugins,
 * and an "Elementor Widgets" submenu for this plugin's settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Paradise_EW_Admin {

    const OPTION_KEY = 'paradise_ew_settings';
    const MENU_SLUG  = 'paradise-widgets';

    /**
     * Widgets that can be toggled on/off.
     * 'key' must match the identifier used in register_widgets().
     */
    private static array $widget_registry = [
        'phone_link' => [
            'label'       => 'Phone Link',
            'description' => 'Clickable phone number with icon, prefix, and formatting options.',
        ],
        'bottom_nav' => [
            'label'       => 'Bottom Navigation Bar',
            'description' => 'Fixed mobile bottom bar with icons, labels, badges, and speed dial.',
        ],
        'author_card' => [
            'label'       => 'Author Card',
            'description' => 'Author profile card with photo, credentials, bio, custom fields, and CTA button.',
        ],
        'phone_button' => [
            'label'       => 'Phone Button',
            'description' => 'Fully-styled CTA button for phone calls or WhatsApp. Supports custom text, icon, colors, hover, and border radius.',
        ],
        'floating_call_btn' => [
            'label'       => 'Floating Call Button',
            'description' => 'Fixed-position call/WhatsApp button that stays visible while scrolling. Supports icon, optional label, pulse animation, and corner positioning.',
        ],
        'announcement_bar' => [
            'label'       => 'Announcement Bar',
            'description' => 'Fixed full-width banner for announcements, promotions, or alerts. Supports icon, message, CTA button, and dismissal with session/days/permanent memory.',
        ],
        'cookie_consent_bar' => [
            'label'       => 'Cookie Consent Bar',
            'description' => 'GDPR/cookie consent bar with Accept and Decline buttons. Stores user choice in localStorage with configurable expiry. Dispatches consent events for analytics integration.',
        ],
    ];

    /**
     * Plugin feature flags — each key defaults to the value in $feature_defaults.
     */
    private static array $feature_registry = [
        'show_profile_social' => [
            'label'       => 'Social Links fields on user profiles',
            'description' => 'Shows the Paradise Social Links section on the WordPress user profile edit page (wp-admin → Users → Edit). Disable if you manage social links elsewhere.',
        ],
    ];

    /** Default value for each feature when the option has never been saved. */
    private static array $feature_defaults = [
        'show_profile_social' => true,
    ];

    public static function init(): void {
        add_action( 'admin_menu',    [ __CLASS__, 'register_menus' ] );
        add_action( 'admin_init',    [ __CLASS__, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
    }

    // -------------------------------------------------------------------------
    // Menus
    // -------------------------------------------------------------------------

    public static function register_menus(): void {
        // Top-level "Paradise" menu — only add it once (other Paradise plugins
        // will also call add_menu_page with the same slug; WordPress deduplicates).
        add_menu_page(
            esc_html__( 'Paradise', 'paradise-elementor-widgets' ),
            esc_html__( 'Paradise', 'paradise-elementor-widgets' ),
            'manage_options',
            self::MENU_SLUG,
            [ __CLASS__, 'render_settings_page' ],
            'data:image/svg+xml;base64,' . base64_encode( self::menu_icon_svg() ),
            58
        );

        // Submenu — "Elementor Widgets" under the Paradise parent
        add_submenu_page(
            self::MENU_SLUG,
            esc_html__( 'Elementor Widgets', 'paradise-elementor-widgets' ),
            esc_html__( 'Elementor Widgets', 'paradise-elementor-widgets' ),
            'manage_options',
            self::MENU_SLUG,         // same slug → this IS the top-level page
            [ __CLASS__, 'render_settings_page' ]
        );
    }

    // -------------------------------------------------------------------------
    // Settings API
    // -------------------------------------------------------------------------

    public static function register_settings(): void {
        register_setting(
            self::OPTION_KEY . '_group',
            self::OPTION_KEY,
            [ 'sanitize_callback' => [ __CLASS__, 'sanitize_settings' ] ]
        );
    }

    /**
     * Sanitize the entire settings array before it is saved to the database.
     * Any key not in the registries is discarded.
     */
    public static function sanitize_settings( mixed $input ): array {
        $clean = [];
        foreach ( array_keys( self::$widget_registry ) as $key ) {
            $clean['widgets'][ $key ] = ! empty( $input['widgets'][ $key ] );
        }
        foreach ( array_keys( self::$feature_registry ) as $key ) {
            $clean['features'][ $key ] = ! empty( $input['features'][ $key ] );
        }
        return $clean;
    }

    // -------------------------------------------------------------------------
    // Public helper — read settings
    // -------------------------------------------------------------------------

    /**
     * Returns the stored settings array with safe defaults.
     * All widgets are enabled by default; integrations are disabled by default.
     */
    public static function get(): array {
        $defaults = [
            'widgets'  => array_fill_keys( array_keys( self::$widget_registry ), true ),
            'features' => self::$feature_defaults,
        ];
        $stored = get_option( self::OPTION_KEY, [] );
        return array_replace_recursive( $defaults, (array) $stored );
    }

    /**
     * Returns true if a specific widget is enabled.
     */
    public static function widget_enabled( string $key ): bool {
        $settings = self::get();
        return $settings['widgets'][ $key ] ?? true;
    }

    /**
     * Returns true if a specific feature flag is enabled.
     */
    public static function feature_enabled( string $key ): bool {
        $settings = self::get();
        return $settings['features'][ $key ] ?? ( self::$feature_defaults[ $key ] ?? false );
    }

    // -------------------------------------------------------------------------
    // Assets
    // -------------------------------------------------------------------------

    public static function enqueue_admin_assets( string $hook ): void {
        // Only load on our settings page
        if ( 'toplevel_page_' . self::MENU_SLUG !== $hook ) {
            return;
        }
        wp_enqueue_style(
            'paradise-ew-admin',
            PARADISE_EW_URL . 'assets/css/admin.css',
            [],
            PARADISE_EW_VERSION
        );
    }

    // -------------------------------------------------------------------------
    // View
    // -------------------------------------------------------------------------

    public static function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'paradise-elementor-widgets' ) );
        }
        $settings = self::get();
        require PARADISE_EW_DIR . 'admin/views/page-settings.php';
    }

    // -------------------------------------------------------------------------
    // Menu icon (simple "P" monogram as inline SVG)
    // -------------------------------------------------------------------------

    private static function menu_icon_svg(): string {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a7aaad">'
             . '<text x="2" y="16" font-size="16" font-family="sans-serif" font-weight="700">P</text>'
             . '</svg>';
    }

    // -------------------------------------------------------------------------
    // Registry accessors (used by the view)
    // -------------------------------------------------------------------------

    public static function get_widget_registry(): array {
        return self::$widget_registry;
    }

    public static function get_feature_registry(): array {
        return self::$feature_registry;
    }
}
