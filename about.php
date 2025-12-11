<?php
include "src/components/header.php";
?>

<style>
    /* Professional Color Scheme */
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

    /* Enhanced Professional Animations */
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

    /* Professional Typography */
    .font-serif {
        font-family: 'Georgia', 'Times New Roman', serif;
    }

    /* Grill-Inspired Background Design */
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
    }

    .grill-element {
        position: absolute;
        background: linear-gradient(45deg, var(--primary-gold), var(--dark-gold));
        border-radius: 2px;
        animation: grillGlow 3s ease-in-out infinite;
    }

    /* Grill marks pattern */
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

    /* Smoke effects */
    .smoke {
        position: absolute;
        width: 8px;
        height: 8px;
        background: rgba(212, 175, 55, 0.3);
        border-radius: 50%;
        animation: smoke 8s linear infinite;
    }

    .smoke:nth-child(5) { left: 15%; bottom: 10%; animation-delay: 0s; }
    .smoke:nth-child(6) { left: 25%; bottom: 15%; animation-delay: 2s; }
    .smoke:nth-child(7) { right: 20%; bottom: 12%; animation-delay: 4s; }
    .smoke:nth-child(8) { right: 30%; bottom: 8%; animation-delay: 6s; }

    /* Interactive Elements with Grill Theme */
    .interactive-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        opacity: 0;
        transform: translateY(30px);
        border: 1px solid rgba(212, 175, 55, 0.2);
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--card-bg) 0%, var(--grill-dark) 100%);
        backdrop-filter: blur(10px);
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

    /* Mission & Vision Cards with Grill Texture */
    .mission-vision-card {
        background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
        padding: 4rem 3.5rem;
        border-radius: 20px;
        border: 1px solid rgba(212, 175, 55, 0.25);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%),
            linear-gradient(-45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%);
        background-size: 20px 20px;
    }
    
    .mission-vision-card::after {
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
    }
    
    .mission-vision-card:hover::after {
        opacity: 1;
    }
    
    .mission-vision-card:hover {
        border-color: rgba(212, 175, 55, 0.5);
        box-shadow: 
            0 20px 40px rgba(212, 175, 55, 0.15),
            inset 0 1px 0 rgba(212, 175, 55, 0.1);
        transform: translateY(-5px);
    }

    /* Grill-inspired Icons */
    .icon-wrapper {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 3rem;
        position: relative;
        transition: all 0.3s ease;
        border: 2px solid rgba(212, 175, 55, 0.3);
    }

    .icon-wrapper::before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        background: var(--card-bg);
        border-radius: 16px;
        z-index: 1;
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.1) 50%, transparent 51%);
        background-size: 10px 10px;
    }

    .icon-wrapper .icon-text {
        font-size: 1.8rem;
        color: var(--primary-gold);
        z-index: 2;
        position: relative;
        font-weight: bold;
        transition: all 0.3s ease;
        text-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
    }

    .mission-vision-card:hover .icon-wrapper {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
    }

    .mission-vision-card:hover .icon-wrapper .icon-text {
        color: var(--light-gold);
        text-shadow: 0 0 15px rgba(245, 232, 200, 0.8);
    }

    /* Place Cards with Grill Style */
    .place-card {
        background: linear-gradient(135deg, var(--card-bg), var(--grill-dark));
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid rgba(212, 175, 55, 0.25);
        backdrop-filter: blur(10px);
        background-image: 
            linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%);
        background-size: 15px 15px;
    }
    
    .place-image {
        transition: transform 0.6s ease;
        transform-origin: center;
    }
    
    .place-card:hover .place-image {
        transform: scale(1.1);
    }
    
    .place-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(212, 175, 55, 0.9) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: flex-end;
        padding: 2rem;
    }
    
    .place-card:hover .place-overlay {
        opacity: 1;
    }
    
    .place-content {
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    
    .place-card:hover .place-content {
        transform: translateY(0);
    }

    /* Scroll Progress */
    .scroll-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-gold), var(--dark-gold));
        z-index: 1001;
        transition: width 0.3s ease;
    }

    /* Grill-inspired Button Styles */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
        color: var(--dark-bg);
        padding: 1.2rem 3rem;
        border-radius: 12px;
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

    .btn-secondary {
        border: 2px solid var(--primary-gold);
        color: var(--primary-gold);
        padding: 1.2rem 3rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        backdrop-filter: blur(10px);
        background: rgba(26, 26, 26, 0.8);
        position: relative;
        overflow: hidden;
    }

    .btn-secondary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
        transition: left 0.6s;
    }

    .btn-secondary:hover::before {
        left: 100%;
    }

    .btn-secondary:hover {
        background: var(--primary-gold);
        color: var(--dark-bg);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
    }

    /* Hero Section Enhancement */
    .hero-section {
        background: linear-gradient(135deg, var(--dark-bg) 0%, #2C2C2C 100%);
        position: relative;
        overflow: hidden;
        padding: 8rem 0 6rem;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('static/image/ULH.jpg') center/cover;
        opacity: 0.08;
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Section Divider */
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
        margin: 6rem 0;
    }

    /* Enhanced Spacing */
    .mission-vision-grid {
        gap: 4rem;
        margin: 6rem 0;
    }

    .content-section {
        margin: 6rem 0;
    }

    .hero-spacing {
        margin-bottom: 4rem;
    }

    .heading-spacing {
        margin-bottom: 3rem;
    }

    .paragraph-spacing {
        margin-bottom: 2.5rem;
        line-height: 1.8;
    }

    .list-spacing {
        margin-top: 2rem;
        padding-top: 2.5rem;
    }

    /* Professional Header Styling */
    .professional-header {
        letter-spacing: -0.02em;
        line-height: 1.1;
    }

    .professional-subheader {
        letter-spacing: 0.02em;
        line-height: 1.6;
    }

    /* Grill-themed Header */
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

    /* Content Container */
    .content-container {
        position: relative;
        z-index: 1;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .mission-vision-card {
            padding: 3rem 2.5rem;
        }
        
        .hero-section {
            padding: 6rem 0 4rem;
        }
        
        .hero-section::before {
            opacity: 0.05;
        }
        
        .mission-vision-grid {
            gap: 3rem;
            margin: 4rem 0;
        }
        
        .content-section {
            margin: 4rem 0;
        }
        
        .icon-wrapper {
            width: 80px;
            height: 80px;
            margin-bottom: 2.5rem;
        }
        
        .icon-wrapper .icon-text {
            font-size: 1.6rem;
        }

        .grill-element, .smoke {
            display: none;
        }

        .grill-pattern {
            opacity: 0.05;
        }
    }

    @media (max-width: 640px) {
        .mission-vision-card {
            padding: 2.5rem 2rem;
        }
        
        .hero-section {
            padding: 5rem 0 3rem;
        }
    }
</style>

<!-- Grill-Inspired Background Design -->
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

<!-- Scroll Progress Bar -->
<div class="scroll-progress" id="scrollProgress"></div>

<!-- Page Wrapper -->
<div class="min-h-screen bg-transparent text-white content-container">

    <!-- Hero Section -->
    <section class="hero-section px-4">
        <div class="max-w-4xl mx-auto hero-content">
            <div class="text-center animate-fadeIn">
                <div class="grill-header">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold font-serif professional-header mb-8">
                        <span class="text-white">Our</span>
                        <span class="text-yellow-400">Culinary</span>
                        <span class="text-white">Journey</span>
                    </h1>
                </div>
                <div class="hero-spacing"></div>
                <p class="text-xl md:text-2xl text-yellow-200 professional-subheader leading-relaxed">
                    Where passion meets perfection in every dish, creating unforgettable dining experiences 
                    that celebrate the art of fine cuisine and genuine hospitality.
                </p>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-12 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-16 items-stretch mission-vision-grid">
                <!-- Mission -->
                <div class="mission-vision-card interactive-card animate-slideLeft">
                    <div class="icon-wrapper">
                        <span class="icon-text">M</span>
                    </div>
                    <h2 class="text-3xl font-bold text-yellow-400 font-serif professional-header heading-spacing">
                        Our Mission
                    </h2>
                    <p class="text-gray-300 text-lg paragraph-spacing">
                        To craft extraordinary dining moments by perfecting every element—from 
                        sourcing premium ingredients to delivering impeccable service. We are 
                        committed to creating memorable experiences that celebrate the art of 
                        fine dining and genuine hospitality.
                    </p>
                    <div class="border-t border-gray-700 list-spacing">
                        <h4 class="text-yellow-400 font-semibold text-lg mb-4">Our Commitment</h4>
                        <ul class="text-gray-400 space-y-3">
                            <li class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-4"></div>
                                Source only the finest, freshest ingredients
                            </li>
                            <li class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-4"></div>
                                Train exceptional hospitality professionals
                            </li>
                            <li class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-4"></div>
                                Create innovative, memorable culinary experiences
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Vision -->
                <div class="mission-vision-card interactive-card animate-slideRight">
                    <div class="icon-wrapper">
                        <span class="icon-text">V</span>
                    </div>
                    <h2 class="text-3xl font-bold text-yellow-400 font-serif professional-header heading-spacing">
                        Our Vision
                    </h2>
                    <p class="text-gray-300 text-lg paragraph-spacing">
                        To establish ourselves as Metro Manila's most trusted culinary destination, 
                        recognized for innovation, consistency, and exceptional guest experiences. 
                        We aspire to set new standards in the dining industry while expanding our 
                        reach to serve more communities.
                    </p>
                    <div class="border-t border-gray-700 list-spacing">
                        <h4 class="text-yellow-400 font-semibold text-lg mb-4">Our Aspirations</h4>
                        <ul class="text-gray-400 space-y-3">
                            <li class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-4"></div>
                                Set industry benchmarks for service excellence
                            </li>
                            <li class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-4"></div>
                                Pioneer culinary innovation and trends
                            </li>
                            <li class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-4"></div>
                                Build lasting relationships with our community
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Divider -->
    <div class="section-divider max-w-6xl mx-auto"></div>

    <!-- Gallery Section -->
    <section class="py-16 px-4 content-section">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16 animate-fadeIn">
                <div class="grill-header">
                    <h2 class="text-4xl font-bold text-yellow-400 font-serif professional-header mb-6">
                        Our Dining Experience
                    </h2>
                </div>
                <p class="text-xl text-gray-300 professional-subheader max-w-2xl mx-auto">
                    Discover the carefully crafted environments where culinary artistry meets sophisticated ambiance
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Gallery Item 1 -->
                <div class="place-card interactive-card animate-fadeInUp">
                    <div class="relative overflow-hidden">
                        <img src="static/image/Front.jpg" alt="Elegant Dining Space" class="place-image w-full h-64 object-cover">
                        <div class="place-overlay">
                            <div class="place-content">
                                <h3 class="text-xl font-bold text-black mb-2">Elegant Dining Space</h3>
                                <p class="text-black text-sm">Sophisticated ambiance perfect for intimate dinners</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white mb-3">Elegant Dining Space</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Our sophisticated dining area offers an intimate setting with carefully curated 
                            lighting and decor, creating the perfect atmosphere for memorable meals.
                        </p>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div class="place-card interactive-card animate-fadeInUp" style="animation-delay: 0.1s">
                    <div class="relative overflow-hidden">
                        <img src="static/image/Floor.jpg" alt="Premium Lounge" class="place-image w-full h-64 object-cover">
                        <div class="place-overlay">
                            <div class="place-content">
                                <h3 class="text-xl font-bold text-black mb-2">Premium Lounge</h3>
                                <p class="text-black text-sm">Comfortable seating for relaxed gatherings</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white mb-3">Premium Lounge</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Experience ultimate comfort in our premium lounge area, designed for 
                            relaxed gatherings and sophisticated social experiences.
                        </p>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div class="place-card interactive-card animate-fadeInUp" style="animation-delay: 0.2s">
                    <div class="relative overflow-hidden">
                        <img src="static/image/Drinks.jpg" alt="Craft Bar" class="place-image w-full h-64 object-cover">
                        <div class="place-overlay">
                            <div class="place-content">
                                <h3 class="text-xl font-bold text-black mb-2">Craft Bar</h3>
                                <p class="text-black text-sm">Expert mixologists creating signature cocktails</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white mb-3">Craft Bar</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Our expert mixologists craft signature cocktails using premium spirits 
                            and fresh ingredients, creating innovative drinks that complement your dining experience.
                        </p>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div class="place-card interactive-card animate-fadeInUp" style="animation-delay: 0.3s">
                    <div class="relative overflow-hidden">
                        <img src="static/image/Party.jpg" alt="Event Venue" class="place-image w-full h-64 object-cover">
                        <div class="place-overlay">
                            <div class="place-content">
                                <h3 class="text-xl font-bold text-black mb-2">Event Venue</h3>
                                <p class="text-black text-sm">Perfect setting for celebrations</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white mb-3">Event Venue</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Host your special occasions in our versatile event space, featuring 
                            state-of-the-art amenities and customizable layouts for any celebration.
                        </p>
                    </div>
                </div>

                <!-- Gallery Item 5 -->
                <div class="place-card interactive-card animate-fadeInUp" style="animation-delay: 0.4s">
                    <div class="relative overflow-hidden">
                        <img src="static/image/Music.jpg" alt="Ambiance" class="place-image w-full h-64 object-cover">
                        <div class="place-overlay">
                            <div class="place-content">
                                <h3 class="text-xl font-bold text-black mb-2">Atmospheric Lighting</h3>
                                <p class="text-black text-sm">Carefully curated mood and ambiance</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white mb-3">Atmospheric Lighting</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Every lighting element is carefully designed to create the perfect mood, 
                            enhancing your dining experience with warmth and sophistication.
                        </p>
                    </div>
                </div>

                <!-- Gallery Item 6 -->
                <div class="place-card interactive-card animate-fadeInUp" style="animation-delay: 0.5s">
                    <div class="relative overflow-hidden">
                        <img src="static/image/Delicious.jpg" alt="Culinary Presentation" class="place-image w-full h-64 object-cover">
                        <div class="place-overlay">
                            <div class="place-content">
                                <h3 class="text-xl font-bold text-black mb-2">Culinary Artistry</h3>
                                <p class="text-black text-sm">Exquisite dishes prepared with precision</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white mb-3">Culinary Artistry</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Witness the artistry of our chefs as they transform premium ingredients 
                            into exquisite dishes that delight both the eyes and palate.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4" style="background: linear-gradient(135deg, var(--card-bg), var(--grill-dark));">
        <div class="max-w-4xl mx-auto text-center animate-fadeIn">
            <div class="grill-header">
                <h2 class="text-4xl font-bold text-yellow-400 font-serif professional-header mb-8">
                    Experience Unparalleled Dining
                </h2>
            </div>
            <p class="text-xl text-gray-300 professional-subheader mb-10 max-w-2xl mx-auto leading-relaxed">
                We invite you to indulge in an exceptional culinary journey where every detail is meticulously 
                crafted to ensure an unforgettable dining experience from the moment you arrive.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="login.php" class="btn-primary">
                    <span>Reserve Your Table</span>
                </a>
                <a href="index.php" class="btn-secondary">
                    <span>Explore Our Menu</span>
                </a>
            </div>
        </div>
    </section>
</div>

<script>
    // Scroll Progress
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById("scrollProgress").style.width = scrolled + "%";
    });

    // Interactive Card Effects
    document.querySelectorAll('.interactive-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Image Zoom Effects
    document.querySelectorAll('.place-image').forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Icon Hover Effects
    document.querySelectorAll('.mission-vision-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.icon-wrapper');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.icon-wrapper');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // Gallery Item Click Handlers
    document.querySelectorAll('.place-card').forEach(card => {
        card.addEventListener('click', function() {
            const title = this.querySelector('h3').textContent;
            console.log(`Viewing: ${title}`);
        });
    });

    // Intersection Observer for Animations
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
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        // Observe all animated elements
        document.querySelectorAll('[class*="animate-"]').forEach(el => {
            observer.observe(el);
        });

        // Button hover effects
        document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // Smooth scrolling for anchor links
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
</script>

<?php
include "src/components/footer.php";
?>