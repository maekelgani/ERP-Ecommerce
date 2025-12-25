/**
 * ============================================================================
 * ADMIN SIDEBAR JAVASCRIPT
 * ============================================================================
 * File        : sidebar.js
 * Description : Script untuk mengelola sidebar admin dashboard
 * Version     : 1.0.0
 * Author      : Nano Komputer Development Team
 * 
 * FITUR:
 * - Toggle sidebar desktop (collapse/expand)
 * - Mobile drawer navigation
 * - Dropdown menu dengan state persistence
 * - Active menu highlighting
 * - Keyboard accessibility
 * ============================================================================
 */

document.addEventListener("DOMContentLoaded", () => {
  // ============================================
  // ELEMENT REFERENCES
  // ============================================
  const mobileDrawer = document.getElementById("mobileDrawer");
  const mobileDrawerOverlay = document.getElementById("mobileDrawerOverlay");
  const closeDrawerBtn = document.getElementById("closeDrawerBtn");
  const openDrawerBtn = document.getElementById("openDrawerBtn");
  const toggleSidebarBtn = document.getElementById("toggleSidebarBtn");
  const sidebarDesktop = document.getElementById("sidebarDesktop");

  // ============================================
  // MOBILE DRAWER FUNCTIONS
  // ============================================
  
  /**
   * Membuka mobile drawer
   */
  function openMobileDrawer() {
    if (mobileDrawer && mobileDrawerOverlay) {
      mobileDrawer.classList.remove("-translate-x-full");
      mobileDrawerOverlay.classList.remove("hidden");
      document.body.style.overflow = "hidden";
      
      // Focus trap untuk aksesibilitas
      mobileDrawer.setAttribute("aria-hidden", "false");
      closeDrawerBtn?.focus();
    }
  }

  /**
   * Menutup mobile drawer
   */
  function closeMobileDrawer() {
    if (mobileDrawer && mobileDrawerOverlay) {
      mobileDrawer.classList.add("-translate-x-full");
      mobileDrawerOverlay.classList.add("hidden");
      document.body.style.overflow = "auto";
      
      // Reset aria-hidden
      mobileDrawer.setAttribute("aria-hidden", "true");
    }
  }

  // ============================================
  // DESKTOP SIDEBAR FUNCTIONS
  // ============================================
  
  /**
   * Toggle sidebar desktop antara collapse dan expand
   */
  function toggleDesktopSidebar() {
    if (sidebarDesktop) {
      const isCollapsed = sidebarDesktop.classList.contains("collapsed");
      
      if (isCollapsed) {
        sidebarDesktop.classList.remove("collapsed");
        localStorage.setItem("sidebar-collapsed", "false");
      } else {
        sidebarDesktop.classList.add("collapsed");
        localStorage.setItem("sidebar-collapsed", "true");
      }
    }
  }

  // Restore saved collapsed state
  const savedCollapsedState = localStorage.getItem("sidebar-collapsed");
  if (savedCollapsedState === "true" && sidebarDesktop) {
    sidebarDesktop.classList.add("collapsed");
  }

  // ============================================
  // EVENT LISTENERS
  // ============================================
  
  // Mobile drawer overlay click
  if (mobileDrawerOverlay) {
    mobileDrawerOverlay.addEventListener("click", closeMobileDrawer);
  }

  // Close drawer button
  if (closeDrawerBtn) {
    closeDrawerBtn.addEventListener("click", closeMobileDrawer);
  }

  // Open drawer button
  if (openDrawerBtn) {
    openDrawerBtn.addEventListener("click", openMobileDrawer);
  }

  // Toggle sidebar button
  if (toggleSidebarBtn) {
    toggleSidebarBtn.addEventListener("click", toggleDesktopSidebar);
  }

  // Close mobile drawer saat menu item diklik
  document.querySelectorAll("#sidebarMenuMobile .menu-item").forEach((item) => {
    item.addEventListener("click", () => {
      if (!item.classList.contains("toggle-btn")) {
        closeMobileDrawer();
      }
    });
  });

  // Keyboard navigation (ESC untuk tutup drawer)
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && mobileDrawer && !mobileDrawer.classList.contains("-translate-x-full")) {
      closeMobileDrawer();
    }
  });

  // ============================================
  // DROPDOWN INITIALIZATION
  // ============================================
  
  /**
   * Inisialisasi dropdown menu dengan state persistence
   * @param {string} containerId - ID container sidebar
   */
  function initializeDropdowns(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const dropdownSections = container.querySelectorAll(".dropdown-section");

    // Restore saved states dan set initial state
    dropdownSections.forEach((section) => {
      const toggleBtn = section.querySelector(".toggle-btn");
      const dropdownName = section.getAttribute("data-dropdown");

      if (!dropdownName || !toggleBtn) return;

      const menu = section.querySelector(".sidebar-dropdown");
      const arrow = section.querySelector(".arrow-icon");
      
      // Prioritaskan jika ada menu aktif di dalam dropdown
      const hasActiveChild = section.querySelector(".menu-item.active");
      
      if (hasActiveChild) {
        menu?.classList.remove("hidden");
        arrow?.classList.add("rotate-180");
        localStorage.setItem(`dropdown-${dropdownName}`, "open");
      } else {
        // Gunakan saved state
        const savedState = localStorage.getItem(`dropdown-${dropdownName}`);
        
        if (savedState === "open") {
          menu?.classList.remove("hidden");
          arrow?.classList.add("rotate-180");
        } else if (savedState === "closed") {
          menu?.classList.add("hidden");
          arrow?.classList.remove("rotate-180");
        }
      }
    });

    // Add click handlers untuk toggle
    dropdownSections.forEach((section) => {
      const btn = section.querySelector(".toggle-btn");
      const menu = section.querySelector(".sidebar-dropdown");
      const arrow = section.querySelector(".arrow-icon");
      const dropdownName = section.getAttribute("data-dropdown");

      if (!btn || !menu || !arrow || !dropdownName) return;

      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        const isHidden = menu.classList.contains("hidden");

        menu.classList.toggle("hidden");
        arrow.classList.toggle("rotate-180");

        // Simpan state ke localStorage
        localStorage.setItem(
          `dropdown-${dropdownName}`,
          isHidden ? "open" : "closed"
        );
      });

      // Keyboard support untuk dropdown
      btn.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          btn.click();
        }
      });
    });
  }

  // Initialize dropdowns untuk desktop dan mobile
  initializeDropdowns("sidebarMenuDesktop");
  initializeDropdowns("sidebarMenuMobile");

  // ============================================
  // ACTIVE MENU HIGHLIGHTING
  // ============================================
  
  /**
   * Highlight menu aktif berdasarkan URL saat ini
   * @param {string} containerId - ID container sidebar
   */
  function highlightActiveMenu(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const menuItems = container.querySelectorAll(".menu-item");
    const currentPath = window.location.pathname;
    const currentPage = currentPath.split('/').pop();

    menuItems.forEach((item) => {
      const itemHref = item.getAttribute("href");
      if (itemHref) {
        const itemPage = itemHref.split('/').pop();
        if (itemPage === currentPage) {
          item.classList.add("active");
          
          // Buka parent dropdown jika ada
          const parentDropdown = item.closest(".dropdown-section");
          if (parentDropdown) {
            const menu = parentDropdown.querySelector(".sidebar-dropdown");
            const arrow = parentDropdown.querySelector(".arrow-icon");
            const dropdownName = parentDropdown.getAttribute("data-dropdown");
            
            if (menu && arrow) {
              menu.classList.remove("hidden");
              arrow.classList.add("rotate-180");
              
              // Simpan state
              if (dropdownName) {
                localStorage.setItem(`dropdown-${dropdownName}`, "open");
              }
            }
          }
        }
      }
    });
  }

  // Highlight active menu untuk desktop dan mobile
  highlightActiveMenu("sidebarMenuDesktop");
  highlightActiveMenu("sidebarMenuMobile");

  // ============================================
  // GLOBAL FUNCTION EXPORTS
  // ============================================
  
  // Export functions ke global scope untuk penggunaan external
  window.openMobileDrawer = openMobileDrawer;
  window.closeMobileDrawer = closeMobileDrawer;
  window.toggleDesktopSidebar = toggleDesktopSidebar;

  // ============================================
  // RESIZE HANDLER
  // ============================================
  
  // Tutup mobile drawer saat resize ke desktop
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      if (window.innerWidth >= 1024) {
        closeMobileDrawer();
      }
    }, 100);
  });

  // ============================================
  // ACCESSIBILITY IMPROVEMENTS
  // ============================================
  
  // Set initial aria-hidden state
  if (mobileDrawer) {
    mobileDrawer.setAttribute("aria-hidden", "true");
  }

  // Add role navigation
  if (sidebarDesktop) {
    sidebarDesktop.setAttribute("role", "navigation");
    sidebarDesktop.setAttribute("aria-label", "Menu navigasi utama");
  }

  if (mobileDrawer) {
    mobileDrawer.setAttribute("role", "navigation");
    mobileDrawer.setAttribute("aria-label", "Menu navigasi mobile");
  }
});
