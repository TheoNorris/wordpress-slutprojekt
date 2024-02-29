<?php get_header(); ?>



<!-- CONTENT -->

<main class="<?php echo is_checkout() ? 'content-checkout' : 'content'; ?>">

    <?php the_title();?>

    <?php the_content(); ?>

</main>

<?php get_footer(); ?>