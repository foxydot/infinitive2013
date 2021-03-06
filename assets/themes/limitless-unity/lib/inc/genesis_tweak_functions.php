<?php
/*** HEADER ***/
//customize msd-social shortcode

/**
 * Add pre-header with social and search
 */ 
function msdlab_pre_header(){
    print '<div class="pre-header">
        <div class="wrap">';
           print do_shortcode('[msd-social]');
           get_search_form();
    print '
        </div>
    </div>';
}

 /**
 * Customize search form input
 */
function msdlab_search_text($text) {
    $text = esc_attr( 'Search...' );
    return $text;
} 
 
 /**
 * Customize search button text
 */
function msdlab_search_button($text) {
    $text = "&#xF002;";
    return $text;
}

/**
 * Customize search form 
 */
function msdlab_search_form($form, $search_text, $button_text, $label){
   if ( genesis_html5() )
        $form = sprintf( '<form method="get" class="search-form" action="%s" role="search">%s<input type="search" name="s" placeholder="%s" /><input type="submit" value="%s" /></form>', home_url( '/' ), esc_html( $label ), esc_attr( $search_text ), esc_attr( $button_text ) );
    else
        $form = sprintf( '<form method="get" class="searchform search-form" action="%s" role="search" >%s<input type="text" value="%s" name="s" class="s search-input" onfocus="%s" onblur="%s" /><input type="submit" class="searchsubmit search-submit" value="%s" /></form>', home_url( '/' ), esc_html( $label ), esc_attr( $search_text ), esc_attr( $onfocus ), esc_attr( $onblur ), esc_attr( $button_text ) );
    return $form;
}

/*** NAV ***/


/*** SIDEBARS ***/
/**
 * Reversed out style SCS
 * This ensures that the primary sidebar is always to the left.
 */
function msdlab_ro_layout_logic() {
    $site_layout = genesis_site_layout();    
    if ( $site_layout == 'sidebar-content-sidebar' ) {
        // Remove default genesis sidebars
        remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
        remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt');
        // Add layout specific sidebars
        add_action( 'genesis_before_content_sidebar_wrap', 'genesis_get_sidebar' );
        add_action( 'genesis_after_content', 'genesis_get_sidebar_alt');
    }
}

function msdlab_register_sidebar_defaults($args){
    $args = array(
            'before_widget' => genesis_markup( array(
                'html5' => '<section id="%1$s" class="widget %2$s">',
                'xhtml' => '<div id="%1$s" class="widget %2$s">',
                'echo'  => false,
            ) ),
            'after_widget'  => genesis_markup( array(
                'html5' => '</div></section>' . "\n",
                'xhtml' => '</div></div>' . "\n",
                'echo'  => false
            ) ),
            'before_title'  => '<h4 class="widget-title widgettitle">',
            'after_title'   => genesis_markup( array(
                'html5' => '</h4>'."\n".'<div class="widget-wrap">',
                'xhtml' => '</h4>'."\n".'<div class="widget-wrap">',
                'echo'  => false,
            ) ),
        );
   return $args;
}

function msdlab_select_sidebars(){
    global $post;
    if((is_home() || is_archive() || is_single()) && $post->post_type == "post" ){
        remove_action('genesis_sidebar', 'genesis_do_sidebar');
        add_action('genesis_sidebar', 'msdlab_do_blog_sidebar');
    }
}

function msdlab_do_blog_sidebar() {

    if ( ! dynamic_sidebar( 'blog-widget-area' )  ) {
    }

}

/**
 * Legacy widget areas
 */
function msdlab_add_legacy_sidebars(){
    // Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
    genesis_register_sidebar( array(
        'name' => __( 'Blog Widget Area', 'infinite' ),
        'id' => 'blog-widget-area',
        'description' => __( 'The blog widget area', 'infinite' ),
        'before_widget' => genesis_markup( array(
                'html5' => '<section id="%1$s" class="widget %2$s">',
                'xhtml' => '<div id="%1$s" class="widget %2$s">',
                'echo'  => false,
            ) ),
            'after_widget'  => genesis_markup( array(
                'html5' => '</div></section>' . "\n",
                'xhtml' => '</div></div>' . "\n",
                'echo'  => false
            ) ),
            'before_title'  => '<h4 class="widget-title widgettitle">',
            'after_title'   => genesis_markup( array(
                'html5' => '</h4>'."\n".'<div class="widget-wrap">',
                'xhtml' => '</h4>'."\n".'<div class="widget-wrap">',
                'echo'  => false,
            ) ),
    ) );
}

/*** CONTENT ***/

/**
 * Customize Breadcrumb output
 */
function msdlab_breadcrumb_args($args) {
    $args['labels']['prefix'] = ''; //marks the spot
    $args['sep'] = ' > ';
    return $args;
}

function msdlab_sharethis_removal(){
    global $post;
    if($post->post_type != 'post'){
        remove_action('the_content', 'st_add_link');
        remove_action('the_content', 'st_add_widget');
    }    
}

