document.addEventListener("DOMContentLoaded", () => {
  const mobileDrawer = document.getElementById("mobileDrawer");
  const mobileDrawerOverlay = document.getElementById("mobileDrawerOverlay");
  const closeDrawerBtn = document.getElementById("closeDrawerBtn");
  const openDrawerBtn = document.getElementById("openDrawerBtn");
  const toggleSidebarBtn = document.getElementById("toggleSidebarBtn");
  const sidebarDesktop = document.getElementById("sidebarDesktop");

  function openMobileDrawer() {
    if (mobileDrawer && mobileDrawerOverlay) {
      mobileDrawer.classList.remove("-translate-x-full");
      mobileDrawerOverlay.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }
  }

  function closeMobileDrawer() {
    if (mobileDrawer && mobileDrawerOverlay) {
      mobileDrawer.classList.add("-translate-x-full");
      mobileDrawerOverlay.classList.add("hidden");
      document.body.style.overflow = "auto";
    }
  }

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

  const savedCollapsedState = localStorage.getItem("sidebar-collapsed");
  if (savedCollapsedState === "true" && sidebarDesktop) {
    sidebarDesktop.classList.add("collapsed");
  }

  if (mobileDrawerOverlay) {
    mobileDrawerOverlay.addEventListener("click", closeMobileDrawer);
  }

  if (closeDrawerBtn) {
    closeDrawerBtn.addEventListener("click", closeMobileDrawer);
  }

  if (openDrawerBtn) {
    openDrawerBtn.addEventListener("click", openMobileDrawer);
  }

  if (toggleSidebarBtn) {
    toggleSidebarBtn.addEventListener("click", toggleDesktopSidebar);
  }

  document.querySelectorAll("#sidebarMenuMobile .menu-item").forEach((item) => {
    item.addEventListener("click", () => {
      if (!item.classList.contains("toggle-btn")) {
        closeMobileDrawer();
      }
    });
  });

  function initializeDropdowns(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const dropdownSections = container.querySelectorAll(".dropdown-section");

    dropdownSections.forEach((section) => {
      const toggleBtn = section.querySelector(".toggle-btn");
      const dropdownName = section.getAttribute("data-dropdown");

      if (!dropdownName || !toggleBtn) return;

      const menu = section.querySelector(".sidebar-dropdown");
      const arrow = section.querySelector(".arrow-icon");
      
      const hasActiveChild = section.querySelector(".menu-item.active");
      
      if (hasActiveChild) {
        menu?.classList.remove("hidden");
        arrow?.classList.add("rotate-180");
        localStorage.setItem(`dropdown-${dropdownName}`, "open");
      } else {
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

        localStorage.setItem(
          `dropdown-${dropdownName}`,
          isHidden ? "open" : "closed"
        );
      });
    });
  }

  initializeDropdowns("sidebarMenuDesktop");
  initializeDropdowns("sidebarMenuMobile");

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
          
          const parentDropdown = item.closest(".dropdown-section");
          if (parentDropdown) {
            const menu = parentDropdown.querySelector(".sidebar-dropdown");
            const arrow = parentDropdown.querySelector(".arrow-icon");
            if (menu && arrow) {
              menu.classList.remove("hidden");
              arrow.classList.add("rotate-180");
            }
          }
        }
      }
    });
  }

  highlightActiveMenu("sidebarMenuDesktop");
  highlightActiveMenu("sidebarMenuMobile");

  window.openMobileDrawer = openMobileDrawer;
  window.closeMobileDrawer = closeMobileDrawer;
  window.toggleDesktopSidebar = toggleDesktopSidebar;
});
