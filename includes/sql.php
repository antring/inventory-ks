<?php
require_once('includes/load.php');


/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table)
{
    global $db;
    if (tableExists($table)) {
        return find_by_sql("SELECT * FROM " . $db->escape($table));
    }
}

/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
    global $db;
    $result = $db->query($sql);
    $result_set = $db->while_loop($result);
    return $result_set;
}

/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table, $id)
{
    global $db;
    $id = (int)$id;
    if (tableExists($table)) {
        $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
        if ($result = $db->fetch_assoc($sql))
            return $result;
        else
            return null;
    }
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table, $id)
{
    global $db;
    if (tableExists($table)) {
        $sql = "DELETE FROM " . $db->escape($table);
        $sql .= " WHERE id=" . $db->escape($id);
        $sql .= " LIMIT 1";
        $db->query($sql);
        return ($db->affected_rows() === 1) ? true : false;
    }
}

/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/

function count_by_id($table)
{
    global $db;
    if (tableExists($table)) {
        $sql = "SELECT COUNT(id) AS total FROM " . $db->escape($table);
        $result = $db->query($sql);
        return ($db->fetch_assoc($result));
    }
}

/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table)
{
    global $db;
    $table_exit = $db->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $db->escape($table) . '"');
    if ($table_exit) {
        if ($db->num_rows($table_exit) > 0)
            return true;
        else
            return false;
    }
}

/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST,
/* coming from the login form.
/*--------------------------------------------------------------*/
function authenticate($username = '', $password = '')
{
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if ($db->num_rows($result)) {
        $user = $db->fetch_assoc($result);
        $password_request = sha1($password);
        if ($password_request === $user['password']) {
            return $user['id'];
        }
    }
    return false;
}

/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST,
/* coming from the login_v2.php form.
/* If you used this method then remove authenticate function.
/*--------------------------------------------------------------*/
function authenticate_v2($username = '', $password = '')
{
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if ($db->num_rows($result)) {
        $user = $db->fetch_assoc($result);
        $password_request = sha1($password);
        if ($password_request === $user['password']) {
            return $user;
        }
    }
    return false;
}


/*--------------------------------------------------------------*/
/* Find current log in user by session id
/*--------------------------------------------------------------*/
function current_user()
{
    static $current_user;
    global $db;
    if (!$current_user) {
        if (isset($_SESSION['user_id'])):
            $user_id = intval($_SESSION['user_id']);
            $current_user = find_by_id('users', $user_id);
        endif;
    }
    return $current_user;
}

