<article class="post-single">
    <div class="container">
        <div class="top-meta">
            <div class="postMetaInline-avatar">
                <?php echo get_avatar( get_the_author_meta( 'user_email' ), 32 );?>
            </div>
            <div class="postMetaInline-feedSummary">
                由<span class="cute"><?php the_author();?></span> 发布于 <span class="cute"><?php the_category( ',' );?></span>
                <span class="postMetaInline--supplemental"><?php echo get_the_date('M d,Y');?></span>
            </div>
        </div>
        <header class="post-single-header">
            <h2 class="block-title"><?php the_title();?></h2>
        </header>
        <div class="entry-content grap">
            <?php the_content();?>
        </div>
    </div>
</article>