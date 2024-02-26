<?php get_header(); ?>



<!-- CONTENT -->

<main class="content<?php echo is_front_page() ? '-1' : ''; ?>">
    <?php the_content(); ?>
    <?php do_action("mytheme_page_content_loaded"); ?>
</main>

<?php get_footer(); ?>