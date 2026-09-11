document.addEventListener("DOMContentLoaded", function () {
  initHeaderScroll();
  initMobileNav();
  initScrollReveal();
  initCounters();
  initGalleryLightbox();
  initContactForm();
  initBackToTop();
  initYear();
});

/* ---------- Header background on scroll ---------- */
function initHeaderScroll() {
  var header = document.querySelector(".site-header");
  if (!header) return;

  function update() {
    if (window.scrollY > 12) {
      header.style.background = "rgba(10, 10, 12, 0.92)";
      header.style.boxShadow = "0 8px 30px rgba(0,0,0,0.35)";
    } else {
      header.style.background = "rgba(10, 10, 12, 0.7)";
      header.style.boxShadow = "none";
    }
  }
  update();
  window.addEventListener("scroll", update, { passive: true });
}

/* ---------- Mobile navigation toggle ---------- */
function initMobileNav() {
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.querySelector(".main-nav");
  if (!toggle || !nav) return;

  function closeNav() {
    nav.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
    document.body.style.overflow = "";
  }

  function openNav() {
    nav.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
    document.body.style.overflow = "hidden";
  }

  toggle.addEventListener("click", function () {
    var isOpen = nav.classList.contains("is-open");
    if (isOpen) {
      closeNav();
    } else {
      openNav();
    }
  });

  nav.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", closeNav);
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeNav();
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth > 860) closeNav();
  });
}

/* ---------- Scroll reveal animations ---------- */
function initScrollReveal() {
  var items = document.querySelectorAll(".reveal");
  if (!items.length) return;

  if (!("IntersectionObserver" in window)) {
    items.forEach(function (el) {
      el.classList.add("in-view");
    });
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("in-view");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15, rootMargin: "0px 0px -40px 0px" }
  );

  items.forEach(function (el) {
    observer.observe(el);
  });
}

/* ---------- Animated stat counters ---------- */
function initCounters() {
  var counters = document.querySelectorAll("[data-count]");
  if (!counters.length) return;

  function animate(el) {
    var target = parseInt(el.getAttribute("data-count"), 10);
    var suffix = el.getAttribute("data-suffix") || "";
    var duration = 1400;
    var start = null;

    function step(timestamp) {
      if (!start) start = timestamp;
      var progress = Math.min((timestamp - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target) + suffix;
      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        el.textContent = target + suffix;
      }
    }
    window.requestAnimationFrame(step);
  }

  if (!("IntersectionObserver" in window)) {
    counters.forEach(animate);
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animate(entry.target);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.4 }
  );

  counters.forEach(function (el) {
    observer.observe(el);
  });
}

/* ---------- Gallery lightbox ---------- */
function initGalleryLightbox() {
  var items = Array.prototype.slice.call(document.querySelectorAll(".gallery-item"));
  var lightbox = document.getElementById("lightbox");
  if (!items.length || !lightbox) return;

  var imgEl = lightbox.querySelector(".lightbox-inner img");
  var captionEl = lightbox.querySelector(".lightbox-caption");
  var closeBtn = lightbox.querySelector(".lightbox-close");
  var prevBtn = lightbox.querySelector(".lightbox-prev");
  var nextBtn = lightbox.querySelector(".lightbox-next");
  var currentIndex = 0;
  var lastFocused = null;

  function openAt(index) {
    currentIndex = (index + items.length) % items.length;
    var item = items[currentIndex];
    var fullSrc = item.getAttribute("data-full") || item.querySelector("img").src;
    var title = item.getAttribute("data-title") || "";
    var desc = item.getAttribute("data-desc") || "";

    imgEl.setAttribute("src", fullSrc);
    imgEl.setAttribute("alt", item.querySelector("img").alt || title);
    captionEl.textContent = desc ? title + " — " + desc : title;

    lastFocused = document.activeElement;
    lightbox.classList.add("is-open");
    lightbox.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    closeBtn.focus();
  }

  function close() {
    lightbox.classList.remove("is-open");
    lightbox.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    if (lastFocused) lastFocused.focus();
  }

  items.forEach(function (item, index) {
    item.setAttribute("tabindex", "0");
    item.setAttribute("role", "button");
    item.addEventListener("click", function () {
      openAt(index);
    });
    item.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        openAt(index);
      }
    });
  });

  closeBtn.addEventListener("click", close);
  prevBtn.addEventListener("click", function () {
    openAt(currentIndex - 1);
  });
  nextBtn.addEventListener("click", function () {
    openAt(currentIndex + 1);
  });

  lightbox.addEventListener("click", function (e) {
    if (e.target === lightbox) close();
  });

  document.addEventListener("keydown", function (e) {
    if (!lightbox.classList.contains("is-open")) return;
    if (e.key === "Escape") close();
    if (e.key === "ArrowRight") openAt(currentIndex + 1);
    if (e.key === "ArrowLeft") openAt(currentIndex - 1);
  });
}

