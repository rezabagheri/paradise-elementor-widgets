<?php
/**
 * Paradise Phone Helper Trait
 *
 * Shared phone normalization and formatting logic used by
 * Paradise_Phone_Link_Widget and Paradise_Phone_Button_Widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Paradise_Phone_Helper {

    /**
     * Build a tel: or https://wa.me/ href from raw phone input.
     *
     * @param string $raw        Raw phone input (any format).
     * @param string $cc         Country code digits (no +).
     * @param string $link_type  'tel' or 'whatsapp'.
     */
    protected function build_phone_href( string $raw, string $cc, string $link_type = 'tel' ): string {
        if ( 'whatsapp' === $link_type ) {
            $digits = preg_replace( '/[^0-9]/', '', $raw );
            if ( strpos( $raw, '+' ) === false ) {
                $digits = $cc . ltrim( $digits, '0' );
            }
            return 'https://wa.me/' . $digits;
        }

        // tel:
        $digits   = preg_replace( '/[^0-9]/', '', $raw );
        $has_plus = strpos( $raw, '+' ) !== false;

        if ( $has_plus ) {
            return 'tel:+' . $digits;
        }

        return 'tel:+' . ltrim( $cc, '+' ) . ltrim( $digits, '0' );
    }

    /**
     * Format phone digits for display.
     *
     * @param string $raw      Raw phone input.
     * @param array  $settings Widget settings array (needs display_format, custom_mask, country_code*).
     */
    protected function format_phone_display( string $raw, array $settings ): string {
        $format = $settings['display_format'] ?? 'raw';

        if ( 'raw' === $format ) {
            return $raw;
        }

        $digits = preg_replace( '/[^0-9]/', '', $raw );

        if ( 'custom_mask' === $format ) {
            $mask   = $settings['custom_mask'] ?? '';
            $result = '';
            $d      = 0;
            for ( $i = 0; $i < strlen( $mask ); $i++ ) {
                $result .= ( '#' === $mask[ $i ] )
                    ? ( $digits[ $d++ ] ?? '' )
                    : $mask[ $i ];
            }
            return $result;
        }

        // Resolve the configured country code (digits only, no +)
        $cc = ( $settings['country_code'] ?? '1' ) === 'custom'
            ? ltrim( $settings['country_code_custom'] ?? '1', '+' )
            : ltrim( $settings['country_code'] ?? '1', '+' );

        // Strip country code prefix when present, regardless of total digit count.
        // This handles e.g. "1 888 123 4567" (10-digit string starting with cc "1")
        // as well as the standard E.164 case "+1 888 123 4567" (11 digits).
        $cc_len = strlen( $cc );
        if ( $cc_len > 0 && substr( $digits, 0, $cc_len ) === $cc ) {
            $stripped = substr( $digits, $cc_len );
            if ( strlen( $stripped ) >= 7 ) { // at least 7 digits = plausible local number
                $digits = $stripped;
            }
        }

        // Trim to 10 digits max (safety fallback for long strings)
        $local = strlen( $digits ) > 10 ? substr( $digits, -10 ) : $digits;
        $area  = substr( $local, 0, 3 );
        $mid   = substr( $local, 3, 3 );
        $end   = substr( $local, 6 );

        switch ( $format ) {
            case 'international':
                return "+{$cc} {$area} {$mid} {$end}";

            case 'local':
                return "({$area}) {$mid}-{$end}";

            case 'dashes':
                return "{$area}-{$mid}-{$end}";

            case 'dots':
                return "{$area}.{$mid}.{$end}";
        }

        return $raw;
    }

    /**
     * Resolve country code digits from widget settings.
     */
    protected function resolve_country_code( array $settings ): string {
        $raw = ( $settings['country_code'] ?? '1' ) === 'custom'
            ? ( $settings['country_code_custom'] ?? '1' )
            : ( $settings['country_code'] ?? '1' );

        return ltrim( $raw, '+' );
    }
}
