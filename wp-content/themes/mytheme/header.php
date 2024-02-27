<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?=get_option('blogname');?></title>
    <!-- //loads everything in head  -->
    <?php wp_head();?>
</head>
<body>
    
    <?php wp_body_open(); ?>
    <header class="header">
    <div class="left_head">
    <div class="logo">
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/logo_black.png'; ?>" alt="Logo">
    </div>
    <div class="search_input">
      	
    <?php echo do_shortcode('[live_search]'); ?>
    </div>
    </div>
    <div class="right_head">
        <div class="column-51">
            <!-- SHOWS THE PRIMARY MENU -->
            <?php 
            $menu = array(
                'theme_location' => 'huvudmeny',
                'menu_id' => 'primary-menu',
                'container' => 'nav',
                'container_class' => 'menu'
            );
            
            wp_nav_menu($menu); ?>    
          <!--   <button class="hamburger">&#9776;</button>      -->
        </div>
        <div class="column-51">
            <!-- SHOWS THE FOOTER MENU -->
            <?php 
            $menu = array(
                'theme_location' => 'cart-meny',
                'menu_id' => 'loginmenu',
                'container' => 'nav',
                'container_class' => 'menu'
            );
            
            wp_nav_menu($menu); ?>    
          <!--   <button class="hamburger">&#9776;</button>      -->
        </div>
        </div>

        
    </header>
    <div class="subheader">
        <?php echo do_shortcode('[woocommerce_category_subheader_navigation]'); ?>
        
        </div>