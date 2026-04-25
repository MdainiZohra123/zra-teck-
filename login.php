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
    // التحقق من وجود البيانات المطلوبة
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        die('جميع الحقول مطلوبة');
    }
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // التحقق من عدم ترك الحقول فارغة
    if (empty($email) || empty($password)) {
        die('جميع الحقول مطلوبة ولا يمكن أن تكون فارغة');
    }
    
    // التحقق من صيغة البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('البريد الإلكتروني غير صحيح');
    }
    
    // البحث عن المستخدم - استخدام prepared statement
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // التحقق من كلمة المرور
        if (password_verify($password, $user['password'])) {
            // بدء الجلسة
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
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
    
    $stmt->close();
}

$conn->close();
?>