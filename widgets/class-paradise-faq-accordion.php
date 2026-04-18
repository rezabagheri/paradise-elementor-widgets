<?php
/**
 * Paradise FAQ Accordion Widget
 *
 * Repeater of Q&A items with smooth accordion or multi-expand behavior.
 * Outputs Schema.org FAQPage JSON-LD for SEO.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Paradise_Faq_Accordion_Widget extends \Elementor\Widget_Base {

    public function get_name(): string     { return 'paradise_faq_accordion'; }
    public function get_title(): string    { return esc_html__( 'FAQ Accordion', 'paradise-elementor-widgets' ); }
    public function get_icon(): string     { return 'eicon-accordion'; }
    public function get_categories(): array { return [ 'paradise' ]; }
    public function get_keywords(): array  { return [ 'faq', 'accordion', 'question', 'answer', 'toggle', 'collapse' ]; }

    public function get_style_depends(): array  { return [ 'paradise-faq-accordion' ]; }
    public function get_script_depends(): array { return [ 'paradise-faq-accordion' ]; }

    // ── Controls ──────────────────────────────────────────────────────────────

    protected function register_controls(): void {

        // ── Content: Items ────────────────────────────────────────────────────

        $this->start_controls_section( 'section_items', [
            'label' => esc_html__( 'FAQ Items', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'items', [
            'label'  => esc_html__( 'Items', 'paradise-elementor-widgets' ),
            'type'   => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name'        => 'question',
                    'label'       => esc_html__( 'Question', 'paradise-elementor-widgets' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => esc_html__( 'What is your question?', 'paradise-elementor-widgets' ),
                    'dynamic'     => [ 'active' => true ],
                ],
                [
                    'name'    => 'answer',
                    'label'   => esc_html__( 'Answer', 'paradise-elementor-widgets' ),
                    'type'    => \Elementor\Controls_Manager::WYSIWYG,
                    'default' => esc_html__( 'Your answer goes here.', 'paradise-elementor-widgets' ),
                    'dynamic' => [ 'active' => true ],
                ],
            ],
            'default' => [
                [
                    'question' => esc_html__( 'What services do you offer?', 'paradise-elementor-widgets' ),
                    'answer'   => esc_html__( 'We offer a wide range of services tailored to your needs. Contact us to learn more.', 'paradise-elementor-widgets' ),
                ],
                [
                    'question' => esc_html__( 'How can I get in touch?', 'paradise-elementor-widgets' ),
                    'answer'   => esc_html__( 'You can reach us by phone, email, or through our contact form.', 'paradise-elementor-widgets' ),
                ],
                [
                    'question' => esc_html__( 'What are your business hours?', 'paradise-elementor-widgets' ),
                    'answer'   => esc_html__( 'We are open Monday through Friday, 9 AM to 5 PM.', 'paradise-elementor-widgets' ),
                ],
            ],
            'title_field' => '{{{ question }}}',
        ] );

        $this->end_controls_section();

        // ── Content: Behavior ─────────────────────────────────────────────────

        $this->start_controls_section( 'section_behavior', [
            'label' => esc_html__( 'Behavior', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'behavior', [
            'label'   => esc_html__( 'Mode', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'accordion',
            'options' => [
                'accordion' => esc_html__( 'Accordion — one open at a time', 'paradise-elementor-widgets' ),
                'multi'     => esc_html__( 'Multi-expand — any can be open', 'paradise-elementor-widgets' ),
            ],
        ] );

        $this->add_control( 'open_first', [
            'label'        => esc_html__( 'Open First Item by Default', 'paradise-elementor-widgets' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ] );

        $this->add_control( 'icon_type', [
            'label'   => esc_html__( 'Icon', 'paradise-elementor-widgets' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'chevron',
            'options' => [
                'chevron' => esc_html__( 'Chevron', 'paradise-elementor-widgets' ),
                'plus'    => esc_html__( 'Plus / Minus', 'paradise-elementor-widgets' ),
                'none'    => esc_html__( 'None', 'paradise-elementor-widgets' ),
            ],
        ] );

        $this->add_control( 'icon_position', [
            'label'     => esc_html__( 'Icon Position', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'right',
            'options'   => [
                'right' => esc_html__( 'Right', 'paradise-elementor-widgets' ),
                'left'  => esc_html__( 'Left', 'paradise-elementor-widgets' ),
            ],
            'condition' => [ 'icon_type!' => 'none' ],
        ] );

        $this->add_control( 'schema_faq', [
            'label'        => esc_html__( 'Output FAQ Schema (SEO)', 'paradise-elementor-widgets' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
            'description'  => esc_html__( 'Adds Schema.org FAQPage JSON-LD markup for Google rich results.', 'paradise-elementor-widgets' ),
        ] );

        $this->end_controls_section();

        // ── Style: Items ──────────────────────────────────────────────────────

        $this->start_controls_section( 'section_style_items', [
            'label' => esc_html__( 'Items', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'item_gap', [
            'label'      => esc_html__( 'Gap Between Items', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 8 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-faq-wrap' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'item_bg', [
            'label'     => esc_html__( 'Background', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-item' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'item_border',
            'selector' => '{{WRAPPER}} .paradise-faq-item',
        ] );

        $this->add_responsive_control( 'item_border_radius', [
            'label'      => esc_html__( 'Border Radius', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-faq-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [
            'name'     => 'item_box_shadow',
            'selector' => '{{WRAPPER}} .paradise-faq-item',
        ] );

        $this->end_controls_section();

        // ── Style: Question ───────────────────────────────────────────────────

        $this->start_controls_section( 'section_style_question', [
            'label' => esc_html__( 'Question', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'question_typography',
            'selector' => '{{WRAPPER}} .paradise-faq-q-text',
        ] );

        $this->add_responsive_control( 'question_padding', [
            'label'      => esc_html__( 'Padding', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-faq-question' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->start_controls_tabs( 'tabs_question' );

        $this->start_controls_tab( 'tab_question_normal', [
            'label' => esc_html__( 'Normal', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'question_color', [
            'label'     => esc_html__( 'Text Color', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-question' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'question_bg', [
            'label'     => esc_html__( 'Background', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-question' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_question_active', [
            'label' => esc_html__( 'Active', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'question_color_active', [
            'label'     => esc_html__( 'Text Color', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-item--open > .paradise-faq-question' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'question_bg_active', [
            'label'     => esc_html__( 'Background', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-item--open > .paradise-faq-question' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // ── Style: Answer ─────────────────────────────────────────────────────

        $this->start_controls_section( 'section_style_answer', [
            'label' => esc_html__( 'Answer', 'paradise-elementor-widgets' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'answer_typography',
            'selector' => '{{WRAPPER}} .paradise-faq-answer-inner',
        ] );

        $this->add_control( 'answer_color', [
            'label'     => esc_html__( 'Text Color', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-answer-inner' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'answer_bg', [
            'label'     => esc_html__( 'Background', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-answer' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'answer_padding', [
            'label'      => esc_html__( 'Padding', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-faq-answer-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'answer_divider_color', [
            'label'     => esc_html__( 'Divider Color', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-item--open > .paradise-faq-answer' => 'border-top-color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        // ── Style: Icon ───────────────────────────────────────────────────────

        $this->start_controls_section( 'section_style_icon', [
            'label'     => esc_html__( 'Icon', 'paradise-elementor-widgets' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'icon_type!' => 'none' ],
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label'      => esc_html__( 'Size', 'paradise-elementor-widgets' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 18 ],
            'selectors'  => [
                '{{WRAPPER}} .paradise-faq-q-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->start_controls_tabs( 'tabs_icon_color' );

        $this->start_controls_tab( 'tab_icon_normal', [
            'label' => esc_html__( 'Normal', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'icon_color', [
            'label'     => esc_html__( 'Color', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-q-icon' => 'color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_icon_active', [
            'label' => esc_html__( 'Active', 'paradise-elementor-widgets' ),
        ] );

        $this->add_control( 'icon_color_active', [
            'label'     => esc_html__( 'Color', 'paradise-elementor-widgets' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .paradise-faq-item--open .paradise-faq-q-icon' => 'color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    // ── SVG icons ─────────────────────────────────────────────────────────────

    private static function chevron_svg(): string {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
    }

    private static function plus_svg(): string {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
    }

    // ── Render ────────────────────────────────────────────────────────────────

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $items    = $settings['items'] ?? [];

        if ( empty( $items ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="paradise-faq-placeholder">'
                   . esc_html__( 'Add FAQ items in the widget settings.', 'paradise-elementor-widgets' )
                   . '</div>';
            }
            return;
        }

        $behavior  = $settings['behavior']      ?? 'accordion';
        $icon_type = $settings['icon_type']     ?? 'chevron';
        $icon_pos  = $settings['icon_position'] ?? 'right';
        $open_first = 'yes' === ( $settings['open_first'] ?? 'yes' );

        $icon_svg = '';
        if ( 'chevron' === $icon_type ) {
            $icon_svg = self::chevron_svg();
        } elseif ( 'plus' === $icon_type ) {
            $icon_svg = self::plus_svg();
        }

        $wrap_classes = [ 'paradise-faq-wrap' ];
        if ( 'none' !== $icon_type ) {
            $wrap_classes[] = 'paradise-faq--icon-' . sanitize_html_class( $icon_type );
            $wrap_classes[] = 'paradise-faq--icon-pos-' . sanitize_html_class( $icon_pos );
        }
        ?>
        <div
            class="<?php echo esc_attr( implode( ' ', $wrap_classes ) ); ?>"
            data-faq-mode="<?php echo esc_attr( $behavior ); ?>"
            itemscope itemtype="https://schema.org/FAQPage"
        >
        <?php foreach ( $items as $idx => $item ) :
            $is_open = $open_first && 0 === $idx;
            $answer_id = 'paradise-faq-ans-' . $this->get_id() . '-' . $idx;
        ?>
            <div
                class="paradise-faq-item<?php echo $is_open ? ' paradise-faq-item--open' : ''; ?>"
                itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"
            >
                <div
                    class="paradise-faq-question"
                    role="button"
                    tabindex="0"
                    aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo esc_attr( $answer_id ); ?>"
                >
                    <span class="paradise-faq-q-text" itemprop="name"><?php echo esc_html( $item['question'] ?? '' ); ?></span>
                    <?php if ( 'none' !== $icon_type ) : ?>
                    <span class="paradise-faq-q-icon" aria-hidden="true"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    <?php endif; ?>
                </div>

                <div
                    class="paradise-faq-answer"
                    id="<?php echo esc_attr( $answer_id ); ?>"
                    role="region"
                    itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer"
                >
                    <div class="paradise-faq-answer-inner" itemprop="text">
                        <?php echo wp_kses_post( $item['answer'] ?? '' ); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        <?php
        if ( 'yes' === ( $settings['schema_faq'] ?? 'yes' ) ) {
            $schema_items = [];
            foreach ( $items as $item ) {
                $q = wp_strip_all_tags( $item['question'] ?? '' );
                $a = wp_strip_all_tags( $item['answer']   ?? '' );
                if ( '' !== $q && '' !== $a ) {
                    $schema_items[] = [
                        '@type'          => 'Question',
                        'name'           => $q,
                        'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $a ],
                    ];
                }
            }
            if ( ! empty( $schema_items ) ) {
                $schema = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    'mainEntity' => $schema_items,
                ];
                echo '<script type="application/ld+json">'
                   . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
                   . '</script>';
            }
        }
    }
}
