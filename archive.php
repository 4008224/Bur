<?php get_header();?>
    <header class="archive-header">
        <?php
        the_archive_title( '<h1 class="page-title">', '</h1>' );
        the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
    </header>
    <div class="postlists">
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post();?>
        <?phpget_template_part( 'content', 'home' );?>
    <?php endwhile;?>
    </div>
    <?php echo tg_get_adjacent_posts_link();?>
<?php endif;?>
<?php get_footer();?>