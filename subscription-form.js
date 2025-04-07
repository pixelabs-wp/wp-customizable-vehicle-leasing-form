document.addEventListener('DOMContentLoaded', function() {
    // Original SVG for selection indicators
    const originalSvg = `
        <svg class="alc-check-svg" xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
            <path d="M0.664062 6C0.664062 2.68629 3.35035 0 6.66406 0H22.6641C22.6641 12.1503 12.8143 22 0.664062 22V6Z" fill="#01c257"/>
            <circle cx="9.43555" cy="8.3125" r="4.8125" fill="white"/>
        </svg>
    `;

    // Form data for dynamic generation
    const formData = {
        subscriptionOptions: [
            { 
                duration: '3 months', 
                value: '3months', 
                price: 1895, 
                selected: false
            },
            { 
                duration: '6 months', 
                value: '6months', 
                price: 1845, 
                selected: false
            },
            { 
                duration: '9 months', 
                value: '9months', 
                price: 1795, 
                selected: true
            }
        ],
        insuranceOptions: [
            { 
                type: 'Standard cover', 
                value: 'standard', 
                description: 'Included', 
                additionalCost: 0, 
                selected: true
            },
            { 
                type: 'Full cover', 
                value: 'full', 
                description: '+ AED 105/month', 
                additionalCost: 105, 
                recommended: true, 
                selected: false
            }
        ],
        mileageOptions: [
            { 
                distance: '2,000 km', 
                value: '2000', 
                description: 'AED 2.50 per additional km', 
                selected: false,
                priceAdjustment: 0
            },
            { 
                distance: '3,000 km', 
                value: '3000', 
                description: 'AED 2.25 per additional km', 
                selected: true,
                priceAdjustment: 0
            },
            { 
                distance: '4,000 km', 
                value: '4000', 
                description: 'AED 2.00 per additional km', 
                selected: false,
                priceAdjustment: 50
            },
            { 
                distance: '5,000 km', 
                value: '5000', 
                description: 'AED 1.75 per additional km', 
                selected: false,
                priceAdjustment: 100
            }
        ],
        watchingCount: 3
    };

    // Render the subscription options
    function renderSubscriptionOptions() {
        const container = document.getElementById('subscription-options');
        container.innerHTML = '';
        
        // Set appropriate grid columns based on number of options
        const optionCount = formData.subscriptionOptions.length;
        if (optionCount === 1) {
            container.className = 'alc-grid alc-grid-cols-1 alc-gap-4';
        } else if (optionCount === 2) {
            container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
        } else {
            container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
        }
        
        formData.subscriptionOptions.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = `alc-subscription-option alc-option-card${option.selected ? ' alc-option-selected' : ''}`;
            optionElement.dataset.value = option.value;
            optionElement.dataset.price = option.price;

            // Add SVG if selected
            if (option.selected) {
                optionElement.insertAdjacentHTML('afterbegin', originalSvg);
            }

            const contentHtml = `
                <div>
                    <div class="alc-font-bold alc-text-lg">${option.duration}</div>
                    <div class="alc-text-sm alc-text-gray-500">AED ${option.price.toLocaleString()}/month</div>
                </div>
            `;
            optionElement.insertAdjacentHTML('beforeend', contentHtml);
            container.appendChild(optionElement);
        });

        // Add event listeners
        const subscriptionOptions = document.querySelectorAll('.alc-subscription-option');
        subscriptionOptions.forEach(option => {
            option.addEventListener('click', function() {
                selectOption(this, subscriptionOptions);
                updateSelectedData('subscriptionOptions', this.dataset.value);
                updateTotalPrice();
            });
        });
    }

    // Render the insurance options
    function renderInsuranceOptions() {
        const container = document.getElementById('insurance-options');
        container.innerHTML = '';
        
        // Set appropriate grid columns based on number of options
        const optionCount = formData.insuranceOptions.length;
        if (optionCount === 1) {
            container.className = 'alc-grid alc-grid-cols-1 alc-gap-4';
        } else if (optionCount === 2) {
            container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
        } else {
            container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
        }
        
        formData.insuranceOptions.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = `alc-insurance-option alc-option-card${option.selected ? ' alc-option-selected' : ''}`;
            optionElement.dataset.value = option.value;
            optionElement.dataset.price = option.additionalCost;

            // Add SVG if selected
            if (option.selected) {
                optionElement.insertAdjacentHTML('afterbegin', originalSvg);
            }

            // Add recommended tag if recommended
            if (option.recommended) {
                optionElement.insertAdjacentHTML('afterbegin', '<span class="alc-recommended-tag">Recommended</span>');
            }

            const textColorClass = option.additionalCost > 0 ? 'alc-text-purple-500' : 'alc-text-gray-500';
            const contentHtml = `
                <div>
                    <div class="alc-font-bold alc-text-lg">${option.type}</div>
                    <div class="alc-text-sm ${textColorClass}">${option.description}</div>
                </div>
            `;
            optionElement.insertAdjacentHTML('beforeend', contentHtml);
            container.appendChild(optionElement);
        });

        // Add event listeners
        const insuranceOptions = document.querySelectorAll('.alc-insurance-option');
        insuranceOptions.forEach(option => {
            option.addEventListener('click', function() {
                selectOption(this, insuranceOptions);
                updateSelectedData('insuranceOptions', this.dataset.value);
                updateTotalPrice();
            });
        });
    }

    // Render the mileage options
    function renderMileageOptions() {
        const container = document.getElementById('mileage-options');
        container.innerHTML = '';
        
        // Set appropriate grid columns based on number of options
        const optionCount = formData.mileageOptions.length;
        if (optionCount === 1) {
            container.className = 'alc-grid alc-grid-cols-1 alc-gap-4';
        } else if (optionCount === 2) {
            container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
        } else {
            container.className = 'alc-grid alc-grid-cols-2 alc-gap-4';
        }
        
        formData.mileageOptions.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = `alc-mileage-option alc-option-card${option.selected ? ' alc-option-selected' : ''}`;
            optionElement.dataset.value = option.value;

            // Add SVG if selected
            if (option.selected) {
                optionElement.insertAdjacentHTML('afterbegin', originalSvg);
            }

            const contentHtml = `
                <div>
                    <div class="alc-font-bold alc-text-lg">${option.distance}</div>
                    <div class="alc-text-sm alc-text-gray-500">${option.description}</div>
                </div>
            `;
            optionElement.insertAdjacentHTML('beforeend', contentHtml);
            container.appendChild(optionElement);
        });

        // Add event listeners
        const mileageOptions = document.querySelectorAll('.alc-mileage-option');
        mileageOptions.forEach(option => {
            option.addEventListener('click', function() {
                selectOption(this, mileageOptions);
                updateSelectedData('mileageOptions', this.dataset.value);
                updateTotalPrice();
            });
        });
    }

    // Helper function to select an option
    function selectOption(selectedOption, allOptions) {
        // Remove selection from all options
        allOptions.forEach(opt => {
            opt.classList.remove('alc-option-selected');
            // Remove any SVG or indicator elements
            const checkSvg = opt.querySelector('.alc-check-svg');
            if (checkSvg) checkSvg.remove();
        });
        
        // Add selection to clicked option
        selectedOption.classList.add('alc-option-selected');
        
        // Add SVG checkmark to selected option
        if (!selectedOption.querySelector('.alc-check-svg')) {
            selectedOption.insertAdjacentHTML('afterbegin', originalSvg);
        }
    }

    // Update the selected data in the formData object
    function updateSelectedData(section, value) {
        if (section === 'subscriptionOptions') {
            formData.subscriptionOptions.forEach(option => {
                option.selected = (option.value === value);
            });
        } else if (section === 'insuranceOptions') {
            formData.insuranceOptions.forEach(option => {
                option.selected = (option.value === value);
            });
        } else if (section === 'mileageOptions') {
            formData.mileageOptions.forEach(option => {
                option.selected = (option.value === value);
            });
        }
    }

    // Function to update the total price based on selections
    function updateTotalPrice() {
        // Get selected subscription
        const selectedSubscription = formData.subscriptionOptions.find(option => option.selected);
        let baseMonthlyPrice = selectedSubscription ? selectedSubscription.price : 1795;
        
        // Get selected insurance
        const selectedInsurance = formData.insuranceOptions.find(option => option.selected);
        
        // Get selected mileage
        const selectedMileage = formData.mileageOptions.find(option => option.selected);
        
        let totalPrice = baseMonthlyPrice;
        
        // Add insurance cost if applicable
        if (selectedInsurance && selectedInsurance.additionalCost > 0) {
            totalPrice += selectedInsurance.additionalCost;
        }
        
        // Add mileage price adjustment if applicable
        if (selectedMileage && selectedMileage.priceAdjustment > 0) {
            totalPrice += selectedMileage.priceAdjustment;
        }
        
        // Apply combination-specific price adjustments
        if (selectedSubscription.value === '9months' && selectedInsurance.value === 'full' && 
            selectedMileage.value === '5000') {
            // Special discount for 9-month subscription with full insurance and 5000 km
            totalPrice -= 50;
        }
        
        // Format the price with a thousand separator
        const formattedPrice = totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        document.getElementById('totalPrice').textContent = 'AED ' + formattedPrice;
    }

    // Form submission
    document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get selected options
        const selectedSubscription = formData.subscriptionOptions.find(option => option.selected);
        const selectedInsurance = formData.insuranceOptions.find(option => option.selected);
        const selectedMileage = formData.mileageOptions.find(option => option.selected);
        
        console.log('Subscription:', selectedSubscription.duration);
        console.log('Insurance:', selectedInsurance.type);
        console.log('Mileage:', selectedMileage.distance);
        
        alert('Subscription successful!');
    });
    
    // Initialize watching counter
    document.getElementById('watchingCounter').textContent = formData.watchingCount;
    
    // Add tooltip functionality for info icons
    const infoIcons = document.querySelectorAll('.alc-info-icon');
    infoIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'alc-tooltip';
            tooltip.style.top = '-35px';
            tooltip.style.left = '10px';
            
            if (this.parentElement.textContent.includes('Insurance')) {
                tooltip.textContent = 'Choose the insurance coverage level for your subscription.';
            } else if (this.parentElement.textContent.includes('Monthly mileage')) {
                tooltip.textContent = 'Maximum kilometers you can drive each month without additional charges.';
            } else if (this.parentElement.textContent.includes('Subscription length')) {
                tooltip.textContent = 'Choose how long you want to subscribe to this vehicle.';
            }
            
            this.appendChild(tooltip);
        });
        
        icon.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.alc-tooltip');
            if (tooltip) tooltip.remove();
        });
    });

    // Initialize the form with data
    renderSubscriptionOptions();
    renderInsuranceOptions();
    renderMileageOptions();
    updateTotalPrice();
}); 