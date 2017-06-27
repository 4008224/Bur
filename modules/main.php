<?php

function bur_title( $title, $sep ) {
    global $paged, $page, $wp_query,$post;

    if ( is_feed() )
        return $title ;

    $title .= get_bloginfo( 'name', 'display' );

    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title = "$title $sep $site_description";

    if ( $paged >= 2 || $page >= 2 )
        $title = "第" .max( $paged, $page ) ."页 ". $sep . " " . $title;
    return $title;
}
add_filter( 'wp_title', 'bur_title', 10, 2 );


function bur_enqueue_scripts() {
    wp_enqueue_style( 'bur-style', get_bloginfo('template_directory') . '/build/css/app.css' );
}
add_action( 'wp_enqueue_scripts', 'bur_enqueue_scripts' );


function bur_get_background_image($post_id){
    if( has_post_thumbnail($post_id) ){
        $timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id),'full');
        $output = $timthumb_src[0];
    } else {
        $content = get_post_field('post_content', $post_id);
        $defaltthubmnail = get_template_directory_uri().'/images/default.jpg';
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if($n > 0){
            $output = $strResult[1][0];
        } else {
            $output = $defaltthubmnail;
        }
    }

    return $output;
}


function bur_is_has_image($post_id){
    static $has_image;
    global $post;
    if( has_post_thumbnail($post_id) ){
        $has_image = true;
    } else {
        $content = get_post_field('post_content', $post_id);
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if($n > 0){
            $has_image = true;
        } else {
            $has_image = false;
        }
    }

    return $has_image;

}


function bur_get_ssl_avatar($avatar) {
    $avatar = str_replace(array("www.gravatar.com","0.gravatar.com","1.gravatar.com","2.gravatar.com"),"secure.gravatar.com",$avatar);
    return $avatar;
}
add_filter('get_avatar', 'bur_get_ssl_avatar');


function bur_get_adjacent_posts_link() {
    global $paged, $wp_query;
    $max_page = '';
    if ( !$max_page )
        $max_page = $wp_query->max_num_pages;
    if ( $max_page < 2)
        return;
    if ( !$paged )
        $paged = 1;
    $output = '<nav class="u-textAlignCenter posts-load-btn">';

    $nextpage = intval($paged) + 1;
    if ( !$max_page || $max_page >= $nextpage )
        $next_post = get_pagenum_link($nextpage);
    $previouspage = intval($paged) - 1;
    if ( $previouspage < 1 )
        $previouspage = 1;
    $previous_post =  get_pagenum_link($previouspage);
    if ( $paged > 1 ) {
        $output .= '<a class="posts-load-prompt"  href="' . $previous_post . '" data-title="Page '.$paged.'">上一页</a>';
    } else {
        $output .= '<span class="posts-load-disabled">上一页</span>';

    }

    $output .= '<span class="posts-load-num">'.$paged.' / '. $max_page .'</span>';

    if ( $nextpage <= $max_page ) {
        $output .= '<a class="posts-load-prompt" data-title="Page '.$nextpage .'" href="' . $next_post . '">下一页</a>';
    } else {
        $output .= '<span class="posts-load-disabled">下一页</span>';

    }
    $output .= '</nav>';

    return $output;
}