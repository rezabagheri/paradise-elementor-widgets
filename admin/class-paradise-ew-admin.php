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
    /**
     * Widget registry — single source of truth for settings, toggle UI, and loading.
     *
     * Keys:
     *   label       — human-readable name shown in the settings page
     *   description — short description shown in the settings page
     *   file        — path relative to PARADISE_EW_DIR
     *   class       — PHP class name to instantiate
     */
    /**
     * Widget registry — UI metadata only.
     *
     * Keys:
     *   label       — human-readable name shown in the settings page
     *   description — short description shown in the settings page
     *
     * The 'file' and 'class' values are derived automatically from the key
     * via key_to_file() and key_to_class(). get_widget_registry() returns
     * the enriched array so consumers never need to call those helpers directly.
     *
     * Key convention  →  file: widgets/class-paradise-{key-with-dashes}.php
     *                 →  class: Paradise_{Key_Ucwords}_Widget
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
        'back_to_top' => [
            'label'       => 'Back to Top',
            'description' => 'Fixed-position button that appears after scrolling past a threshold and smoothly returns the user to the top of the page.',
        ],
        'off_canvas_menu' => [
            'label'       => 'Off-Canvas Menu',
            'description' => 'Slide-in panel with a WordPress menu. Triggered by an inline button or the Paradise.openOffCanvas() JS API (e.g. from Bottom Nav).',
        ],
        'sticky_header' => [
            'label'       => 'Sticky Header',
            'description' => 'Place inside any Elementor section to make it sticky. Applies scroll effects (shadow, background change, shrink) when scrolling past a threshold.',
        ],
        'google_map' => [
            'label'       => 'Google Map',
            'description' => 'Embeds a Google Map via iframe. Source can be a Site Info address (Map URL field) or a manually entered URL. Supports border radius, shadow, and height controls.',
        ],
        'social_links' => [
            'label'       => 'Social Links',
            'description' => 'Row or column of social media icon links. Source: Site Info socials or custom list. Supports brand/uniform colors, hover animations, icon shapes, and icon+label display modes.',
        ],
        'business_hours' => [
            'label'       => 'Business Hours',
            'description' => 'Displays business hours from Site Info with a live Open Now / Closed badge. Highlights today\'s row and supports 12/24-hour format.',
        ],
        'local_business_schema' => [
            'label'       => 'LocalBusiness Schema',
            'description' => 'Invisible widget that outputs Schema.org JSON-LD markup using Site Info data (name, phone, address, hours, social links). Helps Google display rich results.',
        ],
        'faq_accordion' => [
            'label'       => 'FAQ Accordion',
            'description' => 'Collapsible Q&A list with accordion or multi-expand mode. Supports Schema.org FAQPage markup for Google rich results.',
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

    /**
     * Returns the widget registry enriched with derived 'file' and 'class' keys.
     * Consumers (e.g. register_widgets()) can use these without knowing the convention.
     */
    public static function get_widget_registry(): array {
        $enriched = [];
        foreach ( self::$widget_registry as $key => $meta ) {
            $enriched[ $key ] = $meta + [
                'file'  => self::key_to_file( $key ),
                'class' => self::key_to_class( $key ),
            ];
        }
        return $enriched;
    }

    /**
     * Derives the widget file path from the registry key.
     * Example: 'google_map' → 'widgets/class-paradise-google-map.php'
     */
    private static function key_to_file( string $key ): string {
        return 'widgets/class-paradise-' . str_replace( '_', '-', $key ) . '.php';
    }

    /**
     * Derives the widget class name from the registry key.
     * Example: 'google_map' → 'Paradise_Google_Map_Widget'
     */
    private static function key_to_class( string $key ): string {
        $parts = array_map( 'ucfirst', explode( '_', $key ) );
        return 'Paradise_' . implode( '_', $parts ) . '_Widget';
    }

    public static function get_feature_registry(): array {
        return self::$feature_registry;
    }
}
