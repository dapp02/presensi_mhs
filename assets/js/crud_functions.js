document.addEventListener('DOMContentLoaded', function() {
    const crudMenuItems = document.querySelectorAll('.crud-menu-item');
    const crudContents = document.querySelectorAll('.crud-content');

    // Function to activate a tab
    function activateTab(targetId) {
        crudMenuItems.forEach(item => {
            if (item.dataset.target === targetId) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        crudContents.forEach(content => {
            if (content.id === targetId + '-crud') {
                content.classList.add('active');
            } else {
                content.classList.remove('active');
            }
        });
    }

    // Handle initial tab activation based on URL hash or default to 'prodi'
    const initialTab = window.location.hash ? window.location.hash.substring(1).replace('-crud', '') : 'prodi';
    activateTab(initialTab);

    // Add click listeners to menu items
    crudMenuItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetId = this.dataset.target;
            activateTab(targetId);
            // Update URL hash
            window.location.hash = targetId + '-crud';
        });
    });

    // Expose activateTab to global scope if needed by other scripts
    window.activateCrudTab = activateTab;
});