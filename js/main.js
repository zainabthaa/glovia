// fade
const faders = document.querySelectorAll('.fade-section');
const appearOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px"
};

const appearOnScroll = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        const bounding = entry.boundingClientRect;

        if (entry.isIntersecting && bounding.top > 0) {
            entry.target.classList.add('visible');
        }

        if (!entry.isIntersecting && bounding.top > 0) {
            entry.target.classList.remove('visible');
        }
    });
}, appearOptions);

faders.forEach(section => {
    appearOnScroll.observe(section);
});

// user icon
document.addEventListener("DOMContentLoaded", function() {
    const accountBtn = document.getElementById("accountBtn");
    const username = localStorage.getItem("username");

    if (username && accountBtn) {
        const firstLetter = username.charAt(0).toUpperCase();
        accountBtn.textContent = firstLetter;
        accountBtn.classList.add("circle-button");
    }
});

// welcome pop-up
document.addEventListener("DOMContentLoaded", function () {
    const username = localStorage.getItem("username");
    const signup = localStorage.getItem("signup");

    if (signup) {
        document.getElementById("welcome-message").innerHTML = `Congratulations, ${username}! Your account has been successfully created.<br><br>You have become a part of our science-driven community.`;
        localStorage.removeItem("signup"); 
    }
    else if (username) {
        document.getElementById("welcome-message").textContent = `Welcome back, ${username}! We're excited to see you again. Let’s make today great!`;
    }

    if (username || signup) {
        document.getElementById("welcome-popup").style.display = "flex";
        localStorage.removeItem("username");
    }
});

function closePopup() {
    document.getElementById("welcome-popup").style.display = "none";
}

