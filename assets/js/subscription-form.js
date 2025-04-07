/**
 * Vehicle Leasing Form Frontend JavaScript
 */
(function() {
    'use strict';
    
    // SVG for selection indicator
    const originalSvg = `
        <svg class="alc-check-svg" xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
            <path d="M0.664062 6C0.664062 2.68629 3.35035 0 6.66406 0H22.6641C22.6641 12.1503 12.8143 22 0.664062 22V6Z" fill="#01c257"/>
            <circle cx="9.43555" cy="8.3125" r="4.8125" fill="white"/>
        </svg>
    `;
    
    /**
     * Initialize the leasing form with data from WordPress
     */
    function initLeasingForm(formData) {
        // Store form data globally for reference
        window.leasingFormData = formData;
        
        // Format data to match expected structure for rendering
        const subscriptionOptions = formatSubscriptionOptions(formData.subscription_options || []);
        const insuranceOptions = formatInsuranceOptions(formData.insurance_options || []);
        const mileageOptions = formatMileageOptions(formData.mileage_options || []);
        
        // Render all sections
        renderSubscriptionOptions(subscriptionOptions);
        renderInsuranceOptions(insuranceOptions);
        renderMileageOptions(mileageOptions);
        
        // Set watching count
        document.getElementById('watchingCounter').textContent = formData.watching_count || 3;
        
        // Add event listener to the form
        document.getElementById('subscriptionForm').addEventListener('submit', handleSubmit);
        
        // Calculate the initial price
        updateTotalPrice();
    }
    
    /**
     * Format subscription options data to expected structure
     */
    function formatSubscriptionOptions(options) {
        if (!options || !options.length) {
            // Default options if none provided
            return [
                { duration: '3 months', value: '3months', price: 1895, selected: false },
                { duration: '6 months', value: '6months', price: 1845, selected: false },
                { duration: '9 months', value: '9months', price: 1795, selected: true }
            ];
        }
        
        return options.map(option => ({
            duration: option.months + ' months',
            value: option.months + 'months',
            price: parseFloat(option.base_price || 0) + parseFloat(option.price_adjustment || 0),
            selected: option.is_selected || false,
            recommended: option.is_recommended || false
        }));
    }
    
    /**
     * Format insurance options data to expected structure
     */
    function formatInsuranceOptions(options) {
        if (!options || !options.length) {
            // Default options if none provided
            return [
                { type: 'Standard cover', value: 'standard', description: 'Included', additionalCost: 0, selected: true },
                { type: 'Full cover', value: 'full', description: '+ AED 105/month', additionalCost: 105, recommended: true, selected: false }
            ];
        }
        
        return options.map(option => ({
            type: option.name,
            value: option.id || option.name.toLowerCase().replace(/\s+/g, '_'),
            description: option.price_adjustment > 0 ? '+ AED ' + option.price_adjustment + '/month' : 'Included',
            additionalCost: parseFloat(option.price_adjustment || 0),
            recommended: option.is_recommended || false,
            selected: option.is_selected || false
        }));
    }
    
    /**
     * Format mileage options data to expected structure
     */
    function formatMileageOptions(options) {
        if (!options || !options.length) {
            // Default options if none provided
            return [
                { distance: '2,000 km', value: '2000', description: 'AED 2.50 per additional km', selected: false, priceAdjustment: 0, recommended: false },
                { distance: '3,000 km', value: '3000', description: 'AED 2.25 per additional km', selected: true, priceAdjustment: 0, recommended: true },
                { distance: '4,000 km', value: '4000', description: 'AED 2.00 per additional km', selected: false, priceAdjustment: 50, recommended: false },
                { distance: '5,000 km', value: '5000', description: 'AED 1.75 per additional km', selected: false, priceAdjustment: 100, recommended: false }
            ];
        }
        
        return options.map(option => ({
            distance: formatNumber(option.miles) + ' km',
            value: option.miles,
            description: option.description || 'AED ' + (option.extra_km_rate || '2.00') + ' per additional km',
            selected: option.is_selected || false,
            priceAdjustment: parseFloat(option.price_adjustment || 0),
            recommended: option.is_recommended || false
        }));
    }
    
    /**
     * Helper to format numbers with commas
     */
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    /**
     * Render subscription options
     */
    function renderSubscriptionOptions(options) {
        const container = document.getElementById('subscription-options');
        
        // Clear container
        container.innerHTML = '';
        
        // Responsive grid - single column on mobile, two columns on larger screens
        container.className = 'alc-grid alc-gap-4 alc-grid-cols-1 sm:alc-grid-cols-2';
        
        options.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = `alc-subscription-option alc-option-card${option.selected ? ' alc-option-selected' : ''}`;
            optionElement.dataset.value = option.value;
            optionElement.dataset.price = option.price;

            // Add SVG if selected
            if (option.selected) {
                optionElement.insertAdjacentHTML('afterbegin', originalSvg);
            }
            
            // Add recommended tag if recommended
            if (option.recommended) {
                optionElement.insertAdjacentHTML('afterbegin', '<span class="alc-recommended-tag">Recommended</span>');
            }

            const contentHtml = `
                <div>
                    <div class="alc-font-bold alc-text-base sm:alc-text-lg">${option.duration}</div>
                    <div class="alc-text-xs sm:alc-text-sm alc-text-gray-500">AED ${option.price.toLocaleString()}/month</div>
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
    
    /**
     * Render insurance options
     */
    function renderInsuranceOptions(options) {
        const container = document.getElementById('insurance-options');
        
        // Clear container
        container.innerHTML = '';
        
        // Responsive grid - single column on mobile, two columns on larger screens
        container.className = 'alc-grid alc-gap-4 alc-grid-cols-1 sm:alc-grid-cols-2';
        
        options.forEach(option => {
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
                    <div class="alc-font-bold alc-text-base sm:alc-text-lg">${option.type}</div>
                    <div class="alc-text-xs sm:alc-text-sm ${textColorClass}">${option.description}</div>
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
    
    /**
     * Render mileage options
     */
    function renderMileageOptions(options) {
        const container = document.getElementById('mileage-options');
        
        // Clear container
        container.innerHTML = '';
        
        // Responsive grid - single column on mobile, two columns on larger screens
        container.className = 'alc-grid alc-gap-4 alc-grid-cols-1 sm:alc-grid-cols-2';
        
        options.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = `alc-mileage-option alc-option-card${option.selected ? ' alc-option-selected' : ''}`;
            optionElement.dataset.value = option.value;
            optionElement.dataset.priceAdjustment = option.priceAdjustment;

            // Add SVG if selected
            if (option.selected) {
                optionElement.insertAdjacentHTML('afterbegin', originalSvg);
            }
            
            // Add recommended tag if recommended
            if (option.recommended) {
                optionElement.insertAdjacentHTML('afterbegin', '<span class="alc-recommended-tag">Recommended</span>');
            }

            const contentHtml = `
                <div>
                    <div class="alc-font-bold alc-text-base sm:alc-text-lg">${option.distance}</div>
                    <div class="alc-text-xs sm:alc-text-sm alc-text-gray-500">${option.description}</div>
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
    
    /**
     * Helper function to select an option
     */
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
    
    /**
     * Update the selected data in the formData object
     */
    function updateSelectedData(section, value) {
        const formData = window.leasingFormData;
        
        if (section === 'subscriptionOptions' && formData.subscription_options) {
            formData.subscription_options.forEach(option => {
                option.is_selected = (option.months + 'months' === value);
            });
        } else if (section === 'insuranceOptions' && formData.insurance_options) {
            formData.insurance_options.forEach(option => {
                const optionValue = option.id || option.name.toLowerCase().replace(/\s+/g, '_');
                option.is_selected = (optionValue === value);
            });
        } else if (section === 'mileageOptions' && formData.mileage_options) {
            formData.mileage_options.forEach(option => {
                option.is_selected = (option.miles === value);
            });
        }
    }
    
    /**
     * Function to update the total price based on selections
     */
    function updateTotalPrice() {
        // Get selected subscription
        const selectedSubscription = document.querySelector('.alc-subscription-option.alc-option-selected');
        let baseMonthlyPrice = selectedSubscription ? parseFloat(selectedSubscription.dataset.price) : 1795;
        
        // Get selected insurance
        const selectedInsurance = document.querySelector('.alc-insurance-option.alc-option-selected');
        let insuranceCost = selectedInsurance ? parseFloat(selectedInsurance.dataset.price) : 0;
        
        // Get selected mileage
        const selectedMileage = document.querySelector('.alc-mileage-option.alc-option-selected');
        let mileageAdjustment = selectedMileage ? parseFloat(selectedMileage.dataset.priceAdjustment) : 0;
        
        // Calculate total price
        let totalPrice = baseMonthlyPrice + insuranceCost + mileageAdjustment;
        
        // Format the price with a thousand separator
        const formattedPrice = totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        document.getElementById('totalPrice').textContent = 'AED ' + formattedPrice;
    }
    
    /**
     * Handle form submission
     */
    function handleSubmit(e) {
        e.preventDefault();
        
        // Get selected options
        const selectedSubscription = document.querySelector('.alc-subscription-option.alc-option-selected');
        const selectedInsurance = document.querySelector('.alc-insurance-option.alc-option-selected');
        const selectedMileage = document.querySelector('.alc-mileage-option.alc-option-selected');
        
        const subscriptionText = selectedSubscription ? selectedSubscription.querySelector('.alc-text-lg').textContent : '';
        const insuranceText = selectedInsurance ? selectedInsurance.querySelector('.alc-text-lg').textContent : '';
        const mileageText = selectedMileage ? selectedMileage.querySelector('.alc-text-lg').textContent : '';
        const totalPrice = document.getElementById('totalPrice').textContent;
        
        // Check for nonce
        const nonceField = document.getElementById('leasing_form_nonce');
        const nonce = nonceField ? nonceField.value : '';
        
        if (!nonce) {
            console.error('Security token missing. Cannot process form.');
            return;
        }
        
        // Data to be sent
        const submissionData = {
            vehicle_id: window.leasingFormData.vehicle_id || 0,
            vehicle_title: window.leasingFormData.vehicle_title || '',
            nonce: nonce,
            subscription: {
                duration: subscriptionText,
                value: selectedSubscription ? selectedSubscription.dataset.value : '',
                price: selectedSubscription ? selectedSubscription.dataset.price : 0
            },
            insurance: {
                type: insuranceText,
                value: selectedInsurance ? selectedInsurance.dataset.value : '',
                price: selectedInsurance ? selectedInsurance.dataset.price : 0
            },
            mileage: {
                distance: mileageText,
                value: selectedMileage ? selectedMileage.dataset.value : '',
                priceAdjustment: selectedMileage ? selectedMileage.dataset.priceAdjustment : 0
            },
            total_price: totalPrice
        };
        
        // WhatsApp integration
        // Get the WhatsApp number from the data or use a default
        const phoneNumber = window.leasingFormData.whatsapp_number || '923105054025';
        
        // Format the message for WhatsApp
        let message = `Hello! I'm interested in leasing a ${submissionData.vehicle_title}.\n\n`;
        message += `Selected options:\n`;
        message += `• Subscription: ${subscriptionText}\n`;
        message += `• Insurance: ${insuranceText}\n`;
        message += `• Monthly mileage: ${mileageText}\n`;
        message += `• Total monthly price: ${totalPrice}\n\n`;
        message += `Please contact me to finalize this subscription. Thank you!`;
        
        // URL encode the message and create the WhatsApp URL
        const encodedMessage = encodeURIComponent(message);
        const whatsappURL = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
        
        // Log the submission data
        console.log('Subscription:', subscriptionText);
        console.log('Insurance:', insuranceText);
        console.log('Mileage:', mileageText);
        console.log('Full data:', submissionData);
        
        // Open WhatsApp in a new tab
        window.open(whatsappURL, '_blank');
    }
    
    // Add tooltip functionality for info icons
    document.addEventListener('DOMContentLoaded', function() {
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
    });
    
    // Make the initialization function available globally
    window.initLeasingForm = initLeasingForm;
    
})(); 