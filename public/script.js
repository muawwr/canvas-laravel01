// Global Theme Toggle
(function() {
    function getStoredTheme() {
        const savedTheme = localStorage.getItem('site-theme');
        return savedTheme === 'light' || savedTheme === 'dark' ? savedTheme : 'dark';
    }

    function syncTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (document.body) {
            document.body.setAttribute('data-theme', theme);
        }

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            const isLight = theme === 'light';
            const label = button.querySelector('[data-theme-toggle-label]');
            button.setAttribute('aria-pressed', String(isLight));
            button.setAttribute('aria-label', isLight ? '\u041f\u0435\u0440\u0435\u043a\u043b\u044e\u0447\u0438\u0442\u044c \u043d\u0430 \u0442\u0435\u043c\u043d\u0443\u044e \u0442\u0435\u043c\u0443' : '\u041f\u0435\u0440\u0435\u043a\u043b\u044e\u0447\u0438\u0442\u044c \u043d\u0430 \u0441\u0432\u0435\u0442\u043b\u0443\u044e \u0442\u0435\u043c\u0443');
            if (label) {
                label.textContent = isLight ? '\u0422\u0435\u043c\u043d\u0430\u044f \u0442\u0435\u043c\u0430' : '\u0421\u0432\u0435\u0442\u043b\u0430\u044f \u0442\u0435\u043c\u0430';
            }
        });
    }

    function init() {
        syncTheme(document.documentElement.getAttribute('data-theme') || getStoredTheme());

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            if (button.dataset.themeToggleReady === 'true') {
                return;
            }

            button.dataset.themeToggleReady = 'true';
            button.addEventListener('click', function() {
                const nextTheme = document.documentElement.getAttribute('data-theme') === 'light'
                    ? 'dark'
                    : 'light';

                localStorage.setItem('site-theme', nextTheme);
                syncTheme(nextTheme);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Mobile Burger Menu Toggle
(function() {
    function init() {
        const burgerMenu = document.getElementById('burgerMenu');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');
        
        if (!burgerMenu || !mobileSidebar) return; // Exit if not on page with burger menu
        
        // Toggle sidebar on burger menu click
        burgerMenu.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.toggle('active');
            mobileSidebar.classList.toggle('active');
            if (mobileOverlay) mobileOverlay.classList.toggle('active');
            
            // Close profile dropdown when burger menu is closed
            if (!this.classList.contains('active') && mobileProfileDropdown) {
                mobileProfileDropdown.classList.remove('active');
            }
        });
        
        // Close sidebar when clicking on overlay
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function() {
                burgerMenu.classList.remove('active');
                mobileSidebar.classList.remove('active');
                mobileOverlay.classList.remove('active');
                // Also close profile dropdown
                if (mobileProfileDropdown) mobileProfileDropdown.classList.remove('active');
            });
        }
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileSidebar.contains(e.target) && e.target !== burgerMenu && !burgerMenu.contains(e.target)) {
                burgerMenu.classList.remove('active');
                mobileSidebar.classList.remove('active');
                if (mobileOverlay) mobileOverlay.classList.remove('active');
                // Also close profile dropdown
                if (mobileProfileDropdown) mobileProfileDropdown.classList.remove('active');
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Mobile Profile Dropdown Toggle
(function() {
    function init() {
        const mobileProfileToggle = document.getElementById('mobileProfileToggle');
        const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');
        
        if (!mobileProfileToggle || !mobileProfileDropdown) return; // Exit if not on mobile
        
        // Toggle dropdown on mobile profile icon click
        mobileProfileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileProfileDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileProfileDropdown.contains(e.target) && e.target !== mobileProfileToggle && !mobileProfileToggle.contains(e.target)) {
                mobileProfileDropdown.classList.remove('active');
            }
        });
        
        // Prevent closing when clicking inside dropdown
        mobileProfileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Profile Dropdown Toggle - Main Page
(function() {
    function init() {
        console.log('Initializing profile dropdown...');
        const profileToggle = document.getElementById('profileToggle');
        const profileDropdown = document.getElementById('profileDropdown');
        
        console.log('profileToggle:', profileToggle);
        console.log('profileDropdown:', profileDropdown);
        
        if (!profileToggle || !profileDropdown) {
            console.warn('РІСљвЂ” Profile elements not found!');
            return; // Exit if not on page with profile toggle
        }
        
        console.log('РІСљвЂњ Profile elements found, adding click listener');
        
        // Toggle dropdown on profile icon click
        profileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('РІСљвЂњ Profile toggle clicked!');
            profileDropdown.classList.toggle('active');
            console.log('Dropdown classes after:', profileDropdown.className);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && e.target !== profileToggle && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
        
        // Prevent closing when clicking inside dropdown
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Profile Dropdown Toggle - Gallery Page
(function() {
    function init() {
        console.log('Initializing gallery profile dropdown...');
        const profileToggle = document.getElementById('profileToggleGallery');
        const profileDropdown = document.getElementById('profileDropdownGallery');
        
        console.log('profileToggleGallery:', profileToggle);
        console.log('profileDropdownGallery:', profileDropdown);
        
        if (!profileToggle || !profileDropdown) {
            console.warn('РІСљвЂ” Gallery profile elements not found!');
            return; // Exit if not on gallery page
        }
        
        console.log('РІСљвЂњ Gallery profile elements found, adding click listener');
        
        // Toggle dropdown on profile icon click
        profileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('РІСљвЂњ Gallery profile toggle clicked!');
            profileDropdown.classList.toggle('active');
            console.log('Gallery dropdown classes after:', profileDropdown.className);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && e.target !== profileToggle && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
        
        // Prevent closing when clicking inside dropdown
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

(() => {
    const track = document.querySelector('.artists-track');
    if (!track) return; // Exit if carousel not found on page
    const cards = Array.from(track.querySelectorAll('.artist-card'));
  
    let CARD_W = 332;                      // РЎв‚¬Р С‘РЎР‚Р С‘Р Р…Р В° Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р С‘
    let GAP = 5;                           // РЎР‚Р В°РЎРѓРЎРѓРЎвЂљР С•РЎРЏР Р…Р С‘Р Вµ Р СР ВµР В¶Р Т‘РЎС“ Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р В°Р СР С‘
    let SHIFT = CARD_W + GAP;
    let active = 0;
  
    // Р В¤РЎС“Р Р…Р С”РЎвЂ Р С‘РЎРЏ Р Т‘Р В»РЎРЏ Р С•Р С—РЎР‚Р ВµР Т‘Р ВµР В»Р ВµР Р…Р С‘РЎРЏ Р С—Р В°РЎР‚Р В°Р СР ВµРЎвЂљРЎР‚Р С•Р Р† Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р С‘ Р Р† Р В·Р В°Р Р†Р С‘РЎРѓР С‘Р СР С•РЎРѓРЎвЂљР С‘ Р С•РЎвЂљ РЎР‚Р В°Р В·Р СР ВµРЎР‚Р В° РЎРЊР С”РЎР‚Р В°Р Р…Р В°
    function getCardDimensions() {
      const screenWidth = window.innerWidth;
      if (screenWidth <= 768) {
        return { width: 181, gap: 5 }; // Р СР С•Р В±Р С‘Р В»РЎРЉР Р…Р В°РЎРЏ Р Р†Р ВµРЎР‚РЎРѓР С‘РЎРЏ
      } else if (screenWidth > 768 && screenWidth <= 1024) {
        return { width: 280, gap: 5 }; // Р С—Р В»Р В°Р Р…РЎв‚¬Р ВµРЎвЂљ
      }
      return { width: 332, gap: 5 }; // Р Т‘Р ВµРЎРѓР С”РЎвЂљР С•Р С— Р Р†Р ВµРЎР‚РЎРѓР С‘РЎРЏ
    }
  
    // Р С›Р В±Р Р…Р С•Р Р†Р В»Р ВµР Р…Р С‘Р Вµ Р С—Р В°РЎР‚Р В°Р СР ВµРЎвЂљРЎР‚Р С•Р Р† Р С—РЎР‚Р С‘ Р С‘Р В·Р СР ВµР Р…Р ВµР Р…Р С‘Р С‘ РЎР‚Р В°Р В·Р СР ВµРЎР‚Р В° РЎРЊР С”РЎР‚Р В°Р Р…Р В°
    function updateDimensions() {
      const dimensions = getCardDimensions();
      CARD_W = dimensions.width;
      GAP = dimensions.gap;
      SHIFT = CARD_W + GAP;
      render();
    }
  
    const clampMod = (n, m) => ((n % m) + m) % m;
  
    function render(){
      cards.forEach((card, i) => {
        // РЎРѓР СР ВµРЎвЂ°Р ВµР Р…Р С‘Р Вµ Р С•РЎвЂљР Р…Р С•РЎРѓР С‘РЎвЂљР ВµР В»РЎРЉР Р…Р С• Р В°Р С”РЎвЂљР С‘Р Р†Р Р…Р С•Р в„–: -2,-1,0,1,2...
        let off = i - active;
        const half = Math.floor(cards.length/2);
        if (off >  half) off -= cards.length;
        if (off < -half) off += cards.length;
  
        // Р ВРЎРѓР С—Р С•Р В»РЎРЉР В·РЎС“Р ВµР С Р В°Р С”РЎвЂљРЎС“Р В°Р В»РЎРЉР Р…Р С•Р Вµ Р В·Р Р…Р В°РЎвЂЎР ВµР Р…Р С‘Р Вµ SHIFT Р Т‘Р В»РЎРЏ РЎР‚Р В°РЎРѓРЎвЂЎР ВµРЎвЂљР В° Р С—Р С•Р В·Р С‘РЎвЂ Р С‘Р С‘
        let x = off * SHIFT;
        let z = 0, scale = 1, opacity = 1, zIndex = 1, rot = 0;
  
        // Р В¦Р ВµР Р…РЎвЂљРЎР‚Р В°Р В»РЎРЉР Р…Р В°РЎРЏ Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р В° (Р В°Р С”РЎвЂљР С‘Р Р†Р Р…Р В°РЎРЏ)
        if (off === 0) { 
            z = 0;   
            scale = 1;    
            opacity = 1;    
            zIndex = 5; 
            rot = 0; 
        }
        // Р РЋР С•РЎРѓР ВµР Т‘Р Р…Р С‘Р Вµ Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р С‘ (РЎРѓР В»Р ВµР Р†Р В° Р С‘ РЎРѓР С—РЎР‚Р В°Р Р†Р В° Р С•РЎвЂљ РЎвЂ Р ВµР Р…РЎвЂљРЎР‚Р В°Р В»РЎРЉР Р…Р С•Р в„–)
        else if (Math.abs(off) === 1) { 
            z = -50;  
            scale = 1; 
            opacity = 0.8;  
            zIndex = 4; 
            rot = off * -3; 
        }
        // Р вЂ™РЎвЂљР С•РЎР‚РЎвЂ№Р Вµ РЎРѓР С•РЎРѓР ВµР Т‘Р Р…Р С‘Р Вµ Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р С‘
        else if (Math.abs(off) === 2) { 
            z = -100; 
            scale = 0.8; 
            opacity = 0; 
            zIndex = 3; 
            rot = off * -5; 
        }
        // Р С›РЎРѓРЎвЂљР В°Р В»РЎРЉР Р…РЎвЂ№Р Вµ Р С”Р В°РЎР‚РЎвЂљР С•РЎвЂЎР С”Р С‘ (РЎРѓР С”РЎР‚РЎвЂ№РЎвЂљРЎвЂ№Р Вµ РЎРѓР В·Р В°Р Т‘Р С‘)
        else { 
            opacity = 0; 
            z = -150; 
            scale = 0.7; 
            zIndex = 1; 
            rot = off * -8; 
        }
  
        card.style.zIndex = zIndex;
        card.style.opacity = opacity;
        // Р ВРЎРѓР С—Р С•Р В»РЎРЉР В·РЎС“Р ВµР С translateX РЎРѓ calc Р С‘ Р В°Р С”РЎвЂљРЎС“Р В°Р В»РЎРЉР Р…РЎвЂ№Р С Р В·Р Р…Р В°РЎвЂЎР ВµР Р…Р С‘Р ВµР С x
        card.style.transform =
          `translateX(calc(-50% + ${x}px)) translateZ(${z}px) scale(${scale}) rotateY(${rot}deg)`;
      });
    }
  
    function next(){ active = clampMod(active + 1, cards.length); render(); }
    function prev(){ active = clampMod(active - 1, cards.length); render(); }
  
    // Р С”Р С•Р В»Р ВµРЎРѓР С‘Р С”Р С• / РЎвЂљР В°РЎвЂЎР С—Р В°Р Т‘
    let throttle = false;
    const carousel = document.querySelector('.artists-carousel');
    
    carousel.addEventListener('wheel', (e) => {
      e.preventDefault();                // РЎвЂЎРЎвЂљР С•Р В±РЎвЂ№ РЎРѓРЎвЂљРЎР‚Р В°Р Р…Р С‘РЎвЂ Р В° Р Р…Р Вµ РЎРѓР С”РЎР‚Р С•Р В»Р В»Р С‘Р В»Р В°РЎРѓРЎРЉ
      if (throttle) return;
      if (e.deltaY > 0 || e.deltaX > 0) next(); else prev();
      throttle = true; setTimeout(() => throttle = false, 450);
    }, { passive:false });
  
    // РЎРѓР Р†Р В°Р в„–Р С— Р Т‘Р В»РЎРЏ РЎвЂљР В°РЎвЂЎР ВµР в„–
    let startX = 0;
    track.addEventListener('pointerdown', e => { startX = e.clientX; track.setPointerCapture(e.pointerId); });
    track.addEventListener('pointerup', e => {
      const dx = e.clientX - startX;
      if (Math.abs(dx) > 40) (dx < 0 ? next : prev)();
    });
  
    // Р С›Р В±РЎР‚Р В°Р В±Р С•РЎвЂљРЎвЂЎР С‘Р С” Р С‘Р В·Р СР ВµР Р…Р ВµР Р…Р С‘РЎРЏ РЎР‚Р В°Р В·Р СР ВµРЎР‚Р В° Р С•Р С”Р Р…Р В° РЎРѓ debounce
    let resizeTimeout;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(updateDimensions, 150);
    });
  
    // Р ВР Р…Р С‘РЎвЂ Р С‘Р В°Р В»Р С‘Р В·Р В°РЎвЂ Р С‘РЎРЏ
    updateDimensions();
  })();

// Gallery Filter Panel - Initialize immediately
(function() {
    // Wait for DOM to be ready
    function init() {
        const filterToggle = document.getElementById('filterToggle');
        const filterPanel = document.getElementById('filterPanel');
        const expandBtn = document.getElementById('expandBtn');
        
        // Function to toggle filter panel
        function toggleFilterPanel() {
            filterPanel.classList.toggle('active');
            filterToggle.classList.toggle('hidden');
        }
        
        // Open filter panel
        if (filterToggle && filterPanel) {
            filterToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleFilterPanel();
            });
        } else {
            return; // Exit silently if not on gallery page
        }
        
        // Close filter panel with expand button
        if (expandBtn && filterPanel && filterToggle) {
            expandBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleFilterPanel();
            });
        }
        
        // Filter options interaction
        const filterOptions = document.querySelectorAll('.filter-option');
        
        // Initialize active states for checked inputs on page load
        filterOptions.forEach(option => {
            const input = option.querySelector('input');
            if (input && input.checked) {
                option.classList.add('active');
            }
        });
        
        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-filter')) {
                    return;
                }
                
                const input = this.querySelector('input');
                if (input) {
                    if (input.type === 'radio') {
                        // Remove active from all radio options in the same group
                        const radioGroup = input.getAttribute('name');
                        document.querySelectorAll(`input[name="${radioGroup}"]`).forEach(radio => {
                            radio.closest('.filter-option').classList.remove('active');
                        });
                        this.classList.add('active');
                        input.checked = true;
                    } else if (input.type === 'checkbox') {
                        // Toggle checkbox state first
                        input.checked = !input.checked;
                        // Then update visual state based on checkbox
                        if (input.checked) {
                            this.classList.add('active');
                        } else {
                            this.classList.remove('active');
                        }
                    }
                } else {
                    this.classList.toggle('active');
                }
            });
        });
        
        // Remove filter buttons
        const removeButtons = document.querySelectorAll('.remove-filter');
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const option = this.closest('.filter-option');
                const input = option.querySelector('input');
                if (input) {
                    input.checked = false;
                }
                option.classList.remove('active');
            });
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // DOM is already loaded
        init();
    }
})();

