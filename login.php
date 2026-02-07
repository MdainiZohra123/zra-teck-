<?php
// إعداد الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zra_tech";

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التحقق من البيانات المرسلة
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // البحث عن المستخدم
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // التحقق من كلمة المرور
        if (password_verify($password, $user['password'])) {
            echo "تم تسجيل الدخول بنجاح!";
            // إعادة التوجيه إلى الصفحة الرئيسية
            header("Location: home.html");
            exit();
        } else {
            echo "كلمة المرور غير صحيحة.";
        }
    } else {
        echo "البريد الإلكتروني غير موجود.";
    }
}

$conn->close();
?>