function msdlab_remove_meta(){
    global $post;
    if($post->post_type == 'msd_news'){
        remove_action( 'genesis_entry_footer', 'genesis_post_meta' ); //remove the meta (filed under, tags, etc.)
    }
}

//* Customize the post info function
add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
    if ( !is_page() ) {
        $post_info = 'Posted by [post_author_posts_link]<br />
        [post_date]&nbsp;&nbsp;&nbsp;'.msdlab_get_comments_number();
        return $post_info;
}}

/**
 * Custom blog loop
 */
 
// Setup Grid Loop
function msdlab_blog_grid(){
    global $loop_counter;
    if(!isset($loop_counter)){$loop_counter=0;}
    add_action('genesis_after_entry','msd_add_loop_counter_to_html5_loop',1);
    if(is_home()){
        remove_action( 'genesis_loop', 'genesis_do_loop' );
        add_action( 'genesis_loop', 'msdlab_grid_loop_helper' );
        add_action('genesis_before_entry', 'msdlab_switch_content');
        remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
        //add_filter('genesis_grid_loop_post_class', 'msdlab_grid_add_bootstrap');
    }
}
function msdlab_grid_loop_helper() {
    if ( function_exists( 'genesis_grid_loop' ) ) {                
        genesis_grid_loop( array(
        'features' => 1,
        'features_on_all'       => false,
        'feature_image_size'    => 'child_full',
        'feature_image_class'   => 'alignnone post-image child_full',
        'feature_content_limit' => 0,
        'grid_image_size'       => 'child_thumbnail',
        'grid_image_class'      => 'alignnone post-image child_thumbnail',
        'grid_content_limit'    => 0,
        'more' => __( '[Continue reading...]', 'adaptation' ),
        ) );
    } else {
        genesis_standard_loop();
    }
}

add_filter( 'pre_get_posts', 'be_archive_query' ,20);
/**
 * Archive Query
 *
 * Sets all archives to 27 per page
 * @since 1.0.0
 * @link http://www.billerickson.net/customize-the-wordpress-query/
 *
 * @param object $query
 */
 
function be_archive_query( $query ) {
    if( $query->is_main_query() && $query->is_home() ){
        $mainppp = 7;
        if(!($query->is_paged())){
            $query->set( 'posts_per_page', $mainppp );
        } else {
            $ppp = $query->query_vars['posts_per_page']!=''?$query->query_vars['posts_per_page']:get_option( 'posts_per_page' );
            $offset = (($query->query_vars['paged']-1)*$ppp)-($ppp-$mainppp);
            $query->set( 'offset', $offset);
        }
    }
}

add_action ( 'genesis_before_entry', 'msdlab_add_pagination');
function msdlab_add_pagination() {
    if ( is_single() && is_cpt('post') ) {
        add_action( 'genesis_entry_footer', 'msdlab_post_navigation_links' );
    }
}

function msdlab_post_navigation_links() {
    previous_post_link('<div class="prev-link alignleft page-nav"><i class="fa fa-arrow-left"></i> %link</div>', 'Previous'); 
    next_post_link('<div class="next-link alignright page-nav">%link <i class="fa fa-arrow-right"></i></div>', 'Next');
}

// Customize Grid Loop Content
function msdlab_switch_content() {
    remove_action('genesis_entry_content', 'genesis_grid_loop_content');
    add_action('genesis_entry_content', 'msdlab_grid_loop_content');
    add_action('genesis_after_entry', 'msdlab_grid_divider');
    add_action('genesis_entry_header', 'msdlab_grid_loop_image', 4);
}

function msdlab_grid_loop_content() {

    global $_genesis_loop_args;
    if ( in_array( 'genesis-feature', get_post_class() ) ) {
        if ( $_genesis_loop_args['feature_image_size'] ) {
            printf( '<a href="%s" title="%s" class="featured_image_wrapper">%s</a>', get_permalink(), the_title_attribute('echo=0'), genesis_get_image( array( 'size' => $_genesis_loop_args['feature_image_size'], 'attr' => array( 'class' => esc_attr( $_genesis_loop_args['feature_image_class'] ) ) ) ) );
        }

        the_excerpt();  
        printf( '<a href="%s" title="%s" class="readmore-button alignright">%s</a>', get_permalink(), the_title_attribute('echo=0'), 'Continue Reading >' );
           
    }
    else {

        //the_excerpt();
    }

}

function msdlab_grid_loop_image() {
    if ( in_array( 'genesis-grid', get_post_class() ) ) {
        global $post;
        echo '<p class="thumbnail"><a href="'.get_permalink().'">'.get_the_post_thumbnail($post->ID, 'child_thumbnail').'</a></p>';
    }
}

function msd_add_loop_counter_to_html5_loop(){
    global $loop_counter;
    $loop_counter++;
}

