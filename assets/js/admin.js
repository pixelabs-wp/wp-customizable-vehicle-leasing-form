/**
 * Admin JavaScript for the Vehicle Leasing Form plugin
 */
(function($) {
    'use strict';
    
    // Initialize when the DOM is fully loaded
    $(document).ready(function() {
        initOptionManagement();
    });
    
    /**
     * Initialize option management for all option types
     */
    function initOptionManagement() {
        // Subscription options management
        initOptionsForType('subscription');
        
        // Insurance options management
        initOptionsForType('insurance');
        
        // Mileage options management
        initOptionsForType('mileage');
    }
    
    /**
     * Initialize options for a specific type
     */
    function initOptionsForType(type) {
        const container = $('#' + type + '-options-list');
        const addButton = $('.add-' + type + '-option');
        const template = $('#tmpl-' + type + '-option').html();
        
        // Add option event
        addButton.on('click', function() {
            const optionCount = container.find('.leasing-option-item').length;
            const newOption = template.replace(/\{\{data\.index\}\}/g, optionCount);
            
            container.append(newOption);
            updateOptionIndexes(container, type);
        });
        
        // Remove option event (delegated)
        container.on('click', '.remove-option', function() {
            $(this).closest('.leasing-option-item').remove();
            updateOptionIndexes(container, type);
        });
        
        // Make the options sortable
        container.sortable({
            handle: 'h4',
            cursor: 'move',
            update: function() {
                updateOptionIndexes(container, type);
            }
        });
    }
    
    /**
     * Update the indexes of options after sorting or removal
     */
    function updateOptionIndexes(container, type) {
        container.find('.leasing-option-item').each(function(index) {
            // Update the option number in the header
            $(this).find('.option-number').text('#' + (index + 1));
            
            // Update all input names
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(new RegExp(type + '_options\\[\\d+\\]', 'g'), type + '_options[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }
    
    /**
     * Form validation for required fields before submitting
     */
    $('#post').on('submit', function(e) {
        let valid = true;
        
        // Check required fields in subscription options
        $('#subscription-options-list').find('input[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Check required fields in insurance options
        $('#insurance-options-list').find('input[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Check required fields in mileage options
        $('#mileage-options-list').find('input[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Prevent form submission if there are errors
        if (!valid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.error').first().offset().top - 100
            }, 500);
            
            alert('Please fill out all required fields for the vehicle leasing options.');
        }
    });
    
})(jQuery); 