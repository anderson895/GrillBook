<?php
include "src/components/header.php";
?>

<style>
    :root {
        --primary-gold: #D4AF37;
        --dark-gold: #B8860B;
        --light-gold: #F5E8C8;
        --dark-bg: #0A0A0A;
        --card-bg: #1A1A1A;
        --text-light: #E5E5E5;
        --text-muted: #A3A3A3;
        --grill-dark: #2A2A2A;
        --grill-light: #3A3A3A;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes float {
        0% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(5deg);
        }
        100% {
            transform: translateY(0px) rotate(0deg);
        }
    }

    @keyframes grillGlow {
        0%, 100% {
            opacity: 0.6;
        }
        50% {
            opacity: 0.8;
        }
    }

    @keyframes smoke {
        0% {
            transform: translateY(0) scale(1);
            opacity: 0;
        }
        50% {
            opacity: 0.3;
        }
        100% {
            transform: translateY(-100px) scale(1.5);
            opacity: 0;
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }

    .animate-fadeIn {
        animation: fadeIn 1s ease-out forwards;
        opacity: 0;
    }

    .animate-slideLeft {
        animation: slideInLeft 0.8s ease-out forwards;
        opacity: 0;
    }

    .animate-slideRight {
        animation: slideInRight 0.8s ease-out forwards;
        opacity: 0;
    }

    .animate-pulse-custom {
        animation: pulse 2s infinite;
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .font-serif {
        font-family: 'Georgia', 'Times New Roman', serif;
    }

    .grill-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
        background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1a1a 100%);
    }

    .grill-pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            linear-gradient(90deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px),
            linear-gradient(0deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px);
        background-size: 50px 50px;
        opacity: 0.1;
    }

    .grill-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(184, 134, 11, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(212, 175, 55, 0.08) 0%, transparent 50%);
    }

    .grill-elements {
        position: absolute;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .grill-element {
        position: absolute;
        background: linear-gradient(45deg, var(--primary-gold), var(--dark-gold));
        border-radius: 2px;
        animation: grillGlow 3s ease-in-out infinite;
        pointer-events: none;
    }

    .grill-element:nth-child(1) {
        width: 120px;
        height: 4px;
        top: 15%;
        left: 10%;
        animation-delay: 0s;
    }

    .grill-element:nth-child(2) {
        width: 4px;
        height: 80px;
        top: 25%;
        right: 15%;
        animation-delay: 1s;
    }

    .grill-element:nth-child(3) {
        width: 100px;
        height: 4px;
        bottom: 30%;
        left: 20%;
        animation-delay: 2s;
    }

    .grill-element:nth-child(4) {
        width: 4px;
        height: 60px;
        bottom: 20%;
        right: 25%;
        animation-delay: 1.5s;
    }

    .smoke {
        position: absolute;
        width: 8px;
        height: 8px;
        background: rgba(212, 175, 55, 0.3);
        border-radius: 50%;
        animation: smoke 8s linear infinite;
        pointer-events: none;
    }

    .smoke:nth-child(5) { left: 15%; bottom: 10%; animation-delay: 0s; }
    .smoke:nth-child(6) { left: 25%; bottom: 15%; animation-delay: 2s; }
    .smoke:nth-child(7) { right: 20%; bottom: 12%; animation-delay: 4s; }
    .smoke:nth-child(8) { right: 30%; bottom: 8%; animation-delay: 6s; }

    .interactive-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: default;
        opacity: 0;
        transform: translateY(30px);
        border: 1px solid rgba(212, 175, 55, 0.2);
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--card-bg) 0%, var(--grill-dark) 100%);
        backdrop-filter: blur(10px);
        pointer-events: auto;
    }
    
    .interactive-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
        transition: left 0.6s;
        pointer-events: none;
    }
    
    .interactive-card:hover::before {
        left: 100%;
    }
    
    .interactive-card.loaded {
        opacity: 1;
        transform: translateY(0);
    }
    
    .interactive-card:hover {
        transform: translateY(-8px);
        box-shadow: 
            0 25px 50px rgba(212, 175, 55, 0.2),
            0 0 0 1px rgba(212, 175, 55, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border-color: rgba(212, 175, 55, 0.4);
    }

    .contact-info-card {
        background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
        padding: 2.5rem;
        border-radius: 16px;
        border: 1px solid rgba(212, 175, 55, 0.25);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        margin-bottom: 1.5rem;
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%),
            linear-gradient(-45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%);
        background-size: 20px 20px;
        pointer-events: auto;
        z-index: 1;
    }
    
    .contact-info-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, 
            rgba(212, 175, 55, 0.05) 0%,
            transparent 30%,
            transparent 70%,
            rgba(212, 175, 55, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .contact-info-card:hover::after {
        opacity: 1;
    }
    
    .contact-info-card:hover {
        border-color: rgba(212, 175, 55, 0.5);
        box-shadow: 
            0 20px 40px rgba(212, 175, 55, 0.15),
            inset 0 1px 0 rgba(212, 175, 55, 0.1);
        transform: translateY(-5px);
    }

    .contact-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
        transition: all 0.3s ease;
        border: 2px solid rgba(212, 175, 55, 0.3);
        pointer-events: none;
    }

    .contact-icon::before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        background: var(--card-bg);
        border-radius: 10px;
        z-index: 1;
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.1) 50%, transparent 51%);
        background-size: 10px 10px;
        pointer-events: none;
    }

    .contact-icon i {
        font-size: 1.5rem;
        color: var(--primary-gold);
        z-index: 2;
        position: relative;
        transition: all 0.3s ease;
        text-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
        pointer-events: none;
    }

    .contact-info-card:hover .contact-icon {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
    }

    .contact-info-card:hover .contact-icon i {
        color: var(--light-gold);
        text-shadow: 0 0 15px rgba(245, 232, 200, 0.8);
    }

    .form-input {
        background: rgba(26, 26, 26, 0.8);
        border: 1px solid rgba(212, 175, 55, 0.3);
        color: var(--text-light);
        padding: 1rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        width: 100%;
        backdrop-filter: blur(10px);
        font-size: 1rem;
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.05) 50%, transparent 51%);
        background-size: 15px 15px;
        pointer-events: auto;
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-gold);
        box-shadow: 
            0 0 0 3px rgba(212, 175, 55, 0.2),
            0 0 20px rgba(212, 175, 55, 0.1),
            inset 0 0 10px rgba(212, 175, 55, 0.05);
        transform: translateY(-2px);
        background: rgba(26, 26, 26, 0.95);
    }

    .form-input:hover {
        border-color: rgba(212, 175, 55, 0.5);
        background: rgba(26, 26, 26, 0.9);
    }

    .form-label {
        color: var(--text-light);
        font-weight: 500;
        margin-bottom: 0.75rem;
        display: block;
        font-size: 1rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
        color: var(--dark-bg);
        padding: 1.2rem 2.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 175, 55, 0.3);
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 
            0 4px 15px rgba(212, 175, 55, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        pointer-events: auto;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.6s;
        pointer-events: none;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 15px 30px rgba(212, 175, 55, 0.4),
            0 0 20px rgba(212, 175, 55, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        color: var(--dark-bg);
    }

    .social-icon {
        width: 55px;
        height: 55px;
        background: rgba(26, 26, 26, 0.8);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.1) 50%, transparent 51%);
        background-size: 10px 10px;
        cursor: pointer;
        z-index: 10;
        pointer-events: auto;
        text-decoration: none;
    }

    .social-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.2), transparent);
        transition: left 0.6s;
        pointer-events: none;
    }

    .social-icon:hover::before {
        left: 100%;
    }

    .social-icon:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: var(--primary-gold);
        box-shadow: 
            0 10px 25px rgba(212, 175, 55, 0.3),
            0 0 15px rgba(212, 175, 55, 0.2);
    }

    .facebook {
        color: #1877F2;
    }

    .instagram {
        color: #E4405F;
    }

    .tiktok {
        color: #000000;
    }

    .facebook:hover {
        background: #1877F2;
        color: white;
    }

    .instagram:hover {
        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        color: white;
    }

    .tiktok:hover {
        background: #000000;
        color: white;
    }

    .social-links-container {
        position: relative;
        z-index: 10;
        pointer-events: auto;
    }

    .map-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(212, 175, 55, 0.3);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        background: var(--card-bg);
        position: relative;
        pointer-events: auto;
    }

    .map-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px solid transparent;
        background: linear-gradient(45deg, var(--primary-gold), var(--dark-gold)) border-box;
        -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        border-radius: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .map-container:hover::before {
        opacity: 1;
    }

    .map-container:hover {
        border-color: var(--primary-gold);
        transform: translateY(-5px);
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.4),
            0 0 20px rgba(212, 175, 55, 0.2);
    }

    .success-message {
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        animation: slideInRight 0.6s ease-out;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        pointer-events: auto;
    }

    .loading-spinner {
        display: none;
        width: 24px;
        height: 24px;
        border: 3px solid rgba(212, 175, 55, 0.3);
        border-top: 3px solid var(--primary-gold);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .content-container {
        position: relative;
        z-index: 1;
        pointer-events: auto;
    }

    .section-spacing {
        margin-bottom: 3rem;
    }

    .grill-header {
        position: relative;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }

    .grill-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
        border-radius: 2px;
    }

    @media (max-width: 768px) {
        .contact-info-card {
            padding: 2rem;
        }
        
        .contact-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
        }
        
        .contact-icon i {
            font-size: 1.25rem;
        }

        .grill-element, .smoke {
            display: none;
        }

        .grill-pattern {
            opacity: 0.05;
        }
    }
