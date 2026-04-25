<?php
// الاتصال بقاعدة البيانات
$servername = "localhost";  // اسم الخادم
$username = "root";         // اسم المستخدم
$password = "";             // كلمة المرور
$dbname = "zratech";        // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من إرسال البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من وجود جميع الحقول المطلوبة
    if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['address']) || 
        !isset($_POST['productRef']) || !isset($_POST['quantity'])) {
        die('جميع الحقول مطلوبة');
    }
    
    // الحصول على القيم من النموذج وتنظيفها
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $productRef = trim($_POST['productRef']);
    $quantity = trim($_POST['quantity']);
    
    // التحقق من عدم ترك الحقول فارغة
    if (empty($name) || empty($phone) || empty($address) || empty($productRef) || empty($quantity)) {
        die('جميع الحقول مطلوبة ولا يمكن أن تكون فارغة');
    }
    
    // التحقق من طول الاسم (3-100 حرف)
    if (strlen($name) < 3 || strlen($name) > 100) {
        die('الاسم يجب أن يكون بين 3 و 100 حرف');
    }
    
    // التحقق من صيغة رقم الهاتف (10-15 رقم)
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        die('رقم الهاتف يجب أن يكون بين 10 و 15 رقم');
    }
    
    // التحقق من طول العنوان (5-255 حرف)
    if (strlen($address) < 5 || strlen($address) > 255) {
        die('العنوان يجب أن يكون بين 5 و 255 حرف');
    }
    
    // التحقق من الكمية (يجب أن تكون رقم موجب بين 1-1000)
    $quantity = intval($quantity);
    if ($quantity < 1 || $quantity > 1000) {
        die('الكمية يجب أن تكون بين 1 و 1000');
    }
    
    // استعلام لإدخال البيانات في الجدول - استخدام prepared statement
    $sql = "INSERT INTO commandes (name, phone, address, productRef, quantity) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $phone, $address, $productRef, $quantity);
    
    if ($stmt->execute()) {
        echo "Commande passée avec succès!";
    } else {
        echo "Erreur: " . $conn->error;
    }
    
    $stmt->close();
}

// إغلاق الاتصال
$conn->close();
?>