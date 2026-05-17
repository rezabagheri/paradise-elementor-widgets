<?php
/**
 * Paradise — Feature Card EXAMPLE Widget
 * =============================================================================
 *
 * THIS IS A LEARNING REFERENCE.
 *
 * It's a fully functional Elementor widget — drop it onto a page and it works
 * — but its real purpose is to walk a developer through every step of
 * building a new Paradise widget on top of Paradise_Widget_Base.
 *
 * Read this file top to bottom and you'll have seen every pattern the
 * production widgets use: the base class, the section split, the common
 * control types, conditional controls, responsive controls, group controls,
 * the {{WRAPPER}} selector convention, and safe rendering.
 *
 * The widget itself shows a card with: an icon, a headline, a paragraph,
 * and an optional CTA button. Nothing fancy — the point is the patterns.
 *
 * NOTE: This widget lives in the "Paradise Examples" category (not the
 * regular "Paradise Widgets" category) so end users can tell at a glance
 * that it's a reference, not a production widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Hard exit if someone hits this file directly. Every PHP file in
          // the plugin starts with this guard.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * The class name MUST match the convention enforced by
 * Paradise_EW_Admin::key_to_class():
 *
 *     registry key 'feature_card_example'  →  class 'Paradise_Feature_Card_Example_Widget'
 *
 * If you rename either side, the loader can't find the class and the widget
 * silently disappears from the editor.
 *
 * We extend Paradise_Widget_Base (not Elementor's Widget_Base directly).
 * That gives us, for free:
 *   - get_categories() returning [ 'paradise' ]
 *   - get_style_depends() returning [ 'paradise-{slug-from-name}' ]
 *   - get_script_depends() returning [] (override only if the widget has JS)
 *   - a $this->get_default_handle() helper for the conventional handle
 *
 * We're overriding get_categories() below to push this widget into the
 * "Paradise Examples" category instead of the default "paradise" one.
 */
class Paradise_Feature_Card_Example_Widget extends Paradise_Widget_Base {

    // -------------------------------------------------------------------------
    // IDENTITY METHODS — what Elementor shows in the widget panel
    // -------------------------------------------------------------------------

    /**
     * Unique identifier. Must match the registry key with a 'paradise_' prefix.
     * Used as data-widget-type in the HTML and to derive the asset handle:
     *   'paradise_feature_card_example'  →  asset handle 'paradise-feature-card-example'
     */
    public function get_name(): string {
        return 'paradise_feature_card_example';
    }

    /**
     * Human-readable name shown in the editor's widget panel and on the
     * settings toggle page.
     */
    public function get_title(): string {
        return esc_html__( 'Feature Card (Example)', 'paradise-widgets-for-elementor' );
    }

    /**
     * Icon shown alongside the title in the panel. Can be:
     *   - an Elementor icon class:  'eicon-info-box'  (preferred — bundled)
     *   - a Font Awesome class:     'fa fa-star'
     * Browse https://elementor.github.io/elementor-icons/ for eicons.
     */
    public function get_icon(): string {
        return 'eicon-info-box';
    }

    /**
     * Searchable keywords for the editor panel's search box. Add any term
     * a user might type to discover this widget.
     */
    public function get_keywords(): array {
        return [ 'feature', 'card', 'icon', 'box', 'service', 'example', 'learning' ];
    }

    /**
     * Override the base's [ 'paradise' ] default — this widget lives in the
     * dedicated "Paradise Examples" category so it doesn't blend with the
     * 15 production widgets in the regular "Paradise Widgets" category.
     *
     * The category slug ('paradise-examples') must match what was registered
     * via $elements_manager->add_category() in the main plugin file.
     */
    public function get_categories(): array {
        return [ 'paradise-examples' ];
    }

    // -------------------------------------------------------------------------
    // ASSET DEPENDENCIES
    // -------------------------------------------------------------------------

