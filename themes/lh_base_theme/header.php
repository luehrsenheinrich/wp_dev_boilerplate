<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php wp_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript">
    	// Check if JavaScript is available
	    document.documentElement.className =
	       document.documentElement.className.replace("no-js","js");
	</script>

    <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
    <header class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-9 col-sm-9 col-md-4 col-lg-4">
                    <?php echo '<a class="no_hover logo" href="' . esc_url( home_url() ) . '"><img src="' . get_option('header_logo') . '" alt="LH Boiler Template Logo" title="Luehrsen // Heinrich GmbH"></a>'; ?>
                </div>
                <div class="col-xs-3 col-sm-3 hidden-md hidden-lg trigger-wrapper">
                    <a class="mobile-nav-trigger no_hover" href="#menu"><i class="fa fa-bars" aria-hidden="true"></i></a>
                </div>
                <div class="hidden-xs hidden-sm col-md-8 col-lg-8">
                    <?php
                        /*
                         * Call the header nav menu
                         */

                        $args = array(
                                "theme_location"    => 'header',
                                'menu_class'        => 'menu clearfix header',
                                'container'         => 'nav',
                                'container_class'   => 'header-menu',
                                'fallback_cb'       => false,
                                'depth'             => 1

                        );
                        wp_nav_menu($args);
                    ?>
                </div>
            </div>
        </div>
    </header>

    <div class="viewport">
        <div class="hidden-md hidden-lg phone-menu">
            <div class="menu-wrapper">
                <nav class="lang-menu">
                    <ul class="menu clearfix lang">
                        <?php if ( function_exists( 'the_msls' ) ) the_msls(); ?>
                    </ul>
                </nav>
            <?php
                /*
                 * Call the head nav menu
                 */

                $args = array(
                        'theme_location'    => 'header',
                        'menu_class'        => 'menu clearfix',
                        'container'         => 'nav',
                        'container_class'   => 'phone-menu',
                        'fallback_cb'       => false,
                        'depth'             => 3
                );

                wp_nav_menu($args);
            ?>
            </div>

        </div>
        <div class="page-wrapper">