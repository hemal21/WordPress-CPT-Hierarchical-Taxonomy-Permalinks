<?php
/**
 * WordPress Custom Post Type and Hierarchical Taxonomy Permalinks
 *
 * Ever wanted to have custom taxonomy permalinks that reflect parent/child relation?
 * Then this is for you. Still in development though.
 *
 * @version 1.0b
 * @author Kenth Hagström
 * @link http://www.facebook.com/keha76
 * @license GNU General Public License v2
 *
 * Last updated 19 sep 2013, 15:30(GMT+1)
 *
 * KNOWN BUGS:
 * - When a 'photo' is published without any taxonomy, the URL url.tld/photos/john does not work.
 * - The full URL url.tld/photos/family/kids/john/ has an unwanted trailing slash.
 *
 * These URL structures work:
 * url.tld/photos/ loads archive-photo.php
 * url.tld/photos/family/ loads taxonomy-album.php
 * url.tld/photos/family/kids/ loads taxonomy-album.php
 * url.tld/photos/family/kids/john loads single-photo.php
 * url.tld/photos/page/2 loads page 2 with archive-photo.php
 *
 * If you try this code, remember to flush your permalinks. Go to 'settings->permalinks' and it's done.
 *
**/
function keha_register_photo_tax_album() {
               
        $labels = array(
                'name'              => _x( 'Albums', 'taxonomy general name', 'blank' ),
                'singular_name'     => _x( 'Album', 'taxonomy singular name', 'blank' ),
                'search_items'      => __( 'Search Albums', 'blank' ),
                'all_items'         => __( 'All Albums', 'blank' ),
                'parent_item'       => __( 'Parent Album', 'blank' ),
                'parent_item_colon' => __( 'Parent Album:', 'blank' ),
                'edit_item'         => __( 'Edit Album', 'blank' ),
                'update_item'       => __( 'Update Album', 'blank' ),
                'add_new_item'      => __( 'Add New Album', 'blank' ),
                'new_item_name'     => __( 'New Album Name', 'blank' ),
                'menu_name'         => __( 'Albums', 'blank' ),
        );      
 
        $rewrite = array(
                'slug' => 'album',
                'with_front' => false,
                'hierarchical' => true
        );
 
        $args = array(
                'hierarchical' => true,
                'labels'       => $labels,
                'show_ui'      => true,
                'query_var'    => true,
                'rewrite'      => $rewrite
        );
        register_taxonomy( 'album', array( 'photo' ), $args );
}
add_action( 'init', 'keha_register_photo_tax_album' );
 
function keha_register_cpt_photo() {
 
        $labels = array(
                'name'               => __( 'Photos', 'blank' ),
                'singular_name'      => _x( 'Photo', 'Photo post type singular', 'blank' ),
                'add_new'            => __( 'Add New', 'blank' ),
                'add_new_item'       => __( 'Add New Photo', 'blank' ),
                'edit_item'          => __( 'Edit Photo', 'blank' ),
                'new_item'           => __( 'New Photo', 'blank' ),
                'all_items'          => __( 'All Photos', 'blank' ),
                'view_item'          => __( 'View Photo', 'blank' ),
                'search_items'       => __( 'Search Photos', 'blank' ),
                'not_found'          => __( 'No photos found', 'blank' ),
                'not_found_in_trash' => __( 'No photos found in Trash', 'blank' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Photos', 'blank' )
        );
 
        $rewrite = array(
                'slug' => 'photos',
                'hierarchical' => true,
                'with_front' => true
        );
 
        $args = array(
                'labels'               => $labels,
                'exclude_from_search'  => false,
                'publicly_queryable'   => true,
                'show_ui'              => true,
                'show_in_nav_menus'    => true,
                'show_in_menu'         => true,
                'show_in_admin_bar'    => true,
                'menu_position'        => 20,
                'menu_icon'            => null, // Handled from CSS
                'capability_type'      => 'post',
                'hierarchical'         => false,
                'public'               => true,
                'supports'             => array( 'title', 'editor', 'thumbnail' ),
                'taxonomies'           => array( 'album' ),
                'rewrite'              => $rewrite,
                'has_archive'          => true,
                'query_var'            => true,
                'can_export'           => true
        );
 
        register_post_type( 'photo', $args );
}
add_action( 'init', 'keha_register_cpt_photo' );
 
function keha_add_rewrite_rules() {
        add_rewrite_rule( '^photos/(.+?)/(.+?)/(.+?)$', 'index.php?album=$matches[1]&album=$matches[2]&photo=$matches[3]', 'top' );
        add_rewrite_rule( '^photos/(.+?)/(.+?)/$', 'index.php?photo=$matches[2]', 'top' );
        add_rewrite_rule( '^photos/(.+?)/(.+?)/(.+?)$', 'index.php?photo=$matches[3]', 'top' );
        add_rewrite_rule( '^photos/(.+?)/(.+?)/?$', 'index.php?album=$matches[2]', 'top' );
        add_rewrite_rule( '^photos/(.+?)$', 'index.php?album=$matches[1]', 'top' );
}
add_action('init', 'keha_add_rewrite_rules');
 
function keha_post_type_link( $post_link, $post, $leavename ) {
 
        global $wp_rewrite;
 
        $draft_or_pending = isset( $post->post_status ) && in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) );
        if ( $draft_or_pending and !$leavename ) {
                return $post_link;
        }
               
        if ( $post->post_type == 'photo' ) {
               
                $post_type_object = get_post_type_object( $post->post_type );
                $post_link = home_url() . '/' . $post_type_object->rewrite['slug'] . '/';
                $parent_dirs = '';
                if ( $terms = get_the_terms( $post->ID, 'album' ) ) {
                        foreach ( $terms as $term ) {
                                if ( $term->parent != 0 ) {
                                        $dirs = keha_get_taxonomy_parents( $term->term_id, 'album', false, '/', true );
                                } else {
                                        $dirs = $term->slug.'/';
                                }
                        }
                }
                $post_link = $post_link . $dirs . $post->post_name;
        }
       
        return $post_link;
}
add_filter( 'post_type_link', 'keha_post_type_link', 10, 3 );
 
/**
 * Custom function based on WordPress own get_category_parents()
 * @link http://core.trac.wordpress.org/browser/tags/3.6.1/wp-includes/category-template.php#L0
 *
 * @param integer $id
 * @param string $taxonomy
 * @param string $link
 * @param string $separator
 * @param string $nicename
 * @param array $visited
 * @return string
 */
function keha_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ) {
        $chain = '';
        $parent = get_term( $id, $taxonomy, OBJECT, 'raw');
        if ( is_wp_error( $parent ) ) {
                return $parent;
        }
 
        if ( $nicename ){
                $name = $parent->slug;
        } else {
                $name = $parent->name;
        }
 
        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
                $visited[] = $parent->parent;
                $chain .= keha_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
        }
 
        if ( $link ) {
                $chain .= '<a href="' . get_term_link( $parent->term_id, $taxonomy ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
        }else {
                $chain .= $name.$separator;
        }
        return $chain;
}