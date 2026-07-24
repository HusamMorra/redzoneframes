// handles things that are used all over the site namely 1) the mobile hamburger menu toggle and 2) the total sales bar chart on the admin dashboard (chart.js)
document.addEventListener('DOMContentLoaded', function () {
    // mobile nav toggle 
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function () {
            mainNav.classList.toggle('nav-open');
        });
    }

// grabbing the canvas element the chart draws into on admin/dashboard.php
const salesChartCanvas = document.getElementById('salesChart');

// only building the chart if the canvas exists and theres real data to show. window.salesLabels/salesData get set by dashboard.php before this runs
if (salesChartCanvas && window.salesLabels && window.salesLabels.length > 0) {
    new Chart(salesChartCanvas, {
        type: 'bar',
        data: {
            labels: window.salesLabels,
            datasets: [{
                label: 'Total Sales ($)',
                data: window.salesData,
                backgroundColor: '#D50A0A',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
}

// live price update on product page 
    // recalculates the price whenever frame color, size, or quantity changes
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        const basePrice = parseFloat(document.getElementById('basePrice').value);
        const frameColorSelect = document.getElementById('frameColor');
        const frameSizeSelect = document.getElementById('frameSize');
        const quantityInput = document.getElementById('quantity');
        const displayPrice = document.getElementById('displayPrice');
        const btnPrice = document.getElementById('btnPrice');

        function updatePrice() {
            const colorModifier = parseFloat(frameColorSelect.selectedOptions[0].dataset.modifier) || 0;
            const sizeModifier = parseFloat(frameSizeSelect.selectedOptions[0].dataset.modifier) || 0;
            const quantity = parseInt(quantityInput.value) || 1;
            const unitPrice = basePrice + colorModifier + sizeModifier;
            const total = unitPrice * quantity;

            // displayPrice shows the price for ONE frame with these options but btnPrice on the button shows the total for however many theyre buying
            displayPrice.textContent = '$' + unitPrice.toFixed(2);
            btnPrice.textContent = '$' + total.toFixed(2);
        }

        frameColorSelect.addEventListener('change', updatePrice);
        frameSizeSelect.addEventListener('change', updatePrice);
        quantityInput.addEventListener('input', updatePrice);
    }
});