<?php
$successMessage = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors['name'] = 'Please enter your name.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Please enter your phone number.';
    }

    if (empty($errors)) {
        // Placeholder for email handling if needed in the future.
        // mail($to, $subject, $body, $headers);
        $successMessage = 'Thank you, we will contact you shortly.';
    }
}
?>
<?php $pageTitle = 'Contact Us | MyBrand Healthcare Equipment'; include 'header.php'; ?>
<section class="section contact">
    <div class="section-header">
        <h1>Contact Us</h1>
        <p>Share your requirements and our team will respond with a tailored quotation.</p>
    </div>
    <div class="contact-wrapper">
        <div class="contact-details">
            <h2>Reach Our Team</h2>
            <p>Call us at <a href="tel:+910000000000">+91 00000 00000</a> or email <a href="mailto:sales@mybrandhealthcare.com">sales@mybrandhealthcare.com</a>.</p>
            <p>Head Office: 123 Healthcare Avenue, Industrial Estate, Mumbai, India.</p>
            <div class="contact-stats">
                <div class="stat">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Support Availability</span>
                </div>
                <div class="stat">
                    <span class="stat-number">48h</span>
                    <span class="stat-label">Quotation Turnaround</span>
                </div>
            </div>
        </div>
        <div class="contact-form-container">
            <?php if ($successMessage): ?>
                <div class="form-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['name'])): ?><span class="error"><?php echo $errors['name']; ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['email'])): ?><span class="error"><?php echo $errors['email']; ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($errors['phone'])): ?><span class="error"><?php echo $errors['phone']; ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="organization">Hospital / Organization</label>
                    <input type="text" id="organization" name="organization" value="<?php echo htmlspecialchars($organization ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="message">Message / Requirements</label>
                    <textarea id="message" name="message" rows="5"><?php echo htmlspecialchars($message ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <button class="btn btn-accent" type="submit">Submit</button>
            </form>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
