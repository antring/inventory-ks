<?php
$page_title = 'Søk';
require_once('includes/load.php');

// Checking userlevel
page_require_level(3);

//Show only own sales, unless userlevel is admin
if (isset($_POST['custnr'])) {
    echo 'banana';
//    echo var_dump($_POST['custnr']);
    $sales = search_custnr($_POST['custnr']);
    if (get_userlevel() == 1) {
        $sales = search_custnr($_POST['custnr']);
    } else {
        $sales = find_all_user_sales();
    }
}

include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-search"></span>
                    <span>Søk</span>
                </strong>
            </div>

            <div class="panel-body">
                <div class="col-md-2">
                    <form method="post">
                        <div class="form-group">
                            <input type="number" name="custnr" placeholder="Kundenummer">
                            <div class="pull-right">
                                <button type="submit" name="storage" class="btn btn-primary">Søk</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-8">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th> Produkt</th>
                                    <th class="text-center" style="width: 5%;"> Antall</th>
                                    <th class="text-center" style="width: 5%;"> Total</th>
                                    <th class="text-center" style="width: 5%;"> Dato</th>
                                    <?php if (get_userlevel() == 1) {
                                        echo("<th class='text-center'> Bruker </th>");
                                    } ?>
                                    <th class="text-center" style="width: 5%;"> Kundenummer</th>
                                    <th class="text-center" style="width: 50%;"> Kommentar</th>
                                    <th class="text-center" style="width: 100px;"> Handlinger</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tbody>
                                <?php if (isset($_POST['custnr'])): foreach ($sales as $sale): ?>
                                    <tr>
                                        <td class="text-center"><?php echo count_id(); ?></td>
                                        <td><?php echo remove_junk($sale['name']); ?></td>
                                        <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
                                        <td class="text-center"><?php echo remove_junk($sale['price']); ?>,-</td>
                                        <td class="text-center"><?php echo $sale['date']; ?></td>
                                        <?php if (get_userlevel() == 1) {
                                            echo("<td class='text-center'>{$sale['username']}</td> ");
                                        } ?>
                                        <td class="text-center"><?php echo $sale['custnr']; ?></td>
                                        <td class="text-center" style="max-width: 250px"><?php echo $sale['comment']; ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="edit_sale.php?id=<?php echo (int)$sale['id']; ?>"
                                                   class="btn btn-warning btn-xs" title="Edit" data-toggle="tooltip">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
