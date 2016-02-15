<?php
/**
 * Basic functions
 *
 * Some basic functions.
 *
 * @package Bigfa
 * @since Pure 1.0
 */
/**
 * Include other functions.
 *
 * @since Pure 1.0
 */
function wpse120418_unregister_categories() {
    register_taxonomy( 'category', array() );
}
//add_action( 'init', 'wpse120418_unregister_categories' );

function empty_the_feed($content){
    return '';
}
add_filter('the_content_feed','empty_the_feed');
add_filter('the_excerpt_rss','empty_the_feed');


function get_twitter_json()
{
    if (get_transient('twittercache')) {
        $content = get_transient('twittercache');
    } else {
        delete_transient('twittercache');
        $getjson = 'http://api.wpista.com/twitter.json?n=5';
        $content = file_get_contents($getjson);
        set_transient('twittercache', $content, 60 * 60 * 24);
    }
    return json_decode($content);
}

function pure_get_setting($key = null)
{
    $setting = get_option(PURE_SETTING_KEY);

    if (!$setting) {
        return false;
    }

    if ($key) {
        if (array_key_exists($key, $setting)) {
            return $setting[$key];
        } else {
            return false;
        }
    } else {
        return $setting;
    }
}

function pure_update_setting($setting)
{
    update_option(PURE_SETTING_KEY, $setting);
}

function pure_empty_setting()
{
    delete_option(PURE_SETTING_KEY);
}

/*Globals*/
global $links;
add_action("init", "init_globals");

function init_globals()
{
    global $links;
    $bookmarks      = get_bookmarks();
    $links          = array();
    $total_comments = array();
    if (!empty($bookmarks)) {
        foreach ($bookmarks as $bookmark) {
            $url = $bookmark->link_url;
            array_push($links, $url);
        }
    }
}
/*Globals*/

/**
 * Load theme config.
 *
 * @since Pure 1.0
 */
function Aladdin($e)
{
    $option = get_option('aladdin_config');
    if (!empty($option[$e])) {
        return $option[$e];
    }

    return false;

}

/**
 * Post and page view.
 *
 * @since Pure 1.0
 */

function set_post_views()
{
    global $post;
    $post_id   = intval($post->ID);
    $count_key = 'views';
    $views     = get_post_custom($post_id);
    $views     = intval($views['views'][0]);
    if (is_single() || is_page()) {
        if (!update_post_meta($post_id, 'views', ($views + 1))) {
            add_post_meta($post_id, 'views', 1, true);
        }
    }
}
add_action('get_header', 'set_post_views');

function custom_the_views($post_id)
{
    $count_key  = 'views';
    $views      = get_post_custom($post_id);
    $views      = intval($views['views'][0]);
    $post_views = intval(post_custom('views'));
    if ($views == '') {
        return 0;
    } else {
        return restyle_text($views);
    }
}

/**
 * Page nav buttons.
 *
 * @since Pure 1.0
 */

function tg_get_adjacent_posts_link()
{
    global $paged, $wp_query;

    if (!$max_page) {
        $max_page = $wp_query->max_num_pages;
    }

    if ($max_page < 2) {
        return;
    }

    if (!$paged) {
        $paged = 1;

    }

    $output = '<nav class="v-textAlignCenter fontSmooth posts-load-btn">';

    $nextpage = intval($paged) + 1;
    if (!$max_page || $max_page >= $nextpage) {
        $next_post = get_pagenum_link($nextpage);
    }

    $previouspage = intval($paged) - 1;
    if ($previouspage < 1) {
        $previouspage = 1;
    }

    $previous_post = get_pagenum_link($previouspage);
    if ($paged > 1) {
        $output .= '<a class="posts-load-prompt"  href="' . $previous_post . '" data-title="Page ' . $paged . '">上一页</a>';
    } else {
        $output .= '<span class="posts-load-disabled">上一页</span>';

    }

    $output .= '<span class="posts-load-num">' . $paged . ' / ' . $max_page . '</span>';

    if ($nextpage <= $max_page) {
        $output .= '<a class="posts-load-prompt" data-title="Page ' . $nextpage . '" href="' . $next_post . '">下一页</a>';
    } else {
        $output .= '<span class="posts-load-disabled">下一页</span>';

    }
    $output .= '</nav>';

    return $output;
}

