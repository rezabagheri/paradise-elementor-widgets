<?php
/**
 * Paradise Phone Button Widget
 *
 * A fully-styled CTA button that dials a phone number or opens WhatsApp.
 * Supports custom text, auto-formatted number, icon, and complete button styling.
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once PARADISE_EW_DIR . 'includes/trait-paradise-phone-helper.php';

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

class Paradise_Phone_Button_Widget extends \Elementor\Widget_Base
{
    use Paradise_Phone_Helper;

    public function get_name(): string
    {
        return 'paradise_phone_button';
    }
    public function get_title(): string
    {
        return 'Phone Button';
    }
    public function get_icon(): string
    {
        return 'eicon-button';
    }
    public function get_categories(): array
    {
        return [ 'paradise' ];
    }
    public function get_keywords(): array
    {
        return [ 'phone', 'button', 'call', 'cta', 'whatsapp' ];
    }

    public function get_style_depends(): array
    {
        return [ 'paradise-phone-button' ];
    }

    // =========================================================================
    // CONTROLS
    // =========================================================================

    protected function register_controls(): void
    {
        $this->section_phone();
        $this->section_button_text();
        $this->section_icon();
        $this->section_style_button();
        $this->section_style_icon();
    }

    // ── Content: Phone ────────────────────────────────────────────────────────

    private function section_phone(): void
    {
        $this->start_controls_section('section_phone', [
            'label' => esc_html__('Phone', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('phone_number', [
            'label'       => esc_html__('Phone Number', 'paradise-elementor-widgets'),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '+1 (888) 780-0904',
            'description' => esc_html__('Accepts any format — normalized automatically.', 'paradise-elementor-widgets'),
            'dynamic'     => [ 'active' => true ],
            'label_block' => true,
        ]);

        $this->add_control('country_code', [
            'label'   => esc_html__('Country Code', 'paradise-elementor-widgets'),
            'type'    => Controls_Manager::SELECT,
            'default' => '1',
            'options' => [
                '1'      => '🇺🇸 US (+1)',
                '44'     => '🇬🇧 UK (+44)',
                '49'     => '🇩🇪 DE (+49)',
                '98'     => '🇮🇷 IR (+98)',
                '971'    => '🇦🇪 UAE (+971)',
                'custom' => esc_html__('Custom', 'paradise-elementor-widgets'),
            ],
        ]);

        $this->add_control('country_code_custom', [
            'label'       => esc_html__('Custom Country Code', 'paradise-elementor-widgets'),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '1',
            'description' => esc_html__('Digits only, without +', 'paradise-elementor-widgets'),
            'condition'   => [ 'country_code' => 'custom' ],
        ]);

        $this->add_control('link_type', [
            'label'     => esc_html__('Action', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'tel',
            'options'   => [
                'tel'      => esc_html__('Phone Call (tel:)', 'paradise-elementor-widgets'),
                'whatsapp' => esc_html__('Open WhatsApp', 'paradise-elementor-widgets'),
            ],
            'separator' => 'before',
        ]);

        $this->end_controls_section();
    }

    // ── Content: Button Text ─────────────────────────────────────────────────

    private function section_button_text(): void
    {
        $this->start_controls_section('section_text', [
            'label' => esc_html__('Button Text', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('text_mode', [
            'label'   => esc_html__('Text Mode', 'paradise-elementor-widgets'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => [
                'auto'   => esc_html__('Prefix + Number', 'paradise-elementor-widgets'),
                'custom' => esc_html__('Custom Text', 'paradise-elementor-widgets'),
            ],
        ]);

        // ── Auto mode ──

        $this->add_control('text_prefix', [
            'label'       => esc_html__('Prefix', 'paradise-elementor-widgets'),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__('CALL ', 'paradise-elementor-widgets'),
            'placeholder' => 'CALL ',
            'description' => esc_html__('Text shown before the number (e.g. "CALL ", "TEL: ").', 'paradise-elementor-widgets'),
            'condition'   => [ 'text_mode' => 'auto' ],
        ]);

        $this->add_control('display_format', [
            'label'   => esc_html__('Number Format', 'paradise-elementor-widgets'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'local',
            'options' => [
                'raw'           => esc_html__('Raw (as entered)', 'paradise-elementor-widgets'),
                'local'         => esc_html__('Local  e.g. (888) 780-0904', 'paradise-elementor-widgets'),
                'international' => esc_html__('International  e.g. +1 888 780 0904', 'paradise-elementor-widgets'),
                'dashes'        => esc_html__('Dashes  e.g. 888-780-0904', 'paradise-elementor-widgets'),
                'dots'          => esc_html__('Dots  e.g. 888.780.0904', 'paradise-elementor-widgets'),
                'custom_mask'   => esc_html__('Custom Mask', 'paradise-elementor-widgets'),
            ],
            'condition' => [ 'text_mode' => 'auto' ],
        ]);

        $this->add_control('custom_mask', [
            'label'       => esc_html__('Custom Mask', 'paradise-elementor-widgets'),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '(###) ###-####',
            'description' => esc_html__('Use # for each digit.', 'paradise-elementor-widgets'),
            'label_block' => true,
            'condition'   => [ 'text_mode' => 'auto', 'display_format' => 'custom_mask' ],
        ]);

        // ── Custom mode ──

        $this->add_control('button_text', [
            'label'       => esc_html__('Button Text', 'paradise-elementor-widgets'),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__('Call Now', 'paradise-elementor-widgets'),
            'dynamic'     => [ 'active' => true ],
            'label_block' => true,
            'condition'   => [ 'text_mode' => 'custom' ],
        ]);

        $this->end_controls_section();
    }

    // ── Content: Icon ────────────────────────────────────────────────────────

    private function section_icon(): void
    {
        $this->start_controls_section('section_icon', [
            'label' => esc_html__('Icon', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('selected_icon', [
            'label'   => esc_html__('Icon', 'paradise-elementor-widgets'),
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'eicon-phone', 'library' => 'elementor-icons' ],
        ]);

        $this->add_control('icon_position', [
            'label'     => esc_html__('Position', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::CHOOSE,
            'default'   => 'before',
            'options'   => [
                'before' => [ 'title' => esc_html__('Before', 'paradise-elementor-widgets'), 'icon' => 'eicon-h-align-left' ],
                'after'  => [ 'title' => esc_html__('After', 'paradise-elementor-widgets'), 'icon' => 'eicon-h-align-right' ],
            ],
            'condition' => [ 'selected_icon[value]!' => '' ],
        ]);

        $this->end_controls_section();
    }

    // ── Style: Button ─────────────────────────────────────────────────────────

    private function section_style_button(): void
    {
        $this->start_controls_section('section_style_btn', [
            'label' => esc_html__('Button', 'paradise-elementor-widgets'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        // ── Layout ────────────────────────────────────

        $this->add_responsive_control('align', [
            'label'     => esc_html__('Alignment', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => esc_html__('Left', 'paradise-elementor-widgets'), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => esc_html__('Center', 'paradise-elementor-widgets'), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => esc_html__('Right', 'paradise-elementor-widgets'), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .paradise-pbn-wrapper' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_width', [
            'label'        => esc_html__('Full Width', 'paradise-elementor-widgets'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'prefix_class' => 'paradise-pbn-full-',
            'selectors'    => [
                '{{WRAPPER}}.paradise-pbn-full-yes .paradise-pbn-btn' => 'width: 100%; justify-content: center;',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => '{{WRAPPER}} .paradise-pbn-btn',
            'separator' => 'before',
        ]);

        // ── Normal / Hover tabs ────────────────────────

        $this->start_controls_tabs('btn_color_tabs');

        $this->start_controls_tab('tab_normal', [
            'label' => esc_html__('Normal', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('btn_bg_color', [
            'label'     => esc_html__('Background', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d3e50',
            'selectors' => [ '{{WRAPPER}} .paradise-pbn-btn' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control('btn_text_color', [
            'label'     => esc_html__('Text Color', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .paradise-pbn-btn' => 'color: {{VALUE}};' ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_hover', [
            'label' => esc_html__('Hover', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('btn_bg_hover_color', [
            'label'     => esc_html__('Background', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .paradise-pbn-btn:hover' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control('btn_text_hover_color', [
            'label'     => esc_html__('Text Color', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .paradise-pbn-btn:hover' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control('hover_animation', [
            'label' => esc_html__('Hover Animation', 'paradise-elementor-widgets'),
            'type'  => Controls_Manager::HOVER_ANIMATION,
            'prefix_class' => 'elementor-animation-',
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // ── Shape ─────────────────────────────────────

        $this->add_responsive_control('btn_padding', [
            'label'      => esc_html__('Padding', 'paradise-elementor-widgets'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', 'rem', '%' ],
            'default'    => [ 'top' => 12, 'right' => 28, 'bottom' => 12, 'left' => 28, 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-pbn-btn' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator'  => 'before',
        ]);

        $this->add_responsive_control('btn_border_radius', [
            'label'      => esc_html__('Border Radius', 'paradise-elementor-widgets'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => 'px', 'isLinked' => true ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-pbn-btn' =>
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'btn_border',
            'selector' => '{{WRAPPER}} .paradise-pbn-btn',
        ]);

        $this->add_control('btn_border_hover_color', [
            'label'     => esc_html__('Border Hover Color', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .paradise-pbn-btn:hover' => 'border-color: {{VALUE}};' ],
            'condition' => [ 'btn_border_border!' => '' ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'btn_box_shadow',
            'selector' => '{{WRAPPER}} .paradise-pbn-btn',
        ]);

        $this->add_control('btn_transition', [
            'label'      => esc_html__('Transition Duration (ms)', 'paradise-elementor-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'range'      => [ 'ms' => [ 'min' => 0, 'max' => 1000 ] ],
            'default'    => [ 'unit' => 'ms', 'size' => 250 ],
            'size_units' => [ 'ms' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-pbn-btn' => 'transition-duration: {{SIZE}}ms;',
            ],
            'separator' => 'before',
        ]);

        $this->end_controls_section();
    }

    // ── Style: Icon ───────────────────────────────────────────────────────────

    private function section_style_icon(): void
    {
        $this->start_controls_section('section_style_icon', [
            'label'     => esc_html__('Icon', 'paradise-elementor-widgets'),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'selected_icon[value]!' => '' ],
        ]);

        $this->add_responsive_control('icon_size', [
            'label'      => esc_html__('Size', 'paradise-elementor-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 6, 'max' => 80 ] ],
            'default'    => [ 'unit' => 'em', 'size' => 1 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-pbn-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .paradise-pbn-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('icon_gap', [
            'label'      => esc_html__('Gap', 'paradise-elementor-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 8 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-pbn-btn' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->start_controls_tabs('icon_color_tabs');

        $this->start_controls_tab('icon_tab_normal', [
            'label' => esc_html__('Normal', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('icon_color', [
            'label'     => esc_html__('Color', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-pbn-icon i'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .paradise-pbn-icon svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('icon_tab_hover', [
            'label' => esc_html__('Hover', 'paradise-elementor-widgets'),
        ]);

        $this->add_control('icon_hover_color', [
            'label'     => esc_html__('Color', 'paradise-elementor-widgets'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-pbn-btn:hover .paradise-pbn-icon i'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .paradise-pbn-btn:hover .paradise-pbn-icon svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    protected function render(): void
    {
        $settings  = $this->get_settings_for_display();
        $raw_phone = trim($settings['phone_number'] ?? '');

        if (empty($raw_phone)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<p style="color:#cc0000;font-size:13px;">&#9888; Phone number is empty.</p>';
            }
            return;
        }

        $cc        = $this->resolve_country_code($settings);
        $link_type = $settings['link_type'] ?? 'tel';
        $href      = $this->build_phone_href($raw_phone, $cc, $link_type);

        // Button label
        if ('custom' === ($settings['text_mode'] ?? 'auto')) {
            $label = trim($settings['button_text'] ?? '') ?: esc_html__('Call Now', 'paradise-elementor-widgets');
        } else {
            $prefix    = $settings['text_prefix'] ?? 'CALL ';
            $formatted = $this->format_phone_display($raw_phone, $settings);
            $label     = $prefix . $formatted;
        }

        // aria-label
        $aria = 'whatsapp' === $link_type
            ? sprintf( /* translators: %s = phone number */ esc_html__('WhatsApp %s', 'paradise-elementor-widgets'), $label)
            : sprintf( /* translators: %s = phone number */ esc_html__('Call %s', 'paradise-elementor-widgets'), $label);

        // Icon
        $icon_html = '';
        $icon      = $settings['selected_icon'] ?? [];
        if (! empty($icon['value'])) {
            ob_start();
            \Elementor\Icons_Manager::render_icon($icon, [ 'aria-hidden' => 'true' ]);
            $icon_html = '<span class="paradise-pbn-icon" aria-hidden="true">' . ob_get_clean() . '</span>';
        }

        $icon_pos   = $settings['icon_position'] ?? 'before';
        $target_attr = 'whatsapp' === $link_type ? ' target="_blank" rel="noopener noreferrer"' : '';

        ?>
        <div class="paradise-pbn-wrapper">
            <a href="<?php echo esc_url($href); ?>"
               class="paradise-pbn-btn"
               aria-label="<?php echo esc_attr($aria); ?>"<?php echo $target_attr; ?>>
                <?php if ($icon_html && 'before' === $icon_pos) : ?>
                    <?php echo $icon_html; ?>
                <?php endif; ?>
                <span class="paradise-pbn-text"><?php echo esc_html($label); ?></span>
                <?php if ($icon_html && 'after' === $icon_pos) : ?>
                    <?php echo $icon_html; ?>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}
