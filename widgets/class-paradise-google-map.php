<?php
/**
 * Paradise Google Map Widget
 *
 * Embeds a Google Map via iframe. Source can be a Site Info address
 * (map_url field) or a manually entered URL.
 *
 * URL normalization: regular Google Maps share URLs are converted to
 * embeddable form by appending output=embed.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Paradise_Google_Map_Widget extends \Elementor\Widget_Base {

    public function get_name(): string    { return 'paradise_google_map'; }
    public function get_title(): string   { return esc_html__( 'Google Map', 'paradise-elementor-widgets' ); }
    public function get_icon(): string    { return 'eicon-google-maps'; }
    public function get_categories(): array { return [ 'paradise' ]; }
    public function get_keywords(): array { return [ 'map', 'google', 'location', 'address', 'embed' ]; }

    public function get_style_depends(): array {
        return [ 'paradise-google-map' ];
    }

    // ── Controls ──────────────────────────────────────────────────────────────

    protected function register_controls(): void {

        // ── Map Source ────────────────────────────────────────────────────────

        $this->start_controls_section( 'section_source', [
            'label' => esc_html__( 'Map Source', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'source', [
            'label'   => esc_html__( 'Source', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'manual',
            'options' => [
                'site_info' => esc_html__( 'Site Info Address', 'paradise-elementor-widgets' ),
                'manual'    => esc_html__( 'Manual URL', 'paradise-elementor-widgets' ),
            ],
        ] );

        $this->add_control( 'address_index', [
            'label'     => esc_html__( 'Select Address', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => Paradise_Site_Info::get_select_options( 'addresses' ),
            'default'   => '',
            'condition' => [ 'source' => 'site_info' ],
        ] );

        $this->add_control( 'manual_url', [
            'label'       => esc_html__( 'Map URL', 'paradise-elementor-widgets' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'https://www.google.com/maps/embed?pb=...',
            'description' => esc_html__( 'Paste any Google Maps URL (place, directions, or share link). For best results use Share → Embed a map → copy the src from the iframe code.', 'paradise-elementor-widgets' ),
            'label_block' => true,
            'condition'   => [ 'source' => 'manual' ],
            'dynamic'     => [ 'active' => true ],
        ] );

        $this->end_controls_section();

        // ── Settings ──────────────────────────────────────────────────────────

        $this->start_controls_section( 'section_settings', [
            'label' => esc_html__( 'Settings', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'height', [
            'label'      => esc_html__( 'Height', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vh' ],
            'range'      => [
                'px' => [ 'min' => 100, 'max' => 900, 'step' => 10 ],
                'vh' => [ 'min' => 10,  'max' => 100 ],
            ],
            'default'    => [ 'unit' => 'px', 'size' => 400 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-gmap-frame' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'allow_fullscreen', [
            'label'        => esc_html__( 'Allow Fullscreen', 'paradise-elementor-widgets' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ] );

        $this->end_controls_section();

        // ── Style ─────────────────────────────────────────────────────────────

        $this->start_controls_section( 'section_style', [
            'label' => esc_html__( 'Map', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'border_radius', [
            'label'      => esc_html__( 'Border Radius', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-gmap-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'map_border',
            'selector' => '{{WRAPPER}} .paradise-gmap-wrap',
        ] );

        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [
            'name'     => 'map_shadow',
            'selector' => '{{WRAPPER}} .paradise-gmap-wrap',
        ] );

        $this->end_controls_section();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Normalize any Google Maps URL into an embeddable iframe src.
     *
     * Google only allows /maps/embed URLs in iframes — share/directions URLs
     * return "refused to connect". This function converts known URL patterns
     * into a valid embed URL by extracting the search query:
     *
     * 1. /maps/embed  or output=embed → already embeddable, return as-is.
     * 2. /maps/dir//DESTINATION/@    → extract destination name.
     * 3. /maps/place/NAME/@          → extract place name.
     * 4. ?q=QUERY                    → use the q parameter directly.
     * 5. Fallback                    → build embed URL from the full URL as query.
     */
    private function normalize_embed_url( string $url ): string {
        if ( empty( $url ) ) return '';

        // Already a valid embed URL — use as-is.
        if ( strpos( $url, '/maps/embed' ) !== false ) return $url;
        if ( strpos( $url, 'output=embed' ) !== false ) return $url;

        $is_google = (
            strpos( $url, 'google.com/maps' ) !== false ||
            strpos( $url, 'maps.google.com' ) !== false
        );

        if ( ! $is_google ) return $url;

        // Directions URL: /maps/dir//DESTINATION/@ — extract the destination segment.
        if ( preg_match( '#/maps/dir//([^/@]+)/@#', $url, $m ) ) {
            $q = rawurldecode( str_replace( '+', ' ', $m[1] ) );
            return 'https://www.google.com/maps/embed?q=' . rawurlencode( $q );
        }

        // Place URL: /maps/place/NAME/@ — extract the place name.
        if ( preg_match( '#/maps/place/([^/@]+)/@#', $url, $m ) ) {
            $q = rawurldecode( str_replace( '+', ' ', $m[1] ) );
            return 'https://www.google.com/maps/embed?q=' . rawurlencode( $q );
        }

        // URL has a ?q= parameter — use it directly.
        parse_str( (string) parse_url( $url, PHP_URL_QUERY ), $params );
        if ( ! empty( $params['q'] ) ) {
            return 'https://www.google.com/maps/embed?q=' . rawurlencode( $params['q'] );
        }

        // Fallback: try appending output=embed (works for some older share URLs).
        $sep = ( strpos( $url, '?' ) !== false ) ? '&' : '?';
        return $url . $sep . 'output=embed';
    }

    // ── Render ────────────────────────────────────────────────────────────────

    protected function render(): void {
        $settings  = $this->get_settings_for_display();
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

        if ( 'site_info' === $settings['source'] ) {
            $raw_url = Paradise_Site_Info::get_value( 'addresses', (int) $settings['address_index'], 'map_url' );
            $from_si = true;
        } else {
            $raw_url = sanitize_text_field( $settings['manual_url'] ?? '' );
            $from_si = false;
        }

        $embed_url = $this->normalize_embed_url( $raw_url );

        if ( empty( $embed_url ) ) {
            if ( $is_editor ) {
                echo '<div class="paradise-gmap-placeholder">'
                   . ( $from_si
                       ? esc_html__( 'The selected address has no Map URL. Go to Paradise → Site Info and add a Google Maps link to that address.', 'paradise-elementor-widgets' )
                       : esc_html__( 'Enter a Google Maps URL in the Map Source settings.', 'paradise-elementor-widgets' ) )
                   . '</div>';
            }
            return;
        }

        $fullscreen_attr = 'yes' === $settings['allow_fullscreen'] ? 'allowfullscreen' : '';
        ?>
        <div class="paradise-gmap-wrap">
            <?php if ( $from_si && $is_editor ) : ?>
            <div class="paradise-gmap-si-badge"><?php esc_html_e( '⚡ Live from Site Info', 'paradise-elementor-widgets' ); ?></div>
            <?php endif; ?>
            <iframe
                class="paradise-gmap-frame"
                src="<?php echo esc_url( $embed_url ); ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                <?php echo $fullscreen_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            ></iframe>
        </div>
        <?php
    }
}
