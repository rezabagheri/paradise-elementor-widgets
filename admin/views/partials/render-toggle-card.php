<?php
/**
 * Partial — paradise_ew_render_toggle_card() helper.
 *
 * Shared by page-widgets.php and page-settings.php. Defined behind a
 * function_exists guard so it stays safe if both views are loaded in the
 * same request (e.g. settings_errors() reload, or a future tabbed UI).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'paradise_ew_render_toggle_card' ) ) {
    /**
     * Render one card of toggles (used for the Widgets, Examples, and Features cards).
     *
     * @param string $section_id  Stable identifier — used by the bulk-action JS
     *                            to find the table whose checkboxes to flip.
     * @param string $title       Card heading shown to the user.
     * @param string $desc        Short paragraph under the heading.
     * @param array  $items       Slug → registry entry (label/description/...).
     * @param string $form_key    Top-level form key — 'widgets' or 'features'.
     * @param array  $stored      Current saved-or-default values for $form_key.
     */
    function paradise_ew_render_toggle_card( string $section_id, string $title, string $desc, array $items, string $form_key, array $stored ): void {
        if ( empty( $items ) ) {
            return;
        }
        ?>
        <div class="paradise-ew-admin__card" data-toggle-section="<?php echo esc_attr( $section_id ); ?>">

            <div class="paradise-ew-admin__card-header">
                <div>
                    <h2 class="paradise-ew-admin__card-title"><?php echo esc_html( $title ); ?></h2>
                    <p class="paradise-ew-admin__card-desc"><?php echo esc_html( $desc ); ?></p>
                </div>
                <div class="paradise-ew-bulk">
                    <button type="button" class="button-link paradise-ew-bulk__btn"
                            data-bulk-target="<?php echo esc_attr( $section_id ); ?>" data-bulk-action="enable">
                        <?php esc_html_e( 'Enable all', 'paradise-widgets-for-elementor' ); ?>
                    </button>
                    <span class="paradise-ew-bulk__sep" aria-hidden="true">·</span>
                    <button type="button" class="button-link paradise-ew-bulk__btn"
                            data-bulk-target="<?php echo esc_attr( $section_id ); ?>" data-bulk-action="disable">
                        <?php esc_html_e( 'Disable all', 'paradise-widgets-for-elementor' ); ?>
                    </button>
                </div>
            </div>

            <table class="paradise-ew-toggles" data-bulk-id="<?php echo esc_attr( $section_id ); ?>">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'paradise-widgets-for-elementor' ); ?></th>
                        <th><?php esc_html_e( 'Description', 'paradise-widgets-for-elementor' ); ?></th>
                        <th><?php esc_html_e( 'Enabled', 'paradise-widgets-for-elementor' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $items as $key => $item ) :
                        $registry_default = $item['default'] ?? true;
                        $current_value    = $stored[ $key ] ?? $registry_default;
                    ?>
                    <tr>
                        <td class="paradise-ew-toggles__name">
                            <?php echo esc_html( $item['label'] ); ?>
                            <?php if ( isset( $item['default'] ) && false === $item['default'] ) : ?>
                                <span class="paradise-ew-badge paradise-ew-badge--default-off"
                                      title="<?php esc_attr_e( 'This entry is disabled by default — enable it explicitly to use it on this site.', 'paradise-widgets-for-elementor' ); ?>">
                                    <?php esc_html_e( 'Off by default', 'paradise-widgets-for-elementor' ); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="paradise-ew-toggles__desc">
                            <?php echo esc_html( $item['description'] ); ?>
                        </td>
                        <td class="paradise-ew-toggles__toggle">
                            <label class="paradise-ew-switch">
                                <input
                                    type="checkbox"
                                    name="<?php echo esc_attr( Paradise_EW_Admin::OPTION_KEY ); ?>[<?php echo esc_attr( $form_key ); ?>][<?php echo esc_attr( $key ); ?>]"
                                    value="1"
                                    <?php checked( $current_value ); ?>
                                >
                                <span class="paradise-ew-switch__slider"></span>
                            </label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
