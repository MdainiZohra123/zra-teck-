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
    // الحصول على القيم من النموذج
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $productRef = $_POST['productRef'];
    $quantity = $_POST['quantity'];

    // استعلام لإدخال البيانات في الجدول
    $sql = "INSERT INTO commandes (name, phone, address, productRef, quantity) 
            VALUES ('$name', '$phone', '$address', '$productRef', '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo "Commande passée avec succès!";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
}

// إغلاق الاتصال
$conn->close();
?>