    // get_style_depends() and get_script_depends() are inherited from
    // Paradise_Widget_Base. The defaults are:
    //
    //   get_style_depends()  → [ 'paradise-feature-card-example' ]   (auto from get_name)
    //   get_script_depends() → [] (no JS file)
    //
    // We don't override them because the defaults are exactly what we need.
    //
    // If this widget needed JS, we would add:
    //
    //   public function get_script_depends(): array {
    //       return [ $this->get_default_handle() ];
    //   }
    //
    // The asset files are registered automatically by the main plugin file —
    // just drop them at the conventional paths:
    //
    //   assets/css/feature-card-example.css     (registered if the registry
    //                                            entry exists — required)
    //   assets/js/feature-card-example.js       (registered only if the
    //                                            registry entry has 'js' => true)

    // -------------------------------------------------------------------------
    // CONTROLS — the editor sidebar
    // -------------------------------------------------------------------------

    /**
     * Elementor calls register_controls() to build the editor sidebar.
     *
     * Splitting it into private per-section methods (instead of one giant
     * function) keeps the file readable as the widget grows. Each method
     * focuses on one logical group of controls. This mirrors how the
     * production widgets in this plugin are organized.
     */
    protected function register_controls(): void {
        $this->register_content_section();
        $this->register_style_icon_section();
        $this->register_style_title_section();
        $this->register_style_description_section();
        $this->register_style_button_section();
        $this->register_style_card_section();
    }