</style>

<div class="grill-background">
    <div class="grill-pattern"></div>
    <div class="grill-overlay"></div>
    <div class="grill-elements">
        <div class="grill-element"></div>
        <div class="grill-element"></div>
        <div class="grill-element"></div>
        <div class="grill-element"></div>
        <div class="smoke"></div>
        <div class="smoke"></div>
        <div class="smoke"></div>
        <div class="smoke"></div>
    </div>
</div>

<div class="min-h-screen bg-transparent px-4 pt-24 pb-12 content-container">
    <div class="max-w-6xl mx-auto">
        
        <div class="text-center mb-16 animate-fadeIn section-spacing">
            <div class="grill-header">
                <h1 class="text-4xl md:text-5xl font-bold font-serif text-yellow-400 mb-6">
                    Get In Touch
                </h1>
            </div>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">
                We're here to assist you with any inquiries. Reach out through any of the options below 
                and we'll respond promptly.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 items-start">
            
            <div class="space-y-6">
                <div class="contact-info-card interactive-card animate-slideLeft">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-yellow-400 mb-3">Email Address</h3>
                    <p class="text-gray-300 mb-4">Send us an email and we'll respond within 24 hours</p>
                    <a href="mailto:ultimateliempohausmarikina@gmail.com" class="text-yellow-400 hover:text-yellow-300 transition-colors text-lg font-medium">
                        ultimateliempohausmarikina@gmail.com
                    </a>
                </div>

                <div class="contact-info-card interactive-card animate-slideLeft" style="animation-delay: 0.1s">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-yellow-400 mb-3">Phone Number</h3>
                    <p class="text-gray-300 mb-4">Call us directly for immediate assistance</p>
                    <a href="tel:+639985486389" class="text-yellow-400 hover:text-yellow-300 transition-colors text-lg font-medium">
                        +63 998 548 6389
                    </a>
                </div>

                <div class="contact-info-card interactive-card animate-slideLeft" style="animation-delay: 0.2s">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-yellow-400 mb-3">Our Location</h3>
                    <p class="text-gray-300 mb-4">Visit us at our prime location in Marikina City</p>
                    <p class="text-yellow-400 text-lg font-medium">
                        Lot 2D, Mayor Gil Fernando Ave., Brgy. Sto. Niño, Marikina City 1800
                    </p>
                </div>

                <div class="contact-info-card interactive-card animate-slideLeft" style="animation-delay: 0.3s">
                    <h3 class="text-xl font-semibold text-yellow-400 mb-6">Connect With Us</h3>
                    <p class="text-gray-300 mb-6">Follow us on social media for updates and special offers</p>
                    <div class="social-links-container flex space-x-4">
                        <a href="https://www.facebook.com/ultimateliempomarikina" target="_blank" 
                           class="social-icon facebook">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="https://www.instagram.com/ulh_marikina/?igsh=Y290MGZtam50NzVu#" target="_blank" 
                           class="social-icon instagram">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="https://www.tiktok.com/@ulh.official?_t=ZS-8ybFExRFgMp&_r=1" target="_blank" 
                           class="social-icon tiktok">
                            <i class="fab fa-tiktok text-lg"></i>
                        </a>
                    </div>
                </div>

                <div class="contact-info-card interactive-card animate-slideLeft" style="animation-delay: 0.4s">
                    <h3 class="text-xl font-semibold text-yellow-400 mb-4">Find Us</h3>
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.258174272683!2d121.10926657599878!3d14.632831776084!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b9b1d61c5c1f%3A0x5a01dbe3d1b3c4f4!2sMayor%20Gil%20Fernando%20Ave%2C%20Marikina%2C%201800%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
                            width="100%" 
                            height="250" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="contact-info-card interactive-card animate-slideRight">
                <h2 class="text-3xl font-bold text-yellow-400 mb-2">Send Us a Message</h2>
                <p class="text-gray-300 mb-8">Fill out the form below and we'll get back to you as soon as possible</p>
                
                <div id="successMessage" class="success-message">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                        <div>
                            <h4 class="font-semibold">Message Sent Successfully!</h4>
                            <p class="text-sm opacity-90">We'll get back to you within 24 hours.</p>
                        </div>
                    </div>
                </div>

                <form id="contactForm" action="https://api.web3forms.com/submit" method="POST" class="space-y-6">
                    <input type="hidden" name="access_key" value="485f8ce2-f14c-40fd-8d84-e92b21e7c0a0" />
                    <input type="text" name="botcheck" class="hidden" style="display:none">

                    <div>
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" required 
                               class="form-input" 
                               placeholder="Enter your full name">
                    </div>

                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" required 
                               class="form-input" 
                               placeholder="Enter your email address">
                    </div>

                    <div>
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" required 
                               class="form-input" 
                               placeholder="Example: Reservation Inquiry">
                    </div>

                    <div>
                        <label class="form-label">Message</label>
                        <textarea name="message" rows="5" required 
                                  class="form-input" 
                                  placeholder="Write your detailed message here..."></textarea>
                    </div>

                    <button type="submit" id="submitButton" class="btn-primary w-full justify-center">
                        <div class="loading-spinner" id="loadingSpinner"></div>
                        <span id="buttonText">Send Message</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const successMessage = document.getElementById('successMessage');
        
        submitButton.disabled = true;
        buttonText.textContent = 'Sending...';
        loadingSpinner.style.display = 'block';
        
        setTimeout(() => {
            successMessage.style.display = 'block';
            
            document.getElementById('contactForm').reset();
            
            submitButton.disabled = false;
            buttonText.textContent = 'Send Message';
            loadingSpinner.style.display = 'none';
            
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }, 2000);
    });

    document.querySelectorAll('.interactive-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    document.querySelectorAll('.social-icon').forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.1)';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    document.querySelectorAll('.contact-info-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.contact-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.contact-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const animationClass = Array.from(entry.target.classList).find(cls => 
                    cls.startsWith('animate-')
                );
                if (animationClass) {
                    entry.target.classList.add('loaded');
                }
            }
        });
    }, { threshold: 0.1 });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[class*="animate-"]').forEach(el => {
            observer.observe(el);
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Test social media links
    document.querySelectorAll('.social-icon').forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('Social link clicked:', this.href);
        });
    });
</script>

<?php
include "src/components/footer.php";
?>