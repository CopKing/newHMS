<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COVID-19 Testing - Home</title>
    
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

        .emergency-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white !important;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .emergency-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1584118624012-df056829fbd0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80') center/cover no-repeat;
            color: white;
            padding: 120px 0;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .badge {
            display: inline-block;
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            color: #2d3436;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(253, 203, 110, 0.3);
        }

        /* Login Options */
        .login-options {
            padding: 80px 0;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            border-top: 5px solid var(--primary-color);
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .login-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .login-card p {
            color: var(--secondary-color);
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .login-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background-color: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .feature-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .feature-box h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .feature-box p {
            color: var(--secondary-color);
            line-height: 1.6;
        }

        /* About Section */
        .about-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }

        .about-img {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .about-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark-color);
        }

        .about-content p {
            color: var(--secondary-color);
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .about-content ul {
            list-style: none;
            padding: 0;
        }

        .about-content ul li {
            margin-bottom: 12px;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
        }

        .about-content ul li i {
            color: var(--success-color);
            margin-right: 10px;
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

        .footer-contact ul {
            list-style: none;
            padding: 0;
        }

        .footer-contact ul li {
            margin-bottom: 15px;
            color: #95a5a6;
            display: flex;
            align-items: flex-start;
        }

        .footer-contact ul li i {
            color: var(--primary-color);
            margin-right: 10px;
            margin-top: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
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

        /* Responsive Design */
        @media (max-width: 991px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .about-img {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 767px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .login-card {
                margin-bottom: 30px;
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
                        <li class="nav-item"><a class="nav-link emergency-btn" href="emergency.php">Emergency</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>HMS - COVID 19 TESTING HMS</h1>
                <p>Quick, Reliable, and Secure Testing Services</p>
                <p>Book your test, manage results, and stay informed</p>
                <span class="badge">✓ 24/7 Service Available</span>
            </div>
        </div>
    </section>

    <!-- Login Options -->
    <section class="login-options">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="login-card" onclick="window.location.href='login.php'">
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3>Login as User</h3>
                        <p>Book appointments, view test results, and manage your health records securely</p>
                        <a href="login.php" class="login-btn">User Portal →</a>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="login-card" onclick="window.location.href='hospital_login.php'">
                        <div class="card-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <h3>Login as Hospital</h3>
                        <p>Manage patient records, update test results, and coordinate testing operations</p>
                        <a href="hospital_login.php" class="login-btn">Hospital Portal →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Features</h2>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>Easy Booking</h4>
                        <p>Book your COVID-19 test appointment online in just a few simple steps.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <h4>Quick Results</h4>
                        <p>Get your test results quickly and securely through our online portal.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Secure Records</h4>
                        <p>All your health information is stored securely with advanced encryption.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-img">
                        <img src="https://images.unsplash.com/photo-1587854692152-cbe660dbde88?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" class="img-fluid" alt="COVID Testing">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content">
                        <h3>About COVID-19 Testing Portal</h3>
                        <p>We provide a comprehensive platform for COVID-19 testing management, connecting patients with healthcare facilities for efficient testing services.</p>
                        <p>Our mission is to make COVID-19 testing accessible, reliable, and secure for everyone.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Nationwide network of testing centers</li>
                            <li><i class="fas fa-check-circle"></i> Fast and accurate test results</li>
                            <li><i class="fas fa-check-circle"></i> Secure health data management</li>
                            <li><i class="fas fa-check-circle"></i> 24/7 customer support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <p>Leading COVID-19 testing management platform providing seamless connection between patients and healthcare facilities.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="http://localhost/newhms/login.php"><i class="fa-jelly-fill fa-regular fa-circle-user"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul>
                            <li><a href="faq.php">FAQ</a></li>
                            <li><a href="guidelines.php">Testing Guidelines</a></li>
                            <li><a href="symptoms.php">COVID-19 Symptoms</a></li>
                            <li><a href="prevention.php">Prevention Tips</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h4 class="footer-heading">Services</h4>
                        <ul>
                            <li><a href="rt-pcr.php">RT-PCR Testing</a></li>
                            <li><a href="rapid-test.php">Rapid Antigen Test</a></li>
                            <li><a href="antibody-test.php">Antibody Testing</a></li>
                            <li><a href="home-collection.php">Home Sample Collection</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-contact">
                        <h4 class="footer-heading">Contact Info</h4>
                        <ul>
                            <li><i class="fas fa-phone-alt"></i> Emergency: 1166</li>
                            <li><i class="fas fa-envelope"></i> support@covidcare.com</li>
                            <li><i class="fas fa-clock"></i> Available 24/7</li>
                            <li><i class="fas fa-map-marker-alt"></i> Nationwide Service</li>
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
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add loading animation when clicking login cards
        document.querySelectorAll('.login-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                this.innerHTML = 'Loading...';
                this.style.opacity = '0.7';
            });
        });
    </script>
</body>
</html>