/**
 * Replace http avatar url with ssl.
 *
 * @since Pure 1.0
 */

function get_ssl_avatar($avatar)
{
    $avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com"), "cn.gravatar.com", $avatar);
    return $avatar;
}
add_filter('get_avatar', 'get_ssl_avatar');

/**
 * Title hook.
 *
 * @since Pure 1.0
 */

function pure_title($title, $sep)
{
    global $paged, $page, $wp_query, $post;

    if (is_feed()) {
        return $title;
    }

    $title .= get_bloginfo('name', 'display');

    $site_description = get_bloginfo('description', 'display');
    if ($site_description && (is_home() || is_front_page())) {
        $title = "$title $sep $site_description";
    }

    if (is_single()) {
        $title = $post->post_title;
    }

    if ($wp_query->get('setting')) {
        $title = "资料修改 $sep " . get_bloginfo('name', 'display');
    }

    if ($wp_query->get('bookmarks')) {
        $title = "我的收藏 $sep " . get_bloginfo('name', 'display');
    }

    if ($wp_query->get('user_cat')) {
        $title = "分类 $sep " . get_bloginfo('name', 'display');
    }

    if (is_search()) {
        $title = get_search_query() . "的搜索結果";
    }

    if (is_tax('feature')) {
        $tax   = $wp_query->get_queried_object();
        $title = $tax->name . ' ' . $sep . ' ' . get_bloginfo('name', 'display');
    }
    if ($paged >= 2 || $page >= 2) {
        $title = "第" . max($paged, $page) . "页 " . $sep . " " . $title;
    }

    return $title;
}
add_filter('wp_title', 'pure_title', 10, 2);

/**
 * Minimize wordpress useless fucntions.
 *
 * @since Pure 1.0
 */

function unregister_default_widgets()
{
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Links');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Nav_Menu_Widget');
}
add_action('widgets_init', 'unregister_default_widgets', 11);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'parent_post_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

/**
 * Thumbnail functions.
 *
 * @since Pure 1.0
 */

function aladdin_get_background_image($post_id, $width = null, $height = null)
{
    if (has_post_thumbnail($post_id)) {
        $timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
        $output       = $timthumb_src[0];
    } else {
        $content         = get_post_field('post_content', $post_id);
        $defaltthubmnail = get_template_directory_uri() . '/images/default.jpg';
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if ($n > 0) {
            $output = $strResult[1][0];
        } else {
            $output = $defaltthubmnail;
        }
    }
    if (pure_get_setting('upyun')) {
        $result = $output;
    } elseif ($height && $width) {
        $user_qiniu = Aladdin('qiniu');

        if ($user_qiniu) {
            $result .= "?imageView/1/w/{$width}/h/{$height}/q/100";
        } else {
            $result = PURE_THEME_URL . "/timthumb.php&#63;src={$output}&#38;w={$width}&#38;h={$height}&#38;zc=1&#38;q=100";
        }

    } else {
        $result = $output;
    }

    return $result;
}

function aladdin_is_has_image($post_id)
{
    static $has_image;
    global $post;
    if (has_post_thumbnail($post_id)) {
        $has_image = true;
    } else {
        $content = get_post_field('post_content', $post_id);
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if ($n > 0) {
            $has_image = true;
        } else {
            $has_image = false;
        }
    }

    return $has_image;

}
/**
 * Site description.
 *
 * @since Pure 1.0
 */

