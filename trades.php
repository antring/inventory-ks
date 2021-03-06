<?php
$page_title = 'Dine returer';
require_once('includes/load.php');

// Checking userlevel
page_require_level(4);

//Show only own sales, unless userlevel is admin
if (get_userlevel() == 1) {
    $trades = find_all_trades();
} else {
    $trades = find_all_user_trades();
}

include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Returer</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th> Produkt</th>
                        <th class="text-center" style="width: 5%;"> Antall</th>
                        <th class="text-center" style="width: 5%;"> Dato</th>
                        <?php if(get_userlevel() == 1) { echo("<th class='text-center'> Bruker </th>"); } ?>
                        <th class="text-center" style="width: 5%;"> Kundenummer</th>
                        <th class="text-center" style="width: 5%;"> Årsak</th>
                        <th class="text-center" style="width: 5%;"> MAC</th>
                        <th class="text-center" style="width: 50%;"> Kommentar</th>
                        <th class="text-center" style="width: 100px;"> Handlinger</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($trades as $trade): ?>
                        <tr>
                            <td class="text-center"><?php echo count_id(); ?></td>
                            <td><?php echo remove_junk($trade['name']); ?></td>
                            <td class="text-center"><?php echo (int)$trade['qty']; ?></td>
                            <td class="text-center"><?php echo $trade['date']; ?></td>
                            <?php if(get_userlevel() == 1) { echo("<td class='text-center'>{$trade['username']}</td> "); } ?>
                            <td class="text-center"><?php echo $trade['custnr']; ?></td>
                            <td class="text-center"><?php echo $trade['categoryName']; if ($trade['categoryName'] === null) {echo 'Annet';} ?></td>
                            <td class="text-center"><?php echo $trade['mac']; ?></td>
                            <td class="text-center" style="max-width: 250px"><?php echo $trade['comment']; ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit_trade.php?id=<?php echo (int)$trade['id']; ?>"
                                       class="btn btn-warning btn-xs" title="Edit" data-toggle="tooltip">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a href="delete_trade.php?id=<?php echo (int)$trade['id']; ?>"
                                       class="btn btn-danger btn-xs" title="Delete" data-toggle="tooltip">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include_once('layouts/footer.php'); ?>
