<?php get_header(); ?>



<!-- CONTENT -->

<main class="<?php echo is_checkout() ? 'content-checkout' : 'content'; ?>">
<main class="content<?php echo is_front_page() ? '-1' : ''; ?>">

   

    <?php the_content(); ?>

</main>

<?php get_footer(); ?>