function msdlab_grid_divider() {
    global $loop_counter, $paged;
    if($loop_counter == 1 && $paged == 0){print '<div class="section-header"><h3 class="recent-posts-header">Recent Posts</h3></div><hr class="grid-separator" />';}
    if(is_paged()){
        if ((($loop_counter) % 2 == 0) && !($paged == 0 && $loop_counter < 2)) echo '<hr class="grid-separator" />';
    } else {
        if ((($loop_counter + 1) % 2 == 0) && !($paged == 0 && $loop_counter < 2)) echo '<hr class="grid-separator" />';
    }
    
}
 function msdlab_grid_add_bootstrap($classes){
     if(in_array('genesis-grid',$classes)){
         $classes[] = 'col-md-6';
     }
     return $classes;
 }
function msdlab_get_comments_number(){
    $num_comments = get_comments_number();
    if ($num_comments == '1') $comments = $num_comments.' ' . __( 'comment', 'adaptation' );
    else $comments = $num_comments.' ' . __( 'comments', 'adaptation' );
    return '<a class="comments" href="'.get_permalink().'/#comments">'.$comments.'</a>';
}

/** Team **/

function msdlab_modify_posts_where($data){
    global $teamblogs,$wpdb;
    if(count($teamblogs)>0){
    foreach($teamblogs AS $k=>$v){
        $blogids[] = $v->ID;
    }
    $ids = implode(',',$blogids);
    $or_where = ' OR '.$wpdb->posts.'.ID IN ('.$ids.')';
    $pattern = '@(AND )('.$wpdb->posts.'.post_author IN \(\d+\))(.*)@';
    preg_match($pattern,$data,$matches);
    $new_data = $matches[1].'('.$matches[2].$or_where.')'.$matches[3];
    return($new_data);
    } else {
        return($data);
    }
}
/*** FOOTER ***/

/**
 * Footer replacement with MSDSocial support
 */
function msdlab_do_social_footer(){
    global $wp_filter;
//ts_var( $wp_filter['the_content'] );
    global $msd_social;
    if(has_nav_menu('footer_menu')){$copyright .= wp_nav_menu( array( 'theme_location' => 'footer_menu','container_class' => 'ftr-menu ftr-links','echo' => FALSE ) );}
    
    if($msd_social){
        $copyright .= '&copy; Copyright '.date('Y').' '.$msd_social->get_bizname().' &middot; All Rights Reserved';
    } else {
        $copyright .= '&copy; Copyright '.date('Y').' '.get_bloginfo('name').' &middot; All Rights Reserved ';
    }
    
    print '<div id="copyright" class="copyright gototop">'.$copyright.'</div><div id="social" class="social creds">';
    print '</div>';
}

/**
 * Menu area for above footer treatment
 */
register_nav_menus( array(
    'footer_menu' => 'Footer Menu'
) );


/*** Blog Header ***/
function msd_add_blog_header(){
    global $post;
    if(get_post_type() == 'post' || get_section()=='blog'){
        $header = '
        <div id="blog-header" class="blog-header">
            <h3>Infinitive Difference Blog</h3>
            <p>Get in the know and keep current with big-picture thinking and actionable insights.</p>
        </div>';
    }
    print $header;
}

/*** SITEMAP ***/
function msdlab_sitemap(){
    $col1 = '
            <h4>'. __( 'Pages:', 'genesis' ) .'</h4>
            <ul>
                '. wp_list_pages( 'echo=0&title_li=' ) .'
            </ul>

            <h4>'. __( 'Categories:', 'genesis' ) .'</h4>
            <ul>
                '. wp_list_categories( 'echo=0&sort_column=name&title_li=' ) .'
            </ul>
            ';

            foreach( get_post_types( array('public' => true) ) as $post_type ) {
              if ( in_array( $post_type, array('post','page','attachment') ) )
                continue;
            
              $pt = get_post_type_object( $post_type );
            
              $col2 .= '<h4>'.$pt->labels->name.'</h4>';
              $col2 .= '<ul>';
            
              query_posts('post_type='.$post_type.'&posts_per_page=-1');
              while( have_posts() ) {
                the_post();
                if($post_type=='news'){
                   $col2 .= '<li><a href="'.get_permalink().'">'.get_the_title().' '.get_the_content().'</a></li>';
                } else {
                    $col2 .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
                }
              }
            wp_reset_query();
            
              $col2 .= '</ul>';
            }

    $col3 = '<h4>'. __( 'Monthly:', 'genesis' ) .'</h4>
            <ul>
                '. wp_get_archives( 'echo=0&type=monthly' ) .'
            </ul>

            <h4>'. __( 'Recent Posts:', 'genesis' ) .'</h4>
            <ul>
                '. wp_get_archives( 'echo=0&type=postbypost&limit=20' ) .'
            </ul>
            ';
    $ret = '<div class="row">
       <div class="col-md-4 col-sm-12">'.$col1.'</div>
       <div class="col-md-4 col-sm-12">'.$col2.'</div>
       <div class="col-md-4 col-sm-12">'.$col3.'</div>
    </div>';
    print $ret;
} 
