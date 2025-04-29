// Dark mode functionality
document.addEventListener('DOMContentLoaded', function() {
  // Get toggle buttons
  const darkModeToggle = document.getElementById('darkModeToggle');
  const mobileDarkModeToggle = document.getElementById('mobile-dark-mode-toggle');
  
  // Check for saved theme preference or use system preference
  const savedTheme = localStorage.getItem('theme');
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  
  // Apply the right theme on initial load
  if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
    document.documentElement.classList.add('dark');
    updateIcons(true);
  } else {
    document.documentElement.classList.remove('dark');
    updateIcons(false);
  }
  
  // Function to toggle dark mode
  function toggleDarkMode() {
    if (document.documentElement.classList.contains('dark')) {
      // Switch to light mode
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
      updateIcons(false);
    } else {
      // Switch to dark mode
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
      updateIcons(true);
    }
  }
  
  // Function to update icon visibility
  function updateIcons(isDarkMode) {
    // Update desktop toggle icons
    if (darkModeToggle) {
      const lightIcon = darkModeToggle.querySelector('.light-icon');
      const darkIcon = darkModeToggle.querySelector('.dark-icon');
      
      if (lightIcon && darkIcon) {
        if (isDarkMode) {
          lightIcon.classList.add('hidden');
          darkIcon.classList.remove('hidden');
        } else {
          lightIcon.classList.remove('hidden');
          darkIcon.classList.add('hidden');
        }
      }
    }
    
    // Update mobile toggle icons
    if (mobileDarkModeToggle) {
      const lightIcon = mobileDarkModeToggle.querySelector('.light-icon');
      const darkIcon = mobileDarkModeToggle.querySelector('.dark-icon');
      
      if (lightIcon && darkIcon) {
        if (isDarkMode) {
          lightIcon.classList.add('hidden');
          darkIcon.classList.remove('hidden');
        } else {
          lightIcon.classList.remove('hidden');
          darkIcon.classList.add('hidden');
        }
      }
    }
  }
  
  // Add event listeners to toggle buttons
  if (darkModeToggle) {
    darkModeToggle.addEventListener('click', toggleDarkMode);
  }
  
  if (mobileDarkModeToggle) {
    mobileDarkModeToggle.addEventListener('click', toggleDarkMode);
  }
  
  // Listen for system preference changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    if (!localStorage.getItem('theme')) {
      if (e.matches) {
        document.documentElement.classList.add('dark');
        updateIcons(true);
      } else {
        document.documentElement.classList.remove('dark');
        updateIcons(false);
      }
    }
  });
});
