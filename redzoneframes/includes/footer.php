<?php
// this closes off the main tag that got opened in header.php
// then just the footer html and script tags after that. the footer is used on every page and allows the customer to navigate the site easier
?>
</main>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-col">
            <h3>RED ZONE <span>FRAMES</span></h3>
            <p>Custom NFL player frames for the biggest fans. From today's stars to retro legends.</p>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="/redzoneframes/shop.php">Shop</a></li>
                <li><a href="/redzoneframes/design-your-own.php">Design Your Own</a></li>
                <li><a href="/redzoneframes/size-guide.php">Size Guide</a></li>
                <li><a href="/redzoneframes/shipping-returns.php">Shipping &amp; Returns</a></li>
				<li><a href="/redzoneframes/privacy-policy.php">Privacy Policy</a></li>
            </ul>
        </div>
       <div class="footer-col">
    <h4>Help</h4>
    <ul>
        <li><a href="/redzoneframes/wiki/index.php">Help Center</a></li>
        <li><a href="/redzoneframes/contact.php">Contact Us</a></li>
    </ul>
</div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Red Zone Frames. All rights reserved. Built for COMP 3340.</p>
    </div>
</footer>
<script src="/redzoneframes/js/main.js"></script>
<script src="/redzoneframes/js/designer.js"></script>
</body>
</html>