    /**
     * CONTENT TAB — Section: Content
     *
     * Holds the user-editable data: icon choice, headline text, paragraph
     * text, link, button label, and alignment. All non-stylistic settings
     * live in the "Content" tab so users find them where they expect.
     */
    private function register_content_section(): void {
        // start_controls_section() opens an accordion section in the editor.
        // The first argument is a unique ID used for CSS classes and for
        // referencing the section from conditions on other sections.
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Content', 'paradise-widgets-for-elementor' ),
                // No 'tab' => means default to Content tab.
                // Other tabs: Controls_Manager::TAB_STYLE, TAB_ADVANCED.
            ]
        );

        // ICONS control — Elementor's icon picker. Returns an array with
        // 'value' (icon class) and 'library' ('fa-solid', 'eicons', etc.).
        // Render with Icons_Manager::render_icon() — never echo directly.
        $this->add_control(
            'icon',
            [
                'label'   => esc_html__( 'Icon', 'paradise-widgets-for-elementor' ),
                'type'    => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        // TEXT control — single-line input. Use 'label_block' => true when
        // the field deserves the full sidebar width (long titles, URLs).
        $this->add_control(
            'title',
            [
                'label'       => esc_html__( 'Title', 'paradise-widgets-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Feature Title', 'paradise-widgets-for-elementor' ),
                'label_block' => true,
            ]
        );

        // TEXTAREA — multi-line input. 'rows' sets initial height.
        $this->add_control(
            'description',
            [
                'label'   => esc_html__( 'Description', 'paradise-widgets-for-elementor' ),
                'type'    => Controls_Manager::TEXTAREA,
                'rows'    => 4,
                'default' => esc_html__( 'Describe this feature in a sentence or two.', 'paradise-widgets-for-elementor' ),
            ]
        );

        // URL control — link picker with target/nofollow options. The returned
        // value is an array: [ 'url', 'is_external', 'nofollow', 'custom_attributes' ].
        // Render with $this->add_link_attributes() — never build the <a> tag
        // attributes by hand.
        $this->add_control(
            'link',
            [
                'label'       => esc_html__( 'Link', 'paradise-widgets-for-elementor' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => 'https://example.com',
                'default'     => [
                    'url'         => '',
                    'is_external' => false,
                    'nofollow'    => false,
                ],
            ]
        );

        // CONDITION pattern — show this control only when 'link[url]' is set.
        // The '!=' empty check uses 'link[url]!' as the key (the bang means
        // "not equal"). Elementor hides controls dynamically as conditions
        // change, no page reload needed.
        $this->add_control(
            'button_text',
            [
                'label'     => esc_html__( 'Button Text', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__( 'Learn More', 'paradise-widgets-for-elementor' ),
                'condition' => [ 'link[url]!' => '' ],
            ]
        );

        // RESPONSIVE control — generates one control per breakpoint (desktop,
        // tablet, mobile). The 'selectors' below output media-query CSS
        // automatically. Use add_responsive_control() any time the value
        // should differ per viewport (alignment, sizing, spacing).
        $this->add_responsive_control(
            'alignment',
            [
                'label'   => esc_html__( 'Alignment', 'paradise-widgets-for-elementor' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'paradise-widgets-for-elementor' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'paradise-widgets-for-elementor' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'paradise-widgets-for-elementor' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                // SELECTORS pattern — Elementor writes CSS automatically when
                // the user changes the value. {{WRAPPER}} expands to the
                // widget's outer element selector, so the rule is scoped to
                // this widget instance only (no leaks to other widgets).
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-card' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * STYLE TAB — Section: Icon
     *
     * From here on every section uses 'tab' => Controls_Manager::TAB_STYLE so
     * Elementor puts them under the "Style" tab in the sidebar.
     *
     * SLIDER + SELECTORS pattern: the user drags a slider, the CSS variable
     * or property updates live. Combined with size_units to support px / em / %.
     */
    private function register_style_icon_section(): void {
        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__( 'Icon', 'paradise-widgets-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // COLOR control — outputs a color picker. The selector hits both the
        // icon font color (for icon fonts) and the SVG fill (for SVG icons).
        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__( 'Color', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#3b82f6',
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paradise-fc-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        // SLIDER control — range input. Set 'range' per unit. The selector
        // uses {{SIZE}}{{UNIT}} placeholders that Elementor fills in.
        $this->add_responsive_control(
            'icon_size',
            [
                'label'      => esc_html__( 'Size', 'paradise-widgets-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range'      => [
                    'px' => [ 'min' => 12, 'max' => 200 ],
                    'em' => [ 'min' => 0.5, 'max' => 10, 'step' => 0.1 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 48 ],
                'selectors'  => [
                    '{{WRAPPER}} .paradise-fc-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .paradise-fc-icon svg' => 'width: 1em; height: 1em;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * STYLE TAB — Section: Title
     *
     * GROUP CONTROL pattern: Group_Control_Typography gives you font family,
     * size, weight, style, line height, letter spacing, transform, decoration
     * — all in one prebuilt control set. Same idea applies for Border, Box
     * Shadow, Background. Always prefer group controls over re-implementing
     * the same set of fields manually.
     */
    private function register_style_title_section(): void {
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__( 'Title', 'paradise-widgets-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__( 'Color', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Group control — typography. Add it with a unique 'name' and a
        // 'selector' (NOT 'selectors' — group controls take a single selector).
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .paradise-fc-title',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * STYLE TAB — Section: Description
     *
     * Same shape as the Title section. Once you've written one Color +
     * Typography pair, the rest are copy-paste with selector swaps. That's
     * a sign you're building consistently.
     */
    private function register_style_description_section(): void {
        $this->start_controls_section(
            'section_style_description',
            [
                'label' => esc_html__( 'Description', 'paradise-widgets-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'     => esc_html__( 'Color', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .paradise-fc-description',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * STYLE TAB — Section: Button
     *
     * The button section is hidden when there's no link, via the same
     * condition pattern used in the Content tab.
     */
    private function register_style_button_section(): void {
        $this->start_controls_section(
            'section_style_button',
            [
                'label'     => esc_html__( 'Button', 'paradise-widgets-for-elementor' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [ 'link[url]!' => '' ],
            ]
        );

        // start_controls_tabs() builds a sub-tab UI inside the section — used
        // here for the classic Normal / Hover state pair.
        $this->start_controls_tabs( 'button_state_tabs' );

        $this->start_controls_tab(
            'button_normal',
            [ 'label' => esc_html__( 'Normal', 'paradise-widgets-for-elementor' ) ]
        );

        $this->add_control(
            'button_color',
            [
                'label'     => esc_html__( 'Text Color', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg',
            [
                'label'     => esc_html__( 'Background', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [ 'label' => esc_html__( 'Hover', 'paradise-widgets-for-elementor' ) ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label'     => esc_html__( 'Text Color', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_hover',
            [
                'label'     => esc_html__( 'Background', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#374151',
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * STYLE TAB — Section: Card
     *
     * Container-level styling: background, padding, border, radius, shadow.
     * DIMENSIONS control = TOP/RIGHT/BOTTOM/LEFT with an optional linked
     * mode. Used here for padding and border-radius.
     */
    private function register_style_card_section(): void {
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => esc_html__( 'Card', 'paradise-widgets-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label'     => esc_html__( 'Background', 'paradise-widgets-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .paradise-fc-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // DIMENSIONS control — outputs {{TOP}}, {{RIGHT}}, {{BOTTOM}}, {{LEFT}}
        // and {{UNIT}} placeholders. The user gets one input with optional
        // linked mode for entering all-sides or per-side values.
        $this->add_responsive_control(
            'card_padding',
            [
                'label'      => esc_html__( 'Padding', 'paradise-widgets-for-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [ 'top' => 24, 'right' => 24, 'bottom' => 24, 'left' => 24, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .paradise-fc-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Group control — Border. Gives type / width / color in one block.
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'card_border',
                'selector' => '{{WRAPPER}} .paradise-fc-card',
            ]
        );

        $this->add_responsive_control(
            'card_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'paradise-widgets-for-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .paradise-fc-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Group control — Box Shadow. Gives full control over the standard
        // CSS shadow properties without you re-implementing them.
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'card_shadow',
                'selector' => '{{WRAPPER}} .paradise-fc-card',
            ]
        );

        $this->end_controls_section();
    }

    // -------------------------------------------------------------------------
    // RENDER — what shows up on the frontend
    // -------------------------------------------------------------------------

    /**
     * render() is called on every page that contains this widget. It must
     * output safe HTML. The four rules to follow:
     *
     *   1. Pull settings via get_settings_for_display() — it resolves dynamic
     *      tags and shortcodes; get_settings() does not.
     *
     *   2. ESCAPE EVERY DYNAMIC VALUE on output. The escape function depends
     *      on context:
     *        - esc_html()   for text content
     *        - esc_attr()   for HTML attribute values
     *        - esc_url()    for URLs
     *        - wp_kses_post() when limited HTML is allowed (e.g. rich text)
     *
     *   3. Use $this->add_link_attributes() for <a> tags built from a URL
     *      control — it handles target / rel / nofollow / custom attributes
     *      correctly and safely.
     *
     *   4. Use Icons_Manager::render_icon() for ICONS controls — it picks
     *      the right tag (<i> vs inline <svg>) based on the library.
     */
    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $has_link = ! empty( $settings['link']['url'] );

        // add_render_attribute() builds an attribute string we'll print
        // later. Adding the same key twice merges (for class) or replaces
        // (for everything else).
        $this->add_render_attribute( 'wrapper', 'class', 'paradise-fc-card' );

        if ( $has_link ) {
            // Lets Elementor inject the right target / rel / custom attrs
            // for the configured link, including handling external / nofollow.
            $this->add_link_attributes( 'button', $settings['link'] );
            $this->add_render_attribute( 'button', 'class', 'paradise-fc-button' );
        }
        ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

            <?php if ( ! empty( $settings['icon']['value'] ) ) : ?>
                <div class="paradise-fc-icon" aria-hidden="true">
                    <?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $settings['title'] ) ) : ?>
                <h3 class="paradise-fc-title">
                    <?php echo esc_html( $settings['title'] ); ?>
                </h3>
            <?php endif; ?>

            <?php if ( ! empty( $settings['description'] ) ) : ?>
                <p class="paradise-fc-description">
                    <?php
                    // wp_kses_post allows the same tags as a post content —
                    // useful when the source is a textarea where the user
                    // might paste a <strong> or <em>.
                    echo wp_kses_post( $settings['description'] );
                    ?>
                </p>
            <?php endif; ?>

            <?php if ( $has_link && ! empty( $settings['button_text'] ) ) : ?>
                <a <?php $this->print_render_attribute_string( 'button' ); ?>>
                    <?php echo esc_html( $settings['button_text'] ); ?>
                </a>
            <?php endif; ?>

        </div>
        <?php
    }
}
