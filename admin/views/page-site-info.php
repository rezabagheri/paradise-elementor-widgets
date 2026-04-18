<?php
/**
 * Paradise Site Info — Admin page template
 *
 * Supports multiple locations, each with phones, emails, address, map, and hours.
 * Social links and business name are global (per brand, not per location).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$name      = Paradise_Site_Info::get_name();
$socials   = Paradise_Site_Info::get( 'socials' );
$locations = Paradise_Site_Info::get_locations();
$days      = Paradise_Site_Info::days();
$platforms = Paradise_Site_Info::social_platforms();
$def_hours = Paradise_Site_Info::default_hours();

if ( empty( $locations ) ) {
    $locations = [
        [ 'label' => '', 'phones' => [], 'emails' => [], 'address' => '', 'map_url' => '', 'hours' => [] ],
    ];
}

/**
 * Render a full location card (header + body).
 *
 * Called for each saved location (integer $loc_idx) and once inside the
 * <template> element (string '__LOC__') so JS can clone it for new locations.
 *
 * @param int|string $loc_idx  Location index or '__LOC__' placeholder.
 * @param array      $loc      Location data (label, phones, emails, address, map_url).
 * @param array      $hours    Merged hours for this location (all 7 days present).
 * @param array      $days     Day slug → display label map.
 * @param array      $platforms Platform slug → display name map.
 */
