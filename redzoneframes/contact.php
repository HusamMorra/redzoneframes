<?php
session_start();
// basic contact page that shows contact info

$pageTitle = "Contact - Red Zone Frames";
$pageDescription = "Get in touch with Red Zone Frames - questions, custom orders, or feedback.";
require_once __DIR__ . '/includes/header.php';
?>
<section class="container section static-page">
    <h1>Contact Us</h1>
    <p>Questions about an order, a custom build, or anything else? We'd love to hear from you.</p>
    <div class="contact-info">
      <p><strong>Email:</strong> <a href="mailto:customersupport@redzoneframes.com">customersupport@redzoneframes.com</a></p>
        <p><strong>Phone:</strong> (519) 123-4567</p>
        <p><strong>Hours:</strong> Monday - Friday, 8am - 5pm EST</p>
        <p><strong>Location:</strong> Windsor, Ontario, Canada</p>
    </div>
    <p>For questions about an existing order, please include your order number so we can look into it faster.</p>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>