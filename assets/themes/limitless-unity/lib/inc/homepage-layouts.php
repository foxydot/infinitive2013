<?php
/*** WIDGET AREAS ***/
/**
 * Hero and (3) widget areas
 */
function msdlab_add_homepage_hero_flex_sidebars(){
    genesis_register_sidebar(array(
    'name' => 'Homepage Hero',
    'description' => 'Homepage hero space',
    'id' => 'homepage-top'
            ));
    genesis_register_sidebar(array(
    'name' => 'Homepage Widget Area',
    'description' => 'Homepage central widget areas',
    'id' => 'homepage-widgets'
            )); 
}

/**
 * Callout Bar widget area
 */
function msdlab_add_homepage_callout_sidebars(){
    genesis_register_sidebar(array(
    'name' => 'Homepage Callout',
    'description' => 'Homepage call to action',
    'id' => 'homepage-callout'
            ));
}
/**
 * Add a hero space with the site description
 */
function msdlab_hero(){
	if(is_active_sidebar('homepage-top')){
		print '<div id="hp-top">';
		print '<div class="wrap">';
		dynamic_sidebar('homepage-top');
        //$walker = new Infinitive_Solutions_Walker;
        //wp_nav_menu( array( 'menu' => 'Homepage Feature Section Menu','container' => '','walker' => $walker ) );
		print '</div>';
		print '</div>';
	}
}

/**
 * Add a hero space with the site description
 */
function msdlab_callout(){
	print '<div id="hp-callout">';
	print '<div class="wrap">';
    if(is_active_sidebar('homepage-callout')){
    	dynamic_sidebar('homepage-callout');
	} else {
        do_action( 'genesis_site_description' );
    }
	print '</div>';
	print '</div>';
}

/**
 * Add flaxible widget area
 */
function msdlab_homepage_widgets(){
	print '<div id="homepage-widgets" class="widget-area">';
	print '<div class="wrap">';
	dynamic_sidebar('homepage-widgets');
	print '</div>';
	print '</div>';
}

class Infinitive_Solutions_Walker extends Walker_Nav_Menu
{

    function start_el(&$output, $item, $depth, $args) {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= '<div class="image"></div>';
        $item_output .= '<div class="text">';       
        $item_output .= $item->description?"\n<div class=\"description\">" . $item->description . "</div>\n":'';
        $item_output .= '<div class="link_title">'.$args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after.' ></div>';
        $item_output .= '</div>';
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    function end_el(&$output, $item, $depth) {
        $output .= "</li>\n";
    }
}

/**
 * Create a long scrollie page with child pages of homepage.
 * Uses featured image for background of each wrap section.
 */
function msdlab_scrollie_page(){
    global $post;
    $edit = get_edit_post_link($post->ID) != ''?'<a href="'.get_edit_post_link($post->ID).'"><i class="icon-edit"></i></a>':'';
    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
    $background = $thumbnail?' style="background-image:url('.$thumbnail[0].');"':'';
    remove_filter('the_content','wpautop',12);
    print '<div id="intro" class="scrollie parent div-intro div0">
                <div class="background-wrapper"'.$background.'>
                        <div class="wrap">
                            <div class="page-content">
                                    <div class="entry-content">';
    print apply_filters('the_content', $post->post_content);
    print '                     </div>
                            '.$edit.'
                            </div>
                        </div>
                    </div>
                </div>';
    print '<div id="callout"><p>'.get_option('blogdescription').'</p></div>';

    add_filter('the_content','wpautop',12);
    $my_wp_query = new WP_Query();
    $args = array(
            'post_type' => 'page',
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'tax_query' => array(
                    array(
                        'taxonomy' => 'msdlab_scrollie',
                        'field' => 'slug',
                        'terms' => 'home'
                        )
                    )
            );
    $children = $my_wp_query->query($args);
    $i = 1;
    foreach($children AS $child){
        $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($child->ID), 'full' );
        $background = $thumbnail?' style="background-image:url('.$thumbnail[0].');"':'';
        $form = $child->post_name=='contact-us'?do_shortcode('[gravityform id="1" name="Untitled Form" title="false" ajax="true"]'):'';
        $edit = get_edit_post_link($child->ID) != ''?'<a href="'.get_edit_post_link($child->ID).'"><i class="icon-edit"></i></a>':'';
        print '<div id="'.$child->post_name.'" class="scrollie child div-'.$child->post_name.' div'.$i.' trigger" postid="'.$child->ID.'">
                <div class="background-wrapper"'.$background.'>
                        <div class="wrap">'.$form.'
                            <div class="page-content">
                                <h2 class="entry-title">'.$child->post_title.'</h2>
                                <div class="entry-content">'.apply_filters('the_content', $child->post_content).'</div>
                                '.$edit.'
                            </div>
                        </div>
                    </div>
                </div>';
        $i++;
    }
}

/**
 * create a taxonomy for long scrollies
 */
function register_taxonomy_scrollie() {

    $labels = array(
            'name' => _x( 'Scrollie Sections', 'scrollie' ),
            'singular_name' => _x( 'Scrollie Section', 'scrollie' ),
            'search_items' => _x( 'Search Scrollie Sections', 'scrollie' ),
            'popular_items' => _x( 'Popular Scrollie Sections', 'scrollie' ),
            'all_items' => _x( 'All Scrollie Sections', 'scrollie' ),
            'parent_item' => _x( 'Parent Scrollie Section', 'scrollie' ),
            'parent_item_colon' => _x( 'Parent Scrollie Section:', 'scrollie' ),
            'edit_item' => _x( 'Edit Scrollie Section', 'scrollie' ),
            'update_item' => _x( 'Update Scrollie Section', 'scrollie' ),
            'add_new_item' => _x( 'Add New Scrollie Section', 'scrollie' ),
            'new_item_name' => _x( 'New Scrollie Section Name', 'scrollie' ),
            'separate_items_with_commas' => _x( 'Separate scrollies with commas', 'scrollie' ),
            'add_or_remove_items' => _x( 'Add or remove scrollies', 'scrollie' ),
            'choose_from_most_used' => _x( 'Choose from the most used scrollies', 'scrollie' ),
            'menu_name' => _x( 'Scrollie Sections', 'scrollie' ),
    );

    $args = array(
            'labels' => $labels,
            'public' => false,
            'show_in_nav_menus' => false,
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,

            'rewrite' => true,
            'query_var' => true
    );

    register_taxonomy( 'msdlab_scrollie', array('page'), $args );
}   