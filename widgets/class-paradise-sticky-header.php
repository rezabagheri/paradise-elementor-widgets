<?php
/**
 * Paradise Sticky Header Widget
 *
 * Place this widget inside any Elementor section/container to make it sticky.
 * The widget itself is invisible; it activates sticky behaviour on the parent
 * section and applies optional scroll effects (bg change, shadow, shrink).
 *
 * Supports both classic sections (.elementor-section) and
 * Flexbox containers (.e-con / .e-container).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Controls_Manager;

class Paradise_Sticky_Header_Widget extends \Elementor\Widget_Base {

    public function get_name(): string      { return 'paradise_sticky_header'; }
    public function get_title(): string     { return esc_html__( 'Sticky Header', 'paradise-elementor-widgets' ); }
    public function get_icon(): string      { return 'eicon-header'; }
    public function get_categories(): array { return [ 'paradise' ]; }
    public function get_keywords(): array   { return [ 'sticky', 'header', 'fixed', 'scroll', 'navigation' ]; }

    public function get_style_depends(): array  { return [ 'paradise-sticky-header' ]; }
    public function get_script_depends(): array { return [ 'paradise-sticky-header' ]; }

    // =========================================================================
    // CONTROLS
    // =========================================================================

    protected function register_controls(): void {
        $this->section_behavior();
        $this->section_style_scrolled();
    }

    // ── Content: Behavior ─────────────────────────────────────────────────────

    private function section_behavior(): void {
        $this->start_controls_section( 'section_behavior', [
            'label' => esc_html__( 'Sticky Behavior', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'sticky_notice', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => esc_html__( 'Place this widget inside the section you want to make sticky. The section will stick to the top of the page while scrolling.', 'paradise-elementor-widgets' ),
            'content_classes' => 'elementor-descriptor',
        ] );

        $this->add_control( 'scroll_threshold', [
            'label'       => esc_html__( 'Scroll Threshold (px)', 'paradise-elementor-widgets' ),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 50,
            'min'         => 0,
            'description' => esc_html__( 'Pixels scrolled from top before the shadow and background effects activate.', 'paradise-elementor-widgets' ),
            'separator'   => 'before',
        ] );

        $this->add_control( 'z_index', [
            'label'   => esc_html__( 'Z-Index', 'paradise-elementor-widgets' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 9990,
            'min'     => 1,
        ] );

        $this->end_controls_section();
    }

    // ── Style: Scrolled State ─────────────────────────────────────────────────

    private function section_style_scrolled(): void {
        $this->start_controls_section( 'section_style_scrolled', [
            'label' => esc_html__( 'Scrolled State', 'paradise-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'scrolled_notice', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => esc_html__( 'These styles apply to the parent section once the scroll threshold is reached.', 'paradise-elementor-widgets' ),
            'content_classes' => 'elementor-descriptor',
        ] );

        $this->add_control( 'scrolled_bg', [
            'label'       => esc_html__( 'Background Color', 'paradise-elementor-widgets' ),
            'type'        => Controls_Manager::COLOR,
            'description' => esc_html__( 'Leave empty to keep the section\'s original background.', 'paradise-elementor-widgets' ),
            'separator'   => 'before',
        ] );

        $this->add_control( 'show_shadow', [
            'label'        => esc_html__( 'Drop Shadow', 'paradise-elementor-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'separator'    => 'before',
        ] );

        $this->add_control( 'shadow_color', [
            'label'     => esc_html__( 'Shadow Color', 'paradise-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.12)',
            'condition' => [ 'show_shadow' => 'yes' ],
        ] );

        $this->add_control( 'animation_duration', [
            'label'     => esc_html__( 'Transition Duration (ms)', 'paradise-elementor-widgets' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 300,
            'min'       => 0,
            'max'       => 1000,
            'separator' => 'before',
        ] );

        $this->add_control( 'shrink_enabled', [
            'label'        => esc_html__( 'Shrink on Scroll', 'paradise-elementor-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => '',
            'separator'    => 'before',
        ] );

        $this->add_control( 'scrolled_padding_v', [
            'label'       => esc_html__( 'Scrolled Vertical Padding', 'paradise-elementor-widgets' ),
            'type'        => Controls_Manager::SLIDER,
            'size_units'  => [ 'px' ],
            'range'       => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'default'     => [ 'size' => 8, 'unit' => 'px' ],
            'description' => esc_html__( 'Top and bottom padding of the section when scrolled.', 'paradise-elementor-widgets' ),
            'condition'   => [ 'shrink_enabled' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    protected function render(): void {
        $settings  = $this->get_settings_for_display();
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

        $threshold   = absint( $settings['scroll_threshold'] ?? 50 );
        $z_index     = absint( $settings['z_index'] ?? 9990 );
        $scrolled_bg = $settings['scrolled_bg'] ?? '';
        $show_shadow = 'yes' === ( $settings['show_shadow'] ?? 'yes' );
        $shadow_color = $settings['shadow_color'] ?? 'rgba(0,0,0,0.12)';
        $duration    = absint( $settings['animation_duration'] ?? 300 );
        $shrink      = 'yes' === ( $settings['shrink_enabled'] ?? '' );
        $scrolled_pv = $settings['scrolled_padding_v']['size'] ?? 8;
        $scrolled_pu = $settings['scrolled_padding_v']['unit'] ?? 'px';

        $data = sprintf(
            'data-psh-threshold="%d" data-psh-z="%d" data-psh-bg="%s" data-psh-shadow="%s" data-psh-shadow-color="%s" data-psh-duration="%d" data-psh-shrink="%s" data-psh-pad="%s"',
            $threshold,
            $z_index,
            esc_attr( $scrolled_bg ),
            $show_shadow ? 'yes' : 'no',
            esc_attr( $shadow_color ),
            $duration,
            $shrink ? 'yes' : 'no',
            esc_attr( $scrolled_pv . $scrolled_pu )
        );

        if ( $is_editor ) {
            $data .= ' data-psh-edit="true"';
        }

        ?>
        <div class="paradise-shdr-ctrl" <?php echo $data; ?>></div>

        <?php if ( $is_editor ) : ?>
        <div class="paradise-shdr-editor-notice">
            <svg viewBox="0 0 20 20" width="16" height="16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-5a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0V6a1 1 0 0 1 1-1Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
            <span>
                <strong><?php esc_html_e( 'Sticky Header active', 'paradise-elementor-widgets' ); ?></strong>
                &mdash;
                <?php esc_html_e( 'this section will stick to the top when scrolling.', 'paradise-elementor-widgets' ); ?>
                <?php if ( $shrink ) : ?>
                <?php esc_html_e( 'Shrink effect enabled.', 'paradise-elementor-widgets' ); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>
        <?php
    }
}
