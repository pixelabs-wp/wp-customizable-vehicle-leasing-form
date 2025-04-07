<?php

/**
 * Leasing form template
 * 
 * This template can be overridden by copying it to yourtheme/leasing-form/leasing-form.php
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="alc-form-container alc-py-8">
    <form id="subscriptionForm" class="alc-card">
        <h1 class="alc-text-2xl alc-font-bold alc-mb-6">Lease a <?php echo esc_html(get_the_title($vehicle_id ?? 0)); ?></h1>

        <!-- Subscription length - will be populated by JS -->
        <div class="alc-mb-6">
            <div class="alc-section-heading">
                Subscription length
                <i class="alc-info-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                        <path d="M12 9h.01" />
                        <path d="M11 12h1v4h1" />
                    </svg></i>
            </div>
            <div id="subscription-options">
                <!-- Options will be inserted here dynamically -->
            </div>
        </div>

        <!-- Insurance - will be populated by JS -->
        <div class="alc-mb-6">
            <div class="alc-section-heading">
                Insurance
                <i class="alc-info-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                        <path d="M12 9h.01" />
                        <path d="M11 12h1v4h1" />
                    </svg></i>
            </div>
            <div id="insurance-options">
                <!-- Options will be inserted here dynamically -->
            </div>
        </div>

        <!-- Monthly mileage allowance - will be populated by JS -->
        <div class="alc-mb-6">
            <div class="alc-section-heading">
                Monthly mileage allowance
                <i class="alc-info-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                        <path d="M12 9h.01" />
                        <path d="M11 12h1v4h1" />
                    </svg></i>
            </div>
            <div id="mileage-options">
                <!-- Options will be inserted here dynamically -->
            </div>
        </div>

        <!-- Total price -->
        <div class="alc-flex alc-justify-between alc-items-center alc-mb-6">
            <div>
                <div class="alc-text-sm alc-text-gray-500">Total monthly price</div>
                <div class="alc-price-display" id="totalPrice">AED 1,900</div>
                <div class="alc-price-info">Inclusive of VAT</div>
            </div>
            <div class="alc-text-sm alc-text-gray-500">
                <span id="watchingCounter">3</span> people watching this car
            </div>
        </div>

        <!-- Submit button -->
        <button type="submit" class="alc-submit-btn alc-w-full">Proceed</button>
    </form>
</div>



<script>
    // The JavaScript will be loaded separately via wp_enqueue_script
    // Vehicle data will be available in the 'leasingFormData' variable
    document.addEventListener('DOMContentLoaded', function() {
        // Check if leasingFormData exists
        if (typeof leasingFormData === 'undefined') {
            console.error('Leasing form data not found!');
            return;
        }

        // Initialize the form
        if (typeof initLeasingForm === 'function') {
            initLeasingForm(leasingFormData);
        }
    });
</script>