/* ---------- Contact form validation ---------- */
function initContactForm() {
  var form = document.getElementById("contact-form");
  if (!form) return;

  var status = document.getElementById("form-status");

  var validators = {
    name: function (value) {
      if (!value.trim()) return "Please enter your full name.";
      if (value.trim().length < 2) return "Name must be at least 2 characters.";
      return "";
    },
    email: function (value) {
      var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!value.trim()) return "Please enter your email address.";
      if (!pattern.test(value)) return "Please enter a valid email address.";
      return "";
    },
    phone: function (value) {
      if (!value.trim()) return "";
      var pattern = /^[0-9+()\-\s]{7,20}$/;
      if (!pattern.test(value)) return "Please enter a valid phone number.";
      return "";
    },
    subject: function (value) {
      if (!value) return "Please select an enquiry type.";
      return "";
    },
    message: function (value) {
      if (!value.trim()) return "Please enter a message.";
      if (value.trim().length < 10) return "Message should be at least 10 characters.";
      return "";
    },
    consent: function (checked) {
      if (!checked) return "Please agree before sending your message.";
      return "";
    },
  };

  function showError(field, message) {
    var wrap = field.closest(".field");
    if (!wrap) return;
    var errorEl = wrap.querySelector(".field-error");
    if (message) {
      wrap.classList.add("has-error");
      if (errorEl) errorEl.textContent = message;
      field.setAttribute("aria-invalid", "true");
    } else {
      wrap.classList.remove("has-error");
      if (errorEl) errorEl.textContent = "";
      field.removeAttribute("aria-invalid");
    }
  }

  function validateField(field) {
    var name = field.name;
    if (!validators[name]) return true;
    var value = field.type === "checkbox" ? field.checked : field.value;
    var message = validators[name](value);
    showError(field, message);
    return !message;
  }

  ["name", "email", "phone", "subject", "message", "consent"].forEach(function (name) {
    var field = form.elements[name];
    if (!field) return;
    var eventType = field.type === "checkbox" || field.tagName === "SELECT" ? "change" : "input";
    field.addEventListener(eventType, function () {
      validateField(field);
    });
    field.addEventListener("blur", function () {
      validateField(field);
    });
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    var isValid = true;
    ["name", "email", "phone", "subject", "message", "consent"].forEach(function (name) {
      var field = form.elements[name];
      if (!field) return;
      if (!validateField(field)) isValid = false;
    });

    status.classList.remove("show", "success", "error");

    if (!isValid) {
      status.classList.add("show", "error");
      status.innerHTML =
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>Please fix the highlighted fields and try again.</span>';
      var firstError = form.querySelector(".has-error input, .has-error select, .has-error textarea");
      if (firstError) firstError.focus();
      return;
    }

    status.classList.add("show", "success");
    status.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span>Thanks! Your message has been sent — our team will reply within 1 business day.</span>';
    form.reset();
    form.querySelectorAll(".field").forEach(function (wrap) {
      wrap.classList.remove("has-error");
    });
  });
}

/* ---------- Back to top button ---------- */
function initBackToTop() {
  var btn = document.querySelector(".back-to-top");
  if (!btn) return;

  window.addEventListener(
    "scroll",
    function () {
      if (window.scrollY > 500) {
        btn.classList.add("show");
      } else {
        btn.classList.remove("show");
      }
    },
    { passive: true }
  );

  btn.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

/* ---------- Footer year ---------- */
function initYear() {
  var el = document.getElementById("year");
  if (el) el.textContent = new Date().getFullYear();
}
