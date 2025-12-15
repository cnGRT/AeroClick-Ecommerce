<?php 
require_once __DIR__ . '/includes/init.php';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>About AeroClick</h1>
        <p class="subtitle">Precision. Performance. Passion.</p>
    </div>

    <div class="about-content">
        <section class="mission-section">
            <h2>Our Mission</h2>
            <p>AeroClick was born from a simple observation: gamers deserve better. In a market flooded with generic electronics retailers, finding the perfect gaming mouse had become an overwhelming challenge. We set out to change that.</p>
            
            <p>We are a specialized e-commerce platform dedicated exclusively to gaming mice enthusiasts and professional users. Our mission is to provide a trusted destination where precision meets performance, and where every click matters.</p>
        </section>

        <section class="story-section">
            <h2>Our Story</h2>
            <p>Founded in 2024 by a team of passionate gamers and tech enthusiasts, AeroClick emerged from countless hours spent researching, testing, and comparing gaming mice. We experienced firsthand the frustration of sifting through incomplete specifications, biased reviews, and marketing hype.</p>
            
            <p>We realized that gamers needed a platform that understood their specific needs - whether you're a competitive esports athlete requiring lightning-fast response times, an MMO player needing multiple programmable buttons, or a creative professional seeking ergonomic comfort for long work sessions.</p>
        </section>

        <!-- Baidu Map Section -->
        <section class="map-section">
            <h2>Our Australian TAFE Connection</h2>
            <p>AeroClick proudly collaborates with Australian TAFE institutions to foster innovation in gaming technology and support the next generation of esports professionals.</p>
            
            <div class="map-container">
                <!-- Baidu Map Container -->
                <div id="baidu-map" style="width: 100%; height: 400px; border-radius: 8px; overflow: hidden;"></div>
                <div class="map-instructions">
                    <p><small>The map shows locations of major TAFE institutes in Australia. Use mouse drag and scroll wheel to zoom for details.</small></p>
                </div>
            </div>
            
            <div class="tafe-info">
                <h3>TAFE (Technical and Further Education)</h3>
                <p>TAFE is Australia's leading provider of vocational education and training, offering qualifications from certificates to advanced diplomas in fields including Information Technology, Digital Media, and Game Development.</p>
                
                <div class="tafe-locations">
                    <h4>Major TAFE Institute Locations:</h4>
                    <ul>
                        <li><strong>TAFE NSW Sydney</strong> - Sydney, New South Wales</li>
                        <li><strong>TAFE Victoria Melbourne</strong> - Melbourne, Victoria</li>
                        <li><strong>TAFE Queensland Brisbane</strong> - Brisbane, Queensland</li>
                        <li><strong>North Metropolitan TAFE Perth</strong> - Perth, Western Australia</li>
                        <li><strong>TAFE SA Adelaide</strong> - Adelaide, South Australia</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="features-section">
            <h2>Why Choose AeroClick?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>🎯 Expert Curation</h3>
                    <p>Every mouse in our catalog is carefully selected and tested. We work directly with reputable brands and authorized distributors to ensure authenticity and quality.</p>
                </div>
                
                <div class="feature-card">
                    <h3>📊 Detailed Specifications</h3>
                    <p>Compare DPI ranges, polling rates, sensor types, weight, connectivity, and more with our standardized, comprehensive product data.</p>
                </div>
                
                <div class="feature-card">
                    <h3>👥 Community Driven</h3>
                    <p>Read authentic reviews from verified purchasers. Our community of gamers provides real-world insights you can trust.</p>
                </div>
                
                <div class="feature-card">
                    <h3>⚡ Advanced Tools</h3>
                    <p>Use our comparison tool to side-by-side compare up to 3 products. Filter by your specific needs and find your perfect match.</p>
                </div>
            </div>
        </section>

        <section class="values-section">
            <h2>Our Values</h2>
            <div class="values-list">
                <div class="value-item">
                    <strong>Transparency:</strong> No hidden specs, no marketing fluff. Just honest, detailed information.
                </div>
                <div class="value-item">
                    <strong>Expertise:</strong> We live and breathe gaming peripherals. Our knowledge is your advantage.
                </div>
                <div class="value-item">
                    <strong>Community:</strong> We believe in the power of shared knowledge and authentic experiences.
                </div>
                <div class="value-item">
                    <strong>Innovation:</strong> Constantly evolving to provide better tools and more comprehensive data.
                </div>
            </div>
        </section>

        <section class="team-section">
            <h2>Join Our Community</h2>
            <p>AeroClick is more than just a store - it's a growing community of gamers, streamers, and tech enthusiasts who share a passion for precision gaming equipment. Create an account to:</p>
            <ul>
                <li>Save your favorite mice for later comparison</li>
                <li>Write reviews and help fellow gamers</li>
                <li>Track your order history</li>
                <li>Get personalized recommendations</li>
                <li>Participate in community discussions</li>
            </ul>
        </section>

        <div class="cta-section">
            <h3>Ready to Find Your Perfect Mouse?</h3>
            <p>Start exploring our curated collection of high-performance gaming mice today.</p>
            <a href="<?= PRODUCTS_URL ?>/" class="btn">Browse Products</a>
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

