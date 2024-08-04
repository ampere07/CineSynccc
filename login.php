<?php
session_start();
require 'vendor/autoload.php';

$successRegisterd = "";
$FailedRegistered = "";
$invalidVerification = "";
$failedLogin = "";
$emailCheck = "";

$currentForm = 'login'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/";
    $client = new MongoDB\Client($uri);
    $collection = $client->cinesync->accounts;

    if (isset($_POST['registerButton'])) {
        $input_username = $_POST['reg_username'];
        $input_password = $_POST['reg_password'];
        $input_email = $_POST['reg_email'];
        $input_verification_code = $_POST['reg_code'];

        $check_email_result = $collection->findOne(['email' => $input_email]);

        if ($check_email_result) {
            $emailCheck = "Email address already in use.";
            $currentForm = 'register';
        } else {
            if (!isset($_SESSION['verification_code']) || $input_verification_code != $_SESSION['verification_code']) {
                $invalidVerification = "Invalid verification code.";
                $currentForm = 'register';
            } else {
                $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);
                $insert_result = $collection->insertOne([
                    'username' => $input_username,
                    'password' => $hashed_password,
                    'email' => $input_email
                ]);

                if ($insert_result->getInsertedCount() > 0) {
                    $successRegisterd = "Registration successful!";
                    unset($_SESSION['verification_code']);
                } else {
                    $FailedRegistered = "Registration failed. Please try again.";
                }
            }
        }
    } elseif (isset($_POST['loginButton'])) {
        $input_username = $_POST['username'];
        $input_password = $_POST['password'];
        if ($input_username === 'admin' && $input_password === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin_dashboard.php');
            exit;
        }
        $user = $collection->findOne(['username' => $input_username]);

        if ($user) {
            if (password_verify($input_password, $user['password'])) {
                $_SESSION['user_id'] = $user['username'];
                echo '<script>window.location.href = "pickmovie.php";</script>';
                exit;
            } else {
                $failedLogin = "Incorrect password.";
            }
        } else {
            $failedLogin = "Username not found.";
        }
    } elseif (isset($_POST['email'])) {
        $email = $_POST['email'];
        $verification_code = generateVerificationCode();

        if (sendVerificationCode($email, $verification_code)) {
            $_SESSION['verification_code'] = $verification_code;
            echo "Verification code sent.";
        } else {
            echo "Failed to send verification email.";
        }
        exit;
    }
}

function generateVerificationCode() {
    return rand(100000, 999999);
}

