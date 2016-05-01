<?php get_header(); ?>
    <div class="angelaBody">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post();?>
                <?php get_template_part( 'content', 'single' );?>
                <?php comments_template(); ?>
            <?php endwhile;?>
        <?php endif;?>
    </div>
<?php get_footer();?>