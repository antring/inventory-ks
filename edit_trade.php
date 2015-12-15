<?php
$page_title = 'Rediger Retur';
require_once('includes/load.php');
// Checking userlevel
page_require_level(3);


$sale = find_by_id('trade', (int)$_GET['id']);
if (!$sale) {
    $session->msg("d", "Missing product id.");
    redirect('trades.php');
}

$product = find_by_id('products', $sale['product_id']);


if (isset($_POST['update_sale'])) {
    $req_fields = array('title', 'quantity', 'price', 'total', 'date', 'custnr', 'comment');
    validate_fields($req_fields);
    if (empty($errors)) {
        $p_id = $db->escape((int)$product['id']);
        $s_qty = $db->escape((int)$_POST['quantity']);
        $s_total = $db->escape($_POST['total']);
        $date = $db->escape($_POST['date']);
        $custnr = $db->escape($_POST['custnr']);
        $comment = $db->escape($_POST['comment']);
        $s_date = date("Y-m-d", strtotime($date));

        $qty_change = $s_qty - $sale['qty'];

        $sql = "UPDATE trade SET";
        $sql .= " product_id= '{$p_id}',qty={$s_qty},price='{$s_total}',date='{$s_date}', custnr='{$custnr}', comment='{$comment}'";
        $sql .= " WHERE id ='{$sale['id']}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            if ($s_qty != $product['ks_storage']) {
                $session->msg('s', "Trade updated.");
                redirect('edit_trade.php?id=' . $sale['id'], false);
            } else {
                $session->msg('s', "Trade updated.");
                redirect('edit_trade.php?id=' . $sale['id'], false);
            }
        } else {
            $session->msg('d', ' Sorry failed to update!');
            redirect('trades.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_trade.php?id=' . (int)$sale['id'], false);
    }
}

include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>All Sales</span>
                </strong>

                <div class="pull-right">
                    <a href="trades.php" class="btn btn-primary">Alle Returer</a>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <th> Produkt</th>
                    <th> Antall</th>
                    <th> Pris</th>
                    <th> Total</th>
                    <th> Dato</th>
                    <th> Kundenummer</th>
                    <th> Kommentar</th>
                    </thead>
                    <tbody id="product_info">
                    <tr>
                        <form method="post">
                            <td id="s_name">
                                <input type="text" class="form-control" id="sug_input" name="title" value="<?php echo remove_junk($product['name']); ?>">

                                <div id="result" class="list-group"></div>
                            </td>
                            <td id="s_qty">
                                <input type="text" class="form-control" name="quantity" value="<?php echo (int)$sale['qty']; ?>">
                            </td>
                            <td id="s_price">
                                <input type="text" class="form-control" name="price" value="<?php echo remove_junk($product['sale_price']); ?>" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="total" value="<?php echo remove_junk($sale['price']); ?>" readonly>
                            </td>
                            <td id="s_date">
                                <input type="date" class="form-control datepicker" name="date" data-date-format="" value="<?php echo remove_junk($sale['date']); ?>">
                            </td>
                            <td>
                                <input type='number' class='form-control' name='custnr' value="<?php echo remove_junk($sale['custnr']); ?>" required>
                            </td>
                            <td>
                                <textarea name="comment" rows="1" style="width: 100%"><?php echo remove_junk($sale['comment']); ?></textarea>
                            </td>
                            <td>
                                <button type="submit" name="update_sale" class="btn btn-primary">Oppdater Retur</button>
                            </td>
                        </form>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<?php include_once('layouts/footer.php'); ?>
