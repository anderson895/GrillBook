<?php
include "src/components/header.php";
?>

<style>
    /* Professional Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
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
    
    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .animate-pulse-custom {
        animation: pulse 2s infinite;
    }

    /* Professional Interactive Elements */
    .btn-interactive {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        border: none;
    }
    
    .btn-interactive::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s;
    }
    
    .btn-interactive:hover::before {
        left: 100%;
    }
    
    .btn-interactive:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(251, 191, 36, 0.4);
    }
    
    .scroll-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        z-index: 1001;
        transition: width 0.3s ease;
    }
    
    .typewriter {
        overflow: hidden;
        border-right: 2px solid #fbbf24;
        white-space: nowrap;
        margin: 0 auto;
        animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
    }
    
    @keyframes typing {
        from { width: 0 }
        to { width: 100% }
    }
    
    @keyframes blink-caret {
        from, to { border-color: transparent }
        50% { border-color: #fbbf24; }
    }
    
    .parallax {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    
    /* Featured Menu Items Section */
    .featured-menu {
        background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
        padding: 4rem 0;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .menu-item {
        background: linear-gradient(145deg, #1a1a1a, #2d2d2d);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(251, 191, 36, 0.2);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        opacity: 0;
        transform: translateY(30px);
    }

    .menu-item.loaded {
        opacity: 1;
        transform: translateY(0);
    }

    .menu-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(251, 191, 36, 0.15);
        border-color: rgba(251, 191, 36, 0.4);
    }

    .menu-item-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .menu-item:hover .menu-item-image {
        transform: scale(1.1);
    }

    .menu-item-content {
        padding: 1.5rem;
    }

    .menu-item-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #fbbf24;
        margin-bottom: 0.5rem;
    }

    .menu-item-description {
        color: #ccc;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .menu-item-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fbbf24;
    }

    .menu-item-category {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(251, 191, 36, 0.9);
        color: #1a1a1a;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Loading States */
    .loading-skeleton {
        background: linear-gradient(90deg, #2d2d2d 25%, #3a3a3a 50%, #2d2d2d 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 8px;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-image {
        width: 100%;
        height: 200px;
        border-radius: 8px 8px 0 0;
    }

    .skeleton-text {
        height: 20px;
        margin-bottom: 10px;
    }

    .skeleton-title {
        height: 24px;
        width: 70%;
    }

    .skeleton-price {
        height: 28px;
        width: 40%;
    }

    /* Business Solutions Section */
    .solutions-section {
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        padding: 4rem 0;
    }

    .solutions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .solution-card {
        background: linear-gradient(145deg, #2d2d2d, #3a3a3a);
        padding: 2.5rem 2rem;
        border-radius: 16px;
        border: 1px solid rgba(251, 191, 36, 0.2);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    
    .solution-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.1), transparent);
        transition: left 0.6s;
    }
    
    .solution-card:hover::before {
        left: 100%;
    }
    
    .solution-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(251, 191, 36, 0.15);
        border-color: rgba(251, 191, 36, 0.4);
    }
    
    .solution-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: #1a1a1a;
    }
    
    .solution-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fbbf24;
        margin-bottom: 1rem;
    }
    
    .solution-description {
        color: #ccc;
        line-height: 1.6;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }
    
    .solution-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .solution-features li {
        color: #fbbf24;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .solution-features li:before {
        content: '✓';
        color: #10b981;
        font-weight: bold;
    }

    /* How It Works Section */
    .how-it-works {
        background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
        padding: 4rem 0;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .step-card {
        background: linear-gradient(145deg, #1a1a1a, #2d2d2d);
        padding: 2.5rem 2rem;
        border-radius: 16px;
        border: 1px solid rgba(251, 191, 36, 0.2);
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .step-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.05), transparent);
        transition: left 0.6s;
    }

    .step-card:hover::before {
        left: 100%;
    }

    .step-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(251, 191, 36, 0.1);
        border-color: rgba(251, 191, 36, 0.4);
    }

    .step-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: #1a1a1a;
        font-weight: bold;
    }

    .step-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fbbf24;
        margin-bottom: 1rem;
    }

    .step-description {
        color: #ccc;
        line-height: 1.6;
        font-size: 0.95rem;
    }
</style>

<!-- Scroll Progress Bar -->
<div class="scroll-progress" id="scrollProgress"></div>

