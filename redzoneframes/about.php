<?php
session_start();
// about.php
// Static page describing the business. Satisfies rubric item 1
// ("A description at least a paragraph describing what this project is about").

$pageTitle = "About - Red Zone Frames";
$pageDescription = "Learn about Red Zone Frames, a custom NFL player frame shop built for the 2026 season and beyond.";
require_once __DIR__ . '/includes/header.php';
?>

<section class="container section static-page">
    <h1>About Red Zone Frames</h1>

    <p>Red Zone Frames is a custom framing shop built around the idea that every football fan deserves a piece of the game that actually looks unique and can fit on any wall or shelf. We design and produce framed player pieces for both today's biggest names and the legends who built the sport before them, and we let fans who want something totally their own build it from scratch through our Design Your Own tool.</p>

    <p>Every frame starts as original artwork, a player's name, number, and team colors, UV-printed onto matboard and mounted inside a real wood-grain shadowbox with a clear acrylic front. Nothing is a stock photo or a licensed jersey print, every piece is something we design ourselves.</p>

    <p>This site was built for a COMP 3340 project. While Red Zone Frames is a fictional business built for the assignment, the idea is real and I have sold many of these frames but 3D printed using PLA instead so all features fully work as this website can be used in the future.</p>

<p>Have a question, a custom idea, or a player you think we should carry? See our <a href="contact.php" class="text-link">Contact page</a> or <a href="request-player.php" class="text-link">request a player</a> - we're always expanding the roster.</p>
</section>

<p>The 20 player frame graphics used throughout the catalog were generated with the help of AI image generation. Reference: OpenAI. (2026). ChatGPT (GPT-5.5) [Large language model]. https://chatgpt.com</p>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
