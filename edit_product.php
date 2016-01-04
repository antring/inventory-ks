<?php
$page_title = 'Edit product';
require_once('includes/load.php');

// Checking userlevel
page_require_level(2);

$product = find_by_id('products', (int)$_GET['id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');
if (!$product) {
    $session->msg("d", "Missing product id.");
    redirect('product.php');
}

if (isset($_POST['product'])) {
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'ks_storage', 'buy_price', 'saleing-price');
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_name = remove_junk($db->escape($_POST['product-title']));
        $p_cat = (int)$_POST['product-categorie'];
        $p_qty = remove_junk($db->escape($_POST['product-quantity']));
        $p_ks_storage = remove_junk($db->escape($_POST['ks_storage']));
        $p_buy = remove_junk($db->escape($_POST['buy_price']));
        $p_product_code = remove_junk($db->escape(($_POST['product_number'])));
        $p_sale = remove_junk($db->escape($_POST['saleing-price']));
        $hasMAC = remove_junk($db->escape($_POST['hasMac']));

        if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
            $media_id = '0';
        } else {
            $media_id = remove_junk($db->escape($_POST['product-photo']));
        }
        $query = "UPDATE products SET";
        $query .= " name ='{$p_name}', quantity ='{$p_qty}',";
        $query .= " ks_storage ='{$p_buy}', sale_price ='{$p_sale}', categorie_id ='{$p_cat}',media_id='{$media_id}', product_number='{$p_product_code}', buy_price='{$p_buy}', hasMAC='{$hasMAC}'";
        $query .= " WHERE id ='{$product['id']}'";
        $result = $db->query($query);
        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', "Product updated ");
            redirect('product.php', false);
        } else {
            $session->msg('d', ' Sorry failed to update!');
            redirect('edit_product.php?id=' . $product['id'], false);
        }

    } else {
        $session->msg("d", $errors);
        redirect('edit_product.php?id=' . $product['id'], false);
    }

}

?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>
                <span class="glyphicon glyphicon-th"></span>
                <span>Add New Product</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-7">
                <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
                    <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon">
                           <i class="glyphicon glyphicon-th-large"></i>
                          </span>
                            <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" name="product-categorie">
                                    <option value=""> Velg en kategori</option>
                                    <?php foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo (int)$cat['id']; ?>" <?php if ($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                                            <?php echo remove_junk($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="product-photo">
                                    <option value=""> No image</option>
                                    <?php foreach ($all_photo as $photo): ?>
                                        <option value="<?php echo (int)$photo['id']; ?>" <?php if ($product['media_id'] === $photo['id']): echo "selected"; endif; ?> >
                                            <?php echo $photo['file_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Hovedlager</label>

                                    <div class="input-group">
                                      <span class="input-group-addon">
                                       <i>Stk</i>
                                      </span>
                                        <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">KS-lager</label>

                                    <div class="input-group">
                                          <span class="input-group-addon">
                                            <i>Stk</i>
                                          </span>
                                        <input type="number" class="form-control" name="ks_storage" value="<?php echo remove_junk($product['ks_storage']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Siebelkode</label>

                                    <div class="input-group">
                                      <span class="input-group-addon">
                                        <i>Stk</i>
                                      </span>
                                        <input type="text" class="form-control" name="product_number" value="<?php echo remove_junk($product['product_number']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Innkjøpspris</label>

                                    <div class="input-group">
                                      <span class="input-group-addon">
                                        <i>Stk</i>
                                      </span>
                                        <input type="number" class="form-control" name="buy_price" value="<?php echo remove_junk($product['buy_price']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">Pris</label>

                                    <div class="input-group">
                                       <span class="input-group-addon">
                                         <i>Pris</i>
                                       </span>
                                        <input type="number" class="form-control" name="saleing-price" value="<?php echo remove_junk($product['sale_price']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty">MAC</label>
                                <select class="form-control" name="hasMac">
                                    <option value="0">Ingen MAC</option>
                                    <option value="1">Har MAC</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <button type="submit" name="product" class="btn btn-danger">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<?php include_once('layouts/footer.php'); ?>