<!-- Hero Section -->
<section class="pt-32 pb-20 text-center px-4 parallax" style="background-image: url('https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80')">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-4xl sm:text-5xl font-extrabold text-yellow-300 mb-6 typewriter">Ultimate Liempo Haus</h2>
        <p class="text-lg sm:text-xl text-yellow-200 max-w-2xl mx-auto mb-8 animate-fadeInUp">Experience the finest grilled specialties and authentic Filipino flavors</p>
        <a href="login.php" class="bg-yellow-400 text-black px-8 py-4 rounded-xl font-bold hover:bg-yellow-300 transition btn-interactive animate-pulse-custom text-lg">
            Make a Reservation
        </a>
    </div>
</section>

<!-- Featured Menu Items Section -->
<section class="featured-menu">
    <div class="max-w-6xl mx-auto px-4">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-yellow-400 mb-4 animate-fadeInUp">Featured Menu Items</h3>
        <p class="text-lg text-yellow-200 text-center mb-12 max-w-2xl mx-auto">Discover our most popular dishes crafted with passion and authentic flavors</p>

        <div id="menuContainer" class="menu-grid">
            <!-- Loading Skeletons -->
            <div class="menu-item">
                <div class="skeleton-image loading-skeleton"></div>
                <div class="menu-item-content">
                    <div class="skeleton-title loading-skeleton skeleton-text"></div>
                    <div class="skeleton-text loading-skeleton"></div>
                    <div class="skeleton-text loading-skeleton"></div>
                    <div class="skeleton-price loading-skeleton"></div>
                </div>
            </div>
            <div class="menu-item">
                <div class="skeleton-image loading-skeleton"></div>
                <div class="menu-item-content">
                    <div class="skeleton-title loading-skeleton skeleton-text"></div>
                    <div class="skeleton-text loading-skeleton"></div>
                    <div class="skeleton-text loading-skeleton"></div>
                    <div class="skeleton-price loading-skeleton"></div>
                </div>
            </div>
            <div class="menu-item">
                <div class="skeleton-image loading-skeleton"></div>
                <div class="menu-item-content">
                    <div class="skeleton-title loading-skeleton skeleton-text"></div>
                    <div class="skeleton-text loading-skeleton"></div>
                    <div class="skeleton-text loading-skeleton"></div>
                    <div class="skeleton-price loading-skeleton"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose GrillBook Section -->
<section class="solutions-section">
    <div class="max-w-6xl mx-auto px-4">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-yellow-400 mb-12 animate-fadeInUp">Why Choose GrillBook?</h3>
        
        <div class="solutions-grid">
            <div class="solution-card">
                <div class="solution-icon">⚡</div>
                <h4 class="solution-title">Easy Reservations</h4>
                <p class="solution-description">Book your table in seconds with our intuitive reservation system. No more waiting on hold or uncertain availability.</p>
                <ul class="solution-features">
                    <li>Instant confirmation</li>
                    <li>Real-time table availability</li>
                    <li>Mobile-friendly booking</li>
                </ul>
            </div>
            
            <div class="solution-card">
                <div class="solution-icon">🎯</div>
                <h4 class="solution-title">Exclusive Deals</h4>
                <p class="solution-description">Get access to special promotions, package deals, and member-only discounts when you book through our platform.</p>
                <ul class="solution-features">
                    <li>Special package deals</li>
                    <li>Seasonal promotions</li>
                    <li>Loyalty rewards</li>
                </ul>
            </div>
            
            <div class="solution-card">
                <div class="solution-icon">🛡️</div>
                <h4 class="solution-title">Secure & Reliable</h4>
                <p class="solution-description">Your reservations are safe with us. We provide secure payment processing and reliable booking confirmations.</p>
                <ul class="solution-features">
                    <li>Secure payments</li>
                    <li>Instant notifications</li>
                    <li>24/7 support</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works">
    <div class="max-w-6xl mx-auto px-4">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-yellow-400 mb-4 animate-fadeInUp">How It Works</h3>
        <p class="text-lg text-yellow-200 text-center mb-12 max-w-2xl mx-auto">Simple steps to enjoy our delicious offerings</p>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon">1</div>
                <h4 class="step-title">Create Account</h4>
                <p class="step-description">Sign up in seconds to access exclusive features and faster booking.</p>
            </div>
            
            <div class="step-card">
                <div class="step-icon">2</div>
                <h4 class="step-title">Choose Your Menu</h4>
                <p class="step-description">Browse our delicious menu items and select your favorites in advance.</p>
            </div>
            
            <div class="step-card">
                <div class="step-icon">3</div>
                <h4 class="step-title">Book & Enjoy</h4>
                <p class="step-description">Reserve your table, confirm your order, and enjoy a seamless dining experience.</p>
            </div>
        </div>
    </div>