function sendVerificationCode($email, $verificationCode) {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cinesync2.5@gmail.com';
        $mail->Password = 'fuylqrthtewdqohp';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('cinesync2.5@gmail.com', 'CineSync');
        $mail->addAddress($email);
        $mail->Subject = 'Verification Code';
        $mail->Body = 'Your verification code is: ' . $verificationCode;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Failed to send email. Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <title>Cine Sync</title>
    <style>
       @font-face {
            font-family: 'Anonymous Pro';
            src: url('fonts/Anonymous_Pro_l.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Roboto';
            src: url('fonts/Roboto-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'Roboto';
            margin: 0;
            padding: 0;
            background-color: #352F44; 
        }
        

.login_page {
    position: absolute;
    top: 325px;
    left: 1000px;
    transform: translate(-50%, -50%);
    background: #b9b8af;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 300px;
    text-align: center;
}

.mainlogin {
    margin-bottom: 20px;
    font-size: 24px;
}

p {
    margin: 10px 0;
    font-size: 14px;
    text-align: left;
}

input[type="text"], input[type="password"] {
    width: calc(100% - 20px);
    padding: 10px;
    background: #b9b8af;
    margin-bottom: 10px;
    border: 1px solid black;
    border-radius: 5px;
    font-size: 14px;
}

button {
    background-color: black;
    color: white;
    border: none;
    padding: 10px 0;
    border-radius: 5px;
    width: 100%;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background-color: white;
    color: black;
}

.bottom_login, .bottom_register {
    margin-top: 10px;
    text-align: center; /* Center the text and link */
}

.bottom_login p, .bottom_register p {
    margin: 0;
    text-align: center; /* Ensure the text is centered */
}

.bottom_login a, .bottom_register a {
    color: blue;
    text-decoration: none;
}

.bottom_login a:hover, .bottom_register a:hover {
    text-decoration: underline;
}

.success-message {
    color: green;
    font-size: 14px;
    margin-bottom: 10px;
    display: block;
}

.failed-message {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
    display: block;
}

.dotted-line1 {
    width: 100%;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="5.5" height="10"><rect width="5" height="5" fill="black"/></svg>');
    background-size: 60px 35px;
    background-repeat: repeat-x;
    top: 0px;
    position: absolute;
    left: 0px;
}
.dotted-line2 {
    width: 100%;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="8" height="10"><rect width="5" height="5" fill="black"/></svg>');
    background-size: 12px 12px;
    background-repeat: repeat-x;
    top: 20px;
    position: absolute;
    left: 0px;
}
.dotted-line3 {
    width: 100%;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="5.5" height="10"><rect width="5" height="5" fill="black"/></svg>');
    background-size: 60px 35px;
    background-repeat: repeat-x;
    top: 621px;
    position: absolute;
    left: 0px;
}
.dotted-line4 {
    width: 100%;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="8" height="10"><rect width="5" height="5" fill="black"/></svg>');
    background-size: 12px 12px;
    background-repeat: repeat-x;
    top: 613px;
    position: absolute;
    left: 0px;
}
.mainbg-container {
    position: absolute;
    top: 20px;
    left: 0;
    width: 100%;
    height: 600px;
    z-index: -1;
}

.mainbg {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.2; /* Adjust opacity here */
}
.logo {
    position: absolute;
    top: 100px;
    width: 500px;
    left: 150px;
}
input.invalid-input {
    border: 1px solid red;
}
.invalid-input::placeholder {
    color: red;
}



.password-toggle {
    position: absolute;
    right: 30px;
    top: 49%;
    transform: translateY(-50%);
    cursor: pointer;
}
.password-toggle1 {
    position: absolute;
    right: 30px;
    top: 63%;
    transform: translateY(-50%);
    cursor: pointer;
}
.password-toggle2 {
    position: absolute;
    right: 30px;
    top: 64%;
    transform: translateY(-50%);
    cursor: pointer;
}
.send-button{
    position: absolute;
    right: 30px;
    top: 77%;
    transform: translateY(-50%);
    cursor: pointer;
}
    </style>
</head>
<body>
    <div class="mainbg-container">
        <img class="mainbg" src="images/background_login.png" alt="Logo" draggable="false">
    </div>
    
    <div class="design">
        <img class="logo" src="images/logo.png" alt="Logo" draggable="false">
        <div class="dotted-line1"></div>
        <div class="dotted-line2"></div>
        <div class="dotted-line3"></div>
        <div class="dotted-line4"></div>
    </div>
    
    <div id="loginContainer" class="login_page" style="display: <?php echo ($currentForm === 'login') ? 'block' : 'none'; ?>;">
        <form method="post" id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1 class="mainlogin">Log in</h1>
                <?php 
                    echo '<span class="success-message">' . $successRegisterd . '</span>';
                    echo '<span class="failed-message">' . $failedLogin . '</span>';
                ?>
            <p>Username</p>
                <input type="text" name="username" class="username" placeholder="Input Username here...">
            <p>Password</p>
                <div class="password-container">
                    <input type="password" name="password" class="password" placeholder="Input Password here...">
                    <span class="password-toggle2" onclick="togglePasswordVisibilityLogin()"><i class="fas fa-eye"></i></span>
                </div>
            
                <button type="submit" name="loginButton" id="loginButton">Log in</button>

            <div class="bottom_login">
                <p>Don't have an account? 
                    <a href="javascript:void(0);" onclick="toggleForm('register')">Register</a>
                </p>
            </div>
        </form>
    </div>
        
    <div id="registerContainer" class="login_page" style="display: <?php echo ($currentForm === 'register' || $invalidVerification || $FailedRegistered) ? 'block' : 'none'; ?>;">
        <form id="registerForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1 class="mainlogin">Register</h1>
                <?php 
                    echo '<span class="failed-message">' . $invalidVerification . '</span>';
                    echo '<span class="failed-message">' . $FailedRegistered . '</span>';
                    echo '<span class="failed-message">' . $emailCheck . '</span>';
                ?>
                <span id="passwordMatchError" class="error-message" style="display: none; color: red;">Passwords do not match</span>
            <p>Username</p>
                <input type="text" name="reg_username" class="reg_username" placeholder="Choose a username..." value="<?php if(isset($_POST['reg_username'])) { echo $_POST['reg_username']; } ?>">
            <p>Email Address</p>
                <input type="text" name="reg_email" class="reg_email" placeholder="ex:testemail@email.com" value="<?php if(isset($_POST['reg_email'])) { echo $_POST['reg_email']; } ?>">
            <p>Password</p>
                <div class="password-container">
                    <input type="password" name="reg_password" class="reg_password" placeholder="Choose a password..." value="<?php if(isset($_POST['reg_password'])) { echo $_POST['reg_password']; } ?>">
                    <span class="password-toggle" onclick="togglePasswordVisibility('reg_password', 'password-toggle')"><i class="fas fa-eye"></i></span>
                </div>
            <p>Confirm Password</p>
                <div class="password-container">
                    <input type="password" name="reg_confirm_password" class="reg_confirm_password" placeholder="Confirm Password..." value="<?php if(isset($_POST['reg_confirm_password'])) { echo $_POST['reg_confirm_password']; } ?>">
                    <span class="password-toggle1" onclick="togglePasswordVisibility('reg_confirm_password', 'password-toggle1')"><i class="fas fa-eye"></i></span>
                </div> 
            <p>Verification Code</p>
                <input type="text" name="reg_code" class="reg_code" value="<?php if(isset($_POST['reg_code'])) { echo $_POST['reg_code']; } ?>">
                <span class="send-button" onclick="sendVerificationCode()">Send</span>
            <div class="bottom_register">
                <button type="submit" name="registerButton">Register</butto>
            </div>
            
                <p>Already have an account? <a href="javascript:void(0);" onclick="toggleForm('login')">Log in here</a></p>
            

        </form>
    </div>
</body>
</html>
    <script>
        function toggleForm(formName) {
            if (formName === 'login') {
                document.getElementById('loginContainer').style.display = 'block';
                document.getElementById('registerContainer').style.display = 'none';
            } else if (formName === 'register') {
                document.getElementById('loginContainer').style.display = 'none';
                document.getElementById('registerContainer').style.display = 'block';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const registerForm = document.getElementById('registerForm');
            const passwordInput = registerForm.querySelector('.reg_password');
            const confirmPasswordInput = registerForm.querySelector('.reg_confirm_password');

            const passwordMatchError = document.getElementById('passwordMatchError');

            registerForm.addEventListener('submit', function(event) {
                const inputs = registerForm.querySelectorAll('input[type="text"], input[type="password"]');
                let isValid = true;
                

                // Check if any input field is empty
                inputs.forEach(input => {
                    if (input.value.trim() === '') {
                        input.classList.add('invalid-input');
                        input.placeholder = 'This field is required';
                        isValid = false;
                    } else {
                        input.classList.remove('invalid-input');
                    }
                });


                
                // Check if password and confirm password match
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.classList.add('invalid-input');
                    confirmPasswordInput.placeholder = 'Passwords do not match';
                    passwordMatchError.style.display = 'block';
                    isValid = false;
                } else {
                    passwordMatchError.style.display = 'none';
                }
                const emailInput = registerForm.querySelector('.reg_email');
                if (emailInput.value.trim() !== '' && !validateEmail(emailInput.value)) {
                    emailInput.classList.add('invalid-input');
                    emailInput.placeholder = 'Invalid email';
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault(); // Prevent form submission
                }
            });

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return re.test(email);
                }
            });

            function togglePasswordVisibility(inputName, toggleClass) {
                const passwordInput = document.querySelector(`input[name="${inputName}"]`);
                const passwordToggle = document.querySelector(`.${toggleClass}`);
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    passwordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = "password";
                    passwordToggle.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }
            function togglePasswordVisibilityLogin() {
                const passwordInput = document.querySelector('input[name="password"]');
                const passwordToggle = document.querySelector('.password-toggle2');
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    passwordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = "password";
                    passwordToggle.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }

            function sendVerificationCode() {
                const emailInput = document.querySelector('.reg_email');
                const email = emailInput.value.trim();

                if (!validateEmail(email)) {
                    alert('Please enter a valid email address.');
                    return;
                }

                // Disable the send button to prevent multiple clicks
                const sendButton = document.querySelector('.send-button');
                sendButton.disabled = true;

                // Start the countdown timer
                let secondsLeft = 60;
                const countdownInterval = setInterval(() => {
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        sendButton.innerHTML = 'Send';
                        sendButton.disabled = false;
                    } else {
                        sendButton.innerHTML = `Resend (${secondsLeft}s)`;
                        secondsLeft--;
                    }
                }, 1000);

                // Fetch code to send verification code via email
                const formData = new FormData();
                formData.append('email', email);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        alert('Verification code sent successfully!');
                    } else {
                        alert('Failed to send verification code. Please try again later.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again later.');
                });
            }

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
    
    </script>

