<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HLR INK STUDIO - Tattoo & Body Art</title>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Internal CSS Styles -->
    <style>
        /* Enable smooth scrolling across the page */
        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #050505;
            color: #e4e4e7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Modern Sticky Header */
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 60px;
            background-color: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo a {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 2px;
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-links a {
            color: #a1a1aa;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #ffffff;
        }

        /* Hero Section Layout matching reference style */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            background: radial-gradient(circle at 30% 50%, #16161a 0%, #050505 70%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .hero-container {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .hero-content h1 {
            font-size: 3.4rem;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 20px;
            color: #ffffff;
            line-height: 1.15;
        }

        .hero-content h1 span {
            color: #888891;
        }

        .hero-content p {
            font-size: 1.05rem;
            color: #888891;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Login / Action Card Box */
        .action-card {
            background-color: #111115;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .action-card h3 {
            font-size: 1.3rem;
            color: #ffffff;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: #a1a1aa;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: #18181d;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #ffffff;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background-color: #ffffff;
            color: #000000;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            margin-top: 5px;
        }

        .btn-submit:hover {
            background-color: #d4d4d8;
            transform: translateY(-1px);
        }

        .action-card-footer {
            text-align: center;
            margin-top: 18px;
            font-size: 0.85rem;
            color: #888891;
        }

        .action-card-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }

        .action-card-footer a:hover {
            text-decoration: underline;
        }

        /* Social Icons inside card */
        .card-socials {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .card-socials a {
            color: #a1a1aa;
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .card-socials a:hover {
            color: #ffffff;
        }

        /* General Page Sections */
        .page-section {
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 80px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        /* Headings & Cards */
        .page-header-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .page-subtitle {
            font-size: 1rem;
            color: #888891;
            margin-bottom: 40px;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            text-align: left;
            margin-top: 10px;
        }

        .card {
            background-color: #111115;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 30px;
            border-radius: 12px;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .card h3 {
            font-size: 1.2rem;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .card p {
            font-size: 0.9rem;
            color: #888891;
            line-height: 1.5;
        }

        /* Gallery Section Styles */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .gallery-item {
            position: relative;
            background-color: #111115;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            overflow: hidden;
            height: 320px;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
        }

        .gallery-overlay h3 {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Hover Effects */
        .gallery-item:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        /* Contact Section */
        .contact-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .contact-info-box {
            background-color: #111115;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 35px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .contact-info-box h3 {
            margin-bottom: 20px;
            font-size: 1.3rem;
            color: #ffffff;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 18px;
            color: #a1a1aa;
            text-align: left;
            font-size: 0.95rem;
        }

        .info-item i {
            color: #ffffff;
            font-size: 1.1rem;
            min-width: 20px;
            text-align: center;
        }

        /* Buttons */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #000000;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-primary:hover {
            background: #d4d4d8;
            transform: translateY(-2px);
        }

        /* Footer */
        .site-footer {
            text-align: center;
            padding: 25px;
            border-top: 1px style solid rgba(255, 255, 255, 0.05);
            color: #71717a;
            font-size: 0.85rem;
            background-color: #050505;
        }

        @media (max-width: 900px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero-buttons {
                justify-content: center;
            }
            .site-header {
                padding: 15px 20px;
            }
            .hero-content h1 {
                font-size: 2.6rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="site-header">
        <div class="logo">
            <a href="#home">
                <!-- <img src="images/logo.png" alt="HLR Logo" class="logo-img"> -->
                HLR INK STUDIO
            </a>
        </div>
        <nav class="nav-links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#services">Services</a>    
            <a href="#contact">Contact</a>
            <a href="../TA_system/auth/register.php">Register</a>
  
        </nav>
    </header>

    <!-- 1. HOME / HERO SECTION (Structured like the reference card layout) -->
    <section id="home" class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Custom Tattoo & <span>Body Art Studio</span></h1>
                <p>Discover bespoke ink craftsmanship, choose your preferred style, and secure your session with our premier artists dedicated to turning your vision into permanent art.</p>
                <div class="hero-buttons">
                    <a href="#gallery" class="btn-primary">Explore Gallery</a>
                </div>
            </div>
            
            <!-- Quick Action / Login / Register Card Box from Reference -->
            <div class="action-card">
                <h3>Client Portal</h3>
                <form action="../TA_system/auth/login.php" method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email5" name="email" class="form-control" placeholder="Enter Email Here" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Password Here" required>
                    </div>
                    <button type="submit" class="btn-submit">Login</button>
                </form>
                <div class="action-card-footer">
                    Don't have an account? <a href="../TA_system/auth/register.php">Sign up here</a>
                </div>
                <!-- Social Links Bar -->
                <div class="card-socials">
                    <a href="https://facebook.com" target="_blank" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://instagram.com" target="_blank" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://tiktok.com" target="_blank" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://youtube.com" target="_blank" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. ABOUT SECTION -->
    <section id="about" class="page-section">
        <div class="container">
            <h2 class="page-header-title">About HLR INK Studio</h2>
            <p class="page-subtitle">Pioneering modern body modification with professional hygiene standards and creative excellence.</p>
            <div class="grid-cards">
                <div class="card">
                    <h3><i class="fa-solid fa-shield-halved" style="color:#ffffff; margin-bottom:10px; display:block;"></i> Safety First</h3>
                    <p>We maintain medical-grade sterilization practices, utilizing single-use needles and high-grade safety protocols for every guest.</p>
                </div>
                <div class="card">
                    <h3><i class="fa-solid fa-palette" style="color:#ffffff; margin-bottom:10px; display:block;"></i> Custom Artistry</h3>
                    <p>Our resident artists specialize in diverse styles ranging from intricate fine-line work and realism to bold neo-traditional patterns.</p>
                </div>
                <div class="card">
                    <h3><i class="fa-solid fa-heart" style="color:#ffffff; margin-bottom:10px; display:block;"></i> Client Comfort</h3>
                    <p>A relaxing, premium studio environment designed to give you a comfortable and memorable tattooing experience from start to finish.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SERVICES SECTION -->
    <section id="services" class="page-section">
        <div class="container">
            <h2 class="page-header-title">Our Services</h2>
            <p class="page-subtitle">Explore our specialized tattoo and body art sessions tailored to your artistic vision.</p>
            <div class="grid-cards">
                <div class="card">
                    <h3>Custom Tattooing</h3>
                    <p>Collaborate directly with our resident artists to create unique freehand or custom reference pieces tailored precisely to your story.</p>
                </div>
                <div class="card">
                    <h3>Cover-Up Specialists</h3>
                    <p>Transform old or unwanted ink into stunning new art seamlessly integrated with sophisticated shading and clever composition.</p>
                </div>
                <div class="card">
                    <h3>Touch-Ups & Care</h3>
                    <p>Ensure your permanent ink stays crisp and vibrant with professional touch-up sessions and expert aftercare guidance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION -->
    <section id="gallery" class="page-section">
        <div class="container">
            <h2 class="page-header-title">Featured Work</h2>
            <p class="page-subtitle">A glimpse into our recent ink craftsmanship and custom sessions.</p>
            
            <div class="gallery-grid">
                <!-- Gallery Item 1 -->
                <div class="gallery-item">
                    <img src="../TA_system/image/biceps.jpeg" alt="Tattoo Art 1">
                    <div class="gallery-overlay">
                        <h3>Illustrative Style</h3>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div class="gallery-item">
                    <img src="../TA_system/image/fullback.jpg" alt="Tattoo Art 2">
                    <div class="gallery-overlay">
                        <h3>Neo-Traditional</h3>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div class="gallery-item">
                    <img src="../TA_system/image/triceps.jpeg" alt="Tattoo Art 3">
                    <div class="gallery-overlay">
                        <h3>Fine Line Blackwork</h3>
                    </div>
                </div>

                 <!-- Gallery Item 4 -->
                <div class="gallery-item">
                    <img src="../TA_system/image/collar.jpeg" alt="Tattoo Art 3">
                    <div class="gallery-overlay">
                        <h3>Fine-line Botanical Tattoo</h3>
                    </div>
                </div>

                 <!-- Gallery Item 5 -->
                <div class="gallery-item">
                    <img src="../TA_system/image/wrist.jpeg" alt="Tattoo Art 3">
                    <div class="gallery-overlay">
                        <h3>Fine Line Blackwork</h3>
                    </div>
                </div>

                 <!-- Gallery Item 6 -->
                <div class="gallery-item">
                    <img src="../TA_system/image/calf.jpeg" alt="Tattoo Art 3">
                    <div class="gallery-overlay">
                        <h3>Realistic   </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. CONTACT SECTION -->
    <section id="contact" class="page-section">
        <div class="container">
            <h2 class="page-header-title">Get In Touch</h2>
            <p class="page-subtitle">Reach out to us or visit our main studio for consultations and bookings.</p>
            <div class="contact-grid">
                <div class="contact-info-box">
                    <h3>Studio Details</h3>
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Main Studio, Eastern Barangay Hilongos, Leyte</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>support@hlrinkstudio.com</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+63 900 000 0000</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Monday – Sunday: 10:00 AM – 8:00 PM</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; 2026 HLR INK STUDIO. All rights reserved.</p>
    </footer>

</body>
</html>