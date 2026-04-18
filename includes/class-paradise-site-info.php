<?php
/**
 * Paradise Site Info
 *
 * Centralized data store for site-wide business information.
 * Supports multiple locations (branches), each with phones, emails,
 * a single address, a Google Map URL, and business hours.
 * Social links and business name are global (per brand, not per location).
 *
 * Data structure (paradise_site_info option):
 * {
 *   name:      string,
 *   socials:   [{platform, url}, ...],
 *   locations: [
 *     { label, phones:[{label,value}], emails:[{label,value}],
 *       address, map_url, hours:{day:{open,from,to}} },
 *     ...
 *   ]
 * }
 *
 * Usage:
 *   Paradise_Site_Info::get('phones', 0)       → phones[] for location 0
 *   Paradise_Site_Info::get_value('phones', 0) → first phone value, location 0
 *   Paradise_Site_Info::get_address(0)         → address string for location 0
 *   Paradise_Site_Info::get_map_url(0)         → map embed URL for location 0
 *   Paradise_Site_Info::get('socials')         → all social links (global)
 *   Paradise_Site_Info::get_hours(0)           → hours array for location 0
 *
 * Shortcode:
 *   [paradise_site_info type="phone" index="0" location="0"]
 *   [paradise_site_info type="phone" label="Main" location="Main Branch"]
 *   [paradise_site_info type="address" location="0"]
 *   [paradise_site_info type="address_map" location="1"]
 *   [paradise_site_info type="social_url" index="0"]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Paradise_Site_Info {

    const OPTION_KEY = 'paradise_site_info';

    // ── Internal data loading ─────────────────────────────────────────────────

    /**
     * Load and normalize stored data. Auto-migrates the legacy flat structure.
     */
    private static function load(): array {
        $data = get_option( self::OPTION_KEY, [] );
        return self::maybe_migrate( (array) $data );
    }

    /**
     * If data uses the pre-2.3 flat structure (no 'locations' key), convert it
     * transparently. The saved option is NOT modified — migration runs on read only.
     */
    private static function maybe_migrate( array $data ): array {
        if ( isset( $data['locations'] ) ) {
            return $data;
        }

        $first = $data['addresses'][0] ?? [];

        return [
            'name'      => $data['name'] ?? '',
            'socials'   => $data['socials'] ?? [],
            'locations' => [
                [
                    'label'   => 'Main',
                    'phones'  => $data['phones'] ?? [],
                    'emails'  => $data['emails'] ?? [],
                    'address' => $first['value']   ?? '',
                    'map_url' => $first['map_url']  ?? '',
                    'hours'   => $data['hours']     ?? [],
                ],
            ],
        ];
    }

    // ── Data access ───────────────────────────────────────────────────────────

    /**
     * Get all saved items for a section type within a location.
     *
     * @param string $type      'phones' | 'emails' | 'socials'
     *                          ('socials' is global — location is ignored).
     * @param int    $location  Location index (0-based).
     * @return array
     */
    public static function get( string $type, int $location = 0 ): array {
        $data = self::load();

        if ( 'socials' === $type ) {
            return array_values( $data['socials'] ?? [] );
        }

        $locations = $data['locations'] ?? [];
        $loc       = $locations[ $location ] ?? $locations[0] ?? [];
        return array_values( $loc[ $type ] ?? [] );
    }

    /**
     * Get a specific field from a specific item in a section.
     *
     * @param string $type      Section type.
     * @param int    $index     Zero-based item index.
     * @param string $field     Field name: 'value' (default), 'label', 'url', 'platform'.
     * @param int    $location  Location index.
     * @return string
     */
    public static function get_value( string $type, int $index, string $field = 'value', int $location = 0 ): string {
        $items = self::get( $type, $location );
        return (string) ( $items[ $index ][ $field ] ?? '' );
    }

    /** Get the address string for a location. */
    public static function get_address( int $location = 0 ): string {
        $data = self::load();
        $loc  = $data['locations'][ $location ] ?? $data['locations'][0] ?? [];
        return (string) ( $loc['address'] ?? '' );
    }

    /** Get the Google Map embed URL for a location. */
    public static function get_map_url( int $location = 0 ): string {
        $data = self::load();
        $loc  = $data['locations'][ $location ] ?? $data['locations'][0] ?? [];
        return (string) ( $loc['map_url'] ?? '' );
    }

    /** Get the global business name. */
    public static function get_name(): string {
        return (string) ( self::load()['name'] ?? '' );
    }

    /** Get all locations as a sequential array. */
    public static function get_locations(): array {
        return array_values( self::load()['locations'] ?? [] );
    }

    /**
     * Return [index => label] options suitable for Elementor SELECT controls.
     */
    public static function get_location_select_options(): array {
        $locations = self::get_locations();

        if ( empty( $locations ) ) {
            return [ '' => esc_html__( '— No locations saved —', 'paradise-elementor-widgets' ) ];
        }

        $options = [];
        foreach ( $locations as $i => $loc ) {
            $label       = trim( $loc['label'] ?? '' );
            $options[ $i ] = $label !== ''
                ? $label
                : sprintf( esc_html__( 'Location %d', 'paradise-elementor-widgets' ), $i + 1 );
        }

        return $options;
    }

    /**
     * Return an [index => display_label] array for a section type within a location.
     */
    public static function get_select_options( string $type, int $location = 0 ): array {
        $items = self::get( $type, $location );

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

    /** @return array<string, string>  slug → display label */
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

    /** Default hours: Mon–Fri 09:00–17:00, weekends closed. */
    public static function default_hours(): array {
        $weekdays = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday' ];
        $defaults = [];
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
     * Get saved hours for a location, merged with defaults so all 7 days are present.
     */
    public static function get_hours( int $location = 0 ): array {
        $data      = self::load();
        $locations = $data['locations'] ?? [];
        $loc       = $locations[ $location ] ?? $locations[0] ?? [];
        $saved     = $loc['hours'] ?? [];
        $result    = [];

        foreach ( self::default_hours() as $day => $defaults ) {
            $entry          = array_merge( $defaults, $saved[ $day ] ?? [] );
            $entry['open']  = (bool) $entry['open'];
            $result[ $day ] = $entry;
        }

        return $result;
    }

    /**
     * Check whether the business is currently open (uses the site timezone).
     */
    public static function is_open_now( int $location = 0 ): bool {
        try {
            $tz  = new DateTimeZone( wp_timezone_string() );
            $now = new DateTime( 'now', $tz );
        } catch ( Exception $e ) {
            $now = new DateTime( 'now' );
        }

        $day   = strtolower( $now->format( 'l' ) );
        $time  = $now->format( 'H:i' );
        $hours = self::get_hours( $location );
        $entry = $hours[ $day ] ?? null;

        if ( ! $entry || ! $entry['open'] || empty( $entry['from'] ) || empty( $entry['to'] ) ) {
            return false;
        }

        return $time >= $entry['from'] && $time <= $entry['to'];
    }

    /** @return array<string, string>  slug → display name */
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

    // ── Export ────────────────────────────────────────────────────────────────

    /**
     * Return a fully-normalized snapshot of all Site Info data for export.
     * Hours are merged with defaults so all 7 days are present in every location.
     */
    public static function export(): array {
        $data      = self::load();
        $locations = [];
        foreach ( $data['locations'] ?? [] as $i => $loc ) {
            $loc['hours'] = self::get_hours( $i );
            $locations[]  = $loc;
        }
        return [
            'name'      => $data['name']    ?? '',
            'socials'   => $data['socials'] ?? [],
            'locations' => $locations,
        ];
    }

    // ── Persistence ───────────────────────────────────────────────────────────

    public static function save( array $raw ): void {
        $data = [
            'name'      => sanitize_text_field( $raw['name'] ?? '' ),
            'socials'   => [],
            'locations' => [],
        ];

        foreach ( $raw['socials'] ?? [] as $item ) {
            $url = esc_url_raw( $item['url'] ?? '' );
            if ( $url === '' ) continue;
            $data['socials'][] = [
                'platform' => sanitize_key( $item['platform'] ?? '' ),
                'url'      => $url,
            ];
        }

        foreach ( $raw['locations'] ?? [] as $loc_raw ) {
            $loc = [
                'label'   => sanitize_text_field( $loc_raw['label']   ?? '' ),
                'phones'  => [],
                'emails'  => [],
                'address' => sanitize_text_field( $loc_raw['address'] ?? '' ),
                'map_url' => esc_url_raw( $loc_raw['map_url']          ?? '' ),
                'hours'   => [],
            ];

            foreach ( $loc_raw['phones'] ?? [] as $item ) {
                $value = sanitize_text_field( $item['value'] ?? '' );
                if ( $value === '' ) continue;
                $loc['phones'][] = [
                    'label' => sanitize_text_field( $item['label'] ?? '' ),
                    'value' => $value,
                ];
            }

            foreach ( $loc_raw['emails'] ?? [] as $item ) {
                $value = sanitize_email( $item['value'] ?? '' );
                if ( $value === '' ) continue;
                $loc['emails'][] = [
                    'label' => sanitize_text_field( $item['label'] ?? '' ),
                    'value' => $value,
                ];
            }

            foreach ( array_keys( self::days() ) as $day ) {
                $entry = $loc_raw['hours'][ $day ] ?? [];
                $loc['hours'][ $day ] = [
                    'open' => ! empty( $entry['open'] ),
                    'from' => sanitize_text_field( $entry['from'] ?? '' ),
                    'to'   => sanitize_text_field( $entry['to']   ?? '' ),
                ];
            }

            $data['locations'][] = $loc;
        }

        // Always keep at least one location
        if ( empty( $data['locations'] ) ) {
            $data['locations'][] = [
                'label' => 'Main', 'phones' => [], 'emails' => [],
                'address' => '', 'map_url' => '', 'hours' => [],
            ];
        }

        update_option( self::OPTION_KEY, $data );
    }

    // ── Shortcode ─────────────────────────────────────────────────────────────

    public static function register_shortcode(): void {
        add_shortcode( 'paradise_site_info', [ __CLASS__, 'shortcode_handler' ] );
    }

    private static function resolve_index( string $type, array $atts, int $location = 0 ): int {
        $label_attr = trim( $atts['label'] ?? '' );

        if ( $label_attr !== '' ) {
            foreach ( self::get( $type, $location ) as $i => $item ) {
                if ( strcasecmp( $item['label'] ?? '', $label_attr ) === 0 ) {
                    return $i;
                }
            }
        }

        return max( 0, (int) ( $atts['index'] ?? 0 ) );
    }

    private static function resolve_location( array $atts ): int {
        $loc = trim( $atts['location'] ?? '' );

        if ( $loc === '' ) return 0;
        if ( ctype_digit( $loc ) ) return (int) $loc;

        foreach ( self::get_locations() as $i => $location ) {
            if ( strcasecmp( $location['label'] ?? '', $loc ) === 0 ) {
                return $i;
            }
        }

        return 0;
    }

    public static function shortcode_handler( array $atts ): string {
        $atts = shortcode_atts( [
            'type'     => 'phone',
            'index'    => 0,
            'label'    => '',
            'location' => '',
        ], $atts, 'paradise_site_info' );

        $location = self::resolve_location( $atts );

        switch ( $atts['type'] ) {
            case 'phone':
                return esc_html( self::get_value( 'phones', self::resolve_index( 'phones', $atts, $location ), 'value', $location ) );

            case 'email':
                return esc_html( self::get_value( 'emails', self::resolve_index( 'emails', $atts, $location ), 'value', $location ) );

            case 'address':
                return esc_html( self::get_address( $location ) );

            case 'address_map':
                return esc_url( self::get_map_url( $location ) );

            case 'social_url':
                return esc_url( self::get_value( 'socials', self::resolve_index( 'socials', $atts ), 'url' ) );

            default:
                return '';
        }
    }
}
