<?php get_header(); ?>
    <div class="angelaBody">
        <div class="postlists">
            <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post();?>
                <?php get_template_part( 'content', 'home' );?>
            <?php endwhile;?>
        </div>
        <?php echo tg_get_adjacent_posts_link();?>
        <?php endif; ?>
    </div>
<?php get_footer();?>