function pure_description()
{
    global $s, $post, $wp_query;
    $description = '';
    $blog_name   = get_bloginfo('name');
    if (is_singular()) {
        $ID                                                         = $post->ID;
        $title                                                      = $post->post_title;
        $author                                                     = $post->post_author;
        $user_info                                                  = get_userdata($author);
        $post_author                                                = $user_info->display_name;
        if (!get_post_meta($ID, "_desription", true)) {$description = $title . ' - 作者: ' . $post_author . ',首发于' . $blog_name;} else { $description = get_post_meta($ID, "_desription", true);}
    } elseif (is_home()) {
        $description = Aladdin('description');
    } elseif (is_tag()) {
        $description = single_tag_title('', false) . " - " . trim(strip_tags(tag_description()));
    } elseif (is_category()) {
        $description = single_cat_title('', false) . " - " . trim(strip_tags(category_description()));
    } elseif (is_search()) {
        $description = $blog_name . ": '" . esc_html($s, 1) . "' 的搜索結果";
    } elseif (is_tax('feature')) {
        $tax         = $wp_query->get_queried_object();
        $description = $tax->name . " - " . $tax->description;
    } else {
        $description = Aladdin('description');
    }
    $description = mb_substr($description, 0, 220, 'utf-8');
    echo "<meta name=\"description\" content=\"$description\">\n";
    $favicon = Aladdin('favicon') ? Aladdin('favicon') : get_template_directory_uri() . "/images/favicon.ico";
    echo '<link type="image/vnd.microsoft.icon" href="' . $favicon . '" rel="shortcut icon">';
}
add_action('wp_head', 'pure_description', 0);
/**
 * head code
 *
 * @since Pure 1.0
 */
function add_head_code()
{
    $code = Aladdin("headcode");
    echo $code;
}

if (Aladdin("headcode") && !current_user_can('manage_options')) {
    add_action('wp_head', 'add_head_code', 100);
}

/**
 * Clean
 *
 * @since Pure 1.0
 */

function tie_clean_options(&$value)
{
    $value = stripslashes($value);
}

/**
 * Update post meta.
 *
 * @since Pure 1.0
 */

function fa_update_post_meta($id, $meta_type, $value)
{
    global $wpdb;
    $wpdb->update(
        $wpdb->posts,
        array(
            $meta_type => $value,
        ),
        array('ID' => $id),
        array(
            '%d',
        ),
        array('%d')
    );
}

/**
 * Post rates
 * on tag close.
 *
 * Assumes first char of $text is tag opening and last char is tag closing.
 * Assumes second char of $text is optionally '/' to indicate closing as in </html>.
 *
 * @since 2.9.0
 * @access private
 *
 * @param string $text Text to check. Must be a tag like <html> or [shortcode].
 * @param array $stack List of open tag elements.
 * @param array $disabled_elements The tag names to match against. Spaces are not allowed in tag names.
 */

add_action('publish_post', 'pure_post_rate_add_ratings_fields');
function pure_post_rate_add_ratings_fields($post_ID)
{
    global $wpdb;
    if (!wp_is_post_revision($post_ID)) {
        add_post_meta($post_ID, '_rating_raters', 0, true);
        add_post_meta($post_ID, '_rating_average', 0, true);
    }
}

add_action('delete_post', 'pure_post_rate_delete_ratings_fields');
function pure_post_rate_delete_ratings_fields($post_ID)
{
    global $wpdb;
    if (!wp_is_post_revision($post_ID)) {
        delete_post_meta($post_ID, '_rating_raters');
        delete_post_meta($post_ID, '_rating_average');
    }
}

function pure_post_rate_rating($post_id = null)
{
    global $wpdb, $post;
    $out_put                                          = '';
    if (is_null($post_id) || $post_id == 0) {$post_id = get_the_ID();}
    $out_put .= pure_post_rate_custom($post_id);
    return $out_put;
}

function pure_add_rate($post_id = 0)
{

    return '<div class="rating-combo" data-post-id="' . $post_id . '"><a class="rating-toggle js-action" href="javascript:;" rel="nofollow" data-action="openRate">投票</a><ul><li><a data-rating="5" rel="nofollow" class="js-action" data-action="rate"><span class="rating-star"><i class="star-5-0"></i></span></a></li><li><a data-rating="4" rel="nofollow" class="js-action" data-action="rate"><span class="rating-star"><i class="star-4-0"></i></span></a></li><li><a data-rating="3" rel="nofollow" class="js-action" data-action="rate"><span class="rating-star"><i class="star-3-0"></i></span></a></li><li><a data-rating="2" rel="nofollow"><span class="rating-star"><i class="star-2-0"></i></span></a></li><li><a data-rating="1" rel="nofollow" class="js-action" data-action="rate"><span class="rating-star"><i class="star-1-0"></i></span></a></li></ul></div><meta content="5" itemprop="bestRating"><meta content="1" itemprop="worstRating">';

}

