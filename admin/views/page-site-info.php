<?php
/**
 * Paradise Site Info — Admin page template
 *
 * @var array $phones    saved phones
 * @var array $emails    saved emails
 * @var array $addresses saved addresses
 * @var array $socials   saved socials
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$phones    = Paradise_Site_Info::get( 'phones' );
$emails    = Paradise_Site_Info::get( 'emails' );
$addresses = Paradise_Site_Info::get( 'addresses' );
$socials   = Paradise_Site_Info::get( 'socials' );
$platforms = Paradise_Site_Info::social_platforms();
?>
<div class="wrap paradise-si-wrap">

    <h1><?php esc_html_e( 'Site Info', 'paradise-elementor-widgets' ); ?></h1>
    <p class="paradise-si-intro">
        <?php esc_html_e( 'Store your site\'s global contact details and social links here. Use them anywhere via Elementor Dynamic Tags (⚡) or the shortcode ', 'paradise-elementor-widgets' ); ?>
        <code>[paradise_site_info type="phone" index="0"]</code>.
    </p>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
    <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Site info saved.', 'paradise-elementor-widgets' ); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( Paradise_Site_Info_Admin::NONCE ); ?>
        <input type="hidden" name="action" value="paradise_save_site_info">

        <!-- ── Phones ─────────────────────────────────────────────────── -->
        <div class="paradise-si-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Phone Numbers', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <table class="widefat paradise-si-table" id="paradise-si-table-phones">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Label', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'Phone Number', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-phones" data-count="<?php echo count( $phones ); ?>">
                        <?php foreach ( $phones as $i => $phone ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[phones][<?php echo $i; ?>][label]" value="<?php echo esc_attr( $phone['label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Main Office', 'paradise-elementor-widgets' ); ?>"></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[phones][<?php echo $i; ?>][value]" value="<?php echo esc_attr( $phone['value'] ); ?>" placeholder="+1 888 123 4567"></td>
                            <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <template id="paradise-si-tpl-phones">
                    <tr>
                        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                        <td><input type="text" class="regular-text" name="paradise_site_info[phones][__INDEX__][label]" value="" placeholder="<?php esc_attr_e( 'e.g. Main Office', 'paradise-elementor-widgets' ); ?>"></td>
                        <td><input type="text" class="regular-text" name="paradise_site_info[phones][__INDEX__][value]" value="" placeholder="+1 888 123 4567"></td>
                        <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                    </tr>
                </template>
                <p><button type="button" class="button paradise-si-add" data-target="phones"><?php esc_html_e( '+ Add Phone', 'paradise-elementor-widgets' ); ?></button></p>
            </div>
        </div>

        <!-- ── Emails ─────────────────────────────────────────────────── -->
        <div class="paradise-si-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Email Addresses', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <table class="widefat paradise-si-table" id="paradise-si-table-emails">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Label', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'Email Address', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-emails" data-count="<?php echo count( $emails ); ?>">
                        <?php foreach ( $emails as $i => $email ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[emails][<?php echo $i; ?>][label]" value="<?php echo esc_attr( $email['label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Support', 'paradise-elementor-widgets' ); ?>"></td>
                            <td><input type="email" class="regular-text" name="paradise_site_info[emails][<?php echo $i; ?>][value]" value="<?php echo esc_attr( $email['value'] ); ?>" placeholder="hello@example.com"></td>
                            <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <template id="paradise-si-tpl-emails">
                    <tr>
                        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                        <td><input type="text" class="regular-text" name="paradise_site_info[emails][__INDEX__][label]" value="" placeholder="<?php esc_attr_e( 'e.g. Support', 'paradise-elementor-widgets' ); ?>"></td>
                        <td><input type="email" class="regular-text" name="paradise_site_info[emails][__INDEX__][value]" value="" placeholder="hello@example.com"></td>
                        <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                    </tr>
                </template>
                <p><button type="button" class="button paradise-si-add" data-target="emails"><?php esc_html_e( '+ Add Email', 'paradise-elementor-widgets' ); ?></button></p>
            </div>
        </div>

        <!-- ── Addresses ──────────────────────────────────────────────── -->
        <div class="paradise-si-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Physical Addresses', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <table class="widefat paradise-si-table" id="paradise-si-table-addresses">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Label', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'Address', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'Map URL', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-addresses" data-count="<?php echo count( $addresses ); ?>">
                        <?php foreach ( $addresses as $i => $addr ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[addresses][<?php echo $i; ?>][label]" value="<?php echo esc_attr( $addr['label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. HQ', 'paradise-elementor-widgets' ); ?>"></td>
                            <td><input type="text" class="regular-text" name="paradise_site_info[addresses][<?php echo $i; ?>][value]" value="<?php echo esc_attr( $addr['value'] ); ?>" placeholder="123 Main St, New York, NY 10001"></td>
                            <td><input type="url" class="regular-text" name="paradise_site_info[addresses][<?php echo $i; ?>][map_url]" value="<?php echo esc_attr( $addr['map_url'] ?? '' ); ?>" placeholder="https://maps.google.com/?q=..."></td>
                            <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <template id="paradise-si-tpl-addresses">
                    <tr>
                        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                        <td><input type="text" class="regular-text" name="paradise_site_info[addresses][__INDEX__][label]" value="" placeholder="<?php esc_attr_e( 'e.g. HQ', 'paradise-elementor-widgets' ); ?>"></td>
                        <td><input type="text" class="regular-text" name="paradise_site_info[addresses][__INDEX__][value]" value="" placeholder="123 Main St, New York, NY 10001"></td>
                        <td><input type="url" class="regular-text" name="paradise_site_info[addresses][__INDEX__][map_url]" value="" placeholder="https://maps.google.com/?q=..."></td>
                        <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                    </tr>
                </template>
                <p><button type="button" class="button paradise-si-add" data-target="addresses"><?php esc_html_e( '+ Add Address', 'paradise-elementor-widgets' ); ?></button></p>
            </div>
        </div>

        <!-- ── Social Links ───────────────────────────────────────────── -->
        <div class="paradise-si-card postbox">
            <div class="postbox-header">
                <h2><?php esc_html_e( 'Social Links', 'paradise-elementor-widgets' ); ?></h2>
            </div>
            <div class="inside">
                <table class="widefat paradise-si-table" id="paradise-si-table-socials">
                    <thead>
                        <tr>
                            <th class="paradise-si-col-drag"></th>
                            <th class="paradise-si-col-label"><?php esc_html_e( 'Platform', 'paradise-elementor-widgets' ); ?></th>
                            <th><?php esc_html_e( 'URL', 'paradise-elementor-widgets' ); ?></th>
                            <th class="paradise-si-col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="paradise-si-socials" data-count="<?php echo count( $socials ); ?>">
                        <?php foreach ( $socials as $i => $social ) : ?>
                        <tr>
                            <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                            <td>
                                <select name="paradise_site_info[socials][<?php echo $i; ?>][platform]" class="paradise-si-select">
                                    <?php foreach ( $platforms as $slug => $name ) : ?>
                                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $social['platform'] ?? '', $slug ); ?>><?php echo esc_html( $name ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="url" class="regular-text" name="paradise_site_info[socials][<?php echo $i; ?>][url]" value="<?php echo esc_attr( $social['url'] ); ?>" placeholder="https://"></td>
                            <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <template id="paradise-si-tpl-socials">
                    <tr>
                        <td class="paradise-si-col-drag"><span class="paradise-si-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
                        <td>
                            <select name="paradise_site_info[socials][__INDEX__][platform]" class="paradise-si-select">
                                <?php foreach ( $platforms as $slug => $name ) : ?>
                                <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="url" class="regular-text" name="paradise_site_info[socials][__INDEX__][url]" value="" placeholder="https://"></td>
                        <td><button type="button" class="button-link paradise-si-remove"><?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?></button></td>
                    </tr>
                </template>
                <p><button type="button" class="button paradise-si-add" data-target="socials"><?php esc_html_e( '+ Add Social Link', 'paradise-elementor-widgets' ); ?></button></p>
            </div>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <?php esc_html_e( 'Save Changes', 'paradise-elementor-widgets' ); ?>
            </button>
        </p>

    </form>
</div>
