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

    // ── Business Hours ────────────────────────────────────────────────────────

    /**
     * Ordered list of days with their display labels.
     * @return array<string, string>  slug → label
     */
    public static function days(): array {
        return [
            'monday'    => esc_html__( 'Monday',    'paradise-elementor-widgets' ),
            'tuesday'   => esc_html__( 'Tuesday',   'paradise-elementor-widgets' ),
            'wednesday' => esc_html__( 'Wednesday', 'paradise-elementor-widgets' ),
            'thursday'  => esc_html__( 'Thursday',  'paradise-elementor-widgets' ),
            'friday'    => esc_html__( 'Friday',    'paradise-elementor-widgets' ),
            'saturday'  => esc_html__( 'Saturday',  'paradise-elementor-widgets' ),
            'sunday'    => esc_html__( 'Sunday',    'paradise-elementor-widgets' ),
        ];
    }

    /**
     * Default hours (Mon–Fri 09:00–17:00, weekends closed).
     */
    public static function default_hours(): array {
        $defaults = [];
        $weekdays = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday' ];
        foreach ( array_keys( self::days() ) as $day ) {
            $defaults[ $day ] = [
                'open' => in_array( $day, $weekdays, true ),
                'from' => '09:00',
                'to'   => '17:00',
            ];
        }
        return $defaults;
    }

    /**
     * Get saved hours, merged with defaults so all 7 days are always present.
     */
    public static function get_hours(): array {
        $data    = get_option( self::OPTION_KEY, [] );
        $saved   = $data['hours'] ?? [];
        $result  = [];
        foreach ( self::default_hours() as $day => $defaults ) {
            $entry          = array_merge( $defaults, $saved[ $day ] ?? [] );
            $entry['open']  = (bool) $entry['open'];
            $result[ $day ] = $entry;
        }
        return $result;
    }

    /**
     * Check whether the business is currently open, based on the site timezone.
     */
    public static function is_open_now(): bool {
        try {
            $tz  = new DateTimeZone( wp_timezone_string() );
            $now = new DateTime( 'now', $tz );
        } catch ( Exception $e ) {
            $now = new DateTime( 'now' );
        }

        $day   = strtolower( $now->format( 'l' ) );
        $time  = $now->format( 'H:i' );
        $hours = self::get_hours();
        $entry = $hours[ $day ] ?? null;

        if ( ! $entry || ! $entry['open'] || empty( $entry['from'] ) || empty( $entry['to'] ) ) {
            return false;
        }

        return $time >= $entry['from'] && $time <= $entry['to'];
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
            'hours'     => [],
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

        // Hours — fixed 7 days, no add/remove
        foreach ( array_keys( self::days() ) as $day ) {
            $entry          = $raw['hours'][ $day ] ?? [];
            $data['hours'][ $day ] = [
                'open' => ! empty( $entry['open'] ),
                'from' => sanitize_text_field( $entry['from'] ?? '' ),
                'to'   => sanitize_text_field( $entry['to'] ?? '' ),
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