function pure_post_rate_custom($post_id = null)
{
    global $wpdb;
    $out_put         = '';
    $get_rating_info = pure_get_rating_info($post_id);
    if (is_singular()) {
        $out_put .= '<div class="rate-holder clearfix" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating"><div class="post-rate"><div class="rating-stars" title="评分 ' . $get_rating_info['average'] . ', 满分 5 星" style="width:' . $get_rating_info['percent'] . '%">评分 <span class="average" itemprop="ratingValue">' . $get_rating_info['average'] . '</span>, 满分 <span>5 星</span></div></div><div class="piao"><span itemprop="ratingCount">' . $get_rating_info['raters'] . '</span> 票</div>';} else {
        $out_put .= '<div class="rate-holder clearfix"><div class="post-rate"><div class="rating-stars" title="评分 ' . $get_rating_info['average'] . ', 满分 5 星" style="width:' . $get_rating_info['percent'] . '%">评分 ' . $get_rating_info['average'] . ', 满分 5 星</div></div><div class="piao">' . $get_rating_info['raters'] . ' 票</div>';
    }

    if (!isset($_COOKIE['post_rate_' . $post_id]) && is_singular()) {
        $out_put .= pure_add_rate($post_id);
    }

    $out_put .= '</div>';
    return $out_put;

}

function pure_post_rate($post_id = null)
{
    if (is_null($post_id) || $post_id == 0) {$post_id = get_the_ID();}
    echo pure_post_rate_custom($post_id);

}

function pure_get_rating_info($post_id = null)
{
    if (is_null($post_id) || $post_id == 0) {$post_id = get_the_ID();}
    global $wpdb, $post;
    $_rating_raters  = get_post_meta($post_id, '_rating_raters', true);
    $_rating_average = get_post_meta($post_id, '_rating_average', true);
    $out_put         = array();
    if (!$_rating_raters || $_rating_raters == '' || $_rating_raters == 0 || !is_numeric($_rating_raters) || !$_rating_average || $_rating_average == '' || !is_numeric($_rating_average)) {
        $out_put['raters']  = 0;
        $out_put['average'] = 0;
        $out_put['percent'] = 0;
    } else {
        $out_put['raters']  = $_rating_raters;
        $out_put['average'] = number_format_i18n(round($_rating_average, 2), 2);
        $rating_per         = $out_put['average'] * 20;
        $out_put['percent'] = round($rating_per, 2);
    }
    $out_put['max_rates'] = 5;
    return ($out_put);
}


/**
 * Make admin page only to admin.
 *
 * @since Pure 1.0
 */

function fa_restrict_admin()
{
    if (!current_user_can('manage_options') && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php') {
        wp_redirect(home_url());
    }
}
add_action('admin_init', 'fa_restrict_admin', 1);

add_filter('show_admin_bar', '__return_false');

/**
 * Theme setup.
 *
 * @since Pure 1.0
 */

function pure_setup()
{

}

add_action('after_setup_theme', 'pure_setup');



/**
 * translate seconds to time.
 *
 * @since Pure 1.0
 */

function sec2time($sec)
{
    $d   = floor($sec / 86400);
    $tmp = $sec % 86400;
    $h   = floor($tmp / 3600);
    $tmp %= 3600;
    $m = floor($tmp / 60);
    $s = $tmp % 60;
    return "<span class='cute'>" . $d . "</span>天<span class='cute'>" . $h . "</span>小时<span class='cute'>" . $m . "</span>分<span class='cute'>" . $s . "</span>秒";
}
/**
 * get_posts.
 *
 * @since Pure 1.0
 */

