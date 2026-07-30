<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Featured Products from Database or Fallback Mock Data
$featured_products = [];
if ($GLOBALS['db_connected']) {
    try {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 ORDER BY p.id DESC LIMIT 8");
        $featured_products = $stmt->fetchAll();
    } catch (Exception $e) {
        $featured_products = [];
    }
}

if (empty($featured_products)) {
    $all_mock = get_mock_products();
    $featured_products = array_filter($all_mock, function($item) {
        return $item['is_featured'] == 1;
    });
}
?>

<!-- Hero Banner Section -->
<section class="hero-section" style="padding: clamp(60px, 15vh, 120px) 0; color: white; min-height: clamp(400px, 70vh, 600px); display: flex; align-items: center;">
    <!-- Background Video -->
    <video autoplay loop muted playsinline style="position: absolute; top: 50%; left: 50%; width: 100%; height: 100%; object-fit: cover; transform: translate(-50%, -50%); z-index: 1;">
        <source src="assets/videos/spices_animation.mp4.mp4" type="video/mp4">
    </video>
    <!-- Dark Overlay for readability -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 2;"></div>

    <div class="container" style="position: relative; z-index: 3; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="hero-content" style="text-align: center;">
                <span class="hero-tag" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4);"><i class="fas fa-leaf"></i> 100% Pure Organic & Natural</span>
                <h1 class="hero-title" style="color: white; font-size: clamp(2.2rem, 6vw, 3.8rem); text-shadow: 2px 2px 15px rgba(0,0,0,0.6); margin-bottom: 20px; line-height: 1.2;">Fresh Homemade <span style="color: #fca311;">Nutrition</span> For Your Family</h1>
                <p class="hero-desc" style="color: #f8f9fa; margin: 0 auto 30px; text-shadow: 1px 1px 8px rgba(0,0,0,0.6); max-width: 700px; font-size: clamp(1rem, 2vw, 1.25rem);">
                    Discover authentic Karnataka masalas, nutrient-dense 35+ multigrain health mixes, and sprouted baby ragi powders crafted with zero preservatives.
                </p>
                <div class="hero-actions" style="justify-content: center;">
                    <a href="shop.php" class="btn btn-primary" style="box-shadow: 0 4px 15px rgba(0,0,0,0.4); font-size: 1.1rem; padding: 14px 28px;"><i class="fas fa-store"></i> Explore All Products</a>
                    <a href="about.php" class="btn" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid white; backdrop-filter: blur(8px); font-size: 1.1rem; padding: 14px 28px;">Our Philosophy</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Features Section -->
<section class="section" style="background: #fff; padding: 40px 0; border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-mortar-pestle"></i></div>
                <h3 class="feature-title">100% Homemade</h3>
                <p class="feature-desc">Slow roasted & ground according to authentic heritage recipes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3 class="feature-title">No Preservatives</h3>
                <p class="feature-desc">Zero artificial colors, chemicals, or synthetic additives guaranteed.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-seedling"></i></div>
                <h3 class="feature-title">Farm Fresh Spices</h3>
                <p class="feature-desc">Directly sourced sun-dried ingredients for maximal aroma.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-truck-fast"></i></div>
                <h3 class="feature-title">Pan-India Shipping</h3>
                <p class="feature-desc">Hassle-free safe doorstep delivery across all pin codes.</p>
            </div>
        </div>
    </div>
</section>

