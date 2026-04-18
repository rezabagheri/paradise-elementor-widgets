<?php
/**
 * Paradise Dynamic Tags
 *
 * Registers a "Paradise" group of Dynamic Tags in Elementor.
 * Tags pull values from Paradise_Site_Info.
 *
 * Tags:
 *   paradise-phone          — TEXT  — phone number string
 *   paradise-phone-url      — URL   — tel:+... href
 *   paradise-email          — TEXT  — email address string
 *   paradise-email-url      — URL   — mailto:... href
 *   paradise-address        — TEXT  — address string
 *   paradise-address-map    — URL   — Google Maps URL
 *   paradise-social-url     — URL   — social media URL
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Manager ───────────────────────────────────────────────────────────────────

class Paradise_Dynamic_Tags {

    public static function register( $manager ): void {
        $manager->register_group( 'paradise', [
            'title' => esc_html__( 'Paradise Site Info', 'paradise-elementor-widgets' ),
        ] );

        $manager->register( new Paradise_Tag_Phone() );
        $manager->register( new Paradise_Tag_Phone_URL() );
        $manager->register( new Paradise_Tag_Email() );
        $manager->register( new Paradise_Tag_Email_URL() );
        $manager->register( new Paradise_Tag_Address() );
        $manager->register( new Paradise_Tag_Address_Map_URL() );
        $manager->register( new Paradise_Tag_Social_URL() );
    }
}

// ── Base helper ───────────────────────────────────────────────────────────────

abstract class Paradise_Tag_Base extends \Elementor\Core\DynamicTags\Tag {

    public function get_group(): string { return 'paradise'; }

    /** Add a Location SELECT control. */
    protected function add_location_select(): void {
        $this->add_control( 'location_index', [
            'label'   => esc_html__( 'Location', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => Paradise_Site_Info::get_location_select_options(),
            'default' => '0',
        ] );
    }

    /** Add a numeric item index control (0-based). */
    protected function add_item_index( string $control_id, string $label ): void {
        $this->add_control( $control_id, [
            'label'   => $label,
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => 0,
            'min'     => 0,
            'max'     => 20,
        ] );
    }
}

// ── Phone — Text ──────────────────────────────────────────────────────────────

class Paradise_Tag_Phone extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-phone'; }
    public function get_title(): string { return esc_html__( 'Phone Number', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_location_select();
        $this->add_item_index( 'phone_index', esc_html__( 'Item Index', 'paradise-elementor-widgets' ) );
    }

    public function render(): void {
        $location = (int) $this->get_settings( 'location_index' );
        $index    = (int) $this->get_settings( 'phone_index' );
        echo esc_html( Paradise_Site_Info::get_value( 'phones', $index, 'value', $location ) );
    }
}

// ── Phone — URL (tel:) ────────────────────────────────────────────────────────

class Paradise_Tag_Phone_URL extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-phone-url'; }
    public function get_title(): string { return esc_html__( 'Phone URL (tel:)', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_location_select();
        $this->add_item_index( 'phone_index', esc_html__( 'Item Index', 'paradise-elementor-widgets' ) );
    }

    public function render(): void {
        $location = (int) $this->get_settings( 'location_index' );
        $raw      = Paradise_Site_Info::get_value( 'phones', (int) $this->get_settings( 'phone_index' ), 'value', $location );
        $digits   = preg_replace( '/[^0-9]/', '', $raw );
        $href     = strpos( $raw, '+' ) !== false ? 'tel:+' . $digits : 'tel:' . $digits;
        echo esc_url( $href );
    }
}

// ── Email — Text ──────────────────────────────────────────────────────────────

class Paradise_Tag_Email extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-email'; }
    public function get_title(): string { return esc_html__( 'Email Address', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_location_select();
        $this->add_item_index( 'email_index', esc_html__( 'Item Index', 'paradise-elementor-widgets' ) );
    }

    public function render(): void {
        $location = (int) $this->get_settings( 'location_index' );
        $index    = (int) $this->get_settings( 'email_index' );
        echo esc_html( Paradise_Site_Info::get_value( 'emails', $index, 'value', $location ) );
    }
}

// ── Email — URL (mailto:) ─────────────────────────────────────────────────────

class Paradise_Tag_Email_URL extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-email-url'; }
    public function get_title(): string { return esc_html__( 'Email URL (mailto:)', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_location_select();
        $this->add_item_index( 'email_index', esc_html__( 'Item Index', 'paradise-elementor-widgets' ) );
    }

    public function render(): void {
        $location = (int) $this->get_settings( 'location_index' );
        $email    = Paradise_Site_Info::get_value( 'emails', (int) $this->get_settings( 'email_index' ), 'value', $location );
        echo esc_url( 'mailto:' . $email );
    }
}

// ── Address — Text ────────────────────────────────────────────────────────────

class Paradise_Tag_Address extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-address'; }
    public function get_title(): string { return esc_html__( 'Address', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_location_select();
    }

    public function render(): void {
        echo esc_html( Paradise_Site_Info::get_address( (int) $this->get_settings( 'location_index' ) ) );
    }
}

// ── Address — Map URL ─────────────────────────────────────────────────────────

class Paradise_Tag_Address_Map_URL extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-address-map'; }
    public function get_title(): string { return esc_html__( 'Address Map URL', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_location_select();
    }

    public function render(): void {
        echo esc_url( Paradise_Site_Info::get_map_url( (int) $this->get_settings( 'location_index' ) ) );
    }
}

// ── Social — URL ──────────────────────────────────────────────────────────────

class Paradise_Tag_Social_URL extends Paradise_Tag_Base {

    public function get_name(): string  { return 'paradise-social-url'; }
    public function get_title(): string { return esc_html__( 'Social Link URL', 'paradise-elementor-widgets' ); }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
    }

    protected function register_controls(): void {
        $this->add_item_index( 'social_index', esc_html__( 'Item Index', 'paradise-elementor-widgets' ) );
    }

    public function render(): void {
        $index = (int) $this->get_settings( 'social_index' );
        echo esc_url( Paradise_Site_Info::get_value( 'socials', $index, 'url' ) );
    }
}
