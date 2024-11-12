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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT suhu, humid, id, lux, ts FROM tb_cuaca WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo "<h2>Data for ID: " . htmlspecialchars($id) . "</h2>";
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Suhu</th>
                    <th>Humidity</th>
                    <th>Kecerahan (Lux)</th>
                    <th>Timestamp</th>
                </tr>
                <tr>
                    <td>" . htmlspecialchars($row['id']) . "</td>
                    <td>" . htmlspecialchars($row['suhu']) . "</td>
                    <td>" . htmlspecialchars($row['humid']) . "</td>
                    <td>" . htmlspecialchars($row['lux']) . "</td>
                    <td>" . htmlspecialchars($row['ts']) . "</td>
                </tr>
              </table>";
    } else {
        echo "<p>No data found for ID: " . htmlspecialchars($id) . "</p>";
    }
} else {
    echo "<p>Please provide an ID.</p>";
}
?>
