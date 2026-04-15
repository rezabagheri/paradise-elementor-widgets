<?php
/**
 * Paradise Floating Call Button Widget
 *
 * A fixed-position CTA button that stays visible as the user scrolls.
 * Supports tel: and WhatsApp, optional label, pulse animation, and
 * full corner/offset/style customisation.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once PARADISE_EW_DIR . 'includes/trait-paradise-phone-helper.php';

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Paradise_Floating_Call_Btn_Widget extends \Elementor\Widget_Base {

    use Paradise_Phone_Helper;

    public function get_name(): string      { return 'paradise_floating_call_btn'; }
    public function get_title(): string     { return 'Floating Call Button'; }
    public function get_icon(): string      { return 'eicon-call-to-action'; }
    public function get_categories(): array { return [ 'paradise' ]; }
    public function get_keywords(): array   { return [ 'phone', 'float', 'fixed', 'call', 'whatsapp', 'cta', 'sticky' ]; }

    public function get_style_depends(): array {
        return [ 'paradise-floating-call-btn' ];
    }

    // =========================================================================
    // CONTROLS
    // =========================================================================

    protected function register_controls(): void {
        $this->section_phone();
        $this->section_label();
        $this->section_style_position();
        $this->section_style_button();
        $this->section_style_pulse();
        $this->section_style_icon();
    }

    // ── Content: Phone ────────────────────────────────────────────────────────

    private function section_phone(): void {
        $this->start_controls_section( 'section_phone', [
            'label' => esc_html__( 'Phone', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'phone_number', [
            'label'       => esc_html__( 'Phone Number', 'paradise-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '+1 (888) 780-0904',
            'dynamic'     => [ 'active' => true ],
            'label_block' => true,
        ] );

        $this->add_control( 'country_code', [
            'label'   => esc_html__( 'Country Code', 'paradise-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '1',
            'options' => [
                '1'      => '🇺🇸 US (+1)',
                '44'     => '🇬🇧 UK (+44)',
                '49'     => '🇩🇪 DE (+49)',
                '98'     => '🇮🇷 IR (+98)',
                '971'    => '🇦🇪 UAE (+971)',
                'custom' => esc_html__( 'Custom', 'paradise-elementor-widgets' ),
            ],
        ] );

        $this->add_control( 'country_code_custom', [
            'label'     => esc_html__( 'Custom Country Code', 'paradise-elementor-widgets' ),
            'type'      => Controls_Manager::TEXT,
            'placeholder' => '1',
            'description' => esc_html__( 'Digits only, without +', 'paradise-elementor-widgets' ),
            'condition' => [ 'country_code' => 'custom' ],
        ] );

        $this->add_control( 'link_type', [
            'label'     => esc_html__( 'Action', 'paradise-elementor-widgets' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'tel',
            'options'   => [
                'tel'      => esc_html__( 'Phone Call (tel:)', 'paradise-elementor-widgets' ),
                'whatsapp' => esc_html__( 'Open WhatsApp', 'paradise-elementor-widgets' ),
            ],
            'separator' => 'before',
        ] );

        $this->end_controls_section();
    }

    // ── Content: Icon & Label ─────────────────────────────────────────────────

    private function section_label(): void {
        $this->start_controls_section( 'section_label', [
            'label' => esc_html__( 'Icon & Label', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'selected_icon', [
            'label'   => esc_html__( 'Icon', 'paradise-elementor-widgets' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-phone', 'library' => 'fa-solid' ],
        ] );

        $this->add_control( 'show_label', [
            'label'        => esc_html__( 'Show Label', 'paradise-elementor-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => '',
            'separator'    => 'before',
        ] );

        $this->add_control( 'label_text', [
            'label'       => esc_html__( 'Label', 'paradise-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__( 'Call Us', 'paradise-elementor-widgets' ),
            'dynamic'     => [ 'active' => true ],
            'condition'   => [ 'show_label' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    // ── Style: Position ───────────────────────────────────────────────────────

    private function section_style_position(): void {
        $this->start_controls_section( 'section_style_pos', [
            'label' => esc_html__( 'Position', 'paradise-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'corner', [
            'label'        => esc_html__( 'Corner', 'paradise-elementor-widgets' ),
            'type'         => Controls_Manager::CHOOSE,
            'default'      => 'bottom-right',
            'options'      => [
                'bottom-left'  => [ 'title' => esc_html__( 'Bottom Left',  'paradise-elementor-widgets' ), 'icon' => 'eicon-v-align-bottom' ],
                'bottom-right' => [ 'title' => esc_html__( 'Bottom Right', 'paradise-elementor-widgets' ), 'icon' => 'eicon-v-align-bottom' ],
                'top-left'     => [ 'title' => esc_html__( 'Top Left',     'paradise-elementor-widgets' ), 'icon' => 'eicon-v-align-top' ],
                'top-right'    => [ 'title' => esc_html__( 'Top Right',    'paradise-elementor-widgets' ), 'icon' => 'eicon-v-align-top' ],
            ],
            'prefix_class' => 'paradise-fcb-corner-',
        ] );

        $this->add_responsive_control( 'offset_v', [
            'label'      => esc_html__( 'Vertical Offset', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', '%' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
            'default'    => [ 'size' => 24, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-wrap' => '--paradise-fcb-offset-v: {{SIZE}}{{UNIT}};',
            ],
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'offset_h', [
            'label'      => esc_html__( 'Horizontal Offset', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', '%' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
            'default'    => [ 'size' => 24, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-wrap' => '--paradise-fcb-offset-h: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'z_index', [
            'label'     => esc_html__( 'Z-Index', 'paradise-elementor-widgets' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 9999,
            'selectors' => [ '{{WRAPPER}} .paradise-fcb-wrap' => 'z-index: {{VALUE}};' ],
            'separator' => 'before',
        ] );

        $this->end_controls_section();
    }

    // ── Style: Button ─────────────────────────────────────────────────────────

    private function section_style_button(): void {
        $this->start_controls_section( 'section_style_btn', [
            'label' => esc_html__( 'Button', 'paradise-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'btn_size', [
            'label'      => esc_html__( 'Size', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 32, 'max' => 120 ] ],
            'default'    => [ 'size' => 60, 'unit' => 'px' ],
            'selectors'  => [
                // Circle: equal width+height when no label
                '{{WRAPPER}} .paradise-fcb-btn--circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                // Pill: only height; width is auto
                '{{WRAPPER}} .paradise-fcb-btn--pill'   => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'btn_typography',
            'selector'  => '{{WRAPPER}} .paradise-fcb-btn',
            'condition' => [ 'show_label' => 'yes' ],
        ] );

        $this->start_controls_tabs( 'btn_tabs' );

            $this->start_controls_tab( 'btn_tab_normal', [
                'label' => esc_html__( 'Normal', 'paradise-elementor-widgets' ),
            ] );

            $this->add_control( 'btn_bg', [
                'label'     => esc_html__( 'Background', 'paradise-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#2d3e50',
                'selectors' => [ '{{WRAPPER}} .paradise-fcb-btn' => 'background-color: {{VALUE}};' ],
            ] );

            $this->add_control( 'btn_color', [
                'label'     => esc_html__( 'Color', 'paradise-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .paradise-fcb-btn'             => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paradise-fcb-icon svg'        => 'fill: {{VALUE}};',
                ],
            ] );

            $this->end_controls_tab();

            $this->start_controls_tab( 'btn_tab_hover', [
                'label' => esc_html__( 'Hover', 'paradise-elementor-widgets' ),
            ] );

            $this->add_control( 'btn_bg_hover', [
                'label'     => esc_html__( 'Background', 'paradise-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .paradise-fcb-btn:hover' => 'background-color: {{VALUE}};' ],
            ] );

            $this->add_control( 'btn_color_hover', [
                'label'     => esc_html__( 'Color', 'paradise-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .paradise-fcb-btn:hover'      => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paradise-fcb-btn:hover .paradise-fcb-icon svg' => 'fill: {{VALUE}};',
                ],
            ] );

            $this->add_control( 'hover_animation', [
                'label'        => esc_html__( 'Animation', 'paradise-elementor-widgets' ),
                'type'         => Controls_Manager::HOVER_ANIMATION,
                'prefix_class' => 'elementor-animation-',
            ] );

            $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'btn_border_radius', [
            'label'      => esc_html__( 'Border Radius', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%', 'isLinked' => true ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-btn' =>
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'btn_border',
            'selector' => '{{WRAPPER}} .paradise-fcb-btn',
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'btn_shadow',
            'selector' => '{{WRAPPER}} .paradise-fcb-btn',
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label'      => esc_html__( 'Padding', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-btn--pill' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition'  => [ 'show_label' => 'yes' ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();
    }

    // ── Style: Pulse ──────────────────────────────────────────────────────────

    private function section_style_pulse(): void {
        $this->start_controls_section( 'section_style_pulse', [
            'label' => esc_html__( 'Pulse Animation', 'paradise-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'pulse_enabled', [
            'label'        => esc_html__( 'Enable Pulse', 'paradise-elementor-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'pulse_color', [
            'label'     => esc_html__( 'Pulse Color', 'paradise-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d3e50',
            'selectors' => [
                '{{WRAPPER}} .paradise-fcb-btn--pulse' => '--paradise-fcb-pulse-color: {{VALUE}};',
            ],
            'condition' => [ 'pulse_enabled' => 'yes' ],
        ] );

        $this->add_control( 'pulse_size', [
            'label'      => esc_html__( 'Spread', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 4, 'max' => 40 ] ],
            'default'    => [ 'size' => 14, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-btn--pulse' => '--paradise-fcb-pulse-spread: {{SIZE}}px;',
            ],
            'condition'  => [ 'pulse_enabled' => 'yes' ],
        ] );

        $this->add_control( 'pulse_duration', [
            'label'      => esc_html__( 'Speed (seconds)', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 's' ],
            'range'      => [ 's' => [ 'min' => 0.5, 'max' => 4, 'step' => 0.1 ] ],
            'default'    => [ 'size' => 2, 'unit' => 's' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-btn--pulse' => 'animation-duration: {{SIZE}}s;',
            ],
            'condition'  => [ 'pulse_enabled' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    // ── Style: Icon ───────────────────────────────────────────────────────────

    private function section_style_icon(): void {
        $this->start_controls_section( 'section_style_icon', [
            'label' => esc_html__( 'Icon', 'paradise-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label'      => esc_html__( 'Size', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 80 ] ],
            'default'    => [ 'size' => 24, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-fcb-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .paradise-fcb-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'icon_gap', [
            'label'      => esc_html__( 'Gap', 'paradise-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 24 ] ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .paradise-fcb-btn' => 'gap: {{SIZE}}{{UNIT}};' ],
            'condition'  => [ 'show_label' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    protected function render(): void {
        $settings  = $this->get_settings_for_display();
        $raw_phone = trim( $settings['phone_number'] ?? '' );

        if ( empty( $raw_phone ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="color:#cc0000;font-size:13px;">&#9888; Phone number is empty.</p>';
            }
            return;
        }

        $cc        = $this->resolve_country_code( $settings );
        $link_type = $settings['link_type'] ?? 'tel';
        $href      = $this->build_phone_href( $raw_phone, $cc, $link_type );

        // aria-label always uses the raw number for screen readers
        $aria = 'whatsapp' === $link_type
            ? sprintf( esc_html__( 'WhatsApp %s', 'paradise-elementor-widgets' ), $raw_phone )
            : sprintf( esc_html__( 'Call %s', 'paradise-elementor-widgets' ), $raw_phone );

        $show_label  = 'yes' === ( $settings['show_label'] ?? '' );
        $pulse       = 'yes' === ( $settings['pulse_enabled'] ?? 'yes' );
        $target_attr = 'whatsapp' === $link_type ? ' target="_blank" rel="noopener noreferrer"' : '';

        $btn_classes = array_filter( [
            'paradise-fcb-btn',
            $show_label ? 'paradise-fcb-btn--pill' : 'paradise-fcb-btn--circle',
            $pulse      ? 'paradise-fcb-btn--pulse' : '',
        ] );

        // Icon
        $icon_html = '';
        $icon = $settings['selected_icon'] ?? [];
        if ( ! empty( $icon['value'] ) ) {
            ob_start();
            \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
            $icon_html = '<span class="paradise-fcb-icon" aria-hidden="true">' . ob_get_clean() . '</span>';
        }

        ?>
        <div class="paradise-fcb-wrap">
            <a href="<?php echo esc_url( $href ); ?>"
               class="<?php echo esc_attr( implode( ' ', $btn_classes ) ); ?>"
               aria-label="<?php echo esc_attr( $aria ); ?>"<?php echo $target_attr; ?>>
                <?php echo $icon_html; ?>
                <?php if ( $show_label ) : ?>
                <span class="paradise-fcb-label">
                    <?php echo esc_html( $settings['label_text'] ?? '' ); ?>
                </span>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}
