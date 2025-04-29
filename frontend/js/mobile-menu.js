// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  const mobileMenu = document.getElementById('mobile-menu');
  
  if (mobileMenuButton && mobileMenu) {
    // Toggle mobile menu visibility
    mobileMenuButton.addEventListener('click', function() {
      mobileMenu.classList.toggle('hidden');
      mobileMenu.classList.toggle('flex');
      
      // Add animation classes
      if (mobileMenu.classList.contains('flex')) {
        mobileMenu.classList.add('animate-fadeIn');
      } else {
        mobileMenu.classList.remove('animate-fadeIn');
      }
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      const isClickInsideMenu = mobileMenu.contains(event.target);
      const isClickOnButton = mobileMenuButton.contains(event.target);
      
      if (!isClickInsideMenu && !isClickOnButton && mobileMenu.classList.contains('flex')) {
        mobileMenu.classList.remove('flex');
        mobileMenu.classList.add('hidden');
      }
    });
  }
  
  // Close mobile menu when window is resized to desktop size
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 768 && mobileMenu && mobileMenu.classList.contains('flex')) {
      mobileMenu.classList.remove('flex');
      mobileMenu.classList.add('hidden');
    }
  });
});
