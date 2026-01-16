// Tags interaction script
document.addEventListener('DOMContentLoaded', function() {
    // Melhora a interação visual dos checkboxes de tags
    const tagCheckboxes = document.querySelectorAll('input[name="tags[]"]');
    
    tagCheckboxes.forEach(checkbox => {
        const label = checkbox.closest('label');
        
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                label.style.opacity = '1';
                label.style.transform = 'scale(1.05)';
            } else {
                label.style.opacity = '0.8';
                label.style.transform = 'scale(1)';
            }
        });
        
        // Adiciona efeito hover
        label.addEventListener('mouseenter', function() {
            if (!checkbox.checked) {
                this.style.opacity = '0.9';
            }
        });
        
        label.addEventListener('mouseleave', function() {
            if (!checkbox.checked) {
                this.style.opacity = '0.8';
            }
        });
    });
});
