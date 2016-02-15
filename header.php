<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,minimal-ui">
    <title><?php wp_title( '-', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
<div id="surface-content">
    <header class="metabar u-textAlignCenter">
        <div class="container u-overflowHidden">
            <a href="<?php echo home_url();?>"><?php echo get_bloginfo( 'name', 'display' );?></a>
        </div>
    </header>