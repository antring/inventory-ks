<?php
$page_title = 'Admin Home Page';
require_once('includes/load.php');
// Checking userlevel
page_require_level(1);
?>
<?php
$c_categorie = count_by_id('categories');
$c_product = count_by_id('products');
$c_sale = count_by_id('sales');
$c_user = count_by_id('users');
$products_sold = find_higest_saleing_product('10');
$recent_products = find_recent_product_added('5');
$recent_sales = find_recent_sale_added('5');
$storageStatus = storage_status();


include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-green">
                <i class="glyphicon glyphicon-user"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"> <?php echo $c_user['total']; ?> </h2>

                <p class="text-muted">Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-red">
                <i class="glyphicon glyphicon-list"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"> <?php echo $c_categorie['total']; ?> </h2>

                <p class="text-muted">Categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-blue">
                <i class="glyphicon glyphicon-shopping-cart"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"> <?php echo $c_product['total']; ?> </h2>

                <p class="text-muted">Products</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-yellow">
                <i>#</i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"> <?php echo $c_sale['total']; ?></h2>

                <p class="text-muted">Sales</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Mest solgte produkter</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Antall solgt</th>
                        <th>Totalt antall</th>
                    <tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products_sold as $product_sold): ?>
                        <tr>
                            <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
                            <td><?php echo (int)$product_sold['totalSold']; ?></td>
                            <td><?php echo (int)$product_sold['totalQty']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Siste salg</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Produkt</th>
                        <th>Dato</th>
                        <th>Pris</th>
                        <th>Bruker</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recent_sales as $recent_sale): ?>
                        <tr>
                            <td class="text-center"><?php echo count_id(); ?></td>
                            <td><?php echo remove_junk(first_character($recent_sale['name'])); ?></td>
                            <td><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></td>
                            <td><?php echo remove_junk(first_character($recent_sale['price'])); ?>,-</td>
                            <td><?php echo remove_junk(first_character($recent_sale['username'])); ?></td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Lagerstatus</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>Produktnavn</th>
                        <th>KS-lager</th>
                        <th>Hovedlager</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($storageStatus as $storage): ?>
                        <tr>
                            <td><?php echo first_character($storage['name']); ?></td>
                            <td><?php echo ($storage['ks_storage']); ?></td>
                            <td><?php echo ($storage['quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<div class="row">

</div>


<?php include_once('layouts/footer.php'); ?>
