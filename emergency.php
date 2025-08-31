<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Help - COVID-19 Testing</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --success-color: #198754;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: var(--dark-color);
            background-color: #f5f7fb;
        }

        /* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .navbar-nav .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1584118624012-df056829fbd0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80') center/cover no-repeat;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        /* Emergency Card Styles */
        .emergency-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.4s ease;
            margin-bottom: 30px;
        }

        .emergency-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-header-danger {
            background: var(--danger-color);
            color: white;
            padding: 15px 25px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .card-header-warning {
            background: var(--warning-color);
            color: #000;
            padding: 15px 25px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .card-body {
            padding: 30px;
        }

        .card-body ul {
            list-style: none;
            padding-left: 0;
        }

        .card-body ul li {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
        }

        .card-body ul li i {
            min-width: 20px;
            margin-top: 5px;
            margin-right: 10px;
        }

        .check-icon {
            color: var(--success-color);
        }

        .warning-icon {
            color: var(--warning-color);
        }

        .danger-icon {
            color: var(--danger-color);
        }

        /* Action Buttons */
        .action-buttons {
            text-align: center;
            margin: 40px 0;
        }

        .btn-emergency {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            color: white;
            padding: 14px 35px;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            box-shadow: 0 6px 20px rgba(211, 47, 47, 0.4);
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px;
            text-decoration: none;
        }

        .btn-emergency:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.5);
            color: white;
        }

        .btn-secondary-action {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn-secondary-action:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }

        /* Location Section */
        .location-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }

        .map-container {
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-locate {
            background: var(--success-color);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-locate:hover {
            background: #146c43;
            transform: translateY(-2px);
        }

        /* Live Chat Widget */
        .chat-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .chat-btn {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chat-btn:hover {
            transform: scale(1.1);
            background: #0b5ed7;
        }

        .chat-panel {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: none;
        }

        .chat-header {
            background: var(--primary-color);
            color: white;
            padding: 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header .close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .chat-body {
            padding: 15px;
            height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 15px;
            max-width: 80%;
        }

        .message.from-user {
            margin-left: auto;
            background: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 15px 15px 0 15px;
        }

        .message.from-agent {
            margin-right: auto;
            background: #e9ecef;
            color: var(--dark-color);
            padding: 10px 15px;
            border-radius: 15px 15px 15px 0;
        }

        .chat-footer {
            padding: 10px;
            background: white;
            border-top: 1px solid #dee2e6;
            display: flex;
        }

        .chat-footer input {
            flex: 1;
            border: 1px solid #ced4da;
            border-radius: 20px;
            padding: 10px 15px;
            outline: none;
        }

        .chat-footer button {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 5px 15px;
            margin-left: 10px;
            cursor: pointer;
        }

        .blink {
            animation: blink 1.5s linear infinite;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.3; }
            100% { opacity: 1; }
        }

        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            padding: 70px 0 0;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
        }

        .footer-logo i {
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .footer-content p {
            color: #95a5a6;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .footer-heading {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--primary-color);
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links ul li {
            margin-bottom: 12px;
        }

        .footer-links ul li a {
            color: #95a5a6;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links ul li a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }

        .copyright {
            background: #1a252f;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
            color: #95a5a6;
            font-size: 0.9rem;
        }

        .copyright a {
            color: var(--primary-color);
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .chat-panel {
                width: 300px;
                bottom: 80px;
                right: -10px;
            }
            .hero-content h1 {
                font-size: 2.2rem;
            }
            .btn-emergency {
                font-size: 1rem;
                padding: 12px 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-virus"></i>
                    COVID-19 Testing
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link active emergency-btn" href="emergency.php">Emergency</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-exclamation-triangle blink"></i> Medical Emergency?</h1>
                <p>If you or someone near you is experiencing life-threatening symptoms, get help immediately.</p>
            </div>
        </div>
    </section>

    <!-- Emergency Info Section -->
    <section class="container mt-5">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="text-danger"><i class="fas fa-heartbeat"></i> Recognize the Emergency</h2>
                <p class="text-muted">Know the warning signs. Time is critical.</p>
            </div>
        </div>

        <!-- Life-Threatening Symptoms -->
        <div class="emergency-card">
            <div class="card-header-danger">
                <i class="fas fa-skull-crossbones"></i> Seek Emergency Care Immediately
            </div>
            <div class="card-body">
                <p class="lead">Call emergency services or go to the nearest hospital if you experience any of the following:</p>
                <ul>
                    <li><i class="fas fa-circle-xmark danger-icon"></i> <strong>Trouble breathing or shortness of breath</strong> – especially sudden or worsening</li>
                    <li><i class="fas fa-circle-xmark danger-icon"></i> <strong>Chest pain or pressure</strong> that doesn't go away</li>
                    <li><i class="fas fa-circle-xmark danger-icon"></i> <strong>Confusion or inability to wake up</strong></li>
                    <li><i class="fas fa-circle-xmark danger-icon"></i> <strong>Bluish lips or face</strong> – a sign of low oxygen</li>
                    <li><i class="fas fa-circle-xmark danger-icon"></i> <strong>Severe dehydration</strong> (dizziness, dry mouth, no urine)</li>
                </ul>
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Do not wait.</strong> These symptoms require immediate medical attention.
                </div>
            </div>
        </div>

        <!-- What To Do -->
        <div class="emergency-card">
            <div class="card-header-warning">
                <i class="fas fa-info-circle"></i> What You Should Do
            </div>
            <div class="card-body">
                <ul>
                    <li><i class="fas fa-phone-alt warning-icon"></i> <strong>Call Emergency Services:</strong> Dial <strong>1166</strong> (or local emergency number) immediately.</li>
                    <li><i class="fas fa-user-shield warning-icon"></i> <strong>Wear a mask</strong> if you're symptomatic to protect others during transport.</li>
                    <li><i class="fas fa-map-marker-alt warning-icon"></i> <strong>Inform them</strong> about your symptoms and possible exposure to COVID-19.</li>
                    <li><i class="fas fa-notes-medical warning-icon"></i> <strong>Bring your ID, insurance, and any medications</strong> you're taking.</li>
                    <li><i class="fas fa-user-friends warning-icon"></i> <strong>Do not drive yourself</strong> unless absolutely necessary.</li>
                </ul>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="tel:1166" class="btn-emergency">
                <i class="fas fa-phone-alt"></i> Call Emergency: 1166
            </a>
            <br class="d-md-none">
            <a href="#map-section" class="btn-secondary-action">
                <i class="fas fa-map-marker-alt"></i> View Nearest Hospital
            </a>
            <a href="symptoms.php" class="btn-secondary-action">
                <i class="fas fa-flask"></i> Check Symptoms Guide
            </a>
        </div>
    </section>

    <!-- Google Maps Section -->
    <section class="location-section" id="map-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-4">
                    <h3><i class="fas fa-map-pin"></i> Nearest Emergency Care Center</h3>
                    <p>We’ve located the nearest hospital for emergency treatment and testing.</p>
                </div>
            </div>
            <div class="map-container">
                <!-- Google Maps Embed (Example: City General Hospital, Imaginary Address) -->
                <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3618.544328270597!2d67.06884797501338!3d24.85977627792957!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33e0666666667%3A0x6666666666666666!2sAga%20Khan%20University%20Hospital!5e0!3m2!1sen!2s!4v1718725000000!5m2!1sen!2s"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="text-center mt-3">
                <p><strong>📍 Aga Khan University Hospital</strong><br>
                Stadium Rd, Karachi, Pakistan<br>
                <strong>Emergency Dept:</strong> Open 24/7 | <a href="tel:1166">Call 1166</a></p>
            </div>
        </div>
    </section>

    <!-- Live Chat Widget -->
    <div class="chat-widget">
        <div class="chat-btn" id="chatToggle">
            <i class="fas fa-comments"></i>
        </div>
        <div class="chat-panel" id="chatPanel">
            <div class="chat-header">
                <span>Live Support</span>
                <button class="close" id="chatClose">&times;</button>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="message from-agent">
                    Hello! This is COVID-19 Support. How can we help you?
                </div>
                <div class="message from-agent">
                    We're available 24/7 for medical guidance.
                </div>
            </div>
            <div class="chat-footer">
                <input type="text" id="chatInput" placeholder="Type your message..." />
                <button id="chatSend"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-content">
                        <div class="footer-logo">
                            <i class="fas fa-virus"></i>
                            COVID-19 Testing
                        </div>
                        <p>Your trusted partner in health. We connect you to care when every second counts.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul>
                            <li><a href="faq.php">FAQ</a></li>
                            <li><a href="guidelines.php">Testing Guidelines</a></li>
                            <li><a href="symptoms.php">Symptoms Checker</a></li>
                            <li><a href="prevention.php">Prevention Tips</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h4 class="footer-heading">Support</h4>
                        <ul>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="#">Live Chat (24/7)</a></li>
                            <li><a href="emergency.php">Emergency Guide</a></li>
                            <li><a href="#">Telehealth Services</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-contact">
                        <h4 class="footer-heading">Emergency Contact</h4>
                        <ul>
                            <li><i class="fas fa-phone-alt"></i> <strong>Emergency Hotline:</strong> 1166</li>
                            <li><i class="fas fa-envelope"></i> emergency@covidcare.com</li>
                            <li><i class="fas fa-clock"></i> Available 24/7</li>
                            <li><i class="fas fa-ambulance"></i> Ambulance Dispatch Available</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> COVID-19 Testing Portal. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Live Chat Toggle
        const chatToggle = document.getElementById('chatToggle');
        const chatClose = document.getElementById('chatClose');
        const chatPanel = document.getElementById('chatPanel');
        const chatInput = document.getElementById('chatInput');
        const chatSend = document.getElementById('chatSend');
        const chatBody = document.getElementById('chatBody');

        chatToggle.addEventListener('click', () => {
            chatPanel.style.display = chatPanel.style.display === 'block' ? 'none' : 'block';
        });

        chatClose.addEventListener('click', () => {
            chatPanel.style.display = 'none';
        });

        chatSend.addEventListener('click', () => {
            const msg = chatInput.value.trim();
            if (msg) {
                const msgEl = document.createElement('div');
                msgEl.className = 'message from-user';
                msgEl.textContent = msg;
                chatBody.appendChild(msgEl);
                chatInput.value = '';

                // Auto reply
                setTimeout(() => {
                    const reply = document.createElement('div');
                    reply.className = 'message from-agent';
                    reply.textContent = 'Thank you. A support agent will respond shortly. For emergencies, please call 1166.';
                    chatBody.appendChild(reply);
                    chatBody.scrollTop = chatBody.scrollHeight;
                }, 1000);
            }
        });

        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                chatSend.click();
            }
        });

        // Blink animation
        document.querySelectorAll('.blink').forEach(el => {
            el.style.animation = 'blink 1.5s linear infinite';
        });
    </script>
</body>
</html>