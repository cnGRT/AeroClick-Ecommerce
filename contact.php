<?php 
require_once __DIR__ . '/includes/init.php';

?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Contact Us</h1>
        <p class="subtitle">We're Here to Help Your Gaming Journey</p>
    </div>

    <div class="contact-content">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have questions about our products, need help choosing the right gaming mouse, or want to share your feedback? We'd love to hear from you. Our team of gaming enthusiasts is here to help you make the perfect choice.</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <h3>📧 Email Support</h3>
                        <p><strong>General Inquiries:</strong> info@aeroclick.com</p>
                        <p><strong>Technical Support:</strong> support@aeroclick.com</p>
                        <p><strong>Partnerships:</strong> partners@aeroclick.com</p>
                        <p><em>Response time: Within 24 hours</em></p>
                    </div>
                    
                    <div class="contact-method">
                        <h3>💬 Community Discord</h3>
                        <p>Join our growing community of gamers and tech enthusiasts:</p>
                        <p><strong>Discord Server:</strong> discord.gg/aeroclick</p>
                        <p>Get real-time advice, share setups, and connect with fellow gamers.</p>
                    </div>
                    
                    <div class="contact-method">
                        <h3>📱 Social Media</h3>
                        <p>Follow us for the latest updates, reviews, and gaming content:</p>
                        <p><strong>Twitter:</strong> @AeroClickGaming</p>
                        <p><strong>Instagram:</strong> @AeroClickOfficial</p>
                        <p><strong>YouTube:</strong> AeroClick Reviews</p>
                    </div>
                </div>
            </div>
            
            <!-- 在 contact-form-section 部分修改表单 -->
<div class="contact-form-section">
    <h2>Send Us a Message</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?= esc($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?= esc($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form class="contact-form" method="POST" action="contact_process.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880"> <!-- 5MB limit -->
        
        <div class="form-group">
            <label for="name">Your Name *</label>
            <input type="text" id="name" name="name" required 
                   value="<?= esc($_SESSION['form_data']['name'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required
                   value="<?= esc($_SESSION['form_data']['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="message">Your Message *</label>
            <textarea id="message" name="message" rows="6" required 
                      placeholder="Tell us how we can help you..."><?= esc($_SESSION['form_data']['message'] ?? '') ?></textarea>
        </div>
        
        <!-- CV/文件上传部分 -->
        <div class="form-group">
            <label for="cv_files">Upload CV/Supporting Documents</label>
            <p class="form-help">You can upload multiple files (PDF, DOC, DOCX, JPG, PNG, ZIP). Max 5MB per file. Maximum 5 files.</p>
            <input type="file" id="cv_files" name="cv_files[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
            <div class="file-preview" id="file-preview"></div>
        </div>
        
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
        </div>
        
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>How do I choose the right gaming mouse?</h3>
                    <p>Consider your grip style (palm, claw, fingertip), game genre (FPS, MOBA, MMO), DPI requirements, and connectivity preferences. Use our comparison tool to narrow down options based on your specific needs.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Do you offer international shipping?</h3>
                    <p>Currently, we ship within the United States, Canada, and select European countries. Shipping options and costs are calculated at checkout based on your location.</p>
                </div>
                
                <div class="faq-item">
                    <h3>What's your return policy?</h3>
                    <p>We offer a 30-day return policy for all products in original condition. If you're not satisfied with your purchase, contact our support team to initiate a return.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Are the products authentic?</h3>
                    <p>Yes! We work exclusively with authorized distributors and reputable brands to ensure all products are 100% authentic and come with manufacturer warranties.</p>
                </div>
            </div>
        </div>
        
        <div class="business-info">
            <h2>Business Information</h2>
            <div class="info-cards">
                <div class="info-card">
                    <h3>🕒 Support Hours</h3>
                    <p><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM EST</p>
                    <p><strong>Saturday:</strong> 10:00 AM - 4:00 PM EST</p>
                    <p><strong>Sunday:</strong> Closed</p>
                </div>
                
                <div class="info-card">
                    <h3>📦 Shipping</h3>
                    <p><strong>Standard:</strong> 3-5 business days</p>
                    <p><strong>Express:</strong> 1-2 business days</p>
                    <p><strong>Free shipping on orders over $99</strong></p>
                </div>
                
                <div class="info-card">
                    <h3>🔒 Security & Trust</h3>
                    <p>• SSL Encrypted Checkout</p>
                    <p>• Privacy-Focused</p>
                    <p>• Verified Reviews Only</p>
                    <p>• Secure Payment Processing</p>
                </div>
            </div>
        </div>
        
        <div class="demo-notice">
            <h3>🎮 Demo Project Notice</h3>
            <p><em>This is a student project for educational purposes. AeroClick is a demonstration e-commerce platform and no real transactions or product shipments occur. The contact information provided is for demonstration only.</em></p>
        </div>
    </div>
</div>

<style>
.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.subtitle {
    color: #00d9ff;
    font-size: 1.2rem;
    margin-top: 0.5rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.contact-info h2, .contact-form-section h2 {
    color: #00d9ff;
    margin-bottom: 1.5rem;
}

.contact-methods {
    display: grid;
    gap: 1.5rem;
}

.contact-method {
    background: #1a1a1a;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #333;
}

.contact-method h3 {
    color: #00d9ff;
    margin-bottom: 1rem;
}

.contact-form {
    background: #1a1a1a;
    padding: 2rem;
    border-radius: 10px;
    border: 1px solid #333;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #00d9ff;
    font-weight: bold;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 0.8rem;
    background: #2d2d2d;
    border: 1px solid #333;
    border-radius: 5px;
    color: #e0e0e0;
    font-size: 1rem;
}

.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: #00d9ff;
    box-shadow: 0 0 0 2px rgba(0, 217, 255, 0.2);
}

.faq-section {
    margin-bottom: 3rem;
}

.faq-section h2 {
    color: #00d9ff;
    text-align: center;
    margin-bottom: 2rem;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.faq-item {
    background: #1a1a1a;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #333;
}

.faq-item h3 {
    color: #00ff88;
    margin-bottom: 1rem;
}

.business-info h2 {
    color: #00d9ff;
    text-align: center;
    margin-bottom: 2rem;
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-card {
    background: #1a1a1a;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #333;
    text-align: center;
}

.info-card h3 {
    color: #00d9ff;
    margin-bottom: 1rem;
}

.demo-notice {
    background: #2d2d2d;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #ff5555;
    text-align: center;
    margin-top: 2rem;
}

.demo-notice h3 {
    color: #ff5555;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>