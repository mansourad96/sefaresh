<?php
// نمایش خطاهای PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// هدرهای CORS برای اجازه دسترسی از دامنه‌های دیگر
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// اطلاعات اتصال به پایگاه داده
$host = 'sql100.infinityfree.com'; // هاست دیتابیس
$dbname = 'if0_38417910_wp161'; // نام دیتابیس
$username = 'if0_38417910'; // نام کاربری
$password = 'ovbL3A3CHHwUrz'; // رمز عبور

try {
    // اتصال به پایگاه داده
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // دریافت داده‌های ارسالی از فرانت‌اند
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception("داده‌های ارسالی نامعتبر هستند.");
    }

    // بررسی وجود کلیدهای مورد نیاز
    if (!isset($data['table']) || !isset($data['items'])) {
        throw new Exception("داده‌های ارسالی ناقص هستند.");
    }

    $tableNumber = $data['table'];
    $items = json_encode($data['items']); // تبدیل آیتم‌ها به JSON

    // ذخیره سفارش در پایگاه داده
    $stmt = $conn->prepare("INSERT INTO orders (table_number, items) VALUES (:table_number, :items)");
    $stmt->bindParam(':table_number', $tableNumber);
    $stmt->bindParam(':items', $items);
    $stmt->execute();

    // پاسخ موفقیت‌آمیز به فرانت‌اند
    echo json_encode(['success' => 'سفارش با موفقیت ذخیره شد!']);
} catch (PDOException $e) {
    // پاسخ خطا به فرانت‌اند
    echo json_encode(['error' => 'خطا در ذخیره‌سازی سفارش: ' . $e->getMessage()]);
} catch (Exception $e) {
    // پاسخ خطا به فرانت‌اند
    echo json_encode(['error' => 'خطا: ' . $e->getMessage()]);
}
?>