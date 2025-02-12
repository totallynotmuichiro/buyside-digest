document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('[data-overlay-show]');
    const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
    let currentModal = null;
    
    // Handle opening modals
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal-trigger');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                currentModal = modal;
                // Show overlay and modal with a slight delay for smooth transition
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('hidden');
                }, 50);
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                // Add escape key listener
                document.addEventListener('keydown', handleEscapeKey);
            }
        });
    });
    
    // Handle closing modals
    const closeModal = () => {
        if (currentModal) {
            currentModal.classList.add('hidden');
            currentModal = null;
        }
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.removeEventListener('keydown', handleEscapeKey);
    };
    
    // Close button click handler
    document.querySelectorAll('[id^="article-modal-"] button').forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    // Overlay click handler
    overlay.addEventListener('click', closeModal);
    
    // Escape key handler
    const handleEscapeKey = (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    };
    
    // Prevent modal close when clicking inside modal content
    document.querySelectorAll('[id^="article-modal-"] > div > div').forEach(modalContent => {
        modalContent.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });
});