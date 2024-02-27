<footer>
    <div class="footer-container">
        <div class="footer-menu-container">
        <div class="logo-div">
            <a href="/">
                <img src="<?=get_template_directory_uri() . '/assets/images/logo.png';?>" alt="logo">
            </a>
                <p>We are a residential interior design firm located in Portland. Our boutique-studio offers more than</p>
        </div>
        <div class="services-div">
            <h4>Services</h4>
                    <?php
                    $menu = array(
                        'theme_location' => 'services',
                        'menu_id' => 'services-menu',
                        'container' => 'div',
                        'container-class' => 'services-div'
                    );

                    wp_nav_menu($menu);
                    ?>
                </div>
                <div class="assistance-div">
                    <h4>Assistance to the buyer</h4>
                    <?php
                    $menu = array(
                        'theme_location' => 'assistance',
                        'menu_id' => 'assistance-menu',
                        'container' => 'div',
                        'container-class' => 'assistance-div'
                    );

                    wp_nav_menu($menu);
                    ?>
                </div>
        </div>
        <div class="social-div">
                    <a href="www.twitter.com" target="_blank"><img src="<?=get_template_directory_uri() . '/assets/images/twitter.png';?>" alt="twitter"></a>
                    <a href="www.facebook.com" target="_blank"><img src="<?=get_template_directory_uri() . '/assets/images/facebook.png';?>" alt="facebook"></a>
                    <a href="www.tiktok.com" target="_blank"><img src="<?=get_template_directory_uri() . '/assets/images/tiktok.png';?>" alt="tik tok"></a>
                    <a href="www.instagram.com" target="_blank"><img src="<?=get_template_directory_uri() . '/assets/images/instagram.png';?>" alt="instagram"></a>
        </div>
    </div>
</footer>
<?php 
wp_footer();
?>
                </body>
                </html>