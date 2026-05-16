<?php
/**
 * Paradise Custom Fields — Elementor Dynamic Tags
 *
 * Registers three dynamic tags — one per Elementor category — that surface
 * user-defined custom fields:
 *
 *   paradise-cf-text   → TEXT_CATEGORY  — text + textarea fields
 *   paradise-cf-url    → URL_CATEGORY   — url fields
 *   paradise-cf-image  → IMAGE_CATEGORY — image fields (Data_Tag, returns array)
 *
 * The SELECT in each tag is built dynamically from the type registry's
 * `el_category` mapping: as users add fields, the matching tag's dropdown
 * fills up automatically — no extra code per new field.
 *
 * Design note: Image needs Data_Tag (returns {id, url}) while Text/URL
 * need Tag (echoes a string). PHP can't multi-inherit, so we follow the
 * same plain-inheritance pattern Elementor Pro itself uses — a small
 * helper class for shared logic, each tag class extends the right base
 * and calls into the helper. Earlier attempts used a trait with an
 * abstract method; that triggered a hard-to-pin editor regression and
 * was reverted in favour of this simpler shape.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Registry helper ─────────────────────────────────────────────────────────

class Paradise_CF_Tags {

    public const GROUP = 'paradise-cf';

    public static function register( $manager ): void {
        $manager->register_group( self::GROUP, [
            'title' => esc_html__( 'Paradise Custom Fields', 'paradise-elementor-widgets' ),
        ] );

        $manager->register( new Paradise_CF_Tag_Text() );
        $manager->register( new Paradise_CF_Tag_URL() );
        $manager->register( new Paradise_CF_Tag_Image() );
    }

    /**
     * Build a [field_key => "Group Label — Field Label"] options array
     * for fields whose type maps to the given Elementor el_category.
     *
     * `text` category includes BOTH 'text' and 'textarea' field types
     * because their registry value of `el_category` is the same.
     */
    public static function field_options_for_category( string $el_category ): array {
        $valid_types = [];
        foreach ( Paradise_Custom_Fields::get_types() as $type_slug => $type ) {
            if ( ( $type['el_category'] ?? '' ) === $el_category ) {
                $valid_types[ $type_slug ] = true;
            }
        }

        $options = [ '' => esc_html__( '— Select a field —', 'paradise-elementor-widgets' ) ];

        // Every array access guarded — editor calls register_controls()
        // on every tag at load, and a single undefined-key warning here
        // would land in the editor's error handler.
        foreach ( Paradise_Custom_Fields::get_groups() as $group ) {
            $g_label = (string) ( $group['label'] ?? '' );
            $g_slug  = (string) ( $group['slug']  ?? '' );
            $group_label = $g_label !== '' ? $g_label : $g_slug;

            foreach ( $group['fields'] ?? [] as $field ) {
                $f_key   = (string) ( $field['key']   ?? '' );
                $f_label = (string) ( $field['label'] ?? '' );
                $f_type  = (string) ( $field['type']  ?? '' );

                if ( $f_key === '' || ! isset( $valid_types[ $f_type ] ) ) {
                    continue;
                }
                $options[ $f_key ] = $group_label . ' — ' . ( $f_label !== '' ? $f_label : $f_key );
            }
        }

        if ( count( $options ) === 1 ) {
            $options[''] = esc_html__( '— No fields available —', 'paradise-elementor-widgets' );
        }

        return $options;
    }

    /**
     * Add the standard "Field" SELECT control to a tag. Called from each
     * concrete tag's register_controls() — keeps the SELECT consistent
     * across the three tags without forcing them to share a base class.
     */
    public static function add_field_control( $tag, string $el_category ): void {
        $tag->add_control( 'field_key', [
            'label'   => esc_html__( 'Field', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => self::field_options_for_category( $el_category ),
            'default' => '',
        ] );
    }
}

// ── Text tag ────────────────────────────────────────────────────────────────

class Paradise_CF_Tag_Text extends \Elementor\Core\DynamicTags\Tag {

    public function get_name(): string  { return 'paradise-cf-text'; }
    public function get_title(): string { return esc_html__( 'Custom Field (Text)', 'paradise-elementor-widgets' ); }
    public function get_group(): string { return Paradise_CF_Tags::GROUP; }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    protected function register_controls(): void {
        Paradise_CF_Tags::add_field_control( $this, 'text' );
    }

    /**
     * Use 'raw' output mode so textarea fields don't emit <p>/<br> into
     * controls that expect plain text (e.g. Heading widget, button labels,
     * image alt). The plain text type ignores $output and returns
     * esc_html() either way — safe for both 'text' and 'textarea'.
     */
    public function render(): void {
        $key = (string) $this->get_settings( 'field_key' );
        if ( $key === '' ) {
            return;
        }
        echo Paradise_Custom_Fields::render( $key, 'raw' ); // phpcs:ignore — render() escapes per type
    }
}

// ── URL tag ─────────────────────────────────────────────────────────────────

class Paradise_CF_Tag_URL extends \Elementor\Core\DynamicTags\Tag {

    public function get_name(): string  { return 'paradise-cf-url'; }
    public function get_title(): string { return esc_html__( 'Custom Field (URL)', 'paradise-elementor-widgets' ); }
    public function get_group(): string { return Paradise_CF_Tags::GROUP; }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
    }

    protected function register_controls(): void {
        Paradise_CF_Tags::add_field_control( $this, 'url' );
    }

    public function render(): void {
        $key = (string) $this->get_settings( 'field_key' );
        if ( $key === '' ) {
            return;
        }
        echo Paradise_Custom_Fields::render( $key ); // phpcs:ignore — render() escapes via esc_url
    }
}

// ── Image tag (Data_Tag — returns array shape, not echoed) ──────────────────

/**
 * Image tags use Data_Tag (not Tag) because Elementor's image controls
 * consume `['id' => int, 'url' => string]` to populate src + srcset.
 * Echoing a URL string would prevent responsive image resolution.
 *
 * `get_value()` is declared `public` here to match Elementor Pro's own
 * built-in image dynamic tags (e.g. Post Featured Image) — the abstract
 * in Data_Tag is protected, but PHP allows widening visibility in
 * subclasses, and the editor's reflection-based discovery seems to
 * prefer public on this method.
 */
class Paradise_CF_Tag_Image extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name(): string  { return 'paradise-cf-image'; }
    public function get_title(): string { return esc_html__( 'Custom Field (Image)', 'paradise-elementor-widgets' ); }
    public function get_group(): string { return Paradise_CF_Tags::GROUP; }

    public function get_categories(): array {
        return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ];
    }

    protected function register_controls(): void {
        Paradise_CF_Tags::add_field_control( $this, 'image' );
    }

    public function get_value( array $options = [] ): array {
        $empty = [ 'id' => 0, 'url' => '' ];
        $key   = (string) $this->get_settings( 'field_key' );
        if ( $key === '' ) {
            return $empty;
        }

        $field = Paradise_Custom_Fields::get_field( $key );
        if ( null === $field ) {
            return $empty;
        }

        $id  = absint( $field['value'] ?? 0 );
        $url = $id ? (string) wp_get_attachment_url( $id ) : '';

        return [
            'id'  => $id,
            'url' => $url,
        ];
    }
}
