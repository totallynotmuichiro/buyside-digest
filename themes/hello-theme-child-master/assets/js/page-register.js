document.addEventListener('DOMContentLoaded', function() {
    const accreditedRadios = document.querySelectorAll('input[name="input_6"]');
    const industryRoleFieldset = document.getElementById('field_7_7');
    const submitButton = document.getElementById('gform_submit_button_7');
    
    function showAlert() {
        const existingAlert = document.querySelector('.investor-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'investor-alert';
        alertDiv.style.cssText = `
            font-size: 0.9rem;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 22px;
            
        `;
        alertDiv.textContent = "Sorry, our database is only for Accredited Investors";

        submitButton.parentNode.style.flexDirection = 'column';
        submitButton.parentNode.insertBefore(alertDiv, submitButton);
    }
    
    function handleAccreditedChange(event) {
        const isNotAccredited = event.target.value.includes('I do not qualify');
        
        if (isNotAccredited) {
            industryRoleFieldset.style.display = 'none';
            
            // Enhanced button disabling
            submitButton.disabled = true;
            submitButton.setAttribute('disabled', 'disabled');
            submitButton.style.opacity = '0.5';
            submitButton.style.cursor = 'not-allowed';
            submitButton.style.pointerEvents = 'none';
            
            // // Remove any click event handlers
            // submitButton.onclick = function(e) {
            //     e.preventDefault();
            //     return false;
            // };
            
            // // Prevent form submission
            // document.getElementById('gform_7').onsubmit = function(e) {
            //     e.preventDefault();
            //     return false;
            // };
            
            showAlert();
        } else {
            industryRoleFieldset.style.display = '';
            
            // Re-enable button
            submitButton.disabled = false;
            submitButton.removeAttribute('disabled');
            submitButton.style.opacity = '1';
            submitButton.style.cursor = 'pointer';
            submitButton.style.pointerEvents = 'auto';
            
            // // Remove prevention handlers
            // submitButton.onclick = null;
            // document.getElementById('gform_7').onsubmit = null;
            
            const existingAlert = document.querySelector('.investor-alert');
            if (existingAlert) {
                existingAlert.remove();
            }
        }
    }
    
    accreditedRadios.forEach(radio => {
        radio.addEventListener('change', handleAccreditedChange);
    });
    
    const selectedRadio = document.querySelector('input[name="input_6"]:checked');
    if (selectedRadio) {
        handleAccreditedChange({ target: selectedRadio });
    }
});