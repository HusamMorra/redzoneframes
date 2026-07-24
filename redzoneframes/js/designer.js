// handles the live price calculator on design-your-own.php and it recalculates the total whenever frame color, size, quantity, or the engraving checkbox changes. 

// kept separate from main.js since this page has its own thing going on that the other pages dont need

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('designerForm');
    if (!form) return; // this script only matters on design-your-own.php

    const basePrice = parseFloat(document.getElementById('basePrice').value);
    const frameColorSelect = document.getElementById('frameColor');
    const frameSizeSelect = document.getElementById('frameSize');
    const engravingCheckbox = document.getElementById('addEngraving');
    const engravingTextWrap = document.getElementById('engravingTextWrap');
	const quantityInput = document.getElementById('quantity');
    const totalDisplay = document.getElementById('designerTotal');

function updateTotal() {
        const colorModifier = parseFloat(frameColorSelect.selectedOptions[0].dataset.modifier) || 0;
        const sizeModifier = parseFloat(frameSizeSelect.selectedOptions[0].dataset.modifier) || 0;
        const engravingModifier = engravingCheckbox.checked ? parseFloat(engravingCheckbox.dataset.modifier) : 0;
        const quantity = parseInt(quantityInput.value) || 1;
        const total = (basePrice + colorModifier + sizeModifier + engravingModifier) * quantity;
        totalDisplay.textContent = '$' + total.toFixed(2);
    }

    // only shows the nameplate text box once the checkbox is actually checked
    function toggleEngravingField() {
        engravingTextWrap.style.display = engravingCheckbox.checked ? 'block' : 'none';
    }

    frameColorSelect.addEventListener('change', updateTotal);
    frameSizeSelect.addEventListener('change', updateTotal);
	 quantityInput.addEventListener('input', updateTotal);
    engravingCheckbox.addEventListener('change', function () {
        updateTotal();
        toggleEngravingField();
    });

    // running this once on load too, incase the browser autofills the checkbox as checked after a refresh, so the text box matches
    toggleEngravingField();
});