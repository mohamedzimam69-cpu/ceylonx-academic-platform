// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-menu a').forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    });
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Navbar background on scroll
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(10, 10, 10, 0.98)';
    } else {
        navbar.style.background = 'rgba(10, 10, 10, 0.95)';
    }
});

// Form submission handling
const assignmentForm = document.getElementById('assignmentForm');
if (assignmentForm) {
    assignmentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(assignmentForm);
        const submitButton = assignmentForm.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        // Show loading state
        submitButton.textContent = 'Submitting...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch('submit_assignment.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('success', 'Assignment submitted successfully! We will contact you shortly.');
                assignmentForm.reset();
            } else {
                showMessage('error', result.message || 'An error occurred. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Network error. Please check your connection and try again.');
        } finally {
            // Reset button state
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });
}

// Show success/error messages
function showMessage(type, message) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message element
    const messageDiv = document.createElement('div');
    messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';
    
    // Insert after form
    const form = document.getElementById('assignmentForm');
    form.parentNode.insertBefore(messageDiv, form.nextSibling);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', () => {
    const animateElements = document.querySelectorAll('.service-card, .step, .contact-item');
    animateElements.forEach(el => observer.observe(el));
});

// Typing effect for hero text (optional enhancement)
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.innerHTML = '';
    
    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// Initialize typing effect on page load
document.addEventListener('DOMContentLoaded', () => {
    const heroTitle = document.querySelector('.hero h1');
    if (heroTitle) {
        const originalText = heroTitle.textContent;
        // Uncomment the line below to enable typing effect
        // typeWriter(heroTitle, originalText, 50);
    }
});

// Form validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#ff0000';
            isValid = false;
        } else {
            field.style.borderColor = 'rgba(255, 255, 255, 0.2)';
        }
    });
    
    // Email validation
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            emailField.style.borderColor = '#ff0000';
            isValid = false;
        }
    }
    
    // Phone validation
    const phoneField = form.querySelector('input[type="tel"]');
    if (phoneField && phoneField.value) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!phoneRegex.test(phoneField.value)) {
            phoneField.style.borderColor = '#ff0000';
            isValid = false;
        }
    }
    
    return isValid;
}

// Real-time form validation
document.addEventListener('DOMContentLoaded', () => {
    const formInputs = document.querySelectorAll('.submit-form input, .submit-form textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('blur', () => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                input.style.borderColor = '#ff0000';
            } else {
                input.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }
        });
        
        input.addEventListener('focus', () => {
            input.style.borderColor = '#00d4ff';
        });
    });
});

// Scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show scroll to top button when scrolling down
window.addEventListener('scroll', () => {
    const scrollButton = document.getElementById('scrollToTop');
    if (scrollButton) {
        if (window.scrollY > 300) {
            scrollButton.style.display = 'block';
        } else {
            scrollButton.style.display = 'none';
        }
    }
});

// Preloader (if needed)
window.addEventListener('load', () => {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.style.display = 'none';
    }
});

// Contact form handling (if separate contact form exists)
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!validateForm(contactForm)) {
            showMessage('error', 'Please fill in all required fields correctly.');
            return;
        }
        
        const formData = new FormData(contactForm);
        const submitButton = contactForm.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        submitButton.textContent = 'Sending...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch('contact.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('success', 'Message sent successfully! We will get back to you soon.');
                contactForm.reset();
            } else {
                showMessage('error', result.message || 'An error occurred. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Network error. Please check your connection and try again.');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });
}