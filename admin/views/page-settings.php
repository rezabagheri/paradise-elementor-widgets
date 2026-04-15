<?php
/**
 * Admin view — Paradise Elementor Widgets settings page.
 * Variables available: $settings (array from Paradise_EW_Admin::get())
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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

    <form method="post" action="options.php">
        <?php settings_fields( Paradise_EW_Admin::OPTION_KEY . '_group' ); ?>

        <div class="paradise-ew-admin__card">
            <h2 class="paradise-ew-admin__card-title">
                <?php esc_html_e( 'Widgets', 'paradise-elementor-widgets' ); ?>
            </h2>
            <p class="paradise-ew-admin__card-desc">
                <?php esc_html_e( 'Enable or disable individual widgets. Disabled widgets are not registered with Elementor and will not appear in the widget panel.', 'paradise-elementor-widgets' ); ?>
            </p>

            <table class="paradise-ew-toggles">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Widget', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Description', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Enabled', 'paradise-elementor-widgets' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( Paradise_EW_Admin::get_widget_registry() as $key => $widget ) : ?>
                    <tr>
                        <td class="paradise-ew-toggles__name">
                            <?php echo esc_html( $widget['label'] ); ?>
                        </td>
                        <td class="paradise-ew-toggles__desc">
                            <?php echo esc_html( $widget['description'] ); ?>
                        </td>
                        <td class="paradise-ew-toggles__toggle">
                            <label class="paradise-ew-switch">
                                <input
                                    type="checkbox"
                                    name="<?php echo esc_attr( Paradise_EW_Admin::OPTION_KEY ); ?>[widgets][<?php echo esc_attr( $key ); ?>]"
                                    value="1"
                                    <?php checked( $settings['widgets'][ $key ] ?? true ); ?>
                                >
                                <span class="paradise-ew-switch__slider"></span>
                            </label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="paradise-ew-admin__card">
            <h2 class="paradise-ew-admin__card-title">
                <?php esc_html_e( 'Features', 'paradise-elementor-widgets' ); ?>
            </h2>
            <p class="paradise-ew-admin__card-desc">
                <?php esc_html_e( 'Enable or disable optional plugin features.', 'paradise-elementor-widgets' ); ?>
            </p>

            <table class="paradise-ew-toggles">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Feature', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Description', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Enabled', 'paradise-elementor-widgets' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( Paradise_EW_Admin::get_feature_registry() as $key => $item ) : ?>
                    <tr>
                        <td class="paradise-ew-toggles__name">
                            <?php echo esc_html( $item['label'] ); ?>
                        </td>
                        <td class="paradise-ew-toggles__desc">
                            <?php echo esc_html( $item['description'] ); ?>
                        </td>
                        <td class="paradise-ew-toggles__toggle">
                            <label class="paradise-ew-switch">
                                <input
                                    type="checkbox"
                                    name="<?php echo esc_attr( Paradise_EW_Admin::OPTION_KEY ); ?>[features][<?php echo esc_attr( $key ); ?>]"
                                    value="1"
                                    <?php checked( $settings['features'][ $key ] ?? true ); ?>
                                >
                                <span class="paradise-ew-switch__slider"></span>
                            </label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php submit_button( esc_html__( 'Save Settings', 'paradise-elementor-widgets' ) ); ?>
    </form>

</div>
