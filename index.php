<?php get_header(); ?>
        <div class="post-list">
            <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post();?>
                <?php get_template_part( 'template-parts/content', 'home' );?>
            <?php endwhile;?>
        </div>
        <?php echo bur_get_adjacent_posts_link();?>
        <?php endif; ?>
<?php get_footer();?>