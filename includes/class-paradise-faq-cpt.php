<?php
/**
 * Paradise FAQ — Custom Post Type
 *
 * Registers the `paradise_faq` CPT and `paradise_faq_cat` taxonomy.
 * Only active when the `faq_cpt` feature flag is enabled in settings.
 *
 * Data model:
 *   post_title   → question
 *   post_content → answer (WYSIWYG)
 *   menu_order   → display order (lower = first)
 *   taxonomy: paradise_faq_cat → category for widget filtering
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Paradise_FAQ_CPT {

    const POST_TYPE = 'paradise_faq';
    const TAXONOMY  = 'paradise_faq_cat';

    public static function init(): void {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
        add_action( 'init', [ __CLASS__, 'register_taxonomy' ] );
        add_filter( 'manage_' . self::POST_TYPE . '_posts_columns',       [ __CLASS__, 'admin_columns' ] );
        add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ __CLASS__, 'admin_column_content' ], 10, 2 );
    }

    // ── Registration ──────────────────────────────────────────────────────────

    public static function register_post_type(): void {
        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name'               => esc_html__( 'FAQs',              'paradise-elementor-widgets' ),
                'singular_name'      => esc_html__( 'FAQ',               'paradise-elementor-widgets' ),
                'add_new_item'       => esc_html__( 'Add New FAQ',       'paradise-elementor-widgets' ),
                'edit_item'          => esc_html__( 'Edit FAQ',          'paradise-elementor-widgets' ),
                'new_item'           => esc_html__( 'New FAQ',           'paradise-elementor-widgets' ),
                'view_item'          => esc_html__( 'View FAQ',          'paradise-elementor-widgets' ),
                'search_items'       => esc_html__( 'Search FAQs',       'paradise-elementor-widgets' ),
                'not_found'          => esc_html__( 'No FAQs found.',    'paradise-elementor-widgets' ),
                'not_found_in_trash' => esc_html__( 'No FAQs in Trash.', 'paradise-elementor-widgets' ),
                'menu_name'          => esc_html__( 'FAQs',              'paradise-elementor-widgets' ),
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'paradise-widgets',
            'show_in_rest'        => true,
            'supports'            => [ 'title', 'editor', 'page-attributes' ],
            'menu_icon'           => 'dashicons-editor-help',
            'rewrite'             => false,
            'has_archive'         => false,
        ] );
    }

    public static function register_taxonomy(): void {
        register_taxonomy( self::TAXONOMY, self::POST_TYPE, [
            'labels' => [
                'name'          => esc_html__( 'FAQ Categories',     'paradise-elementor-widgets' ),
                'singular_name' => esc_html__( 'FAQ Category',       'paradise-elementor-widgets' ),
                'add_new_item'  => esc_html__( 'Add New Category',   'paradise-elementor-widgets' ),
                'edit_item'     => esc_html__( 'Edit Category',      'paradise-elementor-widgets' ),
                'search_items'  => esc_html__( 'Search Categories',  'paradise-elementor-widgets' ),
                'not_found'     => esc_html__( 'No categories found.', 'paradise-elementor-widgets' ),
                'menu_name'     => esc_html__( 'Categories',         'paradise-elementor-widgets' ),
            ],
            'public'            => false,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'hierarchical'      => true,
            'rewrite'           => false,
        ] );
    }

    // ── Admin list columns ────────────────────────────────────────────────────

    public static function admin_columns( array $columns ): array {
        $new = [];
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'title' === $key ) {
                $new['faq_answer_preview'] = esc_html__( 'Answer Preview', 'paradise-elementor-widgets' );
            }
        }
        return $new;
    }

    public static function admin_column_content( string $column, int $post_id ): void {
        if ( 'faq_answer_preview' !== $column ) {
            return;
        }
        $content = get_post_field( 'post_content', $post_id );
        $plain   = wp_strip_all_tags( $content );
        echo esc_html( mb_strlen( $plain ) > 80 ? mb_substr( $plain, 0, 80 ) . '…' : $plain );
    }

    // ── Data access ───────────────────────────────────────────────────────────

    /**
     * Query FAQ posts and return them as [ ['question' => …, 'answer' => …], … ].
     *
     * @param int    $category_id  Term ID of paradise_faq_cat (0 = all).
     * @param int    $limit        Max posts (-1 = all).
     * @param string $orderby      'menu_order' | 'date' | 'title'.
     */
    public static function get_items( int $category_id = 0, int $limit = -1, string $orderby = 'menu_order' ): array {
        $args = [
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => in_array( $orderby, [ 'menu_order', 'date', 'title' ], true ) ? $orderby : 'menu_order',
            'order'          => 'title' === $orderby ? 'ASC' : 'ASC',
            'no_found_rows'  => true,
        ];

        if ( $category_id > 0 ) {
            $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery
                [
                    'taxonomy' => self::TAXONOMY,
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ],
            ];
        }

        $posts = get_posts( $args );
        $items = [];

        foreach ( $posts as $post ) {
            $items[] = [
                'question' => get_the_title( $post ),
                'answer'   => apply_filters( 'the_content', $post->post_content ),
            ];
        }

        return $items;
    }

    /**
     * Return [ term_id => name ] options for the category SELECT control.
     */
    public static function get_category_options(): array {
        $terms = get_terms( [
            'taxonomy'   => self::TAXONOMY,
            'hide_empty' => false,
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return [ 0 => esc_html__( 'All', 'paradise-elementor-widgets' ) ];
        }

        $options = [ 0 => esc_html__( 'All', 'paradise-elementor-widgets' ) ];
        foreach ( $terms as $term ) {
            $options[ $term->term_id ] = $term->name;
        }

        return $options;
    }
}
