<?php
// الاتصال بقاعدة البيانات
$host = 'localhost'; // اسم الخادم
$dbname = 'zra_tech'; // اسم قاعدة البيانات
$username = 'root'; // اسم المستخدم لقاعدة البيانات
$password = ''; // كلمة المرور لقاعدة البيانات

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// التحقق من أن البيانات أُرسلت عبر النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $gmail = $_POST['gmail'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';

    // التحقق من صحة البيانات
    if (empty($name) || empty($gmail) || empty($password) || empty($confirmPassword) || empty($birthdate)) {
        echo "جميع الحقول مطلوبة.";
        exit;
    }

    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        echo "البريد الإلكتروني غير صالح.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "كلمتا المرور غير متطابقتين.";
        exit;
    }

    // تشفير كلمة المرور
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // إدخال البيانات إلى قاعدة البيانات
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, gmail, password, birthdate) VALUES (:name, :gmail, :password, :birthdate)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gmail', $gmail);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->execute();
        echo "تم التسجيل بنجاح!";
    } catch (PDOException $e) {
        echo "خطأ أثناء التسجيل: " . $e->getMessage();
    }
}
?>
