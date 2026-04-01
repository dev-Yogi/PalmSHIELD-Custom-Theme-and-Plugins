(function() {
    'use strict';
    
    // Use a more robust ready state check
    function domReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    function initGateDataCollection() {
        console.log('Initializing gate data collection...');
        
        // Try to find the Add to Quote button with retry logic
        function attachToQuoteButton(retries = 0) {
            const addToQuoteBtn = document.querySelector('.add-request-quote-button');
            
            if (addToQuoteBtn) {
                console.log('Add to Quote button found, attaching listener');
                
                // Remove any existing listeners to prevent duplicates
                const newBtn = addToQuoteBtn.cloneNode(true);
                addToQuoteBtn.parentNode.replaceChild(newBtn, addToQuoteBtn);
                
                newBtn.addEventListener('click', function(e) {
                    console.log('Add to Quote clicked');
                    
                    // Small delay to ensure any dynamic content is loaded
                    setTimeout(function() {
                        const gateData = gatherGateData();
                        
                        if (gateData && gateData.length > 0) {
                            const gateDataString = JSON.stringify(gateData);
                            try {
                                localStorage.setItem('pendingGateData', gateDataString);
                                console.log('Gate data stored in localStorage:', gateDataString);
                            } catch (error) {
                                console.error('Error storing gate data:', error);
                            }
                        } else {
                            console.log('No gate data to store');
                        }
                    }, 100);
                });
            } else if (retries < 10) {
                console.log('Add to Quote button not found, retrying... (attempt ' + (retries + 1) + ')');
                setTimeout(function() {
                    attachToQuoteButton(retries + 1);
                }, 500);
            } else {
                console.log('Add to Quote button not found after multiple retries');
            }
        }
        
        attachToQuoteButton();
    }

    function gatherGateData() {
        console.log('Starting to gather gate data...');
        
        // First check if gates are enabled
        const addGatesSelect = document.getElementById('add_gates');
        
        if (!addGatesSelect) {
            console.log('Gates selector (#add_gates) not found');
            return [];
        }
        
        if (addGatesSelect.value !== 'yes') {
            console.log('Gates not enabled (value is: ' + addGatesSelect.value + ')');
            return [];
        }

        let gateData = [];
        const gateElements = document.querySelectorAll('.gate-option');
        console.log('Found gate elements:', gateElements.length);

        gateElements.forEach(function(element, index) {
            try {
                const type = element.querySelector('select[id^="gate_type_"]')?.value;
                const height = element.querySelector('input[id^="gate_height_"]')?.value;
                const space = element.querySelector('input[id^="gate_space_"]')?.value;
                const quantity = element.querySelector('input[id^="gate_quantity_"]')?.value;

                console.log('Gate ' + (index + 1) + ' data:', { 
                    type: type, 
                    height: height, 
                    space: space, 
                    quantity: quantity 
                });

                if (type && height && space && quantity) {
                    gateData.push({
                        type: type,
                        height: parseFloat(height),
                        space: parseFloat(space),
                        quantity: parseInt(quantity, 10)
                    });
                }
            } catch (error) {
                console.error('Error gathering data for gate ' + (index + 1) + ':', error);
            }
        });

        console.log('Final gathered gate data:', gateData);
        return gateData;
    }

    // Initialize when DOM is ready
    domReady(initGateDataCollection);
    
    // Also listen for dynamic content loading (for AJAX-loaded content)
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('yith_ywraq_loaded', initGateDataCollection);
    }
})();