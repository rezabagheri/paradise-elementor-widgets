<?php
/**
 * Paradise Import / Export — Admin page template
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$import_status = isset( $_GET['import'] ) ? sanitize_key( $_GET['import'] ) : '';
$import_reason = isset( $_GET['reason'] ) ? sanitize_key( $_GET['reason'] ) : '';

$error_messages = [
    'no_file'      => esc_html__( 'No file was uploaded. Please choose a JSON file and try again.', 'paradise-elementor-widgets' ),
    'not_json'     => esc_html__( 'The file must be a .json file. Please upload the correct file.', 'paradise-elementor-widgets' ),
    'read_error'   => esc_html__( 'The file could not be read. Please check file permissions and try again.', 'paradise-elementor-widgets' ),
    'invalid_json' => esc_html__( 'The file does not contain valid JSON. It may be corrupt or not a Paradise export file.', 'paradise-elementor-widgets' ),
];
?>
<div class="wrap">

    <h1><?php esc_html_e( 'Import / Export', 'paradise-elementor-widgets' ); ?></h1>

    <?php if ( 'success' === $import_status ) : ?>
    <div class="notice notice-success is-dismissible">
        <p><?php esc_html_e( 'Import complete. Your data has been restored successfully.', 'paradise-elementor-widgets' ); ?></p>
    </div>
    <?php elseif ( 'error' === $import_status ) : ?>
    <div class="notice notice-error is-dismissible">
        <p><?php echo $error_messages[ $import_reason ] ?? esc_html__( 'Import failed. Please try again.', 'paradise-elementor-widgets' ); ?></p>
    </div>
    <?php endif; ?>

    <!-- ── Export ─────────────────────────────────────────────────────────── -->
    <div class="postbox paradise-ie-card">
        <div class="postbox-header">
            <h2><?php esc_html_e( 'Export', 'paradise-elementor-widgets' ); ?></h2>
        </div>
        <div class="inside">
            <p><?php esc_html_e( 'Download a backup of your Site Info (locations, phones, emails, addresses, social links, business hours) and widget settings as a JSON file.', 'paradise-elementor-widgets' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( Paradise_Import_Export::NONCE_EXPORT ); ?>
                <input type="hidden" name="action" value="paradise_export_data">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e( 'Export Data', 'paradise-elementor-widgets' ); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- ── Import ─────────────────────────────────────────────────────────── -->
    <div class="postbox paradise-ie-card">
        <div class="postbox-header">
            <h2><?php esc_html_e( 'Import', 'paradise-elementor-widgets' ); ?></h2>
        </div>
        <div class="inside">
            <p><?php esc_html_e( 'Upload a previously exported Paradise JSON file to restore your data.', 'paradise-elementor-widgets' ); ?></p>
            <div class="notice notice-warning inline">
                <p><?php esc_html_e( 'This will overwrite your current Site Info and widget settings. Export first if you want to keep the existing data.', 'paradise-elementor-widgets' ); ?></p>
            </div>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                <?php wp_nonce_field( Paradise_Import_Export::NONCE_IMPORT ); ?>
                <input type="hidden" name="action" value="paradise_import_data">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="paradise_import_file"><?php esc_html_e( 'JSON File', 'paradise-elementor-widgets' ); ?></label>
                        </th>
                        <td>
                            <input type="file" id="paradise_import_file" name="paradise_import_file" accept=".json,application/json">
                        </td>
                    </tr>
                </table>
                <button type="submit" class="button button-primary" id="paradise-import-btn" disabled>
                    <?php esc_html_e( 'Import Data', 'paradise-elementor-widgets' ); ?>
                </button>
            </form>
            <script>
            document.getElementById('paradise_import_file').addEventListener('change', function () {
                document.getElementById('paradise-import-btn').disabled = !this.files.length;
            });
            </script>
        </div>
    </div>

</div>

<style>
.paradise-ie-card {
    max-width: 700px;
    margin-bottom: 20px;
}
.paradise-ie-card .postbox-header h2 {
    font-size: 14px;
}
.paradise-ie-card .inside {
    padding: 12px 16px 16px;
}
.paradise-ie-card .inside p {
    margin-top: 0;
}
.paradise-ie-card .notice.inline {
    margin: 10px 0 14px;
}
</style>