.about-content section {
    margin-bottom: 3rem;
    padding: 2rem;
    background: #1a1a1a;
    border-radius: 10px;
    border: 1px solid #333;
}

.about-content h2 {
    color: #00d9ff;
    margin-bottom: 1rem;
    border-bottom: 2px solid #00d9ff;
    padding-bottom: 0.5rem;
}

/* Map Section Styles */
.map-section {
    background: #1a1a1a;
}

.map-container {
    margin: 1.5rem 0;
    position: relative;
}

.map-instructions {
    margin-top: 0.5rem;
    text-align: center;
    color: #888;
}

.tafe-info {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #252525;
    border-radius: 8px;
}

.tafe-info h3 {
    color: #00d9ff;
    margin-bottom: 1rem;
}

.tafe-locations {
    margin-top: 1.5rem;
}

.tafe-locations h4 {
    color: #00ff88;
    margin-bottom: 1rem;
}

.tafe-locations ul {
    list-style: none;
    padding-left: 0;
}

.tafe-locations li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
    border-bottom: 1px solid #333;
}

.tafe-locations li:last-child {
    border-bottom: none;
}

.tafe-locations li:before {
    content: "📍";
    position: absolute;
    left: 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.feature-card {
    background: #252525;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #00d9ff;
}

.feature-card h3 {
    color: #00d9ff;
    margin-bottom: 0.8rem;
}

.values-list {
    display: grid;
    gap: 1rem;
}

.value-item {
    padding: 1rem;
    background: #252525;
    border-radius: 6px;
    border-left: 3px solid #00ff88;
}

.team-section ul {
    list-style: none;
    padding-left: 0;
}

.team-section li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
}

.team-section li:before {
    content: "✓";
    color: #00ff88;
    position: absolute;
    left: 0;
    font-weight: bold;
}

.cta-section {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 10px;
    border: 1px solid #00d9ff;
}

.cta-section h3 {
    color: #00d9ff;
    margin-bottom: 1rem;
}
</style>

<!-- Baidu Maps API -->
<script type="text/javascript" src="https://api.map.baidu.com/api?v=3.0&ak=yroe1Iz7noWW0AOkRLHW6ySCQaoYcP3J"></script>
<script type="text/javascript">
// Baidu Maps API Implementation
// Note: Make sure 'grant.fwh.is' is added to your AK's Referrer Whitelist in the Baidu console.

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map instance
    var map = new BMap.Map("baidu-map");
    
    // Set center coordinates (Central Australia)
    var point = new BMap.Point(133.7751, -25.2744);
    map.centerAndZoom(point, 4);
    
    // Enable scroll wheel zoom
    map.enableScrollWheelZoom(true);
    
    // Add zoom control
    map.addControl(new BMap.ZoomControl());
    
    // Add map type control
    map.addControl(new BMap.MapTypeControl());
    
    // Add markers for major Australian TAFE locations
    var tafeLocations = [
        {name: "TAFE NSW Sydney", point: new BMap.Point(151.2070, -33.8688), city: "Sydney"},
        {name: "TAFE Victoria Melbourne", point: new BMap.Point(144.9631, -37.8136), city: "Melbourne"},
        {name: "TAFE Queensland Brisbane", point: new BMap.Point(153.0251, -27.4698), city: "Brisbane"},
        {name: "North Metropolitan TAFE Perth", point: new BMap.Point(115.8613, -31.9523), city: "Perth"},
        {name: "TAFE SA Adelaide", point: new BMap.Point(138.6007, -34.9285), city: "Adelaide"}
    ];
    
    // Add marker for each TAFE location
    tafeLocations.forEach(function(location) {
        var marker = new BMap.Marker(location.point);
        map.addOverlay(marker);
        
        // Create info window
        var infoWindow = new BMap.InfoWindow(
            "<strong>" + location.name + "</strong><br/>" +
            location.city + ", Australia<br/>" +
            "Technical and Further Education"
        );
        
        // Add click event listener
        marker.addEventListener("click", function() {
            this.openInfoWindow(infoWindow);
        });
    });
    
    // Add simplified Australia boundary for visual reference
    var boundaryStyle = {
        strokeColor: "#00d9ff",
        fillColor: "",
        strokeWeight: 2,
        strokeOpacity: 0.5
    };
    
    // Simplified Australia bounding coordinates
    var australiaBounds = [
        new BMap.Point(113.338953, -10.683333),
        new BMap.Point(153.569286, -10.683333),
        new BMap.Point(153.569286, -39.130423),
        new BMap.Point(113.338953, -39.130423)
    ];
    
    var polygon = new BMap.Polygon(australiaBounds, boundaryStyle);
    map.addOverlay(polygon);
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>