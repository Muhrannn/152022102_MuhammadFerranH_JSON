<?php

$host = 'localhost:3305'; 
$dbname = 'cuaca'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$query = "SELECT suhu, humid, id, lux, ts FROM tb_cuaca ORDER BY ts ASC";
$stmt = $pdo->query($query);

$suhu = [];
$humid = [];
$id_values = [];
$lux = [];
$timestamps = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $suhu[] = $row['suhu'];
    $humid[] = $row['humid'];
    $id_values[] = $row['id'];
    $lux[] = $row['lux'];
    $timestamps[] = $row['ts']; 
}

$timestampObjects = array_map(function($ts) {
    return new DateTime($ts);
}, $timestamps);

array_multisort($timestampObjects, SORT_ASC, $suhu, $humid, $lux, $id_values);


$suhu_min = min($suhu);
$suhu_max = max($suhu);
$suhu_avg = array_sum($suhu) / count($suhu);

$nilai_suhu_humid_brightness = [];
$month_year_max = [];

foreach ($timestampObjects as $index => $timestamp) {
    $entry = [
        'id' => $id_values[$index],
        'suhu' => $suhu[$index],
        'humid' => $humid[$index],
        'kecerahan' => $lux[$index],
        'timestamp' => $timestamp->format('Y-m-d H:i:s') 
    ];
    $nilai_suhu_humid_brightness[] = $entry;

    $month_year = $timestamp->format('Y-m');
    if (!in_array($month_year, array_column($month_year_max, 'month_year'))) {
        $month_year_max[] = ['month_year' => $month_year];
    }
}


$response = [
    'suhumin' => $suhu_min,
    'suhumax' => $suhu_max,
    'suhurata' => $suhu_avg,
    'nilai_suhu_humid_brightness' => $nilai_suhu_humid_brightness,
    'month_year_max' => $month_year_max
];

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
?>