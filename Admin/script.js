document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.menu-item');
    const contentSections = document.querySelectorAll('.content-section');
    const sectionTitle = document.getElementById('section-title'); // Get the section title element

    // Function to show the selected section
    const showSection = (sectionId) => {
        contentSections.forEach(section => {
            if (section.id === sectionId) {
                section.classList.add('active');
                section.classList.remove('hidden');
            } else {
                section.classList.remove('active');
                section.classList.add('hidden');
            }
        });
    };

    // Function to activate a menu item and update the section title
    const activateMenuItem = (item) => {
        const section = item.getAttribute('data-section');
        showSection(section);

        // Highlight the active menu item
        menuItems.forEach(mi => mi.classList.remove('active-menu'));
        item.classList.add('active-menu');

        // Update the section title
        sectionTitle.textContent = section; // Set the section title text
    };

    // Initialize: Show Dashboard by default
    const defaultSection = document.querySelector('.menu-item.active-menu') || menuItems[0];
    if (defaultSection) {
        activateMenuItem(defaultSection);
    }

    // Add click and keypress event listeners to menu items
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            activateMenuItem(item);
        });

        item.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                activateMenuItem(item);
            }
        });
    });
});

document.querySelectorAll('.dashboard-block').forEach(block => {
    block.addEventListener('click', function() {
        const section = this.getAttribute('data-section');
        document.querySelectorAll('.content-section').forEach(sectionEl => {
            sectionEl.classList.remove('active');
            sectionEl.classList.add('hidden');
        });
        document.getElementById(section).classList.add('active');
        document.getElementById(section).classList.remove('hidden');
        document.getElementById('section-title').innerText = section; // Update the section title
    });
});
