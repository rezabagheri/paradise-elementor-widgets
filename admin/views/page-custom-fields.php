<?php
/**
 * Paradise Custom Fields — Admin page template
 *
 * Renders groups of user-defined fields. Each field row is type-agnostic
 * in markup: every value-input variant (text, textarea, url, image) is
 * present but only the one matching the row's type is visible. JS toggles
 * visibility when the type <select> changes.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$groups   = Paradise_Custom_Fields::get_groups();
$types    = Paradise_Custom_Fields::get_types();
$type_opts = Paradise_Custom_Fields::get_type_options();

/**
 * Render the type-specific value control(s) for a field row.
 * All variants are output; CSS+JS keep only the one matching $type visible.
 *
 * @param string $name_prefix  Base name attr, e.g. paradise_custom_fields[groups][0][fields][0]
 * @param string $type         Field type slug (text/textarea/url/image).
 * @param mixed  $value        Stored value (string or attachment ID).
 */
function paradise_cf_render_value( string $name_prefix, string $type, $value ): void {
    $val = is_scalar( $value ) ? (string) $value : '';
    ?>
    <div class="paradise-cf-value" data-active-type="<?php echo esc_attr( $type ); ?>">

        <!-- text -->
        <div class="paradise-cf-value-variant" data-type="text">
            <input type="text"
                class="regular-text"
                name="<?php echo esc_attr( $name_prefix ); ?>[value]"
                value="<?php echo $type === 'text' ? esc_attr( $val ) : ''; ?>"
                <?php disabled( $type !== 'text' ); ?>
                placeholder="<?php esc_attr_e( 'e.g. © 2026 Acme Inc.', 'paradise-elementor-widgets' ); ?>">
        </div>

        <!-- textarea -->
        <div class="paradise-cf-value-variant" data-type="textarea">
            <textarea
                class="large-text"
                name="<?php echo esc_attr( $name_prefix ); ?>[value]"
                rows="3"
                <?php disabled( $type !== 'textarea' ); ?>
                placeholder="<?php esc_attr_e( 'Multi-line text…', 'paradise-elementor-widgets' ); ?>"><?php echo $type === 'textarea' ? esc_textarea( $val ) : ''; ?></textarea>
        </div>

        <!-- url -->
        <div class="paradise-cf-value-variant" data-type="url">
            <input type="url"
                class="regular-text"
                name="<?php echo esc_attr( $name_prefix ); ?>[value]"
                value="<?php echo $type === 'url' ? esc_attr( $val ) : ''; ?>"
                <?php disabled( $type !== 'url' ); ?>
                placeholder="https://">
        </div>

        <!-- image (attachment ID + preview + media picker) -->
        <div class="paradise-cf-value-variant" data-type="image">
            <?php
            $img_id  = ( $type === 'image' ) ? absint( $val ) : 0;
            $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'thumbnail' ) : '';
            ?>
            <div class="paradise-cf-image-picker" data-has-image="<?php echo $img_id ? '1' : '0'; ?>">
                <input type="hidden"
                    name="<?php echo esc_attr( $name_prefix ); ?>[value]"
                    value="<?php echo esc_attr( (string) $img_id ); ?>"
                    <?php disabled( $type !== 'image' ); ?>
                    class="paradise-cf-image-id">
                <div class="paradise-cf-image-preview"<?php echo $img_url ? '' : ' hidden'; ?>>
                    <img src="<?php echo esc_url( (string) $img_url ); ?>" alt="">
                </div>
                <button type="button" class="button paradise-cf-image-choose">
                    <?php echo $img_id ? esc_html__( 'Change Image', 'paradise-elementor-widgets' ) : esc_html__( 'Choose Image', 'paradise-elementor-widgets' ); ?>
                </button>
                <button type="button" class="button-link paradise-cf-image-remove"<?php echo $img_id ? '' : ' hidden'; ?>>
                    <?php esc_html_e( 'Remove', 'paradise-elementor-widgets' ); ?>
                </button>
            </div>
        </div>

    </div>
    <?php
}

