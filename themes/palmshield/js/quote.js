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

    function populateFormFields() {
        console.log('Attempting to populate form fields...');
        
        const form = document.getElementById('gform_2');
        if (!form) {
            console.log('Form gform_2 not found');
            return false;
        }

        console.log('Form found, proceeding with population');

        // Get or create the fields
        let gateField = form.querySelector('input[name="input_17"]');
        let productField = form.querySelector('input[name="input_15"]');
        
        if (!gateField) {
            gateField = document.createElement('input');
            gateField.type = 'hidden';
            gateField.name = 'input_17';
            gateField.id = 'input_2_17';
            form.appendChild(gateField);
            console.log('Created hidden gate field');
        }
        
        if (!productField) {
            productField = document.createElement('input');
            productField.type = 'hidden';
            productField.name = 'input_15';
            productField.id = 'input_2_15';
            form.appendChild(productField);
            console.log('Created hidden product field');
        }

        // Get gate data from localStorage
        try {
            const storedGateData = localStorage.getItem('pendingGateData');
            if (storedGateData) {
                console.log('Retrieved gate data from localStorage:', storedGateData);
                gateField.value = storedGateData;
                
                // Trigger change event
                if (typeof Event === 'function') {
                    gateField.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    // Fallback for older browsers
                    const evt = document.createEvent('HTMLEvents');
                    evt.initEvent('change', true, true);
                    gateField.dispatchEvent(evt);
                }
                
                console.log('Gate field value set:', gateField.value);
            } else {
                console.log('No gate data found in localStorage');
            }
        } catch (error) {
            console.error('Error retrieving gate data from localStorage:', error);
        }

        // Populate product data
        const quoteTable = document.getElementById('yith-ywrq-table-list');
        if (!quoteTable) {
            console.log('Quote table not found');
            return false;
        }

        const quoteItems = quoteTable.querySelectorAll('tbody > tr.cart_item');
        let quoteDetails = [];
        
        console.log('Found ' + quoteItems.length + ' quote items');
        
        quoteItems.forEach(function(item, index) {
            const productNameEl = item.querySelector('.product-name');
            const productName = productNameEl ? productNameEl.innerText.trim() : null;
            
            const quantityInput = item.querySelector('.product-quantity input');
            const quantityEl = item.querySelector('.product-quantity');
            const quantity = quantityInput ? quantityInput.value : 
                           (quantityEl ? quantityEl.innerText.trim() : '1');
            
            console.log('Item ' + (index + 1) + ':', {
                name: productName,
                quantity: quantity
            });
            
            if (productName) {
                quoteDetails.push(productName + ' (Qty: ' + (quantity || '1') + ')');
            }
        });

        if (quoteDetails.length > 0) {
            const productString = quoteDetails.join(' | ');
            console.log('Setting product data:', productString);
            productField.value = productString;
            
            // Trigger change event
            if (typeof Event === 'function') {
                productField.dispatchEvent(new Event('change', { bubbles: true }));
            } else {
                const evt = document.createEvent('HTMLEvents');
                evt.initEvent('change', true, true);
                productField.dispatchEvent(evt);
            }
            
            return true;
        } else {
            console.log('No products in the quote.');
            
            // Hide form and show message
            form.style.display = 'none';
            
            // Check if message already exists
            if (!document.getElementById('no-products-message')) {
                const addProductMessage = document.createElement('p');
                addProductMessage.id = 'no-products-message';
                addProductMessage.style.cssText = 'padding: 15px; background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 4px;';
                addProductMessage.textContent = 'In order to get your FREE quote, first add products you\'re interested in quoting.';
                
                const messageContainer = document.getElementById('yith-ywraq-message');
                if (messageContainer) {
                    messageContainer.appendChild(addProductMessage);
                } else {
                    form.parentNode.insertBefore(addProductMessage, form);
                }
            }
            
            return false;
        }
    }

    function initFormPopulation() {
        console.log('Initializing form population...');
        
        // Try to populate immediately if form exists
        if (document.getElementById('gform_2')) {
            console.log('Form found on initial load');
            setTimeout(populateFormFields, 500);
        }

        // Watch for the form to be added to the DOM
        const formObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    for (let i = 0; i < mutation.addedNodes.length; i++) {
                        const node = mutation.addedNodes[i];
                        if (node.id === 'gform_2' || (node.nodeType === 1 && node.querySelector && node.querySelector('#gform_2'))) {
                            console.log('Quote form detected in DOM');
                            setTimeout(populateFormFields, 500);
                        }
                    }
                }
            });
        });

        formObserver.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Handle form submission
        document.addEventListener('submit', function(event) {
            if (event.target.id === 'gform_2') {
                console.log('Form submission detected');
                
                // Ensure fields are populated one last time
                populateFormFields();

                // After submission, clear the stored gate data and reset the form fields
                setTimeout(function() {
                    const gateField = document.querySelector('input[name="input_17"]');
                    const productField = document.querySelector('input[name="input_15"]');
                    
                    // Clear the form fields
                    if (gateField) {
                        gateField.value = '';
                        console.log('Cleared gate field');
                    }
                    if (productField) {
                        productField.value = '';
                        console.log('Cleared product field');
                    }

                    // Clear storage after form submission
                    try {
                        localStorage.removeItem('pendingGateData');
                        console.log('Gate data cleared from localStorage');
                    } catch (error) {
                        console.error('Error clearing localStorage:', error);
                    }
                }, 1000);
            }
        }, true);
    }

    // Initialize when DOM is ready
    domReady(initFormPopulation);
    
    // Also listen for YITH-specific events if jQuery is available
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('yith_ywraq_loaded', function() {
            console.log('YITH quote loaded event triggered');
            setTimeout(populateFormFields, 500);
        });
    }
})();