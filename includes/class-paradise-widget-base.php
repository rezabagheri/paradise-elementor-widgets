<?php
/**
 * Paradise Widget Base
 *
 * Abstract base class extended by every Paradise widget. Centralises:
 *
 *   - registry conventions (Elementor category, conventional asset-handle
 *     naming derived from get_name())
 *   - shared UI helpers that would otherwise be re-implemented per widget
 *     (editor-mode notices, in-panel RAW_HTML descriptors)
 *
 * Subclasses MUST still implement:
 *   - get_name()           — unique identifier, e.g. 'paradise_phone_link'
 *   - get_title()          — human label shown in the editor panel
 *   - get_icon()           — Elementor or Font Awesome icon class
 *   - register_controls()  — the controls
 *   - render()             — the frontend markup
 *
 * Subclasses MAY override:
 *   - get_keywords()                              — searchable terms
 *   - get_style_depends() / get_script_depends() — to ADD handles to the
 *     conventional default (e.g. Bottom Nav adds elementor-icons-fa-*)
 *
 * Loading: this file is required from the main plugin file BEFORE any
 * concrete widget file is included, so subclasses can resolve the parent
 * class. Elementor's Widget_Base only exists after Elementor has loaded,
 * which is guaranteed by the time elementor/widgets/register fires.
 *
 * @package Paradise_Widgets_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Paradise_Widget_Base extends \Elementor\Widget_Base {

    /**
     * Every Paradise widget belongs to the 'paradise' category in the
     * Elementor editor panel. Final-ish — subclasses shouldn't override.
     */
    public function get_categories(): array {
        return [ 'paradise' ];
    }

    /**
     * Conventional CSS handle derived from the widget name:
     *
     *   get_name()  = 'paradise_phone_link'
     *   →  handle  = 'paradise-phone-link'
     *
     * The main plugin file registers a CSS handle with this exact name
     * for every entry in the widget registry. Subclasses that need extra
     * dependencies (e.g. Bottom Nav using Elementor's bundled Font Awesome
     * icons) override get_style_depends() and call get_default_handle() to
     * keep the conventional handle alongside their extras.
     */
    public function get_style_depends(): array {
        return [ $this->get_default_handle() ];
    }

    /**
     * Default: widget has no JS. Subclasses with a JS file override and
     * return [ $this->get_default_handle() ] (and the registry entry must
     * carry 'js' => true so the main plugin file registers the script).
     */
    public function get_script_depends(): array {
        return [];
    }

    /**
     * Convert the snake_case widget name to its dash-prefixed asset handle:
     *
     *   'paradise_phone_link' → 'paradise-phone-link'
     *
     * Kept protected because the conventional handle is internal — public
     * callers should rely on get_style_depends() / get_script_depends().
     */
    protected function get_default_handle(): string {
        return str_replace( '_', '-', $this->get_name() );
    }

    // =========================================================================
    // SHARED UI HELPERS (callable from render() and register_controls())
    // =========================================================================

    /**
     * Render an editor-mode notice box.
     *
     * Outputs a small info badge (circle-i SVG + body span) inside a div
     * styled like Elementor's own editor hints. Intended to appear ONLY
     * when the editor is loaded — the caller is responsible for guarding
     * with `\Elementor\Plugin::$instance->editor->is_edit_mode()`. The
     * helper does not check the editor mode itself because the caller
     * typically already needs that boolean for other render branches.
     *
     * The body argument is HTML, not plain text. The caller escapes any
     * interpolated values (via esc_html__, esc_html, etc.) before passing
     * the string in. This keeps the helper flexible — different widgets
     * arrange the title/body/value parts differently (some end with a
     * <strong> value, some have a conditional appendix, some have multiple
     * emphasis segments) and a rigid "title + body" signature would force
     * awkward workarounds.
     *
     * Wrapper CSS class defaults to "{widget-handle}-editor-notice" so it
     * matches the per-widget stylesheets that already exist. Override only
     * if a widget needs a non-conventional class name.
     *
     * @param string $body_html Pre-escaped HTML body. Use sprintf() +
     *                          esc_html__() / esc_html() to compose it
     *                          safely at the call site.
     * @param string $wrapper_class Optional CSS class for the outer div.
     *                              Defaults to the conventional handle +
     *                              '-editor-notice'.
     */
    protected function editor_notice( string $body_html, string $wrapper_class = '' ): void {
        if ( '' === $wrapper_class ) {
            $wrapper_class = $this->get_default_handle() . '-editor-notice';
        }

        // wp_kses-allowed tags: enough for the common patterns
        // (<strong>, <em>, <br>, <code>) without permitting injection.
        $allowed = [
            'strong' => [],
            'em'     => [],
            'br'     => [],
            'code'   => [],
            'span'   => [ 'class' => true ],
        ];

        ?>
        <div class="<?php echo esc_attr( $wrapper_class ); ?>">
            <svg viewBox="0 0 20 20" width="16" height="16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-5a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0V6a1 1 0 0 1 1-1Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
            <span><?php echo wp_kses( $body_html, $allowed ); ?></span>
        </div>
        <?php
    }

    /**
     * Add a RAW_HTML "descriptor" control — Elementor's small informational
     * note that appears between regular controls in the panel.
     *
     * Replaces the 5-line pattern:
     *
     *     $this->add_control( 'notice', [
     *         'type'            => Controls_Manager::RAW_HTML,
     *         'raw'             => esc_html__( '…', '…' ),
     *         'content_classes' => 'elementor-descriptor',
     *     ] );
     *
     * with:
     *
     *     $this->add_descriptor( 'notice', esc_html__( '…', '…' ) );
     *
     * The $extra array is merged AFTER the defaults, so callers can layer
     * on per-call args like 'condition' or 'separator' without losing the
     * 'type' / 'content_classes' that make the descriptor look right:
     *
     *     $this->add_descriptor( 'notice', esc_html__( '…', '…' ), [
     *         'condition' => [ 'source' => 'site_info' ],
     *         'separator' => 'before',
     *     ] );
     *
     * @param string $id           Control ID (unique within the section).
     * @param string $escaped_html Pre-escaped HTML. Use esc_html__() at the
     *                             call site for translated text.
     * @param array  $extra        Optional extra control args merged on top.
     */
    protected function add_descriptor( string $id, string $escaped_html, array $extra = [] ): void {
        $args = array_merge(
            [
                'type'            => \Elementor\Controls_Manager::RAW_HTML,
                'raw'             => $escaped_html,
                'content_classes' => 'elementor-descriptor',
            ],
            $extra
        );

        $this->add_control( $id, $args );
    }
}