/**
 * Render a single field row inside a group.
 *
 * @param int|string $group_idx  Numeric for saved groups, '__GROUP__' inside <template>.
 * @param int|string $field_idx  Numeric for saved fields, '__INDEX__' inside <template>.
 * @param array      $field      Field data (key, label, type, value).
 * @param array      $type_opts  [slug => label] for the type <select>.
 */
function paradise_cf_render_field_row( $group_idx, $field_idx, array $field, array $type_opts ): void {
    $key   = $field['key']   ?? '';
    $label = $field['label'] ?? '';
    $type  = $field['type']  ?? 'text';
    $value = $field['value'] ?? '';
    $name  = 'paradise_custom_fields[groups][' . $group_idx . '][fields][' . $field_idx . ']';
    ?>
    <tr class="paradise-cf-field-row" data-type="<?php echo esc_attr( $type ); ?>">
        <td class="paradise-cf-col-drag"><span class="paradise-cf-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span></td>
        <td>
            <input type="text" class="regular-text paradise-cf-key-input"
                name="<?php echo esc_attr( $name ); ?>[key]"
                value="<?php echo esc_attr( $key ); ?>"
                placeholder="<?php esc_attr_e( 'unique_key', 'paradise-elementor-widgets' ); ?>">
        </td>
        <td>
            <input type="text" class="regular-text"
                name="<?php echo esc_attr( $name ); ?>[label]"
                value="<?php echo esc_attr( $label ); ?>"
                placeholder="<?php esc_attr_e( 'Human-readable label', 'paradise-elementor-widgets' ); ?>">
        </td>
        <td>
            <select name="<?php echo esc_attr( $name ); ?>[type]" class="paradise-cf-type-select">
                <?php foreach ( $type_opts as $slug => $tlabel ) : ?>
                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $type, $slug ); ?>><?php echo esc_html( $tlabel ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <?php paradise_cf_render_value( $name, $type, $value ); ?>
        </td>
        <td class="paradise-cf-actions">
            <button type="button" class="button-link paradise-cf-copy-shortcode" aria-label="<?php esc_attr_e( 'Copy shortcode', 'paradise-elementor-widgets' ); ?>" title="<?php esc_attr_e( 'Copy shortcode', 'paradise-elementor-widgets' ); ?>"><span class="dashicons dashicons-shortcode" aria-hidden="true"></span></button>
            <button type="button" class="button-link paradise-cf-remove-field" aria-label="<?php esc_attr_e( 'Remove this field', 'paradise-elementor-widgets' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
        </td>
    </tr>
    <?php
}

/**
 * Render a full group card (header + fields table).
 *
 * @param int|string $group_idx  Numeric for saved groups, '__GROUP__' inside <template>.
 * @param array      $group      Group data (slug, label, fields).
 * @param array      $type_opts  [slug => label] for the type <select>.
 */