function get_the_link_items($id = null)
{
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output    = '';
    if (!empty($bookmarks)) {
        $output .= '<div class="infoCard--wrap">';
        foreach ($bookmarks as $bookmark) {
            $output .= '<div class="infoCard v-clearfix infoCard--padded"><div class="infoCard-avatar">' . get_avatar($bookmark->link_notes, 64) . '</div><div class="infoCard-info">
            <div class="infoCard-wrapper"><a class="link link--primary" href="' . $bookmark->link_url . '" target="_blank" >' . $bookmark->link_name . '</a><div class="infoCard-bio">' . $bookmark->link_description . '</div></div></div></div>';
        }
        $output .= '</div>';
    }
    return $output;
}

function get_link_items()
{
    $linkcats = get_terms('link_category');
    if (!empty($linkcats)) {
        foreach ($linkcats as $linkcat) {
            $result .= '<div class="heading v-clearfix heading--borderedTop heading--light">
<div class="heading-content v-floatLeft">
<h3 class="heading-title">' . $linkcat->name . '</h3>
</div>
</div>';
           

            $result .= get_the_link_items($linkcat->term_id);
        }
    } else {
        $result = get_the_link_items();
    }
    return $result;
}

/**
 * get_posts.
 *
 * @since Pure 1.0
 */

function get_lists_posts()
{
    global $posts, $post;
    $args = array(
        'posts_per_page' => 4,
        'meta_key'       => 'views',
        'orderby'        => 'meta_value_num',
    );
    $output    = '<ul class="list list--withTitleSubtitle">';
    $postslist = get_posts($args);
    $i         = 0;
    if (!empty($postslist)) {
        foreach ($postslist as $post):
            setup_postdata($post);
            $i++;
            $class = aladdin_is_has_image($post->ID) ? 'list-item' : 'list-item list--withoutImage';
            $output .= '<li class="' . $class . '"><div class="list-itemInfo">';
            if (aladdin_is_has_image($post->ID)) {
                $output .= '<img src="' . aladdin_get_background_image($post->ID) . '!80x50" width=80 height=50 class="list-itemImage">';
            }

            $output .= '<h4 class="list-itemTitle"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4><p class="list-itemDescription">' . human_time_diff(get_the_date('U')) . ' ago / ' . custom_the_views($post->ID) . ' reads</p></div></li>';

        endforeach;
    } else {
        $output .= '<li class="list-item">24小时内没有发布文章</li>';
    }
    wp_reset_postdata();
    $output .= '</ul>';
    return $output;

}

function get_widget_posts(){
    global $posts,$post;
    $args = array(
        'posts_per_page' => 5,
        'post_type'      => array('post', 'help'),
    );
    $output = '<ul class="list list--withIcon">';
    $postslist = get_posts( $args );
    $i = 0;
    if(!empty($postslist)){
        foreach ( $postslist as $post ) :
            setup_postdata( $post );
            $i++;
            $output .= '<li class="list-item"><button class="button button--circle u-disablePointerEvents">
<span class="list-index">'.$i.'</span>
</button><div class="list-itemInfo"><h4 class="list-itemTitle"><a href="' . get_permalink() . '">' . get_the_title() .'</a></h4>
<p class="list-itemDescription">'. get_the_author() . ' / ' . get_comments_number() .' comments</p></div></li>';

        endforeach;
    }else{
        $output .= '<li class="list-item">24小时内没有发布文章</li>';
    }
    wp_reset_postdata();
    $output .= '</ul>';
    return $output;

}

function restyle_text($number)
{
    if ($number >= 1000) {
        return round($number / 1000, 2) . "k"; // NB: you will want to round this
    } else {
        return $number;
    }
}
/**
 * Header button.
 *
 * @since Pure 1.0
 */

function header_button()
{
    if (is_user_logged_in()):
        $this_user = wp_get_current_user();
        $output    = '<a class="metabar-user-avatar js-action" data-action="openUserActions" href="javascript:;">' . get_avatar($this_user->user_email, 35) . '</a>';
    else:
        $output = '<button class="button button--primary" data-action="openLoginForm">Sign in / Sign up</button>';

    endif;
    return $output;
}

function single_bottom_ad()
{

    if (wp_is_mobile() && pure_get_setting("mobile_single_ad")):
        echo pure_get_setting("mobile_single_ad");
    elseif (pure_get_setting("single_ad")):
        echo pure_get_setting("single_ad");
    endif;

}
add_filter('pre_option_link_manager_enabled', '__return_true');

