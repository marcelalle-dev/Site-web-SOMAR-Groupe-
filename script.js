// Dans votre script.js existant, ajoutez :
// Fonction pour enregistrer l'accès
function registerAccess() {
    localStorage.setItem('somar_access', 'true');
    sessionStorage.setItem('somar_access', 'true');
   
    // Définir un cookie valide 24h
    const date = new Date();
    date.setTime(date.getTime() + (24 * 60 * 60 * 1000));
    document.cookie = `somar_access=true; expires=${date.toUTCString()}; path=/`;
}
// Vérifier l'accès au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Si on vient de la page verify.php, enregistrer l'accès
    if (window.location.search.includes('access=granted')) {
        registerAccess();
    }
   
    // Afficher le nom du visiteur s'il est disponible
    const visitorName = localStorage.getItem('visitor_name');
    if (visitorName) {
        const welcomeElement = document.createElement('div');
        welcomeElement.className = 'visitor-welcome';
        welcomeElement.innerHTML = `
            <div style="background: #2ecc71; color: white; padding: 10px; text-align: center;">
                <i class="fas fa-user-check"></i> Bienvenue ${visitorName} !
            </div>
        `;
        document.body.insertBefore(welcomeElement, document.body.firstChild);
    }
});
// ============================================
// GESTION DU MENU HAMBURGER
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initModals();
    initForms();
    initSmoothScroll();
    initAnimatedStats();
});
function initMobileMenu() {
    const menuButton = document.getElementById('mobile-menu');
    const navMenu = document.querySelector('nav ul');
   
    if (!menuButton || !navMenu) return;
   
    menuButton.addEventListener('click', function(e) {
        e.stopPropagation();
        navMenu.classList.toggle('active');
        this.classList.toggle('active');
    });
   
    const navLinks = document.querySelectorAll('nav ul li a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            menuButton.classList.remove('active');
        });
    });
   
    document.addEventListener('click', function(e) {
        if (!navMenu.contains(e.target) && !menuButton.contains(e.target)) {
            navMenu.classList.remove('active');
            menuButton.classList.remove('active');
        }
    });
   
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            navMenu.classList.remove('active');
            menuButton.classList.remove('active');
        }
    });
}
// ============================================
// GESTION DES MODALS
// ============================================
function initModals() {
    let currentModal = null;
   
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
       
        currentModal = modal;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
       
        setTimeout(() => {
            modal.querySelector('.modal-content').style.transform = 'scale(1)';
            modal.querySelector('.modal-content').style.opacity = '1';
        }, 10);
    };
   
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId) || currentModal;
        if (!modal) return;
       
        modal.querySelector('.modal-content').style.transform = 'scale(0.9)';
        modal.querySelector('.modal-content').style.opacity = '0';
       
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.documentElement.style.overflow = 'auto';
            currentModal = null;
        }, 300);
    };
   
    document.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.course-modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });
   
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && currentModal) {
            closeModal();
        }
    });
   
    // Gestion des téléchargements
    const downloadButtons = document.querySelectorAll('.download-btn');
    downloadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const fileName = this.getAttribute('href').split('/').pop();
            const courseName = this.parentElement.querySelector('h4').textContent;
           
            // Animation de confirmation
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Téléchargement...';
            this.style.backgroundColor = '#2ecc71';
            this.style.pointerEvents = 'none';
           
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check"></i> Téléchargé !';
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.backgroundColor = '';
                    this.style.pointerEvents = 'auto';
                }, 2000);
            }, 1000);
           
            trackDownload(fileName, courseName);
        });
    });
}
// ============================================
// GESTION DES FORMULAIRES
// ============================================
function initForms() {
    // Formulaire de contact
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
           
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;
           
            if (!name || !email || !message) {
                showNotification('Veuillez remplir tous les champs obligatoires', 'error');
                return;
            }
           
            if (!validateEmail(email)) {
                showNotification('Veuillez entrer une adresse email valide', 'error');
                return;
            }
           
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            submitBtn.disabled = true;
           
            setTimeout(() => {
                showNotification('Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.', 'success');
                contactForm.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
               
                // Enregistrer le message dans localStorage (pour démo)
                let messages = JSON.parse(localStorage.getItem('contact_messages') || '[]');
                messages.push({
                    name: name,
                    email: email,
                    subject: document.getElementById('subject').value,
                    message: message,
                    date: new Date().toISOString()
                });
                localStorage.setItem('contact_messages', JSON.stringify(messages));
            }, 1500);
        });
    }
   
    // Formulaire newsletter
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
           
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
           
            if (!validateEmail(email)) {
                showNotification('Veuillez entrer une adresse email valide', 'error');
                return;
            }
           
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Abonnement...';
            submitBtn.disabled = true;
           
            setTimeout(() => {
                showNotification('Merci de vous être abonné à notre newsletter !', 'success');
                newsletterForm.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
               
                // Enregistrer l'abonnement
                let subscriptions = JSON.parse(localStorage.getItem('newsletter_subscriptions') || '[]');
                subscriptions.push({
                    email: email,
                    date: new Date().toISOString()
                });
                localStorage.setItem('newsletter_subscriptions', JSON.stringify(subscriptions));
            }, 1500);
        });
    }
}
// ============================================
// DÉFILEMENT FLUIDE
// ============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
           
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
           
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerHeight = document.querySelector('header').offsetHeight;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;
               
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
   
    // Animation de la navbar
    let lastScrollTop = 0;
    const header = document.querySelector('header');
   
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
       
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }
       
        lastScrollTop = scrollTop;
        animateOnScroll();
    });
}
// ============================================
// ANIMATION DES STATISTIQUES
// ============================================
function initAnimatedStats() {
    const statItems = document.querySelectorAll('.stat-item h3');
   
    const realValues = {
        0: 100,
        1: 50,
        2: 95,
        3: 2
    };
   
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                statItems.forEach((stat, index) => {
                    animateCounter(stat, realValues[index]);
                });
                observer.disconnect();
            }
        });
    }, { threshold: 0.5 });
   
    const statsSection = document.querySelector('.stats');
    if (statsSection) observer.observe(statsSection);
}
function animateCounter(element, targetValue) {
    const duration = 2000;
    const step = 20;
    const totalSteps = duration / step;
    const increment = targetValue / totalSteps;
    let currentValue = 0;
   
    const timer = setInterval(() => {
        currentValue += increment;
        if (currentValue >= targetValue) {
            element.textContent = targetValue + (element.textContent.includes('%') ? '%' : '+');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(currentValue) + (element.textContent.includes('%') ? '%' : '+');
        }
    }, step);
}
// ============================================
// FONCTIONS UTILITAIRES
// ============================================
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
function showNotification(message, type = 'info') {
    // Supprimer les notifications existantes
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) existingNotification.remove();
   
    // Créer la notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            ${message}
        </div>
    `;
   
    // Styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 300px;
        max-width: 400px;
    `;
   
    const colors = {
        success: '#27ae60',
        error: '#e74c3c',
        info: '#3498db',
        warning: '#f39c12'
    };
   
    notification.style.backgroundColor = colors[type] || colors.info;
   
    document.body.appendChild(notification);
   
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
   
    // Fermer en cliquant
    notification.addEventListener('click', () => {
        notification.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => notification.remove(), 300);
    });
}
function trackDownload(filename, coursename) {
    console.log(`Téléchargement : ${filename} - ${coursename}`);
   
    // Enregistrer le téléchargement dans localStorage
    let downloads = localStorage.getItem('downloads') || '{}';
    downloads = JSON.parse(downloads);
   
    if (!downloads[filename]) {
        downloads[filename] = 0;
    }
    downloads[filename]++;
   
    localStorage.setItem('downloads', JSON.stringify(downloads));
   
    // Afficher le nombre total de téléchargements
    const totalDownloads = Object.values(downloads).reduce((a, b) => a + b, 0);
    console.log(`Total des téléchargements : ${totalDownloads}`);
}
function animateOnScroll() {
    const elements = document.querySelectorAll('.service-card, .about-image, .testimonial-card');
   
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
       
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('animated');
        }
    });
}
// ============================================
// INITIALISATION AU CHARGEMENT
// ============================================
window.addEventListener('load', function() {
    // Afficher un message de bienvenue
    setTimeout(() => {
        const firstVisit = !localStorage.getItem('has_visited');
        if (firstVisit) {
            showNotification('Bienvenue sur SOMAR Groupe ! Découvrez nos ressources éducatives.', 'info');
            localStorage.setItem('has_visited', 'true');
        }
    }, 1000);
   
    // Afficher le nombre total de téléchargements (pour démo)
    const downloads = JSON.parse(localStorage.getItem('downloads') || '{}');
    const totalDownloads = Object.values(downloads).reduce((a, b) => a + b, 0);
    if (totalDownloads > 0) {
        console.log(`Nombre total de documents téléchargés : ${totalDownloads}`);
    }
});