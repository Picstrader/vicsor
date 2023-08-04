
<?php
define('HOST', 'kyivst16.mysql.tools');
define('USER', 'kyivst16_simplesalenft');
define('PASSWORD', '84V;z^tn5C');
define('DATABASE', 'kyivst16_simplesalenft');

$connect = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

$sql = "SELECT * FROM `trade_pg_tb` ORDER BY `id` DESC LIMIT 17";
$res = mysqli_query($connect, $sql);
while ($arr[] = mysqli_fetch_assoc($res)) {
  $arr2 = $arr;
}



?>



<table class="trade_table">
  <thead class="trade_table__head">
    <tr class="trade_table__head-row">
      <td class="trade_table__head-row-data">
        <img src="#" title="Тема" class="trade_table__head-row-data-image">
        <div class="trade_table__head-row-data-bottom">
          <img src="#" class="trade_table__head-row-data-bottom-img">
          <p class="trade_table__head-row-data-bottom-sort">Сортировка</p>
        </div>
      </td>
      <td class="trade_table__head-row-data">
        <p class="trade_table__head-row-data-title">Ст. разм</p>
        <img src="#" class="trade_table__head-row-data-image">
        <div class="trade_table__head-row-data-bottom">
          <img src="#" class="trade_table__head-row-data-bottom-img">
          <p class="trade_table__head-row-data-bottom-sort">Сортировка</p>
        </div>
      </td>
      <td class="trade_table__head-row-data">
        <img src="#" class="trade_table__head-row-data-image">
        <p class="trade_table__head-row-data-title">Разм. кол.</p>
        <div class="trade_table__head-row-data-bottom">
          <img src="#" class="trade_table__head-row-data-bottom-img">
          <p class="trade_table__head-row-data-bottom-sort">Сортировка</p>
        </div>
      </td>
      <td class="trade_table__head-row-data">
        <img src="#" class="trade_table__head-row-data-image">
        <p class="trade_table__head-row-data-title">Win</p>
        <div class="trade_table__head-row-data-bottom">
          <img src="#" class="trade_table__head-row-data-bottom-img">
          <p class="trade_table__head-row-data-bottom-sort">Сортировка</p>
        </div>
      </td>
      <td class="trade_table__head-row-data">
        <p class="trade_table__head-row-data-title">Размещено в коллекции/Всего в коллекции</p>
        <div class="trade_table__head-row-data-bottom">
          <img src="#" class="trade_table__head-row-data-bottom-img">
          <p class="trade_table__head-row-data-bottom-sort">Сортировка</p>
        </div>
      </td>
      <td class="trade_table__head-row-data">
        <img src="#" class="trade_table__head-row-data-image">
        <p class="trade_table__head-row-data-title">Прибыль</p>
        <div class="trade_table__head-row-data-bottom">
          <img src="#" class="trade_table__head-row-data-bottom-img">
          <p class="trade_table__head-row-data-bottom-sort">Сортировка</p>
        </div>
      </td>
      <td class="trade_table__head-row-data">
        <img src="#" class="trade_table__head-row-data-image">
        <p class="trade_table__head-row-data-title">Действие</p>
      </td>
    </tr>
  </thead>
  <tbody class="trade_table__body">


<?php

foreach ($arr2 as $index => $row) {
  echo('<tr class="trade_table__body-row">');
  foreach ($row as $key => $value) {
    if ($key != 'id') {
      if ($key == 'operation') {
        echo('<td class="trade_table__body-row-data"><a href="#&operation=' . $row['id'] . '">' . $value . '</a></td>');
      } else {
        echo('<td class="trade_table__body-row-data">' . $value . '</td>');
      }
    }
  }
  echo '</tr>';
}
?>

  </tbody>
</table>