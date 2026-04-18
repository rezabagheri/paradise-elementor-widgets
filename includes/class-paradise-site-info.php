<?php
/**
 * Paradise Site Info
 *
 * Data model for site-wide global values: phone numbers, email addresses,
 * physical addresses, and social links. Stored as a single WordPress option.
 *
 * Usage in PHP templates:
 *   Paradise_Site_Info::get('phones')               → array of all phones
 *   Paradise_Site_Info::get_value('phones', 0)       → "+1 888 123 4567"
 *   Paradise_Site_Info::get_value('socials', 0, 'url') → "https://instagram.com/..."
 *
 * Shortcode:
 *   [paradise_site_info type="phone" index="0"]
 *   [paradise_site_info type="phone" label="Main Office"]
 *   [paradise_site_info type="email" index="0"]
 *   [paradise_site_info type="address" index="0"]
 *   [paradise_site_info type="address_map" index="0"]
 *   [paradise_site_info type="social_url" index="0"]
 *
 * When both label and index are provided, label takes precedence.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Paradise_Site_Info {

    const OPTION_KEY = 'paradise_site_info';

    // ── Data access ───────────────────────────────────────────────────────────

    /**
     * Get all saved items for a section type.
     * @param string $type  'phones' | 'emails' | 'addresses' | 'socials'
     * @return array
     */
    public static function get( string $type ): array {
        $data = get_option( self::OPTION_KEY, [] );
        return array_values( $data[ $type ] ?? [] );
    }

    /**
     * Get a specific field from a specific item.
     *
     * @param string $type   Section type.
     * @param int    $index  Zero-based index.
     * @param string $field  Field name: 'value' (default), 'label', 'url', 'platform'.
     * @return string
     */
    public static function get_value( string $type, int $index, string $field = 'value' ): string {
        $items = self::get( $type );
        return (string) ( $items[ $index ][ $field ] ?? '' );
    }

    /**
     * Return an [index => display_label] array suitable for SELECT controls.
     * Returns a placeholder when the section is empty.
     */
    public static function get_select_options( string $type ): array {
        $items = self::get( $type );

        if ( empty( $items ) ) {
            return [ '' => esc_html__( '— No items saved —', 'paradise-elementor-widgets' ) ];
        }

        $options = [];
        foreach ( $items as $i => $item ) {
            if ( 'socials' === $type ) {
                $platform_name = self::social_platforms()[ $item['platform'] ?? '' ] ?? ( $item['platform'] ?? '' );
                $options[ $i ] = $platform_name;
            } else {
                $label = trim( $item['label'] ?? '' );
                $val   = trim( $item['value'] ?? '' );
                $short = mb_strlen( $val ) > 24 ? mb_substr( $val, 0, 24 ) . '…' : $val;
                $options[ $i ] = $label !== '' ? $label . ' — ' . $short : $short;
            }
        }

        return $options;
    }

    /**
     * Supported social platforms.
     * @return array<string, string>  slug → display name
     */
    public static function social_platforms(): array {
        return [
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            'twitter'   => 'X (Twitter)',
            'linkedin'  => 'LinkedIn',
            'youtube'   => 'YouTube',
            'tiktok'    => 'TikTok',
            'pinterest' => 'Pinterest',
            'snapchat'  => 'Snapchat',
            'threads'   => 'Threads',
            'whatsapp'  => 'WhatsApp',
        ];
    }

    // ── Persistence ───────────────────────────────────────────────────────────

    /**
     * Sanitize raw POST data and save to the database.
     */
    public static function save( array $raw ): void {
        $data = [
            'phones'    => [],
            'emails'    => [],
            'addresses' => [],
            'socials'   => [],
        ];

        foreach ( [ 'phones', 'emails' ] as $type ) {
            foreach ( $raw[ $type ] ?? [] as $item ) {
                $value = sanitize_text_field( $item['value'] ?? '' );
                if ( $value === '' ) continue;
                $data[ $type ][] = [
                    'label' => sanitize_text_field( $item['label'] ?? '' ),
                    'value' => $value,
                ];
            }
        }

        foreach ( $raw['addresses'] ?? [] as $item ) {
            $value = sanitize_text_field( $item['value'] ?? '' );
            if ( $value === '' ) continue;
            $data['addresses'][] = [
                'label'   => sanitize_text_field( $item['label'] ?? '' ),
                'value'   => $value,
                'map_url' => esc_url_raw( $item['map_url'] ?? '' ),
            ];
        }

        foreach ( $raw['socials'] ?? [] as $item ) {
            $url = esc_url_raw( $item['url'] ?? '' );
            if ( $url === '' ) continue;
            $data['socials'][] = [
                'platform' => sanitize_key( $item['platform'] ?? '' ),
                'url'      => $url,
            ];
        }

        update_option( self::OPTION_KEY, $data );
    }

    // ── Shortcode ─────────────────────────────────────────────────────────────

    /**
     * Register [paradise_site_info] shortcode.
     * Call once on 'init'.
     */
    public static function register_shortcode(): void {
        add_shortcode( 'paradise_site_info', [ __CLASS__, 'shortcode_handler' ] );
    }

    /**
     * Resolve the item index from shortcode attributes.
     * If 'label' is provided, find the first item whose label matches (case-insensitive).
     * Falls back to 'index' when no label is given or no match is found.
     */
    private static function resolve_index( string $type, array $atts ): int {
        $label_attr = trim( $atts['label'] ?? '' );

        if ( $label_attr !== '' ) {
            $items = self::get( $type );
            foreach ( $items as $i => $item ) {
                if ( strcasecmp( $item['label'] ?? '', $label_attr ) === 0 ) {
                    return $i;
                }
            }
        }

        return max( 0, (int) ( $atts['index'] ?? 0 ) );
    }

    /**
     * [paradise_site_info type="phone" index="0"]
     * [paradise_site_info type="phone" label="Main Office"]
     * [paradise_site_info type="email" index="0"]
     * [paradise_site_info type="address" index="0"]
     * [paradise_site_info type="address_map" index="0"]
     * [paradise_site_info type="social_url" index="0"]
     *
     * When both label and index are provided, label takes precedence.
     */
    public static function shortcode_handler( array $atts ): string {
        $atts = shortcode_atts( [
            'type'  => 'phone',
            'index' => 0,
            'label' => '',
        ], $atts, 'paradise_site_info' );

        switch ( $atts['type'] ) {
            case 'phone':
                return esc_html( self::get_value( 'phones', self::resolve_index( 'phones', $atts ) ) );

            case 'email':
                return esc_html( self::get_value( 'emails', self::resolve_index( 'emails', $atts ) ) );

            case 'address':
                return esc_html( self::get_value( 'addresses', self::resolve_index( 'addresses', $atts ) ) );

            case 'address_map':
                return esc_url( self::get_value( 'addresses', self::resolve_index( 'addresses', $atts ), 'map_url' ) );

            case 'social_url':
                return esc_url( self::get_value( 'socials', self::resolve_index( 'socials', $atts ), 'url' ) );

            default:
                return '';
        }
    }
}
