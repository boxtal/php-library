<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <title>Boxtale PHP Library</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="<?php echo EMC_PARENT_DIR; ?>assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo EMC_PARENT_DIR; ?>assets/css/style.css">
        <script src="<?php echo EMC_PARENT_DIR; ?>assets/js/jquery-1.11.3.min.js"></script>
        <script src="<?php echo EMC_PARENT_DIR; ?>assets/bootstrap/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-default">
              <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="<?php echo EMC_PARENT_DIR; ?>index.php"><img src="<?php echo EMC_PARENT_DIR; ?>assets/img/logo_fr.png" /></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <?php
                    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                    $pathFragments = explode('/', $path);
                    $slug = end($pathFragments);
                ?>
                 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav">
                    <li class="<?php if ("index.php" == $slug) {
                        echo "active";
} ?>"><a href="<?php echo EMC_PARENT_DIR; ?>index.php">Home <span class="sr-only">(current)</span></a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Quotation <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li class="<?php if ("get_cotation.php" == $slug && !isset($_GET['dest'])) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_cotation.php">Paris to Bordeaux</a>
                        </li>
                        <li class="<?php if ("get_cotation.php" == $slug && isset($_GET['dest']) && $_GET['dest'] == 'Sydney') {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_cotation.php?dest=Sydney">Paris to Sydney (international)</a>
                        </li>                        
                        <li class="<?php if ("get_cotation.php" == $slug && isset($_GET['dest']) && $_GET['dest'] == 'Barcelona') {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_cotation.php?soucre=Madrid&dest=Barcelona">Madrid to Barcelona (Spanish)</a>
                        </li>
                        <li class="<?php if ("get_cotation_multiple.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_cotation_multiple.php">Multiple quotation</a>
                        </li>
                      </ul>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Order <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li class="<?php if ("make_order.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/make_order.php">Paris to Marseille (domestic)</a>
                        </li>
                        <li class="<?php if ("make_order_with_insurance.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/make_order_with_insurance.php">Paris to Marseille with insurance (domestic)</a>
                        </li>
                        <li class="<?php if ("make_order_inter.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/make_order_inter.php">Paris to Sydney (international)</a>
                        </li>
                        <li class="<?php if ("get_status.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_status.php">Order status</a>
                        </li>
                      </ul>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Parcel points <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li class="<?php if ("get_list_parcel_points.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_list_parcel_points.php">Next to a given postcode</a>
                        </li>
                        <li class="<?php if ("get_parcel_point.php" == $slug) {
                            echo "active";
} ?>">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_parcel_point.php">Parcel points details from id</a>
                        </li>
                      </ul>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Miscellaneous <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li class="">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_carriers_list.php">Carrier list</a>
                        </li>
                        <li class="">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_categories.php">Categories</a>
                        </li>
                        <li class="">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_country.php">Countries</a>
                        </li>
                        <li class="">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_news.php">API News</a>
                        </li>
                        <li class="">
                            <a href="<?php echo EMC_PARENT_DIR; ?>samples/post_signup.php">User signup</a>
                        </li>
                      </ul>
                    </li>
                    <li><a href="http://ecommerce.envoimoinscher.com/api/documentation/" target="_blank">Documentation (french)</a></li>
                  </ul>
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>
