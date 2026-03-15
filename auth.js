// auth.js - Shared authentication logic for DanceVerse

const Auth = {
    isLoggedIn: function() {
        return localStorage.getItem('isLoggedIn') === 'true';
    },

    login: function() {
        localStorage.setItem('isLoggedIn', 'true');
        window.location.href = 'index.html';
    },

    logout: function() {
        localStorage.removeItem('isLoggedIn');
        window.location.href = 'index.html';
    },

    requireAuth: function() {
        if (!this.isLoggedIn()) {
            alert('Please login or register to access this section.');
            window.location.href = 'login.html';
        }
    },

    updateNav: function() {
        const loginLink = document.querySelector('nav a[href="login.html"]');
        if (loginLink) {
            if (this.isLoggedIn()) {
                loginLink.textContent = 'Logout';
                loginLink.href = 'javascript:void(0)';
                loginLink.onclick = (e) => {
                    e.preventDefault();
                    this.logout();
                };
            } else {
                loginLink.textContent = 'Login';
                loginLink.href = 'login.html';
                loginLink.onclick = null;
            }
        }
    }
};

// Auto-run nav update on every page that includes this script
document.addEventListener('DOMContentLoaded', () => {
    Auth.updateNav();
});