<!-- Popular Product Showcase -->
<section class="section" style="background: #fdfbf7;">
    <div class="container">
        <div class="section-title">
            <span class="section-subtitle">Explore Our Range</span>
            <h2>Pure Ingredients, Traditional Recipes</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 15px auto 0;">Every product is carefully crafted in small batches using sun-dried ingredients to bring authentic Karnataka flavors to your home.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            
            <!-- Category 1 -->
            <a href="shop.php?category=masala-powders" style="display: block; text-decoration: none; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); background: #fff; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)';">
                <div style="background: #eef4f0; padding: 40px 20px; text-align: center;">
                    <img src="assets/images/products/sambar-powder.png" alt="Masala Powders" style="height: 180px; width: 100%; object-fit: contain; mix-blend-mode: multiply; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                </div>
                <div style="padding: 20px; text-align: center; border-top: 1px solid #f0f0f0;">
                    <h3 style="color: #1b4332; font-size: 1.25rem; margin-bottom: 5px;">Authentic Masalas</h3>
                    <span style="color: var(--secondary-color); font-weight: 600; font-size: 0.9rem;">Explore Range <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <!-- Category 2 -->
            <a href="shop.php?category=health-mixes" style="display: block; text-decoration: none; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); background: #fff; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)';">
                <div style="background: #fdf5e6; padding: 40px 20px; text-align: center;">
                    <img src="assets/images/products/multigrain-health-mix.png" alt="Health Mixes" style="height: 180px; width: 100%; object-fit: contain; mix-blend-mode: multiply; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                </div>
                <div style="padding: 20px; text-align: center; border-top: 1px solid #f0f0f0;">
                    <h3 style="color: #1b4332; font-size: 1.25rem; margin-bottom: 5px;">Health Mixes</h3>
                    <span style="color: var(--secondary-color); font-weight: 600; font-size: 0.9rem;">Explore Range <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <!-- Category 3 -->
            <a href="shop.php?category=baby-food" style="display: block; text-decoration: none; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); background: #fff; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)';">
                <div style="background: #fff0f5; padding: 40px 20px; text-align: center;">
                    <img src="assets/images/products/baby-ragi-sari.png" alt="Baby Food" style="height: 180px; width: 100%; object-fit: contain; mix-blend-mode: multiply; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                </div>
                <div style="padding: 20px; text-align: center; border-top: 1px solid #f0f0f0;">
                    <h3 style="color: #1b4332; font-size: 1.25rem; margin-bottom: 5px;">Baby Food (Ragi)</h3>
                    <span style="color: var(--secondary-color); font-weight: 600; font-size: 0.9rem;">Explore Range <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <!-- Category 4 -->
            <a href="shop.php?category=sweets-laddus" style="display: block; text-decoration: none; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); background: #fff; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)';">
                <div style="background: #fff8dc; padding: 40px 20px; text-align: center;">
                    <img src="assets/images/products/dry-fruits-laddu.jpg" alt="Sweets & Laddus" style="height: 180px; object-fit: cover; border-radius: 50%; width: 180px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); margin: 0 auto;">
                </div>
                <div style="padding: 20px; text-align: center; border-top: 1px solid #f0f0f0;">
                    <h3 style="color: #1b4332; font-size: 1.25rem; margin-bottom: 5px;">Homemade Laddus</h3>
                    <span style="color: var(--secondary-color); font-weight: 600; font-size: 0.9rem;">Explore Range <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="shop.php" class="btn btn-primary" style="padding: 12px 30px; font-size: 1.05rem;">Go to Full Shop Catalog</a>
        </div>
    </div>
</section>

<!-- About Brand Highlight Banner -->
<section class="section" style="background: linear-gradient(135deg, #1b4332, #2d6a4f); color: #fff; padding: 70px 0;">
    <div class="container">
        <div class="hero-grid">
            <div>
                <span style="color: var(--secondary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">Why Choose RM's Sampoorna?</span>
                <h2 style="color: #fff; font-size: clamp(2rem, 5vw, 2.4rem); margin: 12px 0 20px;">Prepared With Care & Pure Natural Goodness</h2>
                <p style="color: #d8f3dc; line-height: 1.8; margin-bottom: 24px; font-size: 1.05rem;">
                    At RM's Sampoorna, every pack of masala, health mix, and ragi powder is crafted in small batches to preserve natural oils and nutritional integrity. We believe healthy living starts with unadulterated home cooking.
                </p>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2rem; font-weight: 800;">35+</h3>
                        <p style="color: #b7e4c7; font-size: 0.85rem;">Natural Multigrains</p>
                    </div>
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2rem; font-weight: 800;">100%</h3>
                        <p style="color: #b7e4c7; font-size: 0.85rem;">Chemical Free</p>
                    </div>
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2rem; font-weight: 800;">5000+</h3>
                        <p style="color: #b7e4c7; font-size: 0.85rem;">Happy Households</p>
                    </div>
                </div>
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1544025162-d76694265947?w=700&auto=format&fit=crop" alt="RM Sampoorna Ingredients" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: var(--radius-md); box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
