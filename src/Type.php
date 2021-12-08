<?php
/*
 * @package marspress/post-type
 */

namespace MarsPress\PostType;

if( ! class_exists( 'Type' ) )
{

    final class Type
    {

        private string $key;

        private bool $translateInterface;

        private bool $override;

        private array $arguments;

        private string $adminNotices;

        private array $rewriteRules;

        public function __construct(
            string $_key,
            string $_labelSingle,
            string $_labelPlural,
            array $_supports,
            string $_slug = '',
            string $_icon = 'dashicons-admin-post',
            int $_menuPosition = 30,
            bool $_hasArchive = true,
            bool $_public = true,
            bool $_showInRest = true,
            bool $_override = false,
            bool $_translateInterface = true,
            string $_textDomain = ''
        )
        {

            if( strlen( $_slug ) === 0 ){
                $_slug = $_key;
            }

            if( strlen( $_textDomain ) === 0 ){
                $_textDomain = $_key;
            }

            $this->key = $_key;
            $this->translateInterface = $_translateInterface;
            $this->override = $_override;
            $this->arguments = [
                'label'             => $_labelSingle,
                'has_archive'       => $_hasArchive,
                'public'            => $_public,
                'menu_icon'         => $_icon,
                'menu_position'     => $_menuPosition,
                'show_in_rest'      => $_showInRest,
                'supports'          => $_supports,
                'labels'            => $this->get_labels( $_labelSingle, $_labelPlural, $_textDomain ),
                'rewrite'           => [
                    'slug'          => $_slug,
                    'with_front'    => true
                ],
                'query_var'         => $_key,
                'capability_type'   => 'post',
                'map_meta_cap'      => true,
            ];

            add_action( 'init', [ $this, 'register_post_type' ], 10, 0 );
            add_action( 'init', [ $this, 'register_rewrite_rules' ], 10, 0 );

        }

        public function get_key(): string
        {

            return $this->key;

        }

        public function get_object(): ?\WP_Post_Type
        {

            return \get_post_type_object( $this->key );

        }

        public function register_post_type()
        {

            if( ! $this->override && \post_type_exists( $this->key ) ){

                $this->adminNotices = "The post type <strong><em>{$this->key}</em></strong> already exists. Please update your post type to something unique.";
                add_action( 'admin_notices', function (){
                    $message = $this->output_admin_notice();
                    echo $message;
                }, 10, 0 );
                return;

            }

            \register_post_type( $this->key, $this->arguments );

        }

        public function register_rewrite_rules()
        {

            if( isset( $this->rewriteRules ) ){

                foreach ( $this->rewriteRules as $_rule ){

                    \add_rewrite_rule( $_rule['rule'], $_rule['match'], $_rule['after'] );

                }

            }

        }

        public function add_rewrite_rule( $_rule, $_match, $_after = 'top' )
        {

            if( ! isset( $this->rewriteRules ) ){

                $this->rewriteRules = [];

            }

            $this->rewriteRules[] = [
                'rule'      => $_rule,
                'match'     => $_match,
                'after'     => $_after,
            ];

        }

        private function output_admin_notice(): string
        {

            if( isset( $this->adminNotices ) && \current_user_can( 'administrator' ) ){

                return "<div style='background: white; padding: 12px 20px; border-radius: 3px; border-left: 5px solid #dc3545;' class='notice notice-error is-dismissible'><p style='font-size: 16px;'>$this->adminNotices</p><small><em>This message is only visible to site admins</em></small></div>";

            }

            return '';

        }

        private function get_labels( $_single, $_plural, $_textDomain ): array
        {

            if( $this->translateInterface ){

                return [
                    'name'                              => _x($_plural, 'Post type general name', $_textDomain),
                    'singular_name'                     => _x($_single, 'Post type singular name', $_textDomain),
                    'menu_name'                         => _x($_plural, 'Admin Menu text', $_textDomain),
                    'name_admin_bar'                    => _x($_single, 'Add New on Toolbar', $_textDomain),
                    'add_new'                           => __("Add New $_single", $_textDomain),
                    'add_new_item'                      => __("Add New $_single", $_textDomain),
                    'new_item'                          => __("New $_single", $_textDomain),
                    'edit_item'                         => __("Edit $_single", $_textDomain),
                    'view_item'                         => __("View $_single", $_textDomain),
                    'all_items'                         => __("All $_plural", $_textDomain),
                    'search_items'                      => __("Search $_plural", $_textDomain),
                    'parent_item_colon'                 => __("Parent $_single:", $_textDomain),
                    'not_found'                         => __("No $_plural found.", $_textDomain),
                    'not_found_in_trash'                => __("No $_plural found in Trash.", $_textDomain),
                    'featured_image'                    => _x("$_single Cover Image", 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', $_textDomain),
                    'set_featured_image'                => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', $_textDomain),
                    'remove_featured_image'             => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', $_textDomain),
                    'use_featured_image'                => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', $_textDomain),
                    'archives'                          => _x("$_plural archives", 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', $_textDomain),
                    'insert_into_item'                  => _x("Insert into $_single", 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', $_textDomain),
                    'uploaded_to_this_item'             => _x("Uploaded to this $_single", 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', $_textDomain),
                    'filter_items_list'                 => _x("Filter $_plural list", 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', $_textDomain),
                    'items_list_navigation'             => _x("$_plural list navigation", 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', $_textDomain),
                    'items_list'                        => _x("$_plural list", 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', $_textDomain),
                    'item_published'                    => __("$_single published", $_textDomain),
                    'item_published_privately'          => __("$_single published privately", $_textDomain),
                    'item_reverted_to_draft'            => __("$_single reverted to draft", $_textDomain),
                    'item_scheduled'                    => __("$_single scheduled", $_textDomain),
                    'item_updated'                      => __("$_single updated", $_textDomain),
                ];

            }

            return [
                'name'                              => $_plural,
                'singular_name'                     => $_single,
                'menu_name'                         => $_plural,
                'name_admin_bar'                    => $_single,
                'add_new'                           => "Add New $_single",
                'add_new_item'                      => "Add New $_single",
                'new_item'                          => "New $_single",
                'edit_item'                         => "Edit $_single",
                'view_item'                         => "View $_single",
                'all_items'                         => "All $_plural",
                'search_items'                      => "Search $_plural",
                'parent_item_colon'                 => "Parent $_single:",
                'not_found'                         => "No $_plural found.",
                'not_found_in_trash'                => "No $_plural found in Trash.",
                'featured_image'                    => "$_single Cover Image",
                'set_featured_image'                => 'Set cover image',
                'remove_featured_image'             => 'Remove cover image',
                'use_featured_image'                => 'Use as cover image',
                'archives'                          => "$_plural archives",
                'insert_into_item'                  => "Insert into $_single",
                'uploaded_to_this_item'             => "Uploaded to this $_single",
                'filter_items_list'                 => "Filter $_plural list",
                'items_list_navigation'             => "$_plural list navigation",
                'items_list'                        => "$_plural list",
                'item_published'                    => "$_single published",
                'item_published_privately'          => "$_single published privately",
                'item_reverted_to_draft'            => "$_single reverted to draft",
                'item_scheduled'                    => "$_single scheduled",
                'item_updated'                      => "$_single updated",
            ];

        }

    }

}