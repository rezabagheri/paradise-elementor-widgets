<?php
/**
 * Paradise Widget Base
 *
 * Abstract base class extended by every Paradise widget. Centralises the
 * small amount of behaviour that is identical across the whole widget set
 * (Elementor category, conventional asset-handle naming) and provides a
 * couple of helpers so subclasses don't repeat boilerplate.
 *
 * Subclasses MUST still implement:
 *   - get_name()           — unique identifier, e.g. 'paradise_phone_link'
 *   - get_title()          — human label shown in the editor panel
 *   - get_icon()           — Elementor or Font Awesome icon class
 *   - register_controls()  — the controls
 *   - render()             — the frontend markup
 *
 * Subclasses MAY override:
 *   - get_keywords()                       — searchable terms
 *   - get_style_depends() / get_script_depends() — to ADD handles to the
 *     conventional default (e.g. Bottom Nav adds elementor-icons-fa-*)
 *
 * Loading: this file is required from the main plugin file BEFORE any
 * concrete widget file is included, so subclasses can resolve the parent
 * class. Elementor's Widget_Base only exists after Elementor has loaded,
 * which is guaranteed by the time elementor/widgets/register fires.
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
}
