<article class="block--list">
    <div class="container ">
        <div class="top-meta">
            <div class="postMetaInline-avatar">
                <?php echo get_avatar( get_the_author_meta( 'user_email' ), 32 );?>
            </div>
            <div class="postMetaInline-feedSummary">
                由<span class="cute"><?php the_author();?></span> 发布于 <span class="cute"><?php the_category( ',' );?></span>
                <span class="postMetaInline--supplemental"><?php echo get_the_date('M d,Y');?></span>
            </div>
        </div>
        <?php if( bur_is_has_image($post->ID) ) :?>
            <a class="block-image" href="<?php the_permalink();?>" style="background-image:url(<?php echo bur_get_background_image($post->ID);?>)"></a>
        <?php endif;?>
        <h2 class="block-title"><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></h2>
        <div class="block-snippet"><?php echo mb_strimwidth(strip_shortcodes(strip_tags($post->post_content)), 0, 120,"...");?></div>
        <div class="u-clearfix block-postMetaWrap">
            <div class="u-floatLeft cute"><?php if(function_exists('wpl_get_like_count')) echo wpl_get_like_count(get_the_ID()) . ' likes' ;?></div>
            <div class="u-floatRight"><?php comments_number('0 条评论', '1 条评论', '% 条评论' );?></div>
        </div>
    </div>
</article>