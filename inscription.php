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
    
    // تنظيف البيانات من المسافات الزائدة
    $name = trim($name);
    $gmail = trim($gmail);
    $birthdate = trim($birthdate);
    
    // التحقق من صحة البيانات
    if (empty($name) || empty($gmail) || empty($password) || empty($confirmPassword) || empty($birthdate)) {
        echo "جميع الحقول مطلوبة.";
        exit;
    }
    
    // التحقق من طول الاسم (3 أحرف على الأقل و 100 على الأكثر)
    if (strlen($name) < 3 || strlen($name) > 100) {
        echo "الاسم يجب أن يكون بين 3 و 100 حرف.";
        exit;
    }
    
    // التحقق من صيغة البريد الإلكتروني
    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        echo "البريد الإلكتروني غير صالح.";
        exit;
    }
    
    // التحقق من تطابق كلمات المرور
    if ($password !== $confirmPassword) {
        echo "كلمتا المرور غير متطابقتين.";
        exit;
    }
    
    // التحقق من قوة كلمة المرور (6 أحرف على الأقل)
    if (strlen($password) < 6) {
        echo "كلمة المرور يجب أن تكون 6 أحرف على الأقل.";
        exit;
    }
    
    // التحقق من صيغة تاريخ الميلاد (YYYY-MM-DD)
    $dateTime = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$dateTime || $dateTime->format('Y-m-d') !== $birthdate) {
        echo "صيغة تاريخ الميلاد غير صحيحة (استخدم YYYY-MM-DD).";
        exit;
    }
    
    // التحقق من عدم تسجيل البريد من قبل
    try {
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE gmail = ?");
        $checkStmt->execute([$gmail]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            echo "هذا البريد الإلكتروني مسجل بالفعل.";
            exit;
        }
    } catch (PDOException $e) {
        echo "خطأ في التحقق: " . $e->getMessage();
        exit;
    }
    
    // تشفير كلمة المرور
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // إدخال البيانات إلى قاعدة البيانات
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, gmail, password, birthdate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $gmail, $hashedPassword, $birthdate]);
        echo "تم التسجيل بنجاح!";
        // يمكنك إضافة إعادة توجيه هنا
        // header("Location: login.html");
        // exit();
    } catch (PDOException $e) {
        echo "خطأ أثناء التسجيل: " . $e->getMessage();
    }
}
?>