// Add Product Steps Navigation
(function() {
    let currentStep = 1;
    
    function init() {
        if (document.querySelector('[data-managed-add-page="custom"]')) return;

        // Get all modals
        const modals = document.querySelectorAll('.add_modal');
        if (modals.length === 0) return; // Exit if not on add page
        
        // Get step indicators
        const stepIndicators = document.querySelectorAll('.add_step');
        const stepLines = document.querySelectorAll('.step_line');
        
        // Next buttons
        const nextButtons = document.querySelectorAll('[data-next]');
        nextButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const nextStep = parseInt(this.getAttribute('data-next'));
                goToStep(nextStep, 'next');
            });
        });
        
        // Back buttons
        const backButtons = document.querySelectorAll('[data-prev]');
        backButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const prevStep = parseInt(this.getAttribute('data-prev'));
                goToStep(prevStep, 'prev');
            });
        });
        
        function goToStep(stepNumber, direction) {
            if (stepNumber === currentStep) return;
            
            // Get current and next modal
            const currentModal = document.querySelector(`.add_modal[data-step="${currentStep}"]`);
            const nextModal = document.querySelector(`.add_modal[data-step="${stepNumber}"]`);
            
            if (!currentModal || !nextModal) return;
            
            // Animate modals
            if (direction === 'next') {
                currentModal.classList.add('slide-out-left');
            } else {
                currentModal.classList.add('slide-out-right');
            }
            
            setTimeout(() => {
                currentModal.classList.remove('active', 'slide-out-left', 'slide-out-right');
                nextModal.classList.add('active');
                currentStep = stepNumber;
                
                // Update step indicators
                updateStepIndicators(stepNumber);
            }, 500);
        }
        
        function updateStepIndicators(stepNumber) {
            // Update step circles - only current step is active
            stepIndicators.forEach((indicator, index) => {
                const indicatorStep = parseInt(indicator.getAttribute('data-step-indicator'));
                if (indicatorStep === stepNumber) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
            
            // Lines don't change color - remove active class from all
            stepLines.forEach((line, index) => {
                line.classList.remove('active');
            });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Add Product Step 3 - Custom Select
(function() {
    function init() {
        if (document.querySelector('[data-managed-add-page="custom"]') || document.querySelector('[data-managed-edit-page="custom"]')) return;

        const customSelects = document.querySelectorAll('.custom_select');
        if (customSelects.length === 0) return; // Exit if not on add page
        
        customSelects.forEach(select => {
            const trigger = select.querySelector('.custom_select_trigger');
            const options = select.querySelectorAll('.custom_select_option');
            const textElement = select.querySelector('.custom_select_text');
            
            // Toggle dropdown
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close all other selects
                customSelects.forEach(s => {
                    if (s !== select) {
                        s.classList.remove('open');
                    }
                });
                
                // Toggle current select
                select.classList.toggle('open');
            });
            
            // Handle option selection
            options.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;
                    
                    // Remove selected from all options
                    options.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected to clicked option
                    this.classList.add('selected');
                    
                    // Update trigger text
                    textElement.textContent = text;
                    select.classList.add('has-value');
                    
                    // Close dropdown
                    select.classList.remove('open');
                });
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            customSelects.forEach(select => {
                select.classList.remove('open');
            });
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Add Product Step 4 - Price Calculator
(function() {
    function init() {
        if (document.querySelector('[data-managed-add-page="custom"]')) return;

        const priceYouGet = document.getElementById('priceYouGet');
        const priceBuyerPays = document.getElementById('priceBuyerPays');
        
        if (!priceYouGet || !priceBuyerPays) return; // Exit if not on add page
        
        // Commission rate (e.g., 10%)
        const COMMISSION_RATE = 0.10;
        
        priceYouGet.addEventListener('input', function() {
            const value = parseFloat(this.value.replace(/[^\d.]/g, ''));
            
            if (isNaN(value) || value <= 0) {
                priceBuyerPays.value = '';
                return;
            }
            
            // Calculate price with commission
            const withCommission = value / (1 - COMMISSION_RATE);
            priceBuyerPays.value = Math.round(withCommission) + ' РІвЂљР…';
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Success Modal
(function() {
    function init() {
        if (document.querySelector('[data-managed-add-page="custom"]')) return;

        const submitBtn = document.querySelector('.add_btn_submit');
        const successModal = document.getElementById('successModal');
        const successOkBtn = document.getElementById('successOkBtn');
        
        if (!submitBtn || !successModal) return; // Exit if not on add page
        
        // Show modal on submit
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show modal with animation
            successModal.style.display = 'flex';
            setTimeout(() => {
                successModal.classList.add('show');
            }, 10);
        });
        
        // Close modal on OK button and redirect to home
        if (successOkBtn) {
            successOkBtn.addEventListener('click', function() {
                successModal.classList.remove('show');
                setTimeout(() => {
                    window.location.href = 'main.html';
                }, 300);
            });
        }
        
        // Close modal on overlay click
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                successModal.classList.remove('show');
                setTimeout(() => {
                    successModal.style.display = 'none';
                }, 300);
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Edit Page - Price Calculator
(function() {
    function init() {
        if (document.querySelector('[data-managed-edit-page="custom"]')) return;

        const priceYouGet = document.getElementById('editPriceYouGet');
        const priceBuyerPays = document.getElementById('editPriceBuyerPays');
        
        if (!priceYouGet || !priceBuyerPays) return; // Exit if not on edit page
        
        // Commission rate (e.g., 10%)
        const COMMISSION_RATE = 0.10;
        
        priceYouGet.addEventListener('input', function() {
            const value = parseFloat(this.value.replace(/[^\d.]/g, ''));
            
            if (isNaN(value) || value <= 0) {
                priceBuyerPays.value = '';
                return;
            }
            
            // Calculate price with commission
            const withCommission = value / (1 - COMMISSION_RATE);
            priceBuyerPays.value = Math.round(withCommission) + ' РІвЂљР…';
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Edit Page - Buttons and Modal
(function() {
    function init() {
        if (document.querySelector('[data-managed-edit-page="custom"]')) return;

        const cancelBtn = document.querySelector('.edit_btn_cancel');
        const submitBtn = document.querySelector('.edit_btn_submit');
        const successModal = document.getElementById('editSuccessModal');
        const successOkBtn = document.getElementById('editSuccessOkBtn');
        
        if (!cancelBtn || !submitBtn) return; // Exit if not on edit page
        
        // Cancel button - redirect to product page
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'one_product.html';
        });
        
        // Submit button - show success modal
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show modal with animation
            successModal.style.display = 'flex';
            setTimeout(() => {
                successModal.classList.add('show');
            }, 10);
        });
        
        // OK button - redirect to product page
        if (successOkBtn) {
            successOkBtn.addEventListener('click', function() {
                successModal.classList.remove('show');
                setTimeout(() => {
                    window.location.href = 'one_product.html';
                }, 300);
            });
        }
        
        // Close modal on overlay click
        if (successModal) {
            successModal.addEventListener('click', function(e) {
                if (e.target === successModal) {
                    successModal.classList.remove('show');
                    setTimeout(() => {
                        successModal.style.display = 'none';
                    }, 300);
                }
            });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Delete Modal
(function() {
    function init() {
        const backBtn = document.getElementById('deleteBackBtn');
        const confirmBtn = document.getElementById('deleteConfirmBtn');
        
        if (!backBtn || !confirmBtn) return; // Exit if not on delete page
        
        // Back button - redirect to product page
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'one_product.html';
        });
        
        // Confirm button - delete and redirect
        confirmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Here you would typically send a delete request to the server
            // For now, just redirect to main page
            window.location.href = 'main.html';
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// ==================== Admin Tabs Switching ====================
(function() {
    function init() {
        const tabs = document.querySelectorAll('.admin_tab');
        const tables = document.querySelectorAll('.admin_table_wrapper');
        
        if (!tabs.length || !tables.length) return;
        
        // Initialize: Hide all tables except the first one (active tab)
        tables.forEach((table, index) => {
            if (index === 0) {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        });
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('admin_tab_active'));
                
                // Add active class to clicked tab
                this.classList.add('admin_tab_active');
                
                // Hide all tables
                tables.forEach(table => {
                    table.style.display = 'none';
                });
                
                // Show target table
                const targetTable = document.querySelector(`[data-table="${targetTab}"]`);
                if (targetTable) {
                    targetTable.style.display = 'block';
                }
            });
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// ==================== Product Description Accordion ====================
(function() {
    function init() {
        const toggleBtn = document.querySelector('.description_toggle');
        const hiddenContent = document.querySelector('.description_hidden');
        
        if (!toggleBtn || !hiddenContent) return;
        
        toggleBtn.addEventListener('click', function() {
            hiddenContent.classList.toggle('active');
            
            // Change button text
            if (hiddenContent.classList.contains('active')) {
                toggleBtn.textContent = 'Р РЋР Р†Р ВµРЎР‚Р Р…РЎС“РЎвЂљРЎРЉ';
            } else {
                toggleBtn.textContent = 'Р В§Р С‘РЎвЂљР В°РЎвЂљРЎРЉ Р Т‘Р В°Р В»Р ВµР Вµ';
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// ==================== Hide Share Button on Footer ====================
(function() {
    function init() {
        const shareBtn = document.querySelector('.share-btn');
        const footer = document.querySelector('.footer');
        
        if (!shareBtn || !footer) return; // Exit if elements not found
        
        // Create Intersection Observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Footer is visible - hide button
                    shareBtn.style.opacity = '0';
                    shareBtn.style.visibility = 'hidden';
                    shareBtn.style.pointerEvents = 'none';
                } else {
                    // Footer is not visible - show button
                    shareBtn.style.opacity = '1';
                    shareBtn.style.visibility = 'visible';
                    shareBtn.style.pointerEvents = 'all';
                }
            });
        }, {
            threshold: 0.1 // Trigger when 10% of footer is visible
        });
        
        // Start observing the footer
        observer.observe(footer);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
