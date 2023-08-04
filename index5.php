<?php
include '../config.php';
function call_chatgpt($text, $api_key, $api_endpoint) {
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [['role' => 'user', 'content' => $text]],
        'temperature' => 0.7,
        'max_tokens' => 750
    ];

    $curl = curl_init($api_endpoint);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 300,
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

function getProducts()
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8");
    if ($db->connect_errno === 0) {
        $query = "SELECT * FROM products;";
        $res = $db->query($query);
        if ($res && $res->num_rows) {
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

$api_key = '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';
$api_endpoint = 'https://api.openai.com/v1/chat/completions';
// $csv_file = '1.csv';
// $csv_data = [];

// if (($handle = fopen($csv_file, "r")) !== false) {
//     while (($row = fgetcsv($handle, 0, ";")) !== false) {
//         $csv_data[] = $row;
//     }
//     fclose($handle);
// }
// $counter = 0;
// $updated_csv_data = [];
$products = getProducts();
$csv_data = [$products[0]];
foreach ($csv_data as $row) {
    // if ($counter == 0) {
    //     $updated_csv_data[] = $row;
    //     $counter++;
    //     continue;
    // }
    echo "Изначальный текст: ".$row["description"]."<br><br><br><br>";
    
    $description = "Перефразируй текст и сделай уникальным на Украинском языке. Длина текста до 200 слов. В текст добавляй название компании Inox Castle: ".$row['description'];
    
    $response_data = call_chatgpt($description, $api_key, $api_endpoint);
    sleep(1);
    
    if (isset($response_data['choices'][0]['message']['content'])) {
        $generated_text = $response_data['choices'][0]['message']['content'];
        $row['description_new'] = $generated_text;
        echo "Переделанный текст: ".$generated_text."<br><br><br><br>";
    }

    //$updated_csv_data[] = $row;
}

// if (($handle = fopen($csv_file, "w")) !== false) {
//     foreach ($updated_csv_data as $row) {
//         fputcsv($handle, $row, ";");
//     }
//     fclose($handle);
// }
?>