function pure_widgets_init()
{

    register_sidebar(array(
        'name'          => '首页边栏',
        'id'            => 'sidebar-1',
        'description'   => '首页',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title"><span class="widget-title-inner">',
        'after_title'   => '</span></h3>',
    ));
    register_sidebar(array(
        'name'          => '分页边栏',
        'id'            => 'sidebar-2',
        'description'   => '分页',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'pure_widgets_init');

function pure_outdate_notice()
{
    global $post;
    if ((time() - get_the_modified_date('U')) < 60 * 60 * 24 * 60) {
        return;
    }

    echo '<div class="outdateNote">本文已超过' . time_ago(get_the_modified_date('U')) . '没有更新，内容可能已过期，如果错误请联系大发。</div>';

}

function pure_sns()
{
    $sns_array = array(
        array(
            'class' => 'weibo1',
            'key'   => 'social-weibo',
            'span'  => '新浪微博',
        ),
        array(
            'class' => 'instagram1',
            'key'   => 'social-instagram',
            'span'  => 'Instagram',
        ),
        array(
            'class' => 'github',
            'key'   => 'social-github',
            'span'  => 'Dribbble',
        ),
        array(
            'class' => 'twitter1',
            'key'   => 'social-twitter',
            'span'  => 'Twitter',
        ),
        array(
            'class' => 'weixin1',
            'key'   => 'social-weixin',
            'span'  => 'Facebook',
        ),
        array(
            'class' => 'rss',
            'key'   => 'social-rss',
            'span'  => '订阅我',
        ),
        array(
            'class' => 'fi32',
            'key'   => 'social-163',
            'span'  => '订阅我',
        ),
    );
    echo '<div class="social-network">';
    foreach ($sns_array as $key => $value) {
        if ($link = pure_get_setting($value['key'])) {
            printf('<a href="%s" target="_blank" rel="external nofollow"><span class="iconfont icon-%s"></span></a>', $link, $value['class'], $value['span']);
        }
    }
    echo '</div>';

}



function pure_relatedpost($post_num = 4)
{
    global $post;
    $exclude_id = get_post_meta($post->ID, '_related', true);
    if (!$exclude_id) {
        return;
    }

    $args = array(
        'post_status'    => 'publish',
        'post_type'      => array('post', 'help'),
        'post__in'       => explode(',', $exclude_id),
        'posts_per_page' => $post_num,
    );
    $posts = get_posts($args);
    echo '<section class="fontSmooth"><h3 class="related--posts-title">相关文章</h3><div class="related--posts">';
    foreach ($posts as $post) {?>
        <div class="related--post v-borderBox">
            <a class="block-image" itemprop="relatedLink" href="<?php the_permalink();?>" rel="nofollow" style="background-image:url(<?php echo aladdin_get_background_image($post->ID);?>!grid)"></a>
            <h4 class="related--post-title">
                <a href="<?php the_permalink();?>"><?php the_title();?></a>
            </h4>
        </div>
    <?php
}wp_reset_postdata();
    echo '</div></section>';
}
add_filter('pre_get_posts', 'myfeed_request');
function myfeed_request($query)
{
    if (is_feed()) {
        $query->set('post_type', array('post', 'help'));
    }
    return $query;
}

require get_template_directory() . '/modules/jssdk.php';
require get_template_directory() . '/modules/MarkdownInterface.php';
require get_template_directory() . '/modules/Markdown.php';
require get_template_directory() . '/modules/MarkdownExtra.php';
require get_template_directory() . '/modules/formatting.php';
require get_template_directory() . '/modules/social.php';
require get_template_directory() . '/modules/static.php';
require get_template_directory() . '/modules/callback.php';
require get_template_directory() . '/modules/classes.php';
require get_template_directory() . '/modules/shortcode.php';
require get_template_directory() . '/modules/widget.php';
require get_template_directory() . '/modules/private.php';
require get_template_directory() . '/modules/buttons.php';
require get_template_directory() . '/modules/package/setting.php';
require get_template_directory() . '/modules/package/oauth.php';