<?php
// Initialize database on page load
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CeylonX - Next Gen Digital Solutions</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>CeylonX</h2>
                <span>Next Gen Digital Solutions</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#submit">Submit</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">🚀 HERO SECTION</div>
                <h1>CeylonX<br><span class="gradient-text">Next Gen Digital Solutions</span></h1>
                <p>Modern academic assistance and digital solutions for computer science and software engineering students.</p>
                <div class="hero-buttons">
                    <a href="#services" class="btn btn-primary">Get Academic Assistance</a>
                    <a href="#submit" class="btn btn-secondary">Submit Your Assignment</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">✨ ABOUT CEYLONX</div>
                <h2>Next Gen Digital Solutions</h2>
            </div>
            <div class="about-content">
                <p>CeylonX – Next Gen Digital Solutions is a modern digital platform providing academic assistance, reference solutions, and technical guidance for computer science and software engineering students.</p>
                <p>We focus on delivering clean, well-structured, and industry-relevant solutions that help students understand concepts and improve practical skills.</p>
                <p>Our mission is to bridge the gap between academic learning and real-world development.</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">🛠️ OUR SERVICES</div>
                <h2>What We Provide</h2>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Academic Assistance</h3>
                    <p>Software Engineering & Computer Science Modules</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-code"></i>
                    <h3>Reference Solutions</h3>
                    <p>Programming Assignments</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-globe"></i>
                    <h3>Web Development</h3>
                    <p>HTML, CSS, JavaScript, PHP, MySQL</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Mobile Development</h3>
                    <p>Mobile Application Development Guidance</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-database"></i>
                    <h3>Database Design</h3>
                    <p>Database Design & SQL Support</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-bug"></i>
                    <h3>Code Review</h3>
                    <p>Code Review, Debugging & Optimization</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-project-diagram"></i>
                    <h3>Final Year Projects</h3>
                    <p>Final Year Project Guidance</p>
                </div>
            </div>
            <div class="services-note">
                <p>✔️ Services are provided strictly for educational and reference purposes only.</p>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">🔄 HOW IT WORKS</div>
            </div>
            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <p>Submit your assignment or project requirements</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <p>Our team analyzes your request</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <p>Receive academic guidance or reference solutions</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <p>Learn, improve, and submit with confidence</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Submit Assignment Section -->
    <section id="submit" class="submit-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">📝 SUBMIT ASSIGNMENT</div>
                <h2>Submit Your Request</h2>
                <p>Provide your assignment or project details clearly. The CeylonX team will review your request and contact you shortly.</p>
            </div>
            <div class="submit-form-container">
                <form id="assignmentForm" class="submit-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject/Module</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="date" id="deadline" name="deadline" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Assignment Details</label>
                        <textarea id="description" name="description" rows="6" required placeholder="Provide detailed information about your assignment or project requirements..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Disclaimer Section -->
    <section class="disclaimer">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">⚖️ DISCLAIMER (IMPORTANT)</div>
                <h2>Disclaimer</h2>
            </div>
            <div class="disclaimer-content">
                <p>CeylonX provides academic assistance and reference solutions strictly for educational purposes only.</p>
                <p>We do not promote plagiarism or submission of provided materials as original work. Students are responsible for following their institution's academic integrity policies.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">📞 CONTACT US</div>
                <h2>Contact CeylonX</h2>
            </div>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>mohamedzimam69@gmail.com</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fab fa-whatsapp"></i>
                    <div>
                        <h3>WhatsApp</h3>
                        <p>0776465222</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Business Hours</h3>
                        <p>9:00 AM – 9:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>CeylonX</h3>
                    <span>Next Gen Digital Solutions</span>
                </div>
                <div class="footer-taglines">
                    <p>Next Gen Digital Solutions for Future Developers</p>
                    <p>Code. Learn. Build. Succeed.</p>
                    <p>Empowering the Next Generation of Tech Professionals</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 CeylonX – Next Gen Digital Solutions. All rights reserved. Academic assistance and digital solutions for educational purposes only.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>