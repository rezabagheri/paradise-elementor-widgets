<?php
/**
 * Admin view — Paradise Elementor Widgets settings page.
 * Variables available: $settings (array from Paradise_EW_Admin::get())
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Bucket the widget registry into production and examples so each group
// renders into its own table with its own bulk-action header. The 'example'
// flag is set per-widget in Paradise_EW_Admin::$widget_registry.
$widget_groups = [
    'production' => [],
    'examples'   => [],
];
foreach ( Paradise_EW_Admin::get_widget_registry() as $key => $widget ) {
    $bucket = ! empty( $widget['example'] ) ? 'examples' : 'production';
    $widget_groups[ $bucket ][ $key ] = $widget;
}

/**
 * Render one group of toggle rows.
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
                    <?php esc_html_e( 'Enable all', 'paradise-elementor-widgets' ); ?>
                </button>
                <span class="paradise-ew-bulk__sep" aria-hidden="true">·</span>
                <button type="button" class="button-link paradise-ew-bulk__btn"
                        data-bulk-target="<?php echo esc_attr( $section_id ); ?>" data-bulk-action="disable">
                    <?php esc_html_e( 'Disable all', 'paradise-elementor-widgets' ); ?>
                </button>
            </div>
        </div>

        <table class="paradise-ew-toggles" data-bulk-id="<?php echo esc_attr( $section_id ); ?>">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Name', 'paradise-elementor-widgets' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'paradise-elementor-widgets' ); ?></th>
                    <th><?php esc_html_e( 'Enabled', 'paradise-elementor-widgets' ); ?></th>
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
                                  title="<?php esc_attr_e( 'This entry is disabled by default — enable it explicitly to use it on this site.', 'paradise-elementor-widgets' ); ?>">
                                <?php esc_html_e( 'Off by default', 'paradise-elementor-widgets' ); ?>
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
?>
<div class="wrap paradise-ew-admin">

    <div class="paradise-ew-admin__header">
        <h1><?php esc_html_e( 'Paradise Elementor Widgets', 'paradise-elementor-widgets' ); ?></h1>
        <span class="paradise-ew-admin__version">
            v<?php echo esc_html( PARADISE_EW_VERSION ); ?>
        </span>
    </div>

    <?php settings_errors(); ?>

    <div class="paradise-ew-filter">
        <label for="paradise-ew-filter-input" class="screen-reader-text">
            <?php esc_html_e( 'Filter widgets and features', 'paradise-elementor-widgets' ); ?>
        </label>
        <input
            type="search"
            id="paradise-ew-filter-input"
            class="paradise-ew-filter__input"
            placeholder="<?php esc_attr_e( 'Filter — type to narrow widgets and features…', 'paradise-elementor-widgets' ); ?>"
            data-paradise-filter
            autocomplete="off"
        >
    </div>

    <form method="post" action="options.php">
        <?php settings_fields( Paradise_EW_Admin::OPTION_KEY . '_group' ); ?>

        <?php paradise_ew_render_toggle_card(
            'widgets-production',
            esc_html__( 'Widgets', 'paradise-elementor-widgets' ),
            esc_html__( 'Enable or disable individual widgets. Disabled widgets are not registered with Elementor and will not appear in the widget panel.', 'paradise-elementor-widgets' ),
            $widget_groups['production'],
            'widgets',
            $settings['widgets'] ?? []
        ); ?>

        <?php paradise_ew_render_toggle_card(
            'widgets-examples',
            esc_html__( 'Developer Examples', 'paradise-elementor-widgets' ),
            esc_html__( 'Reference widgets shipped to help developers learn the Paradise widget patterns. They live in a separate "Paradise Examples" category in the editor and are disabled by default — enable them when you want to inspect the reference inside Elementor.', 'paradise-elementor-widgets' ),
            $widget_groups['examples'],
            'widgets',
            $settings['widgets'] ?? []
        ); ?>

        <?php paradise_ew_render_toggle_card(
            'features',
            esc_html__( 'Features', 'paradise-elementor-widgets' ),
            esc_html__( 'Enable or disable optional plugin features.', 'paradise-elementor-widgets' ),
            Paradise_EW_Admin::get_feature_registry(),
            'features',
            $settings['features'] ?? []
        ); ?>

        <?php submit_button( esc_html__( 'Save Settings', 'paradise-elementor-widgets' ) ); ?>
    </form>

</div>
