<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Mpdf\Mpdf;

require_login();

if (!isset($_GET['order_id'])) {
    die("Invalid request.");
}

$orderId = (int)$_GET['order_id'];
$pdo = Database::getInstance()->getConnection();

// Fetch Order Details
// Security: Verify order belongs to user or user is admin
$sql = "SELECT o.*, u.username, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?";

if (!is_admin()) {
    $sql .= " AND o.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId, $_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId]);
}

$order = $stmt->fetch();

if (!$order) {
    die("Order not found or access denied.");
}

// Fetch Items
$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmtItems->execute([$orderId]);
$items = $stmtItems->fetchAll();

// Generate HTML for Invoice
$html = '
<html>
<head>
<style>
body { font-family: sans-serif; }
.header { text-align: center; margin-bottom: 20px; }
.details { width: 100%; margin-bottom: 20px; }
.details td { vertical-align: top; }
.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.items th, .items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
.items th { background-color: #f2f2f2; }
.total { text-align: right; font-weight: bold; font-size: 1.2em; }
</style>
</head>
<body>

<div class="header">
    <h1>Factura Fiscala</h1>
    <p>DAW Store SRL</p>
</div>

<table class="details">
    <tr>
        <td>
            <strong>Furnizor:</strong><br>
            DAW Store SRL<br>
            Bucuresti, Romania<br>
            CIF: RO123456<br>
            RC: J40/123/2020
        </td>
        <td style="text-align: right;">
            <strong>Client:</strong><br>
            ' . htmlspecialchars($order['username']) . '<br>
            ' . htmlspecialchars($order['email']) . '<br>
            <br>
            <strong>Adresa Livrare:</strong><br>
            ' . htmlspecialchars($order['delivery_address'] ?? 'N/A') . '
        </td>
    </tr>
</table>

<p><strong>Comanda #:</strong> ' . $order['id'] . '</p>
<p><strong>Data:</strong> ' . date('d.m.Y', strtotime($order['created_at'])) . '</p>

<table class="items">
    <thead>
        <tr>
            <th>Produs</th>
            <th>Cantitate</th>
            <th>Pret Unitar (RON)</th>
            <th>Total (RON)</th>
        </tr>
    </thead>
    <tbody>';

foreach ($items as $item) {
    $lineTotal = $item['quantity'] * $item['price_at_purchase'];
    $html .= '
        <tr>
            <td>' . htmlspecialchars($item['name']) . '</td>
            <td>' . $item['quantity'] . '</td>
            <td>' . number_format($item['price_at_purchase'], 2) . '</td>
            <td>' . number_format($lineTotal, 2) . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<div class="total">
    Total de Plata: ' . number_format($order['total_amount'], 2) . ' RON
</div>

</body>
</html>';

// Initialize mPDF and output
try {
    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Factura_Comanda_' . $orderId . '.pdf', 'D'); // D for Download
} catch (\Mpdf\MpdfException $e) {
    echo "Error generating invoice: " . $e->getMessage();
}