/*--------------------------------------------------------------*/
/* Find all user by
/* Joining users table and user gropus table
/*--------------------------------------------------------------*/
function find_all_user()
{
    global $db;
    $results = array();
    $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
    $sql .= "g.group_name ";
    $sql .= "FROM users u ";
    $sql .= "LEFT JOIN user_groups g ";
    $sql .= "ON g.group_level=u.user_level ORDER BY u.name ASC";
    $result = find_by_sql($sql);
    return $result;
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/

function updateLastLogIn($user_id)
{
    global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Find all Group name
/*--------------------------------------------------------------*/
function find_by_groupName($val)
{
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return ($db->num_rows($result) === 0 ? true : false);
}

/*--------------------------------------------------------------*/
/* Find group level
/*--------------------------------------------------------------*/
function find_by_groupLevel($level)
{
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return ($db->num_rows($result) === 0 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for checking which user level has access to page
/*--------------------------------------------------------------*/
function page_require_level($require_level)
{
    global $session;
    $current_user = current_user();
    $login_level = find_by_groupLevel($current_user['user_level']);
    $_SESSION['user_level'] = find_by_groupLevel($current_user['user_level']);

    //if user not logged in
    if (!$session->isUserLoggedIn(true)):
        $session->msg('d', 'Please login...');
        redirect('index.php', false);
    //if Group status Deactivate
    elseif ($login_level['group_status'] === '0'):
        $session->msg('d', 'This account has been locked!');
        redirect('home.php', false);
    //checking log in User level and Require level is Less than or equal to
    elseif ($current_user['user_level'] <= (int)$require_level):
        return true;
    else:
        $session->msg("d", "Sorry! you dont have permission to view the page.");
        redirect('home.php', false);
    endif;

}

function get_userlevel()
{
    $user = current_user();
    return $user['user_level'];
}

/*--------------------------------------------------------------*/
/* Function for Finding all product name
/* JOIN with categorie  and media database table
/*--------------------------------------------------------------*/
function join_product_table()
{
    global $db;
    $sql = " SELECT p.id,p.name,p.quantity,p.ks_storage,p.sale_price,p.media_id,p.date,c.name";
    $sql .= " AS categorie,m.file_name AS image";
    $sql .= " FROM products p";
    $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql .= " LEFT JOIN media m ON m.id = p.media_id";
    $sql .= " ORDER BY p.id ASC";
    return find_by_sql($sql);

}

/*--------------------------------------------------------------*/
/* Function for Finding all product name
/* Request coming from ajax.php for auto suggest
/*--------------------------------------------------------------*/

function find_product_by_title($product_name)
{
    global $db;
    $p_name = remove_junk($db->escape($product_name));
    $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
    $result = find_by_sql($sql);
    return $result;
}

/*--------------------------------------------------------------*/
/* Function for Finding all product info by product title
/* Request coming from ajax.php
/*--------------------------------------------------------------*/
function find_all_product_info_by_title($title)
{
    global $db;
    $sql = "SELECT * FROM products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .= " LIMIT 1";
    return find_by_sql($sql);
}

function get_product_info_by_id($id)
{
    global $db;
    $sql = "SELECT * FROM products WHERE id = {$id}";
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Update product quantity
/*--------------------------------------------------------------*/
function update_product_qty($qty, $p_id)
{
    global $db;
    $qty = (int)$qty;
    $id = (int)$p_id;
    $sql = "UPDATE products SET ks_storage=ks_storage -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return ($db->affected_rows() === 1 ? true : false);
}

function update_bedrift_qty($qty, $p_id)
{
    global $db;
    $qty = (int)$qty;
    $id = (int)$p_id;
    $sql = "UPDATE products SET quantity = quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return ($db->affected_rows() === 1 ? true : false);
}

function update_entre_qty($qty, $p_id)
{
    global $db;
    $qty = (int)$qty;
    $id = (int)$p_id;
    $sql = "UPDATE products SET m_storage = m_storage -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return ($db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for Display Recent product Added
/*--------------------------------------------------------------*/
function find_recent_product_added($limit)
{
    global $db;
    $sql = " SELECT p.id,p.name,p.sale_price,p.media_id,c.name AS categorie,";
    $sql .= "m.file_name AS image FROM products p";
    $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql .= " LEFT JOIN media m ON m.id = p.media_id";
    $sql .= " ORDER BY p.id DESC LIMIT " . $db->escape((int)$limit);
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Find Highest selling Product
/*--------------------------------------------------------------*/
function find_higest_saleing_product($limit)
{
    global $db;
    $sql = "SELECT p.name, COUNT(s.product_id) AS totalSold, SUM(s.qty) AS totalQty";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON p.id = s.product_id ";
    $sql .= " GROUP BY s.product_id";
    $sql .= " ORDER BY SUM(s.qty) DESC LIMIT " . $db->escape((int)$limit);
    return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for find all sales
/*--------------------------------------------------------------*/
function find_all_sale()
{
    global $db;
    $sql = "SELECT s.id, s.qty, s.price, s.date, p.name, s.comment, s.custnr, u.username, s.mac";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.FK_userID = u.id";
    $sql .= " ORDER BY s.date DESC, id DESC LIMIT 100";
    return find_by_sql($sql);
}

function find_all_entre_sale()
{
    global $db;
    $sql = "SELECT s.id, s.qty, s.price, s.date, p.name, s.comment, s.custnr, u.username, s.mac";
    $sql .= " FROM entre_sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.FK_userID = u.id";
    $sql .= " ORDER BY s.date DESC, id DESC LIMIT 100";
    return find_by_sql($sql);
}

function find_all_user_sales()
{
    $userID = $_SESSION['user_id'];

    $sql = "SELECT s.id,s.qty,s.price,s.date,p.name, s.comment, s.custnr, s.mac";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " WHERE s.FK_userID = '$userID'";
    $sql .= " ORDER BY s.date DESC, id DESC LIMIT 100";

    return find_by_sql($sql);
}

function find_all_entre_user_sales()
{
    $userID = $_SESSION['user_id'];

    $sql = "SELECT s.id,s.qty,s.price,s.date,p.name, s.comment, s.custnr, s.mac";
    $sql .= " FROM entre_sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " WHERE s.FK_userID = '$userID'";
    $sql .= " ORDER BY s.date DESC, id DESC LIMIT 100";

    return find_by_sql($sql);
}

function find_all_trades()
{
    global $db;
    $sql = "SELECT t.id, t.qty, t.price, t.date, p.name, t.comment, t.custnr, u.username, r.categoryName, t.mac";
    $sql .= " FROM trade t";
    $sql .= " LEFT JOIN products p ON t.product_id = p.id";
    $sql .= " LEFT JOIN users u ON t.FK_userID = u.id";
    $sql .= " LEFT JOIN returnCategory r on FK_returnCategory = r.id";
    $sql .= " ORDER BY t.date DESC, id DESC LIMIT 100";
    return find_by_sql($sql);
}

function find_all_user_trades()
{
    $userID = $_SESSION['user_id'];

    $sql = "SELECT t.id, t.qty, t.price, t.date, p.name, t.comment, t.custnr, r.categoryName, t.mac";
    $sql .= " FROM trade t";
    $sql .= " LEFT JOIN products p ON t.product_id = p.id";
    $sql .= " LEFT JOIN returnCategory r on FK_returnCategory = r.id";
    $sql .= " WHERE t.FK_userID = '$userID'";
    $sql .= " ORDER BY t.date DESC, id DESC LIMIT 100";
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Display Recent sale
/*--------------------------------------------------------------*/
function find_recent_sale_added($limit)
{
    global $db;
    $sql = "SELECT s.id,s.qty,s.price,s.date,p.name,u.username";
    $sql .= " FROM sales s LEFT JOIN users u ON s.FK_userID = u.id";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " ORDER BY s.date DESC, id DESC LIMIT " . $db->escape((int)$limit);
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date, $end_date)
{
    global $db;
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));
    $sql = "SELECT s.date, p.name,p.sale_price,p.buy_price,u.username,s.FK_userID, ";
    $sql .= "COUNT(s.product_id) AS total_records,";
    $sql .= "s.qty AS total_sales,";
    $sql .= "(p.sale_price * s.qty) AS total_saleing_price,";
    $sql .= "(p.buy_price * s.qty) AS total_buying_price ";
    $sql .= "FROM sales s ";
    $sql .= "JOIN users u ON s.FK_userID ";
    $sql .= "LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
    $sql .= " GROUP BY DATE(s.date),p.name";
    $sql .= " ORDER BY DATE(s.date) DESC";
    return $db->query($sql);
}

function get_sales_by_date($start, $end, $id)
{
    global $db;
    $start = date("Y-m-d", strtotime($start));
    $end = date("Y-m-d", strtotime($end));
    $sql = "SELECT p.name, SUM(s.qty) as sold, COUNT(s.id) as sales, p.ks_storage FROM sales s LEFT JOIN products p ON p.id = s.product_id WHERE s.product_id = {$id} AND s.date BETWEEN '{$start}' AND '{$end}'";
    return find_by_sql($sql);
}

function get_storage($start, $id)
{
    global $db;
    $start = date("Y-m-d", strtotime($start));
    $sql = "SELECT p.name, SUM(s.qty) as sold, COUNT(s.id) as sales, p.ks_storage FROM sales s LEFT JOIN products p ON p.id = s.product_id WHERE s.product_id = {$id} AND s.date >= '{$start}'";
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function dailySales($year, $month)
{
    global $db;
    $sql = "SELECT s.qty, u.username,DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
    $sql .= "(p.sale_price * s.qty) AS total_saleing_price";
    $sql .= " FROM sales s JOIN users u ON s.FK_userID = u.id";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
    $sql .= " GROUP BY DATE_FORMAT( s.date,  '%e' ),s.product_id ORDER BY s.id DESC";
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate Monthly sales report
/*--------------------------------------------------------------*/
function monthlySales($year)
{
    global $db;
    $sql = "SELECT s.qty,";
    $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
    $sql .= "(p.sale_price * s.qty) AS total_saleing_price";
    $sql .= " FROM sales s";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
    $sql .= " GROUP BY DATE_FORMAT( s.date,  '%c' ),s.product_id";
    $sql .= " ORDER BY date_format(s.date, '%c' ) ASC";
    return find_by_sql($sql);
}

/* Return storagestatus */
function storage_status()
{
    $sql = "SELECT name, ks_storage, quantity, m_storage, id FROM products";
    return find_by_sql($sql);
}

function get_last_product_id()
{
    global $db;
    $sql = "SELECT COUNT(id) FROM products";
    return find_by_sql($sql);
}

function get_products_from_categories()
{
    global $db;
    $sql = "SELECT * FROM `products` ORDER BY id ASC";
    return find_by_sql($sql);
}

function get_products_user()
{
    global $db;
    $sql = "SELECT * FROM `products` WHERE categorie_id != 4 ORDER BY id ASC";
    return find_by_sql($sql);
}

function get_categories_user()
{
    global $db;
    $sql = "SELECT * FROM `categories` WHERE id != 4 ORDER BY id ASC";
    return find_by_sql($sql);
}

function search_custnr($cust)
{
    global $db;
    $sql = "SELECT s.id, s.qty, s.price, s.date, p.name, s.comment, s.custnr, u.username";
    $sql .= " FROM sales s ";
    $sql .= " LEFT JOIN products p ON s.product_id = p.id";
    $sql .= " LEFT JOIN users u ON s.FK_userID = u.id";
    $sql .= " WHERE custnr LIKE '%{$cust}%'";
    $sql .= " ORDER BY s.date DESC, id DESC LIMIT 50";
    return find_by_sql($sql);
}

function storage_fix_deletion($id, $qty)
{
    global $db;
    $sql = "UPDATE `products` SET ks_storage = (ks_storage + {$qty}) WHERE id = {$id}";
    return find_by_sql($sql);
}

function storage_fix_entre_deletion($id, $qty)
{
    global $db;
    $sql = "UPDATE `products` SET m_storage = (m_storage + {$qty}) WHERE id = {$id}";
    return find_by_sql($sql);
}

function sales_search($custnr)
{
    global $db;
    $sql = "SELECT * FROM sales WHERE custnr = {$custnr}";
    return find_by_sql($sql);
}


function storage_log($qty, $ks, $prod)
{
    global $db;
    $sql = "INSERT INTO `logg`(`userID`, `quantity`, `ks_storage`, `productID`) VALUES ({$_SESSION['user_id']}, {$qty}, {$ks}, {$prod})";
    $db->query($sql);
}

function storage_log_ext($hoved, $ks, $mon, $id)
{
    global $db;
    $sql = "INSERT INTO lagerlogging (diff_hoved, diff_ks, diff_m, hovedlager, kslager, monlager, FK_prod, FK_userID) VALUES ({$hoved},{$ks},{$mon},(SELECT `quantity` FROM products WHERE id = {$id}), (SELECT `ks_storage` FROM products WHERE id = {$id}), (SELECT `m_storage` FROM products WHERE id = {$id}), {$id}, {$_SESSION['user_id']})";
    $db->query($sql);
}

function storage_log_ext_ks($ks, $id)
{
    global $db;
    $sql = "INSERT INTO lagerlogging (diff_hoved, diff_ks, diff_m, hovedlager, kslager, monlager, FK_prod, FK_userID) VALUES (0,{$ks},0,(SELECT `quantity` FROM products WHERE id = {$id}), (SELECT `ks_storage` FROM products WHERE id = {$id}), (SELECT `m_storage` FROM products WHERE id = {$id}), {$id}, {$_SESSION['user_id']})";
    $db->query($sql);
}

function storage_log_ext_mon($mon, $id)
{
    global $db;
    $sql = "INSERT INTO lagerlogging (diff_hoved, diff_ks, diff_m, hovedlager, kslager, monlager, FK_prod, FK_userID) VALUES (0, 0, {$mon},(SELECT `quantity` FROM products WHERE id = {$id}), (SELECT `ks_storage` FROM products WHERE id = {$id}), (SELECT `m_storage` FROM products WHERE id = {$id}), {$id}, {$_SESSION['user_id']})";
    $db->query($sql);
}

function get_log()
{
    global $db;
    $sql = "SELECT p.name, l.ks_storage, l.quantity, l.updated, u.username FROM logg l LEFT JOIN products p ON l.productID = p.id JOIN users u ON l.userID = u.id ORDER BY l.id DESC LIMIT 100";
    return find_by_sql($sql);
}

function get_log_ext()
{
    global $db;
    $sql = "SELECT p.name, l.diff_hoved, l.diff_ks, l.diff_m, l.timecreated, l.hovedlager, l.kslager, l.monlager, u.username FROM lagerlogging l LEFT JOIN products p ON l.FK_prod = p.id JOIN users u ON l.FK_userID = u.id ORDER BY l.id DESC LIMIT 100";
    return find_by_sql($sql);
}

function get_unique_pid($start, $end)
{
    global $db;
    $sql = "SELECT DISTINCT product_id FROM sales WHERE date BETWEEN '{$start}' AND '{$end}' ORDER BY product_id ASC";
    return find_by_sql($sql);
}

function get_unique_pid_trades($start, $end)
{
    global $db;
    $sql = "SELECT DISTINCT t.product_id, p.name FROM trade t LEFT JOIN products p ON t.product_id = p.id WHERE t.date BETWEEN '{$start}' AND '{$end}' ORDER BY product_id ASC";
    return find_by_sql($sql);
}

function get_trades_by_dates($start, $end, $id, $cat)
{
    global $db;
    $start = date("Y-m-d", strtotime($start));
    $end = date("Y-m-d", strtotime($end));
    $sql = "SELECT SUM(t.qty) as antall FROM trade t WHERE product_id = {$id} AND t.FK_returnCategory = {$cat} AND t.date BETWEEN '{$start}' AND '{$end}' ";
    return find_by_sql($sql);
}

function get_trade_total($start, $end, $id)
{
    global $db;
    $start = date("Y-m-d", strtotime($start));
    $end = date("Y-m-d", strtotime($end));
    $sql = "SELECT SUM(t.qty) as totalt FROM trade t WHERE t.product_id = {$id} AND t.date BETWEEN '{$start}' AND '{$end}'";
    return find_by_sql($sql);
}

function get_active_category($id)
{
    global $db;
    $sql = "SELECT r.categoryName FROM trade t LEFT JOIN returnCategory r ON t.FK_returnCategory = r.id WHERE t.id = {$id}";
    return find_by_sql($sql);
}