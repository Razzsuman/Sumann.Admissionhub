<?php
// contact.php
session_start();

// Database connection - pehle check karen config.php exist karta hai ya nahi
if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    // Agar config.php nahi hai to direct connection
    $conn = new mysqli("localhost", "root", "", "admissionhub");
}

// Form handling
$success = "";
$errors = [];
$name = $email = $phone = $message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // Agar errors nahi hain to database mein save karen
    if (empty($errors)) {
        // Prepared statement use karen for security
        $sql = "INSERT INTO contact (name, email, phone, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $phone, $message);
            
            if ($stmt->execute()) {
                $success = "Thank you for your message! We'll get back to you soon.";
                // Form clear karen
                $name = $email = $phone = $message = "";
            } else {
                $errors[] = "Sorry, there was an error sending your message. Please try again.";
            }
            
            $stmt->close();
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Bangalore Admission Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Contact page specific styles */
        .contact-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .contact-info {
            margin-top: 3rem;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .info-item {
            margin-bottom: 1rem;
        }
        
        .info-item h3 {
            color: #007bff;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation - aapke existing navigation ko reuse karen -->
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h2>Admission Hub</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="contact-container">
            <h1>Contact Us</h1>
            <p>Have questions about admissions? Get in touch with us!</p>
            
            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-form">
                <form method="POST" action="contact.php">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($name); ?>" 
                               required 
                               placeholder="Enter your full name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               required 
                               placeholder="Enter your email address">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($phone); ?>" 
                               placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Your Message *</label>
                        <textarea id="message" 
                                  name="message" 
                                  class="form-control" 
                                  rows="5" 
                                  required 
                                  placeholder="Tell us about your admission query..."><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info">
                <h2>Other Ways to Reach Us</h2>
                <div class="info-item">
                    <h3>📧 Email</h3>
                    <p>info@bangaloreadmissionhub.com</p>
                </div>
                <div class="info-item">
                    <h3>📞 Phone</h3>
                    <p>+91 9876543210</p>
                </div>
                <div class="info-item">
                    <h3>📍 Address</h3>
                    <p>Bangalore, Karnataka, India</p>
                </div>
                <div class="info-item">
                    <h3>🕒 Business Hours</h3>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                    <p>Saturday: 10:00 AM - 2:00 PM</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Bangalore Admission Hub. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Form validation client side
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name) {
                alert('Please enter your name');
                e.preventDefault();
                return;
            }
            
            if (!email) {
                alert('Please enter your email');
                e.preventDefault();
                return;
            }
            
            if (!message) {
                alert('Please enter your message');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>
