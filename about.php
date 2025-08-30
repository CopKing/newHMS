<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - COVID-19 Testing</title>
    
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
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1584362917165-526a968579e8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80') center/cover no-repeat;
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

        /* About Section */
        .about-section {
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

        /* Mission Section */
        .mission-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }

        .mission-box {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: all 0.3s ease;
        }

        .mission-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .mission-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .mission-box h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark-color);
            text-align: center;
        }

        .mission-box p {
            color: var(--secondary-color);
            line-height: 1.6;
            text-align: center;
        }

        /* Team Section */
        .team-section {
            padding: 80px 0;
            background-color: white;
        }

        .team-member {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .team-img {
            height: 250px;
            overflow: hidden;
        }

        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-member:hover .team-img img {
            transform: scale(1.1);
        }

        .team-info {
            padding: 25px;
            text-align: center;
        }

        .team-info h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .team-info p {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 500;
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .team-social a {
            width: 36px;
            height: 36px;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .team-social a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }

        .stat-box {
            text-align: center;
            padding: 20px;
        }

        .stat-box i {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .stat-box h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-box p {
            font-size: 1.1rem;
            opacity: 0.9;
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
            
            .stat-box h3 {
                font-size: 2rem;
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
                        <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
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
                <h1>About COVID-19</h1>
                <p>Our mission is to provide accessible, reliable, and secure COVID-19 testing services to communities nationwide</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Story</h2>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-img">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" class="img-fluid" alt="About Us">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content">
                        <h3>Excellence in Healthcare Testing</h3>
                        <p>Founded in 2020 during the height of the global pandemic, our COVID-19 Testing Portal was established by a team of dedicated healthcare professionals and technology experts who recognized the critical need for efficient, accessible testing services.</p>
                        <p>Our founders brought together decades of experience in healthcare management, laboratory sciences, and digital innovation to create a platform that would revolutionize how communities access COVID-19 testing. What began as a response to an urgent public health crisis has evolved into a comprehensive testing ecosystem serving millions of Americans.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Pioneered digital-first approach to pandemic response</li>
                            <li><i class="fas fa-check-circle"></i> Built partnerships with 500+ healthcare facilities nationwide</li>
                            <li><i class="fas fa-check-circle"></i> Developed proprietary technology for rapid result delivery</li>
                            <li><i class="fas fa-check-circle"></i> Maintained 99.9% accuracy rate across all testing methods</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Mission & Vision</h2>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="mission-box">
                        <div class="mission-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4>Our Mission</h4>
                        <p>To provide accessible, reliable, and efficient COVID-19 testing services to all communities, helping to control the spread of the virus and protect public health.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="mission-box">
                        <div class="mission-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h4>Our Vision</h4>
                        <p>To become the leading platform for COVID-19 testing management, setting the standard for digital healthcare solutions and pandemic response.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="mission-box">
                        <div class="mission-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Our Values</h4>
                        <p>Integrity, innovation, and compassion guide everything we do. We're committed to delivering the highest quality testing services with care and respect for every individual.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container"> 
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <i class="fas fa-hospital"></i>
                        <h3>500+</h3>
                        <p>Testing Centers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <i class="fas fa-users"></i>
                        <h3>2M+</h3>
                        <p>Tests Conducted</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>50</h3>
                        <p>States Covered</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <i class="fas fa-award"></i>
                        <h3>99.9%</h3>
                        <p>Accuracy Rate</p>
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
                <p>&copy; <?php echo date('Y'); ?> HMS. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
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
    </script>
</body>
</html>