function paradise_cf_render_group( $group_idx, array $group, array $type_opts ): void {
    $slug   = $group['slug']   ?? '';
    $label  = $group['label']  ?? '';
    $fields = $group['fields'] ?? [];
    ?>
    <div class="paradise-cf-group" data-group="<?php echo $group_idx; ?>">

        <div class="paradise-cf-group-header">
            <span class="paradise-cf-group-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'paradise-elementor-widgets' ); ?>"></span>
            <input type="text" class="paradise-cf-group-label"
                name="paradise_custom_fields[groups][<?php echo $group_idx; ?>][label]"
                value="<?php echo esc_attr( $label ); ?>"
                placeholder="<?php esc_attr_e( 'Group label (e.g. Footer)', 'paradise-elementor-widgets' ); ?>">
            <input type="text" class="paradise-cf-group-slug"
                name="paradise_custom_fields[groups][<?php echo $group_idx; ?>][slug]"
                value="<?php echo esc_attr( $slug ); ?>"
                placeholder="<?php esc_attr_e( 'slug', 'paradise-elementor-widgets' ); ?>">
            <button type="button" class="paradise-cf-group-toggle button-link" aria-expanded="true">
                <span class="dashicons dashicons-arrow-down-alt2"></span>
            </button>
            <button type="button" class="paradise-cf-remove-group button-link" aria-label="<?php esc_attr_e( 'Remove this group', 'paradise-elementor-widgets' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
        </div>

        <div class="paradise-cf-group-body">
            <table class="widefat paradise-cf-table">
                <thead>
                    <tr>
                        <th class="paradise-cf-col-drag"></th>
                        <th><?php esc_html_e( 'Key', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Label', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Type', 'paradise-elementor-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Value', 'paradise-elementor-widgets' ); ?></th>
                        <th class="paradise-cf-col-action"></th>
                    </tr>
                </thead>
                <tbody class="paradise-cf-fields" data-count="<?php echo count( $fields ); ?>">
                    <?php foreach ( $fields as $j => $field ) {
                        paradise_cf_render_field_row( $group_idx, $j, $field, $type_opts );
                    } ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="button paradise-cf-add-field" data-group="<?php echo $group_idx; ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Add Field', 'paradise-elementor-widgets' ); ?>
                </button>
            </p>
        </div>

    </div>
    <?php
}
?>
<div class="wrap paradise-ew-admin paradise-cf-wrap">

    <div class="paradise-ew-admin__header">
        <h1><?php esc_html_e( 'Custom Fields', 'paradise-elementor-widgets' ); ?></h1>
        <span class="paradise-ew-admin__version">v<?php echo esc_html( PARADISE_EW_VERSION ); ?></span>
        <span class="paradise-ew-admin__dirty" hidden>
            <?php esc_html_e( 'Unsaved changes', 'paradise-elementor-widgets' ); ?>
        </span>
    </div>

    <div class="paradise-cf-intro">
        <p><?php esc_html_e( 'Define your own static fields once, then reuse them anywhere — via shortcode or (coming soon) Elementor Dynamic Tags. Field keys are globally unique; groups are just for organisation.', 'paradise-elementor-widgets' ); ?></p>
        <ul class="paradise-cf-intro__methods">
            <li>
                <strong><?php esc_html_e( 'Shortcode:', 'paradise-elementor-widgets' ); ?></strong>
                <code>[paradise_field key="copyright"]</code>
                <?php esc_html_e( '— or for images:', 'paradise-elementor-widgets' ); ?>
                <code>[paradise_field key="logo" output="html"]</code>
            </li>
        </ul>
    </div>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
    <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Custom fields saved.', 'paradise-elementor-widgets' ); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( Paradise_Custom_Fields_Admin::NONCE ); ?>
        <input type="hidden" name="action" value="paradise_save_custom_fields">

        <div id="paradise-cf-groups" data-count="<?php echo count( $groups ); ?>">
            <?php foreach ( $groups as $i => $group ) {
                paradise_cf_render_group( $i, $group, $type_opts );
            } ?>
        </div>

        <p>
            <button type="button" id="paradise-cf-add-group" class="button">
                <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Add Group', 'paradise-elementor-widgets' ); ?>
            </button>
        </p>

        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <?php esc_html_e( 'Save Changes', 'paradise-elementor-widgets' ); ?>
            </button>
        </p>
    </form>
</div>

<!-- ── Templates (cloned by JS) ──────────────────────────────────────────── -->

<template id="paradise-cf-tpl-field-row">
    <?php paradise_cf_render_field_row( '__GROUP__', '__INDEX__', [
        'key' => '', 'label' => '', 'type' => 'text', 'value' => '',
    ], $type_opts ); ?>
</template>

<template id="paradise-cf-tpl-group">
    <?php paradise_cf_render_group( '__GROUP__', [
        'slug' => '', 'label' => '', 'fields' => [],
    ], $type_opts ); ?>
</template>