function paradise_si_render_location( $loc_idx, array $loc, array $hours, array $days, array $platforms ): void {
    $label   = $loc['label']   ?? '';
    $phones  = $loc['phones']  ?? [];
    $emails  = $loc['emails']  ?? [];
    $address = $loc['address'] ?? '';
    $map_url = $loc['map_url'] ?? '';
    ?>
    <div class="paradise-si-location" data-location="<?php echo $loc_idx; ?>">

        <!-- ── Header ─────────────────────────────────────────────────────── -->
        <div class="paradise-si-location-header">
            <span class="paradise-si-loc-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span>
            <input
                type="text"
                class="paradise-si-location-label"
                name="paradise_site_info[locations][<?php echo $loc_idx; ?>][label]"
                value="<?php echo esc_attr( $label ); ?>"
                placeholder="<?php esc_attr_e( 'e.g. Main Branch', 'paradise-elementor-widgets' ); ?>"
            >
            <button type="button" class="paradise-si-location-toggle button-link" aria-expanded="true">
                <span class="dashicons dashicons-arrow-down-alt2"></span>
            </button>
            <button type="button" class="paradise-si-remove-location button-link"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button>
        </div>

        <!-- ── Body ──────────────────────────────────────────────────────── -->
        <div class="paradise-si-location-body">

            <!-- Phones ──────────────────────────────────────────────────── -->
            <div class="paradise-si-section">
                <h4 class="paradise-si-section-title"><?php esc_html_e( 'Phone Numbers', 'paradise-elementor-widgets' ); ?></h4>
                <table class="widefat paradise-si-table">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Label', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'Phone Number', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-phones-<?php echo $loc_idx; ?>" class="paradise-si-rows" data-count="<?php echo count( $phones ); ?>">
                        <?php foreach ( $phones as $j => $phone ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[locations][<?php echo $loc_idx; ?>][phones][<?php echo $j; ?>][label]" value="<?php echo esc_attr( $phone['label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Main Office', 'paradise-elementor-widgets' ); ?>"></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[locations][<?php echo $loc_idx; ?>][phones][<?php echo $j; ?>][value]" value="<?php echo esc_attr( $phone['value'] ); ?>" placeholder="+1 888 123 4567"></td>
                            <td><button type="button" class="button-link paradise-si-remove-row"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><button type="button" class="button paradise-si-add-row" data-section="phones" data-location="<?php echo $loc_idx; ?>"><?php esc_html_e( '+ Add Phone', 'paradise-elementor-widgets' ); ?></button></p>
            </div>

            <!-- Emails ──────────────────────────────────────────────────── -->
            <div class="paradise-si-section">
                <h4 class="paradise-si-section-title"><?php esc_html_e( 'Email Addresses', 'paradise-elementor-widgets' ); ?></h4>
                <table class="widefat paradise-si-table">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Label', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'Email Address', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-emails-<?php echo $loc_idx; ?>" class="paradise-si-rows" data-count="<?php echo count( $emails ); ?>">
                        <?php foreach ( $emails as $j => $email ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[locations][<?php echo $loc_idx; ?>][emails][<?php echo $j; ?>][label]" value="<?php echo esc_attr( $email['label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Support', 'paradise-elementor-widgets' ); ?>"></td>
                            <td><input type="email" class="regular-text" name="paradise_site_info[locations][<?php echo $loc_idx; ?>][emails][<?php echo $j; ?>][value]" value="<?php echo esc_attr( $email['value'] ); ?>" placeholder="hello@example.com"></td>
                            <td><button type="button" class="button-link paradise-si-remove-row"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><button type="button" class="button paradise-si-add-row" data-section="emails" data-location="<?php echo $loc_idx; ?>"><?php esc_html_e( '+ Add Email', 'paradise-elementor-widgets' ); ?></button></p>
            </div>

            <!-- Address & Map ────────────────────────────────────────────── -->
            <div class="paradise-si-section">
                <h4 class="paradise-si-section-title"><?php esc_html_e( 'Address & Map', 'paradise-elementor-widgets' ); ?></h4>
                <table class="paradise-si-kv-table">
                    <tbody>
                        <tr>
                            <th><?php esc_html_e( 'Address', 'paradise-elementor-widgets' ); ?></th>
                            <td>
                                <input type="text" class="regular-text"
                                    name="paradise_site_info[locations][<?php echo $loc_idx; ?>][address]"
                                    value="<?php echo esc_attr( $address ); ?>"
                                    placeholder="<?php esc_attr_e( '123 Main St, New York, NY 10001', 'paradise-elementor-widgets' ); ?>"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Map URL', 'paradise-elementor-widgets' ); ?></th>
                            <td>
                                <input type="url" class="regular-text paradise-si-map-url-input"
                                    name="paradise_site_info[locations][<?php echo $loc_idx; ?>][map_url]"
                                    value="<?php echo esc_attr( $map_url ); ?>"
                                    placeholder="https://www.google.com/maps/embed?pb=..."
                                >
                                <p class="description"><?php esc_html_e( 'Google Maps → Share → Embed a map → copy the src URL from the iframe code.', 'paradise-elementor-widgets' ); ?></p>
                                <div class="paradise-si-map-preview"<?php echo $map_url ? '' : ' style="display:none"'; ?>>
                                    <iframe
                                        src="<?php echo esc_url( $map_url ); ?>"
                                        loading="lazy"
                                        allowfullscreen
                                        referrerpolicy="no-referrer-when-downgrade"
                                    ></iframe>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Business Hours ───────────────────────────────────────────── -->
            <div class="paradise-si-section">
                <h4 class="paradise-si-section-title"><?php esc_html_e( 'Business Hours', 'paradise-elementor-widgets' ); ?></h4>
                <table class="widefat paradise-si-table paradise-si-hours-table">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-day"><?php esc_html_e( 'Day', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-open"><?php esc_html_e( 'Open', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'From', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'To', 'paradise-elementor-widgets' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $days as $slug => $day_label ) :
                            $entry   = $hours[ $slug ] ?? [ 'open' => false, 'from' => '09:00', 'to' => '17:00' ];
                            $is_open = ! empty( $entry['open'] );
                            $row_id  = 'paradise-si-hours-row-' . $loc_idx . '-' . $slug;
                        ?>
                        <tr id="<?php echo esc_attr( $row_id ); ?>" class="paradise-si-hours-row<?php echo $is_open ? '' : ' paradise-si-hours-closed'; ?>">
                            <td class="paradise-si-col-day"><strong><?php echo esc_html( $day_label ); ?></strong></td>
                            <td class="paradise-si-col-open">
                                <label class="paradise-si-toggle">
                                    <input
                                        type="checkbox"
                                        name="paradise_site_info[locations][<?php echo $loc_idx; ?>][hours][<?php echo esc_attr( $slug ); ?>][open]"
                                        value="1"
                                        <?php checked( $is_open ); ?>
                                        data-row="<?php echo esc_attr( $row_id ); ?>"
                                        class="paradise-si-hours-toggle"
                                    >
                                    <span class="paradise-si-toggle-label"><?php echo $is_open ? esc_html__( 'Open', 'paradise-elementor-widgets' ) : esc_html__( 'Closed', 'paradise-elementor-widgets' ); ?></span>
                                </label>
                            </td>
                            <td>
                                <input type="time" class="paradise-si-time"
                                    name="paradise_site_info[locations][<?php echo $loc_idx; ?>][hours][<?php echo esc_attr( $slug ); ?>][from]"
                                    value="<?php echo esc_attr( $entry['from'] ?? '09:00' ); ?>"
                                    <?php echo $is_open ? '' : 'disabled'; ?>
                                >
                            </td>
                            <td>
                                <input type="time" class="paradise-si-time"
                                    name="paradise_site_info[locations][<?php echo $loc_idx; ?>][hours][<?php echo esc_attr( $slug ); ?>][to]"
                                    value="<?php echo esc_attr( $entry['to'] ?? '17:00' ); ?>"
                                    <?php echo $is_open ? '' : 'disabled'; ?>
                                >
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- .paradise-si-location-body -->
    </div><!-- .paradise-si-location -->
    <?php
}
?>
<div class="wrap paradise-si-wrap">

    <h1><?php esc_html_e( 'Site Info', 'paradise-elementor-widgets' ); ?></h1>
    <p class="paradise-si-intro">
        <?php esc_html_e( 'Store your business details here. Use them via Elementor Dynamic Tags (⚡) or the shortcode ', 'paradise-elementor-widgets' ); ?>
        <code>[paradise_site_info type="phone" location="0"]</code>.
    </p>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
    <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Site info saved.', 'paradise-elementor-widgets' ); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( Paradise_Site_Info_Admin::NONCE ); ?>
        <input type="hidden" name="action" value="paradise_save_site_info">

        <!-- ── Business Name ──────────────────────────────────────────────── -->
        <div class="paradise-si-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Business Name', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <p>
                    <input
                        type="text"
                        name="paradise_site_info[name]"
                        value="<?php echo esc_attr( $name ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'e.g. Acme Corp', 'paradise-elementor-widgets' ); ?>"
                    >
                </p>
            </div>
        </div>

        <!-- ── Social Links (global) ──────────────────────────────────────── -->
        <div class="paradise-si-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Social Links', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <table class="widefat paradise-si-table">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Platform', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'URL', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-socials" class="paradise-si-rows-global" data-count="<?php echo count( $socials ); ?>">
                        <?php foreach ( $socials as $i => $social ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td>
                                <select name="paradise_site_info[socials][<?php echo $i; ?>][platform]" class="paradise-si-select">
                                    <?php foreach ( $platforms as $slug => $pname ) : ?>
                                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $social['platform'] ?? '', $slug ); ?>><?php echo esc_html( $pname ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="url" class="regular-text" name="paradise_site_info[socials][<?php echo $i; ?>][url]" value="<?php echo esc_attr( $social['url'] ); ?>" placeholder="https://"></td>
                            <td><button type="button" class="button-link paradise-si-remove-row"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><button type="button" id="paradise-si-add-social" class="button"><?php esc_html_e( '+ Add Social Link', 'paradise-elementor-widgets' ); ?></button></p>
            </div>
        </div>

        <!-- ── Locations ──────────────────────────────────────────────────── -->
        <div class="paradise-si-card paradise-si-locations-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Locations', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <div id="paradise-si-locations" data-count="<?php echo count( $locations ); ?>">
                    <?php foreach ( $locations as $i => $loc ) :
                        paradise_si_render_location( $i, $loc, Paradise_Site_Info::get_hours( $i ), $days, $platforms );
                    endforeach; ?>
                </div>
                <p>
                    <button type="button" id="paradise-si-add-location" class="button"><?php esc_html_e( '+ Add Location', 'paradise-elementor-widgets' ); ?></button>
                </p>
            </div>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <?php esc_html_e( 'Save Changes', 'paradise-elementor-widgets' ); ?>
            </button>
        </p>

    </form>
</div>

<!-- ── Templates ──────────────────────────────────────────────────────────── -->

<template id="paradise-si-tpl-social-row">
    <tr>
        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
        <td>
            <select name="paradise_site_info[socials][__INDEX__][platform]" class="paradise-si-select">
                <?php foreach ( $platforms as $slug => $pname ) : ?>
                <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $pname ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="url" class="regular-text" name="paradise_site_info[socials][__INDEX__][url]" value="" placeholder="https://"></td>
        <td><button type="button" class="button-link paradise-si-remove-row"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
    </tr>
</template>

<template id="paradise-si-tpl-phones-row">
    <tr>
        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
        <td><input type="text" class="regular-text" name="paradise_site_info[locations][__LOC__][phones][__INDEX__][label]" value="" placeholder="<?php esc_attr_e( 'e.g. Main Office', 'paradise-elementor-widgets' ); ?>"></td>
        <td><input type="text" class="regular-text" name="paradise_site_info[locations][__LOC__][phones][__INDEX__][value]" value="" placeholder="+1 888 123 4567"></td>
        <td><button type="button" class="button-link paradise-si-remove-row"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
    </tr>
</template>

<template id="paradise-si-tpl-emails-row">
    <tr>
        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
        <td><input type="text" class="regular-text" name="paradise_site_info[locations][__LOC__][emails][__INDEX__][label]" value="" placeholder="<?php esc_attr_e( 'e.g. Support', 'paradise-elementor-widgets' ); ?>"></td>
        <td><input type="email" class="regular-text" name="paradise_site_info[locations][__LOC__][emails][__INDEX__][value]" value="" placeholder="hello@example.com"></td>
        <td><button type="button" class="button-link paradise-si-remove-row"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
    </tr>
</template>

<template id="paradise-si-tpl-location">
    <?php paradise_si_render_location( '__LOC__', [
        'label'   => '',
        'phones'  => [],
        'emails'  => [],
        'address' => '',
        'map_url' => '',
    ], $def_hours, $days, $platforms ); ?>
</template>
