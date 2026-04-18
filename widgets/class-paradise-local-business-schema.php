<?php
/**
 * Paradise LocalBusiness Schema Widget
 *
 * Invisible widget — outputs a JSON-LD <script> block with Schema.org
 * LocalBusiness markup. All data is pulled automatically from Site Info
 * (phones, addresses, socials, business hours). Helps search engines
 * display rich results (address, phone, hours, rating).
 *
 * Place this widget once per page (e.g. in the footer template).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Paradise_Local_Business_Schema_Widget extends \Elementor\Widget_Base {

    public function get_name(): string    { return 'paradise_local_business_schema'; }
    public function get_title(): string   { return esc_html__( 'LocalBusiness Schema', 'paradise-elementor-widgets' ); }
    public function get_icon(): string    { return 'eicon-code'; }
    public function get_categories(): array { return [ 'paradise' ]; }
    public function get_keywords(): array { return [ 'schema', 'seo', 'json-ld', 'structured', 'local', 'business' ]; }

    public function get_style_depends(): array { return [ 'paradise-local-business-schema' ]; }

    // ── Controls ──────────────────────────────────────────────────────────────

    protected function register_controls(): void {

        $this->start_controls_section( 'section_business', [
            'label' => esc_html__( 'Business Info', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'business_name', [
            'label'       => esc_html__( 'Business Name', 'paradise-elementor-widgets' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => get_bloginfo( 'name' ),
            'description' => esc_html__( 'Leave blank to use the site title.', 'paradise-elementor-widgets' ),
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ] );

        $this->add_control( 'business_type', [
            'label'   => esc_html__( 'Business Type', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'LocalBusiness',
            'options' => [
                'LocalBusiness'     => 'LocalBusiness (generic)',
                'MedicalBusiness'   => 'MedicalBusiness',
                'HealthAndBeautyBusiness' => 'Health & Beauty',
                'LodgingBusiness'   => 'Hotel / Lodging',
                'FoodEstablishment' => 'Restaurant / Food',
                'AutomotiveBusiness' => 'Automotive',
                'FinancialService'  => 'Financial Service',
                'LegalService'      => 'Legal Service',
                'HomeAndConstructionBusiness' => 'Home & Construction',
                'SportsActivityLocation' => 'Sports / Fitness',
                'EntertainmentBusiness' => 'Entertainment',
                'Store'             => 'Store / Retail',
                'ProfessionalService' => 'Professional Service',
            ],
        ] );

        $this->add_control( 'description', [
            'label'      => esc_html__( 'Description', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::TEXTAREA,
            'rows'       => 3,
            'dynamic'    => [ 'active' => true ],
        ] );

        $this->add_control( 'price_range', [
            'label'   => esc_html__( 'Price Range', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                ''      => esc_html__( '— Not specified —', 'paradise-elementor-widgets' ),
                '$'     => '$',
                '$$'    => '$$',
                '$$$'   => '$$$',
                '$$$$'  => '$$$$',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_site_info', [
            'label' => esc_html__( 'Site Info Mapping', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'location_index', [
            'label'       => esc_html__( 'Location', 'paradise-elementor-widgets' ),
            'type'        => \Elementor\Controls_Manager::SELECT,
            'options'     => Paradise_Site_Info::get_location_select_options(),
            'default'     => '0',
            'description' => esc_html__( 'Pulls phone, address, and hours from this location.', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'include_socials', [
            'label'        => esc_html__( 'Include Social Links (sameAs)', 'paradise-elementor-widgets' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ] );

        $this->add_control( 'include_hours', [
            'label'        => esc_html__( 'Include Business Hours', 'paradise-elementor-widgets' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ] );

        $this->end_controls_section();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    protected function render(): void {
        $settings  = $this->get_settings_for_display();
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => $settings['business_type'] ?: 'LocalBusiness',
            'name'     => $settings['business_name'] ?: get_bloginfo( 'name' ),
            'url'      => get_home_url(),
        ];

        if ( ! empty( $settings['description'] ) ) {
            $schema['description'] = sanitize_text_field( $settings['description'] );
        }

        if ( ! empty( $settings['price_range'] ) ) {
            $schema['priceRange'] = $settings['price_range'];
        }

        $location = (int) ( $settings['location_index'] ?? 0 );

        // Phone (first phone of the selected location)
        $phone = Paradise_Site_Info::get_value( 'phones', 0, 'value', $location );
        if ( $phone !== '' ) {
            $schema['telephone'] = $phone;
        }

        // Address
        $address = Paradise_Site_Info::get_address( $location );
        if ( $address !== '' ) {
            $schema['address'] = [
                '@type'         => 'PostalAddress',
                'streetAddress' => $address,
            ];
        }

        // Social sameAs
        if ( 'yes' === $settings['include_socials'] ) {
            $socials = Paradise_Site_Info::get( 'socials' );
            $same_as = array_values( array_filter( array_column( $socials, 'url' ) ) );
            if ( ! empty( $same_as ) ) {
                $schema['sameAs'] = $same_as;
            }
        }

        // Opening hours
        if ( 'yes' === $settings['include_hours'] ) {
            $day_map = [
                'monday'    => 'Monday',
                'tuesday'   => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday'  => 'Thursday',
                'friday'    => 'Friday',
                'saturday'  => 'Saturday',
                'sunday'    => 'Sunday',
            ];
            $specs = [];
            foreach ( Paradise_Site_Info::get_hours( $location ) as $slug => $entry ) {
                if ( $entry['open'] && ! empty( $entry['from'] ) && ! empty( $entry['to'] ) ) {
                    $specs[] = [
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => 'https://schema.org/' . $day_map[ $slug ],
                        'opens'     => $entry['from'],
                        'closes'    => $entry['to'],
                    ];
                }
            }
            if ( ! empty( $specs ) ) {
                $schema['openingHoursSpecification'] = $specs;
            }
        }

        $json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

        echo '<script type="application/ld+json">' . $json . '</script>';

        if ( $is_editor ) {
            echo '<div class="paradise-lbs-editor-notice">'
               . '<strong>' . esc_html__( 'LocalBusiness Schema', 'paradise-elementor-widgets' ) . '</strong> — '
               . esc_html__( 'JSON-LD is injected here on the live page. Invisible to visitors.', 'paradise-elementor-widgets' )
               . '</div>';
        }
    }
}
