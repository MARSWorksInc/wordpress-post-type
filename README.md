# MarsPress PostType
### Installation
Require the composer package in your composer.json with `marspress/post-type` with minimum `dev-main` OR run `composer require marspress/post-type`

### Resource
* https://developer.wordpress.org/reference/functions/register_post_type/

### Usage

#### Creating a New Post Type
`$myPostType = new \MarsPress\PostType\Type();`

The Type class  takes 13 parameters, 4 required and 9 optional.
* Key (required)(string)
    * The unique identifier for the post type.
    * This should only contain lower case characters, hyphens, and underscores.
    * IMPORTANT: the key should not exceed 20 characters in length. Post Type keys in WordPress can only be up to 20 characters long.
* Single Label (required)(string)
    * The single label for your Post Type.
    * This displays in admin menus and areas.
* Plural Label (required)(string)
    * The single label for your Post Type.
    * This displays in admin menus and areas.
* Supports (required)(array)
    * An array of WordPress features that the Post Type supports.
    * Resource: https://developer.wordpress.org/reference/functions/register_post_type/#supports
    * Generally, the simplest post types support `title`, `editor`, `thumbnail`. Although if your post type only is making use of custom fields, you may not need `editor`
* Slug (optional)(string)
  * The slug for the custom post type.
  * Defaults to the `key`.
  * This should be given without leading and proceeding slashes. E.g. `test/sample` would result in your posts permalinks being `/test/sample/<post_name>`
* Icon (optional)(string)
  * The dashicon to use for the Post Type.
  * Defaults to `dashicons-admin-post`
  * Resource: https://developer.wordpress.org/resource/dashicons/
* Menu Position (optional)(int)
  * Position in the admin menu.
  * Defaults to `30`.
  * Resource: https://developer.wordpress.org/reference/functions/register_post_type/#menu_position
* Has Archive (optional)(bool)
  * Whether the Post Type should have an archive route generated for it or not.
  * Defaults to `true`.
  * E.g. if true, an archive route will be generated based on your post type slug, the archive route will be `/test/sample/`
* Public (optional)(bool)
  * If the Post Type should be visible to the public.
  * Defaults to `true`.
  * Generally should always stay true unless if you are using the post type strictly for admin usages.
* Show In Rest (optional)(bool)
  * If the Post Type should be available to the REST API.
  * Defaults to `true`.
  * IMPORTANT: because Gutenberg uses the REST API, this should be `true` if you have to use the `editor` support.
* Override (optional)(bool)
  * If your Post Type should override existing post types.
  * Defaults to `false`.
  * This is useful if you need to modify any of the Core WordPress post types, or post types from other plugins.
* Translate Interface (optional)(bool)
  * Whether the post type labels should be translated in the admin interface.
  * Defaults to `true`.
  * Useful if you have a multi-language site but want the admin are to remain in English.
* Text Domain (optional)(string)
  * The text domain used for the translation interface.
  * Defaults to the `key`.
  * This is only used if Translate Interface is set to `true`.

#### Registering Custom Rewrite Rules
You must already have initiated a new class with `$myPostType = new \MarsPress\PostType\Type();`

You can then call the `add_rewrite_rule` method with `$myPostType->add_rewrite_rule();`

IMPORTANT: after registering new rewrite rules, it is required to flush the rule cache. This can be done in WordPress admin by going to Settings > Permalinks and clicking the "Save Changes" button (you do not actually have to make any changes).

The `add_rewrite_rule` method takes 3 parameters, 2 required and 1 optional.
* Rule (required)(string)
  * This should be your regex rule such as `test/samples/(.*)/(.*)?$`
* Match (required)(string)
  * This should be your matching URL query. It is important to not double quote the `$matches[]` as that will result in PHP warnings and the rewrite rule failing.
  * E.g. `index.php?post_type=sample&sample=$matches[2]&sample_category=$matches[1]`
  * All the front-end WordPress matches will start with `index.php`.
* After (optional)(string)
  * This sets the priority of the rewrite rule in the stack of WordPress rewrite rules.
  * Defaults to `top`.
  * Valid values are `top` or `bottom`.

#### Available Methods
You must already have initiated a new class with `$myPostType = new \MarsPress\PostType\Type();`
* `$myPostType->get_key();`
  * This will return the key of the post type.
  * This is useful for WordPress conditions such as `if( is_singular( $myPostType->get_key() ) ){}`
* `$myPostType->get_object();`
  * This will return the WP_Post_Type object.
  * Resource: https://developer.wordpress.org/reference/classes/wp_post_type/