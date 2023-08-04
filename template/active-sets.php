<?php
define('HOST', 'kyivst16.mysql.tools');
define('USER', 'kyivst16_simplesalenft');
define('PASSWORD', '84V;z^tn5C');
define('DATABASE', 'kyivst16_simplesalenft');

define('RECS_PER_PG', 20);
define('PGS_ASIDE', 5);



$connect = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

$sql = "SELECT COUNT(*) AS count FROM `trade`";
$res = mysqli_query($connect, $sql);
$ar_count = mysqli_fetch_assoc($res);
$tb_recs_quantity = $ar_count['count'];
$kol_pages = ceil($tb_recs_quantity / RECS_PER_PG);
$kol_pages = intval($kol_pages);


$sql = "SELECT * FROM `trade` ORDER BY `id` DESC LIMIT 0, " .  RECS_PER_PG;
$res = mysqli_query($connect, $sql);
while ($arr[] = mysqli_fetch_assoc($res)) {
  $arr2 = $arr;
}

?>

<section class="active-sets">
    <p class="active-sets__title">Active sets</p>
    <div class="active-sets__filter">
        <div class="active-sets__filter-inner">
            <div class="main_form__sect">
                <div class="main_form__sect-title">Placement cost</div>
                <div class="main_form_input">
                    <div class="main-form__sect-input-field">
                        <input id="p_c_min" type="number" placeholder="Min"></input>
                    </div>
                    <div class="main_form_to">
                    to
                    </div>
                    <div class="main-form__sect-input-field">
                        <input id="p_c_max" type="number" placeholder="Max"></input>
                    </div>
                </div>
                <div class="main_form__sect-title"><?= $fs['main_currency'] ?></div>
            </div>
            <div class="main_form__sect">
                <div class="main_form__sect-title">Total photos in set</div>
                <div class="main_form_input">
                    <div class="main-form__sect-input-field">
                        <input id="t_ph_min" type="number" placeholder="Min"></input>
                    </div>
                    <div class="main_form_to">
                    to
                    </div>
                    <div class="main-form__sect-input-field">
                        <input id="t_ph_max" type="number" placeholder="Max"></input>
                    </div>
                </div>
                <div class="main_form__sect-title">Pics</div>
            </div>
            <div class="main_form__sect">
                <div class="main_form__sect-title">Total purchasable photos</div>
                <div class="main_form_input">
                    <div class="main-form__sect-input-field">
                        <input id="pur_min" type="number" placeholder="Min"></input>
                    </div>
                    <div class="main_form_to">
                    to
                    </div>
                    <div class="main-form__sect-input-field">
                        <input id="pur_max" type="number" placeholder="Max"></input>
                    </div>
                </div>
                <div class="main_form__sect-title">Pics</div>
                </div>
            </div>
    </div>
    <div class="table-wrapper">

    <table class="fl-table">
        <thead>
        <tr>
            <th class="fl-table__first">Placement cost</th>
            <th>Total photos in set</th>
            <th>Total purchasable photos</th>
            <th>Profit %</th>
            <th>Profit USD</th>
            <th>Заполненость сета</th>
            <th class="fl-table__last">Время до окончания сета</th>
        </tr>
        </thead>
        <tbody id="main_tbody">

    <?php
        if (isset($arr2)) {
            foreach ($arr2 as $index => $row) {
              echo('<tr>');

              $count_users = explode(";", $row['users_photos']);
              $count_users = count($count_users);
              $profit_usd = ($row['cost']*($row['total_photos']-$row['pur_photos']))/$row['pur_photos'];
              $profit_usd = $profit_usd*0.9;
              $profit_percent = $profit_usd*100/$row['cost'];
              echo('<td>' . $row['cost'] . '</td>');
              echo('<td>' . $row['total_photos'] . '</td>');
              echo('<td>' . $row['pur_photos'] . '</td>');
              echo('<td>'.round($profit_percent, 0).'</td>');
              echo('<td>'.round($profit_usd, 0).'</td>');
              echo('<td>'.$count_users.'/'.$row['total_photos'].'</td>');
              echo('<td>' . $row['time'] . '</td>');


            echo('</tr>');
            }
        }
    ?>

        </tbody>
    </table>
</div>





<div class="active_sets__pagination" id="active_sets__pagination">

<?php

// $kol_pages = 100;
$l = PGS_ASIDE;
$r = PGS_ASIDE;

$d = $kol_pages;
$c = 1 ;
$db = 1;
$de = $db + $d - 1;
$lb = $c-$l; if ($lb < $db) { $lb = $db; }
// $le = $c - 1; if ($le < $db) { $le = $db; }
// $rb = $c + 1; if ($rb >$de) { $rb = $de; }
$re = $c + $r; if ($re > $de) {$re = $de; }
$prev = $c - 1; //Check if $prev<$db will later
$next = $c + 1; //Check if $next>$de will later
$first = $db;
$last = $de;

//Вывод ссылки "Предыдущая страница"
if ($prev < $db) { echo("<a href='#' class='active_sets__pagination-not_activ' data-page='no'><img class='active_sets__pagination-not_activ-prev_img' src='./inc/assets/img/main-not-active-pg.png'></a>"); }
else{              echo("<a href='#' class='active_sets__pagination-previous' data-page='$prev'><img class='active_sets__pagination-previous-img' src='./inc/assets/img/main-prev-next-pg.png'></a>"); }

//Вывод ссылки "Первая страница"
if ($first !== $lb) { echo("<a href='#' class='active_sets__pagination-first' data-page='$first'>$first </a>"); }

if (($lb - $first) > 1) { echo('...'); }

//Вывод ссылок основного блока, включая текущую. Текущей ссылке присваивается класс "c".
for ($i=$lb; $i <= $re; $i++) {
    if ($i == $c) {
        echo("<a href='#' class='active_sets__pagination-current' data-page='no'>$i</a>");
    }else {
        echo("<a href='#' class='active_sets__pagination-a' data-page='$i'>$i</a>");
    }
}

if (($last - $re) > 1) { echo('...'); }

//Вывод ссылки "Последняя страница"
if ($last !== $re) { echo("<a href='#' class='active_sets__pagination-last' data-page='$last'>$last</a>"); }

//Вывод ссылки "Next"
if ($next > $de) { echo("<a href='#' class='active_sets__pagination-not_activ' data-page='no'><img class='active_sets__pagination-not_activ-next_img' src='./inc/assets/img/main-not-active-pg.png'></a>"); }
else {             echo("<a href='#' class='active_sets__pagination-next' data-page='$next'><img class='active_sets__pagination-next-img' src='./inc/assets/img/main-prev-next-pg.png'></a>"); }

?>





</div>

</section>