<?php get_header(); ?>



<!-- CONTENT -->


<main class="<?php echo is_checkout() ? 'content-checkout' : (is_front_page() ? 'content-frontpage' : 'content'); ?>">

 

    <?php the_content(); ?>

</main>

<?php get_footer(); ?>