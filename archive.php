<?php get_header();?>
    <header class="archive-header">
        <?php
        the_archive_title( '<h1 class="page-title">', '</h1>' );
        the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
    </header>
    <div class="post-list">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post();?>
                <?php get_template_part( 'template-parts/content', 'home' );?>
            <?php endwhile;?>
            <?php echo bur_get_adjacent_posts_link();?>
        <?php endif;?>
    </div>
<?php get_footer();?>