</section>

<script>
    // Professional Scroll Progress
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById("scrollProgress").style.width = scrolled + "%";
    });

    // Fetch and Display Menu Items
    function fetchMenuItems() {
        console.log('Fetching menu items...');
        
        fetch('controller/end-points/controller.php?requestType=fetch_all_menu')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Menu data received:', data);
                if (data.status === 200 && data.data && Array.isArray(data.data)) {
                    displayMenuItems(data.data);
                } else {
                    console.error('Invalid data format:', data);
                    showErrorMessage('Failed to load menu items - invalid data format');
                    showEmptyMenuState();
                }
            })
            .catch(error => {
                console.error('Error fetching menu:', error);
                showErrorMessage('Network error loading menu: ' + error.message);
                showEmptyMenuState();
            });
    }

    function displayMenuItems(menuItems) {
        const menuContainer = document.getElementById('menuContainer');
        if (!menuContainer) {
            console.error('Menu container not found');
            return;
        }

        // Clear loading skeletons
        menuContainer.innerHTML = '';

        console.log('Displaying menu items:', menuItems.length);

        // Take only first 6 items for featured section
        const featuredItems = menuItems.slice(0, 6);

        if (featuredItems.length === 0) {
            console.log('No menu items found');
            showEmptyMenuState();
            return;
        }

        // Display menu items
        featuredItems.forEach((item, index) => {
            const menuItem = document.createElement('div');
            menuItem.className = 'menu-item animate-fadeInUp';
            menuItem.style.animationDelay = `${index * 0.1}s`;
            
            // Use actual image from database or fallback
            const imageUrl = item.menu_image_banner 
                ? `static/upload/${item.menu_image_banner}`
                : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
            
            menuItem.innerHTML = `
                <div class="menu-item-category">${item.menu_category || 'Special'}</div>
                <img src="${imageUrl}" alt="${item.menu_name}" class="menu-item-image" 
                     onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'">
                <div class="menu-item-content">
                    <h4 class="menu-item-title">${item.menu_name || 'Delicious Dish'}</h4>
                    <p class="menu-item-description">${item.menu_description || 'Experience our signature dish prepared with the finest ingredients and authentic flavors.'}</p>
                    <div class="menu-item-price">₱${parseFloat(item.menu_price || 0).toFixed(2)}</div>
                </div>
            `;
            
            menuContainer.appendChild(menuItem);
        });

        // Animate cards after a short delay
        setTimeout(() => {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.add('loaded');
            });
        }, 100);
    }

    function showEmptyMenuState() {
        const menuContainer = document.getElementById('menuContainer');
        if (!menuContainer) return;

        menuContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="text-yellow-400 text-6xl mb-4">🍽️</div>
                <h4 class="text-xl font-bold text-yellow-400 mb-2">Menu Coming Soon</h4>
                <p class="text-gray-400 mb-6">We're preparing something delicious for you!</p>
                <button onclick="fetchMenuItems()" class="bg-yellow-400 text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-300 transition">
                    Refresh Menu
                </button>
            </div>
        `;
    }

    function showErrorMessage(message) {
        if (typeof alertify !== 'undefined') {
            alertify.error(message);
        } else {
            console.error(message);
            // Fallback: show simple alert
            alert('Error: ' + message);
        }
    }

    // Scroll Animations
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    // Initialize Page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, fetching menu items...');
        
        // Fetch menu items on page load
        fetchMenuItems();
        
        // Observe all interactive elements
        document.querySelectorAll('.menu-item, .solution-card, .step-card, .animate-fadeInUp').forEach(el => {
            observer.observe(el);
        });
        
    });

    // Enhanced Get Started Button
    const getStartedBtn = document.querySelector('.btn-interactive');
    if (getStartedBtn) {
        getStartedBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        getStartedBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }
</script>

<?php
include "src/components/footer.php";
?>