<?php
/**
 * Paradise Bottom Navigation Bar Widget
 *
 * Backward-compatible with Paradise_Bottom_Nav_Widget v1.x:
 *  - get_name()  → 'paradise_bottom_nav'  (changed from 'paradise_bottom_nav')
 *  - CSS handle  → 'paradise-bn-bottom-nav-style'       (changed from 'paradise-bn-style')
 *  - JS  handle  → 'paradise-bn-bottom-nav-script'      (changed from 'paradise-bn-script')
 *  - All original control IDs preserved
 *
 * v2.1 additions:
 *  - Elementor responsive visibility (replaces custom breakpoint controls)
 *  - Editor preview: pixel-perfect fixed positioning inside iframe
 *  - Speed dial auto-open in editor
 *  - Clicks disabled in editor (standard Elementor behavior)
 */

if (! defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class Paradise_Bottom_Nav_Widget extends Widget_Base
{
    // ═══════════════════════════════════════════════════════════════
    // IDENTITY
    // ═══════════════════════════════════════════════════════════════

    public function get_name(): string
    {
        return 'paradise_bottom_nav';
    }
    public function get_title(): string
    {
        return 'Bottom Navigation Bar';
    }
    public function get_icon(): string
    {
        return 'eicon-nav-menu';
    }
    public function get_categories(): array
    {
        return [ 'paradise' ];
    }
    public function get_keywords(): array
    {
        return [ 'bottom', 'nav', 'mobile', 'menu', 'navigation', 'bar', 'tab' ];
    }

    public function get_style_depends(): array
    {
        return [
            'paradise-bn-bottom-nav-style',
            'elementor-icons-fa-solid',
            'elementor-icons-fa-brands',
            'elementor-icons-fa-regular',
        ];
    }

    public function get_script_depends(): array
    {
        return [ 'paradise-bn-bottom-nav-script' ];
    }

    // ═══════════════════════════════════════════════════════════════
    // CONTROLS
    // ═══════════════════════════════════════════════════════════════

    protected function register_controls(): void
    {
        $this->section_source();
        $this->section_wp_menu();
        $this->section_repeater();
        $this->section_visibility();
        $this->section_center_button();
        $this->section_active_state();

        $this->section_style_bar();
        $this->section_style_items();
        $this->section_style_indicator();
        $this->section_style_badge();
        $this->section_style_center();
        $this->section_style_animation();
    }

    // ───────────────────────────────────────────────────────────────
    // CONTENT: Source
    // ───────────────────────────────────────────────────────────────

    private function section_source(): void
    {
        $this->start_controls_section('sec_source', [
            'label' => 'Items Source',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('items_source', [
            'label'   => 'Source',
            'type'    => Controls_Manager::SELECT,
            'default' => 'repeater',
            'options' => [
                'repeater' => 'Manual (Repeater)',
                'wp_menu'  => 'WordPress Menu',
            ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // CONTENT: WordPress Menu
    // ───────────────────────────────────────────────────────────────

    private function section_wp_menu(): void
    {
        $this->start_controls_section('sec_wp_menu', [
            'label'     => 'WordPress Menu',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'items_source' => 'wp_menu' ],
        ]);

        $this->add_control('wp_menu_slug', [
            'label'       => 'Select Menu',
            'type'        => Controls_Manager::SELECT,
            'options'     => $this->get_wp_menus(),
            'default'     => '',
            'description' => 'Create menus via Appearance → Menus.',
        ]);

        $this->add_control('wp_menu_icons_note', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => '<small style="opacity:.7">Add icons via the <strong>CSS Classes</strong> field on each menu item (<em>Screen Options → CSS Classes</em>).<br>Example: <code>fas fa-home</code></small>',
            'content_classes' => 'elementor-descriptor',
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // CONTENT: Manual Repeater
    // ───────────────────────────────────────────────────────────────

    private function section_repeater(): void
    {
        $this->start_controls_section('sec_repeater', [
            'label'     => 'Menu Items',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'items_source' => 'repeater' ],
        ]);

        $repeater = new Repeater();

        $repeater->add_control('item_label', [
            'label'       => 'Label',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Home',
            'label_block' => true,
        ]);

        $repeater->add_control('item_icon', [
            'label'   => 'Icon',
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-home', 'library' => 'fa-solid' ],
        ]);

        $repeater->add_control('item_url', [
            'label'       => 'Link',
            'type'        => Controls_Manager::URL,
            'placeholder' => site_url('/'),
            'default'     => [ 'url' => '' ],
        ]);

        // ── Badge ──────────────────────────────────────────────────
        $repeater->add_control('badge_enabled', [
            'label'        => 'Badge',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'no',
            'separator'    => 'before',
        ]);

        $repeater->add_control('badge_source', [
            'label'     => 'Badge Value Source',
            'type'      => Controls_Manager::SELECT,
            'default'   => 'static',
            'options'   => [
                'static' => 'Static Number',
                'woo'    => 'WooCommerce Cart',
                'js'     => 'JS-driven (Paradise.setBadge)',
            ],
            'condition' => [ 'badge_enabled' => 'yes' ],
        ]);

        $repeater->add_control('badge_value', [
            'label'     => 'Badge Number',
            'type'      => Controls_Manager::NUMBER,
            'default'   => 1,
            'min'       => 0,
            'max'       => 99,
            'condition' => [
                'badge_enabled' => 'yes',
                'badge_source'  => 'static',
            ],
        ]);

        $repeater->add_control('badge_hide_zero', [
            'label'        => 'Hide When Zero',
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [ 'badge_enabled' => 'yes' ],
        ]);

        $repeater->add_control('item_custom_id', [
            'label'       => 'CSS ID',
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'e.g. nav-home',
            'description' => 'For CSS/JS targeting and JS-driven badge.',
            'separator'   => 'before',
        ]);

        $this->add_control('nav_items', [
            'label'       => 'Items',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'item_label' => 'Home',    'item_icon' => [ 'value' => 'fas fa-home',        'library' => 'fa-solid' ], 'item_url' => [ 'url' => '' ] ],
                [ 'item_label' => 'Search',  'item_icon' => [ 'value' => 'fas fa-search',      'library' => 'fa-solid' ], 'item_url' => [ 'url' => '' ] ],
                [ 'item_label' => 'About',   'item_icon' => [ 'value' => 'fas fa-info-circle', 'library' => 'fa-solid' ], 'item_url' => [ 'url' => '' ] ],
                [ 'item_label' => 'Contact', 'item_icon' => [ 'value' => 'fas fa-envelope',    'library' => 'fa-solid' ], 'item_url' => [ 'url' => '' ] ],
            ],
            'title_field' => '{{{ item_label }}}',
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // CONTENT: Visibility
    // Uses Elementor's native responsive system so editor preview,
    // responsive mode, and frontend all behave identically.
    // ───────────────────────────────────────────────────────────────

    private function section_visibility(): void
    {
        $this->start_controls_section('sec_visibility', [
            'label' => 'Visibility & Labels',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_labels', [
            'label'        => 'Show Labels',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        // ── Responsive visibility ──────────────────────────────────
        // Elementor writes the media-query CSS automatically.
        // The widget is hidden by default (CSS: display:none).
        // This control overrides display per breakpoint — exactly
        // like the native "Hide on Device" control in Elementor Pro.
        $this->add_control('vis_heading', [
            'label'     => 'Show Bar On',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control('bar_display', [
            'label'     => 'Display',
            'type'      => Controls_Manager::SELECT,
            'options'   => [
                'none'  => 'Hidden',
                'block' => 'Visible',
            ],
            // Default per breakpoint:
            // desktop → hidden (none), tablet → visible, mobile → visible
            'default'          => 'none',
            'tablet_default'   => 'block',
            'mobile_default'   => 'block',
            'selectors'        => [
                '{{WRAPPER}} .paradise-bn-wrapper' => 'display: {{VALUE}};',
            ],
            // When hidden, also remove body padding
            'render_type'      => 'template',
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // CONTENT: Center Button
    // ───────────────────────────────────────────────────────────────

    private function section_center_button(): void
    {
        $this->start_controls_section('sec_center', [
            'label' => 'Center Button (Floating)',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('center_enabled', [
            'label'        => 'Enable Center Button',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control('center_label', [
            'label'     => 'Label',
            'type'      => Controls_Manager::TEXT,
            'default'   => 'Menu',
            'condition' => [ 'center_enabled' => 'yes' ],
        ]);

        $this->add_control('center_icon', [
            'label'     => 'Icon',
            'type'      => Controls_Manager::ICONS,
            'default'   => [ 'value' => 'fas fa-plus', 'library' => 'fa-solid' ],
            'condition' => [ 'center_enabled' => 'yes' ],
        ]);

        $this->add_control('center_action', [
            'label'     => 'Button Action',
            'type'      => Controls_Manager::SELECT,
            'default'   => 'link',
            'options'   => [
                'link'       => 'Simple Link',
                'speed_dial' => 'Speed Dial',
                'js_hook'    => 'JS Hook',
            ],
            'separator' => 'before',
            'condition' => [ 'center_enabled' => 'yes' ],
        ]);

        $this->add_control('center_url', [
            'label'     => 'Link',
            'type'      => Controls_Manager::URL,
            'default'   => [ 'url' => '' ],
            'condition' => [ 'center_enabled' => 'yes', 'center_action' => 'link' ],
        ]);

        $this->add_control('center_js_hook', [
            'label'       => 'Hook Name',
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'e.g. openCart',
            'description' => 'Fires: <code>ebn:hook:openCart</code> on document.',
            'condition'   => [ 'center_enabled' => 'yes', 'center_action' => 'js_hook' ],
        ]);

        $this->add_control('speed_dial_note', [
            'type'      => Controls_Manager::RAW_HTML,
            'raw'       => '<small style="opacity:.7">Items shown (bottom→top) when center button is tapped.</small>',
            'condition' => [ 'center_enabled' => 'yes', 'center_action' => 'speed_dial' ],
        ]);

        $dial_repeater = new Repeater();
        $dial_repeater->add_control('dial_label', [ 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Item' ]);
        $dial_repeater->add_control('dial_icon', [ 'label' => 'Icon',  'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-star', 'library' => 'fa-solid' ] ]);
        $dial_repeater->add_control('dial_url', [ 'label' => 'Link',  'type' => Controls_Manager::URL,   'default' => [ 'url' => '' ] ]);

        $this->add_control('speed_dial_items', [
            'label'       => 'Speed Dial Items',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $dial_repeater->get_controls(),
            'default'     => [
                [ 'dial_label' => 'Call',     'dial_icon' => [ 'value' => 'fas fa-phone',    'library' => 'fa-solid'  ], 'dial_url' => [ 'url' => '' ] ],
                [ 'dial_label' => 'WhatsApp', 'dial_icon' => [ 'value' => 'fab fa-whatsapp', 'library' => 'fa-brands' ], 'dial_url' => [ 'url' => '' ] ],
            ],
            'title_field' => '{{{ dial_label }}}',
            'condition'   => [ 'center_enabled' => 'yes', 'center_action' => 'speed_dial' ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // CONTENT: Active State
    // ───────────────────────────────────────────────────────────────

    private function section_active_state(): void
    {
        $this->start_controls_section('sec_active', [
            'label' => 'Active State',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('active_detection', [
            'label'   => 'Detection Method',
            'type'    => Controls_Manager::SELECT,
            'default' => 'both',
            'options' => [
                'url'    => 'URL Match Only',
                'manual' => 'Manual Only',
                'both'   => 'URL Match + Manual Fallback',
            ],
        ]);

        $this->add_control('active_manual_index', [
            'label'       => 'Default Active Item',
            'type'        => Controls_Manager::NUMBER,
            'default'     => 1,
            'min'         => 1,
            'max'         => 10,
            'description' => 'Item number (1 = first). Used when no URL match is found.',
            'condition'   => [ 'active_detection' => [ 'manual', 'both' ] ],
        ]);

        $this->add_control('active_match_mode', [
            'label'     => 'URL Match Mode',
            'type'      => Controls_Manager::SELECT,
            'default'   => 'pathname',
            'options'   => [
                'pathname' => 'Pathname Only (ignore query & hash)',
                'full'     => 'Full URL (include query string)',
            ],
            'condition' => [ 'active_detection' => [ 'url', 'both' ] ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // STYLE: Bar
    // ───────────────────────────────────────────────────────────────

    private function section_style_bar(): void
    {
        $this->start_controls_section('sec_style_bar', [
            'label' => 'Bar',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('bar_position', [
            'label'   => 'Bar Position',
            'type'    => Controls_Manager::SELECT,
            'default' => 'full',
            'options' => [
                'full'     => 'Full Width',
                'floating' => 'Floating / Centered',
            ],
        ]);

        $this->add_control('bar_max_width', [
            'label'      => 'Max Width',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 280, 'max' => 800 ], '%' => [ 'min' => 40, 'max' => 100 ] ],
            'default'    => [ 'unit' => '%', 'size' => 92 ],
            'selectors'  => [ '{{WRAPPER}} .paradise-bn-wrapper' => 'max-width: {{SIZE}}{{UNIT}};' ],
            'condition'  => [ 'bar_position' => 'floating' ],
        ]);

        $this->add_control('bar_bottom_offset', [
            'label'      => 'Bottom Offset',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 16 ],
            'selectors'  => [ '{{WRAPPER}} .paradise-bn-wrapper' => 'bottom: {{SIZE}}px;' ],
            'condition'  => [ 'bar_position' => 'floating' ],
        ]);

        $this->add_control('bar_bg', [
            'label'     => 'Background Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .paradise-bn-bar' => 'background-color: {{VALUE}};' ],
            'separator' => 'before',
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'bar_border',
            'selector' => '{{WRAPPER}} .paradise-bn-bar',
            'fields_options' => [
                'border' => [ 'default' => 'solid' ],
                'width'  => [ 'default' => [ 'top' => '1', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ] ],
                'color'  => [ 'default' => '#e8ecf0' ],
            ],
        ]);

        $this->add_control('bar_radius', [
            'label'      => 'Border Radius',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-bn-bar'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .paradise-bn-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'bar_shadow',
            'selector' => '{{WRAPPER}} .paradise-bn-bar',
            'fields_options' => [
                'box_shadow_type' => [ 'default' => 'yes' ],
                'box_shadow'      => [ 'default' => [ 'horizontal' => 0, 'vertical' => -4, 'blur' => 16, 'spread' => 0, 'color' => 'rgba(0,0,0,0.07)' ] ],
            ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // STYLE: Items
    // ───────────────────────────────────────────────────────────────

    private function section_style_items(): void
    {
        $this->start_controls_section('sec_style_items', [
            'label' => 'Items',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('icon_size', [
            'label'      => 'Icon Size',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 14, 'max' => 36 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 22 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-bn-item i'   => 'font-size: {{SIZE}}px;',
                '{{WRAPPER}} .paradise-bn-item svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ],
        ]);

        $this->add_control('icon_label_gap', [
            'label'      => 'Icon / Label Gap',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 12 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 4 ],
            'selectors'  => [ '{{WRAPPER}} .paradise-bn-item' => 'gap: {{SIZE}}px;' ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'label_typography',
            'label'    => 'Label Typography',
            'selector' => '{{WRAPPER}} .paradise-bn-label',
            'fields_options' => [
                'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 10 ] ],
                'font_weight' => [ 'default' => '700' ],
            ],
        ]);

        $this->start_controls_tabs('tabs_item_colors');

        $this->start_controls_tab('tab_item_normal', [ 'label' => 'Normal' ]);
        $this->add_control('icon_color', [
            'label'     => 'Icon Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#b0bec5',
            'selectors' => [
                '{{WRAPPER}} .paradise-bn-item:not(.paradise-bn-item--active) i'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .paradise-bn-item:not(.paradise-bn-item--active) svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
            ],
        ]);
        $this->add_control('label_color', [
            'label'     => 'Label Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#b0bec5',
            'selectors' => [ '{{WRAPPER}} .paradise-bn-item:not(.paradise-bn-item--active) .paradise-bn-label' => 'color: {{VALUE}};' ],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab('tab_item_active', [ 'label' => 'Active' ]);
        $this->add_control('icon_color_active', [
            'label'     => 'Icon Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#F5A623',
            'selectors' => [
                '{{WRAPPER}} .paradise-bn-item--active i'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .paradise-bn-item--active svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
            ],
        ]);
        $this->add_control('label_color_active', [
            'label'     => 'Label Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#F5A623',
            'selectors' => [ '{{WRAPPER}} .paradise-bn-item--active .paradise-bn-label' => 'color: {{VALUE}};' ],
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // STYLE: Active Indicator
    // ───────────────────────────────────────────────────────────────

    private function section_style_indicator(): void
    {
        $this->start_controls_section('sec_style_indicator', [
            'label' => 'Active Indicator',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('indicator_style', [
            'label'   => 'Style',
            'type'    => Controls_Manager::SELECT,
            'default' => 'top_bar',
            'options' => [
                'none'    => 'None',
                'top_bar' => 'Top Bar (sliding)',
                'bot_bar' => 'Bottom Bar (sliding)',
                'dot'     => 'Dot (below icon)',
                'pill'    => 'Pill Background',
                'glow'    => 'Glow (icon shadow)',
            ],
        ]);

        $this->add_control('indicator_color', [
            'label'     => 'Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#F5A623',
            'selectors' => [
                '{{WRAPPER}} .paradise-bn-indicator'                    => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .paradise-bn-item--active .paradise-bn-dot'       => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .paradise-bn-item--active.paradise-bn-pill'       => 'background-color: {{VALUE}}1a;',
                '{{WRAPPER}} .paradise-bn-item--active .paradise-bn-item-icon' => 'filter: drop-shadow(0 0 6px {{VALUE}});',
            ],
            'condition' => [ 'indicator_style!' => 'none' ],
        ]);

        $this->add_control('indicator_size', [
            'label'      => 'Size',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 2, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 24 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-bn-indicator' => 'width: {{SIZE}}px;',
                '{{WRAPPER}} .paradise-bn-dot'       => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ],
            'condition'  => [ 'indicator_style' => [ 'top_bar', 'bot_bar', 'dot' ] ],
        ]);

        $this->add_control('indicator_animated', [
            'label'        => 'Animated (slide)',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [ 'indicator_style' => [ 'top_bar', 'bot_bar' ] ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // STYLE: Badge
    // ───────────────────────────────────────────────────────────────

    private function section_style_badge(): void
    {
        $this->start_controls_section('sec_style_badge', [
            'label' => 'Badge',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('badge_bg', [
            'label'     => 'Background Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#e53935',
            'selectors' => [ '{{WRAPPER}} .paradise-bn-badge' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control('badge_text_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .paradise-bn-badge' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control('badge_size', [
            'label'      => 'Size',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 12, 'max' => 24 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 16 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-bn-badge' => 'min-width: {{SIZE}}px; height: {{SIZE}}px; font-size: calc({{SIZE}}px * 0.625);',
            ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // STYLE: Center Button
    // ───────────────────────────────────────────────────────────────

    private function section_style_center(): void
    {
        $this->start_controls_section('sec_style_center', [
            'label'     => 'Center Button',
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'center_enabled' => 'yes' ],
        ]);

        $this->add_control('center_size', [
            'label'      => 'Button Size',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 44, 'max' => 80 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 56 ],
            'selectors'  => [ '{{WRAPPER}} .paradise-bn-center-btn' => 'width: {{SIZE}}px; height: {{SIZE}}px;' ],
        ]);

        $this->add_control('center_bg', [
            'label'     => 'Background Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#F5A623',
            'selectors' => [ '{{WRAPPER}} .paradise-bn-center-btn' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control('center_icon_color', [
            'label'     => 'Icon Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .paradise-bn-center-btn i'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .paradise-bn-center-btn svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
            ],
        ]);

        $this->add_control('center_icon_size', [
            'label'      => 'Icon Size',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 14, 'max' => 36 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 22 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-bn-center-btn i'   => 'font-size: {{SIZE}}px;',
                '{{WRAPPER}} .paradise-bn-center-btn svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ],
        ]);

        $this->add_control('center_lift', [
            'label'      => 'Lift (above bar)',
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 18 ],
            'selectors'  => [ '{{WRAPPER}} .paradise-bn-center-wrap' => 'transform: translateY(-{{SIZE}}px);' ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'center_shadow',
            'selector' => '{{WRAPPER}} .paradise-bn-center-btn',
            'fields_options' => [
                'box_shadow_type' => [ 'default' => 'yes' ],
                'box_shadow'      => [ 'default' => [ 'horizontal' => 0, 'vertical' => 4, 'blur' => 18, 'spread' => 0, 'color' => 'rgba(245,166,35,0.5)' ] ],
            ],
        ]);

        $this->end_controls_section();
    }

    // ───────────────────────────────────────────────────────────────
    // STYLE: Entrance Animation
    // ───────────────────────────────────────────────────────────────

    private function section_style_animation(): void
    {
        $this->start_controls_section('sec_style_animation', [
            'label' => 'Entrance Animation',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('anim_enabled', [
            'label'        => 'Enable',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control('anim_style', [
            'label'     => 'Style',
            'type'      => Controls_Manager::SELECT,
            'default'   => 'slide_up',
            'options'   => [ 'slide_up' => 'Slide Up', 'fade' => 'Fade In', 'both' => 'Slide Up + Fade' ],
            'condition' => [ 'anim_enabled' => 'yes' ],
        ]);

        $this->add_control('anim_duration', [
            'label'     => 'Duration (ms)',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 100, 'max' => 1000, 'step' => 50 ] ],
            'default'   => [ 'size' => 350 ],
            'condition' => [ 'anim_enabled' => 'yes' ],
        ]);

        $this->end_controls_section();
    }

    // ═══════════════════════════════════════════════════════════════
    // RENDER
    // ═══════════════════════════════════════════════════════════════

    protected function render(): void
    {
        $s          = $this->get_settings_for_display();
        $is_edit    = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_rtl     = is_rtl();
        $show_labels = ($s['show_labels']    ?? 'yes') === 'yes';
        $center_on   = ($s['center_enabled'] ?? 'yes') === 'yes';
        $source      = $s['items_source']  ?? 'repeater';
        $center_act  = $s['center_action'] ?? 'link';
        $bar_pos     = $s['bar_position']  ?? 'full';

        $current_url = esc_url_raw(
            (is_ssl() ? 'https' : 'http') . '://'
            . sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST']   ?? ''))
            . sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''))
        );

        // ── JS config ─────────────────────────────────────────────
        $data = [
            'isEditMode'  => $is_edit,
            'detection'   => $s['active_detection']   ?? 'both',
            'matchMode'   => $s['active_match_mode']  ?? 'pathname',
            'manualIndex' => (int) ($s['active_manual_index'] ?? 1),
            'indicator'   => $s['indicator_style']    ?? 'top_bar',
            'animated'    => ($s['indicator_animated'] ?? 'yes') === 'yes',
            'barPos'      => $bar_pos,
            'animEnabled' => ($s['anim_enabled'] ?? 'yes') === 'yes',
            'animStyle'   => $s['anim_style']    ?? 'slide_up',
            'animDuration' => (int) ($s['anim_duration']['size'] ?? 350),
            // Speed dial open by default in editor so users can see items
            'editorDialOpen' => $is_edit && $center_on && $center_act === 'speed_dial',
            // Responsive visibility — mirrors add_responsive_control('bar_display')
            // defaults: desktop hidden, tablet+mobile visible
            'showOnMobile'  => ( $s['bar_display_mobile']  ?? 'block' ) === 'block',
            'showOnTablet'  => ( $s['bar_display_tablet']  ?? 'block' ) === 'block',
            'showOnDesktop' => ( $s['bar_display']          ?? 'none'  ) === 'block',
        ];

        // ── Wrapper classes ────────────────────────────────────────
        $wrapper_classes = implode(' ', array_filter([
            'paradise-bn-wrapper',
            'paradise-bn-pos-' . esc_attr($bar_pos),
            $is_edit ? 'paradise-bn-is-editor' : '',
        ]));

        $dir = $is_rtl ? 'rtl' : 'ltr';
        ?>
        <nav class="<?php echo $wrapper_classes; ?>"
             dir="<?php echo esc_attr($dir); ?>"
             aria-label="<?php esc_attr_e('Bottom navigation', 'paradise-elementor-widgets'); ?>"
             data-paradise-bn='<?php echo wp_json_encode($data); ?>'>

            <div class="paradise-bn-bar">

                <?php
                $ind = $s['indicator_style'] ?? 'top_bar';
        if (in_array($ind, [ 'top_bar', 'bot_bar' ], true)) : ?>
                    <div class="paradise-bn-indicator paradise-bn-indicator--<?php echo esc_attr($ind); ?>" aria-hidden="true"></div>
                <?php endif;

        if ($source === 'repeater') {
            $items = $s['nav_items'] ?? [];
            $total = count($items);
            $mid   = $center_on ? (int) floor($total / 2) : -1;
            foreach ($items as $i => $item) :
                if ($center_on && $i === $mid) {
                    $this->render_center_btn($s, $show_labels, $center_act);
                }
                $this->render_item_repeater($item, $show_labels, $current_url, $s);
            endforeach;
            if ($center_on && $mid >= $total) {
                $this->render_center_btn($s, $show_labels, $center_act);
            }
        } else {
            $slug  = $s['wp_menu_slug'] ?? '';
            $items = $this->get_menu_items($slug);
            $total = count($items);
            $mid   = $center_on ? (int) floor($total / 2) : -1;
            foreach ($items as $i => $item) :
                if ($center_on && $i === $mid) {
                    $this->render_center_btn($s, $show_labels, $center_act);
                }
                $this->render_item_wp_menu($item, $show_labels, $current_url);
            endforeach;
            if ($center_on && $mid >= $total) {
                $this->render_center_btn($s, $show_labels, $center_act);
            }
        }
        ?>

            </div><!-- .paradise-bn-bar -->

            <?php if ($center_on && $center_act === 'speed_dial') :
                // In editor: open by default so user can see dial items
                $dial_classes = 'paradise-bn-speed-dial' . ($is_edit ? ' paradise-bn-speed-dial--open' : '');
                $dial_hidden  = $is_edit ? 'false' : 'true';
                ?>
                <div class="<?php echo $dial_classes; ?>" aria-hidden="<?php echo $dial_hidden; ?>" role="menu">
                    <?php foreach (array_reverse($s['speed_dial_items'] ?? []) as $dial) :
                        $d_url = $dial['dial_url']['url'] ?? '';
                        $d_ext = ! empty($dial['dial_url']['is_external']);
                        ?>
                        <a href="<?php echo esc_url($d_url ?: '#'); ?>"
                           class="paradise-bn-dial-item"
                           role="menuitem"
                           <?php echo $d_ext ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                            <span class="paradise-bn-dial-icon" aria-hidden="true">
                                <?php Icons_Manager::render_icon($dial['dial_icon'], [ 'aria-hidden' => 'true' ]); ?>
                            </span>
                            <span class="paradise-bn-dial-label"><?php echo esc_html($dial['dial_label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </nav>
        <?php
    }

    // ═══════════════════════════════════════════════════════════════
    // RENDER HELPERS
    // ═══════════════════════════════════════════════════════════════

    private function render_badge(array $item): void
    {
        if (($item['badge_enabled'] ?? 'no') !== 'yes') {
            return;
        }

        $source    = $item['badge_source']    ?? 'static';
        $hide_zero = ($item['badge_hide_zero'] ?? 'yes') === 'yes';
        $css_id    = $item['item_custom_id'] ?? '';
        $value     = 0;

        if ($source === 'static') {
            $value = (int) ($item['badge_value'] ?? 0);
        } elseif ($source === 'woo') {
            $value = function_exists('WC') ? (int) WC()->cart->get_cart_contents_count() : 0;
        }

        $hide_attr  = ($hide_zero && $value === 0 && $source !== 'js') ? ' style="display:none"' : '';
        $data_attrs = $source === 'js' ? ' data-paradise-bn-badge-target="' . esc_attr($css_id) . '"' : '';

        printf(
            '<span class="paradise-bn-badge"%s%s>%s</span>',
            $hide_attr,
            $data_attrs,
            $source !== 'js' ? esc_html($value > 99 ? '99+' : (string) $value) : ''
        );
    }

    private function render_item_repeater(array $item, bool $show_labels, string $current_url, array $s): void
    {
        $url       = $item['item_url']['url'] ?? '';
        $url       = $url ?: site_url('/');
        $ext       = ! empty($item['item_url']['is_external']);
        $active    = trailingslashit($url) === trailingslashit($current_url);
        $id_attr   = ! empty($item['item_custom_id']) ? ' id="' . esc_attr($item['item_custom_id']) . '"' : '';
        $ind_style = $s['indicator_style'] ?? 'top_bar';

        $classes = 'paradise-bn-item';
        if ($active) {
            $classes .= ' paradise-bn-item--active';
        }
        if ($ind_style === 'pill') {
            $classes .= ' paradise-bn-pill';
        }
        ?>
        <a<?php echo $id_attr; ?>
           href="<?php echo esc_url($url); ?>"
           class="<?php echo esc_attr($classes); ?>"
           <?php echo $ext ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
           aria-current="<?php echo $active ? 'page' : 'false'; ?>">
            <span class="paradise-bn-item-icon" aria-hidden="true">
                <?php Icons_Manager::render_icon($item['item_icon'], [ 'aria-hidden' => 'true' ]); ?>
                <?php $this->render_badge($item); ?>
            </span>
            <?php if ($ind_style === 'dot') : ?>
                <span class="paradise-bn-dot" aria-hidden="true"></span>
            <?php endif; ?>
            <?php if ($show_labels) : ?>
                <span class="paradise-bn-label"><?php echo esc_html($item['item_label']); ?></span>
            <?php endif; ?>
        </a>
        <?php
    }

    private function render_item_wp_menu(object $item, bool $show_labels, string $current_url): void
    {
        $url    = $item->url;
        $active = trailingslashit($url) === trailingslashit($current_url);
        $classes_raw = trim(implode(' ', array_filter((array) $item->classes)));
        $icon_data   = $this->css_class_to_icon_data($classes_raw ?: 'fas fa-circle');
        ?>
        <a href="<?php echo esc_url($url); ?>"
           class="paradise-bn-item<?php echo $active ? ' paradise-bn-item--active' : ''; ?>"
           aria-current="<?php echo $active ? 'page' : 'false'; ?>">
            <span class="paradise-bn-item-icon" aria-hidden="true">
                <?php Icons_Manager::render_icon($icon_data, [ 'aria-hidden' => 'true' ]); ?>
            </span>
            <?php if ($show_labels) : ?>
                <span class="paradise-bn-label"><?php echo esc_html($item->title); ?></span>
            <?php endif; ?>
        </a>
        <?php
    }

    private function render_center_btn(array $s, bool $show_labels, string $center_act): void
    {
        $label = $s['center_label'] ?? '';
        $url   = $s['center_url']['url'] ?? '';
        $ext   = ! empty($s['center_url']['is_external']);
        $hook  = $s['center_js_hook'] ?? '';

        switch ($center_act) {
            case 'link':
                $tag       = 'a';
                $href_attr = 'href="' . esc_url($url ?: site_url('/')) . '"';
                $ext_attr  = $ext ? 'target="_blank" rel="noopener noreferrer"' : '';
                $data_attr = 'data-paradise-bn-action="link"';
                break;
            case 'js_hook':
                $tag       = 'button';
                $href_attr = 'type="button"';
                $ext_attr  = '';
                $data_attr = 'data-paradise-bn-action="js_hook" data-paradise-bn-hook="' . esc_attr($hook) . '"';
                break;
            default:
                $tag       = 'button';
                $href_attr = 'type="button"';
                $ext_attr  = '';
                $data_attr = 'data-paradise-bn-action="speed_dial"';
        }
        ?>
        <div class="paradise-bn-center-wrap">
            <<?php echo $tag; ?> <?php echo $href_attr; ?> <?php echo $ext_attr; ?> <?php echo $data_attr; ?>
                class="paradise-bn-center-btn"
                aria-label="<?php echo esc_attr($label); ?>"
                aria-expanded="false">
                <span class="paradise-bn-center-icon" aria-hidden="true">
                    <?php Icons_Manager::render_icon($s['center_icon'], [ 'aria-hidden' => 'true' ]); ?>
                </span>
            </<?php echo $tag; ?>>
            <?php if ($show_labels && $label) : ?>
                <span class="paradise-bn-label paradise-bn-center-label"><?php echo esc_html($label); ?></span>
            <?php endif; ?>
        </div>
        <?php
    }

    // ═══════════════════════════════════════════════════════════════
    // UTILITIES
    // ═══════════════════════════════════════════════════════════════

    private function css_class_to_icon_data(string $classes): array
    {
        $library_map = [ 'fas' => 'fa-solid', 'far' => 'fa-regular', 'fab' => 'fa-brands', 'fal' => 'fa-light', 'fad' => 'fa-duotone' ];
        $parts  = array_values(array_filter(explode(' ', $classes)));
        $prefix = $parts[0] ?? 'fas';
        return [ 'value' => implode(' ', $parts), 'library' => $library_map[ $prefix ] ?? 'fa-solid' ];
    }

    private function get_wp_menus(): array
    {
        $menus  = wp_get_nav_menus();
        $result = [ '' => '— Select —' ];
        foreach ($menus as $menu) {
            $result[ $menu->slug ] = $menu->name;
        }
        return $result;
    }

    private function get_menu_items(string $slug): array
    {
        if (empty($slug)) {
            return [];
        }
        $menu = wp_get_nav_menu_object($slug);
        if (! $menu) {
            return [];
        }
        $items = wp_get_nav_menu_items($menu->term_id);
        return is_array($items)
            ? array_values(array_filter($items, fn ($i) => empty($i->menu_item_parent)))
            : [];
    }
}
