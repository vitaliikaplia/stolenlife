window.addEventListener('DOMContentLoaded', () => {

    if (document.querySelector('.custom-preloader')) {
        startPreloader(document.querySelector('.custom-preloader'));
    }

    if (document.querySelector('.authors-wrapper')) {
        startHorizontalScroll(document.querySelectorAll('.author-container'))
    }

    if (document.querySelector('.target')) {
        createCursor(document.querySelector(".custom-cursor"))
    }

    if (document.querySelector('.gallery-wrapper')) {
        startGallery(document.querySelector('.gallery-wrapper'))
    }
});

/**
 * fix onload animation bug
 */
const fixChromeLoadingPageAnimationIssue = function(){
    document.body.classList.remove('preload');
}
window.addEventListener("DOMContentLoaded", fixChromeLoadingPageAnimationIssue);

// preloader
function startPreloader(preloader) {
    const body = document.querySelector('body');

    if (!preloader) {
        body.classList.add('is-ready');
        return;
    }

    preloader.style.display = 'flex';
    const isVisited = checkPreloader();


    if (isVisited) {

        if (!body || !preloader) return;
        body.classList.add('is-loading');
        body.classList.add('no-scroll');
        body.classList.remove('is-ready');

        createPreloader(preloader, 2500)

        setTimeout(() => {
            body.classList.remove('is-loading');
            body.classList.remove('no-scroll');
            body.classList.add('is-ready');
        }, 2500);

        runAnimations()
    } else {

        preloader.style.display = 'none';
        preloader.style.opacity = '0';
        body.classList.remove('is-loading');
        body.classList.remove('no-scroll');
        body.classList.add('is-ready');
        runAnimations()
    }
}

function createPreloader(preloader, time) {

    const progressCircle = preloader.querySelector('.progress');
    const countEl = preloader.querySelector('.loading-count');

    const radius = 70;
    const circumference = 2 * Math.PI * radius;

    progressCircle.style.strokeDasharray = circumference;
    progressCircle.style.strokeDashoffset = circumference;

    countEl.textContent = "0";

    let progress = 0;
    const duration = time;
    const start = performance.now();

    function animate(time) {
        const elapsed = time - start;
        progress = Math.min((elapsed / duration) * 100, 100);

        countEl.textContent = Math.round(progress);

        let offset;
        offset = circumference - (progress / 100) * circumference;
        progressCircle.style.strokeDashoffset = offset;

        if (progress < 100) {
            requestAnimationFrame(animate);
        } else {

            setTimeout(() => {
                preloader.style.opacity = "0";
                setTimeout(() => preloader.remove(), 1000);
            }, 200);
        }
    }

    requestAnimationFrame(animate);
}

function checkPreloader() {
    if (!sessionStorage.getItem('siteVisited')) {
        sessionStorage.setItem('siteVisited', 'true');
        return true
    } else {
        return false
    }
}

//horizontal scroll
function startHorizontalScroll(authors) {
    if (!authors || !authors.length > 2) return;

    gsap.registerPlugin(ScrollTrigger);
    let sections = gsap.utils.toArray(authors);
    let prevButton = document.querySelector(".prev");
    let nextButton = document.querySelector(".next");

    let maxScroll = window.innerWidth * (sections.length - 1);

    ScrollTrigger.matchMedia({
        "(min-width: 960px)": function () {
            let scrollTween = gsap.to(sections, {
                xPercent: -100 * (sections.length - 1),
                ease: "none",
                scrollTrigger: {
                    trigger: ".authors-wrapper",
                    start: "center center",
                    pin: true,
                    scrub: 1,
                    end: () => `+=${maxScroll}`,
                    anticipatePin: 1,
                    pinSpacing: true,
                }
            });

            let currentIndex = 0;

            function goToSection(index) {
                currentIndex = gsap.utils.clamp(0, sections.length - 1, index);
                let progress = currentIndex / (sections.length - 1);
                gsap.to(scrollTween, {
                    progress: progress,
                    duration: 0.6,
                    ease: "power2.inOut"
                });
            }

            nextButton.addEventListener("click", () => {
                goToSection(currentIndex + 1);
            });

            prevButton.addEventListener("click", () => {
                goToSection(currentIndex - 1);
            });
        }
    });
}

function createCursor(cursor) {

    if (!cursor) return;
    const targets = document.querySelectorAll(".target");

    gsap.set(cursor, {autoAlpha: 0, scale: 0.5});

    targets.forEach(target => {
        // === Включаем курсор при входе ===
        target.addEventListener("mouseenter", () => {
            gsap.to(cursor, {
                autoAlpha: 1,
                scale: 1,
                duration: 0.3,
                ease: "power3.out"
            });

            target.addEventListener("mousemove", moveCursor);
            target.addEventListener("mousedown", pressCursor);
            target.addEventListener("mouseup", releaseCursor);
        });

        target.addEventListener("mouseleave", () => {
            gsap.to(cursor, {
                autoAlpha: 0,
                scale: 0.5,
                duration: 0.3,
                ease: "power3.in"
            });

            target.removeEventListener("mousemove", moveCursor);
            target.removeEventListener("mousedown", pressCursor);
            target.removeEventListener("mouseup", releaseCursor);
        });
    });

    function moveCursor(e) {
        gsap.to(cursor, {
            x: e.clientX - cursor.offsetWidth / 2,
            y: e.clientY - cursor.offsetHeight / 2,
            duration: 0.15,
            ease: "power3.out"
        });
    }

    function pressCursor() {
        gsap.to(cursor, {
            scale: 0.9,
            duration: 0.15,
            ease: "power2.out"
        });
    }

    function releaseCursor() {
        gsap.to(cursor, {
            scale: 1.1,
            duration: 0.2,
            ease: "power2.out",
            onComplete: () => {
                gsap.to(cursor, {
                    scale: 1,
                    duration: 0.2,
                    ease: "elastic.out(1, 0.4)"
                });
            }
        });
    }
}

// animation
function runAnimations() {
    const observerOptions = {
        root: null,
        rootMargin: "0px",
        threshold: [0.1, 0.9]
    };

    const animatedElements = document.querySelectorAll(".animated");

    animatedElements.forEach(el => {
        if (el.classList.contains("left")) {
            el.style.opacity = 0;
            el.style.transform = "translateX(-50%)";
        } else if (el.classList.contains("right")) {
            el.style.opacity = 0;
            el.style.transform = "translateX(50%)";
        } else if (el.classList.contains("center")) {
            el.style.opacity = 0;
        } else if (el.classList.contains("list")) {
            el.querySelectorAll("a").forEach(el => {
                el.style.opacity = 0;
                el.style.transform = "translateY(-50px)";
            });
        }
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const el = entry.target;

            if (entry.isIntersecting && entry.intersectionRatio >= 0.1) {
                if (el.classList.contains("left")) {
                    el.style.transition = "all 1.2s ease-out";
                    el.style.opacity = 1;
                    el.style.transform = "translateX(0)";
                } else if (el.classList.contains("right")) {
                    el.style.transition = "all 1.2s ease-out";
                    el.style.opacity = 1;
                    el.style.transform = "translateX(0)";
                } else if (el.classList.contains("center")) {
                    el.style.transition = "opacity 1.2s ease-out";
                    el.style.opacity = 1;
                } else if (el.classList.contains("list")) {
                    el.querySelectorAll("a").forEach((el, i) => {
                        el.style.transition = `all 0.8s ${i * 0.2}s ease-out`;
                        el.style.opacity = 1;
                        el.style.transform = "translateY(0)";
                    });
                }
            } else {
                if (el.classList.contains("left")) {
                    el.style.opacity = 0;
                    el.style.transform = "translateX(-50%)";
                } else if (el.classList.contains("right")) {
                    el.style.opacity = 0;
                    el.style.transform = "translateX(50%)";
                } else if (el.classList.contains("center")) {
                    el.style.opacity = 0;
                } else if (el.classList.contains("list")) {
                    el.querySelectorAll("a").forEach(el => {
                        el.style.opacity = 0;
                        el.style.transform = "translateY(-50px)";
                    });
                }
            }
        });
    }, observerOptions);

    animatedElements.forEach(el => observer.observe(el));
}

function startGallery(gallery) {
    const buttons = document.querySelectorAll(".gallery-item");
    const body = document.querySelector('body');
    const closeButton = gallery.querySelector('.close-gallery');
    const slider = gallery.querySelector('.gallery-swiper'); // Точніше
    const cap = {
        root: gallery.querySelector('.gallery-caption'),
        title: gallery.querySelector('.gc-title'),
        desc:  gallery.querySelector('.gc-desc'),
        loc:   gallery.querySelector('.gc-location'),
        lim:   gallery.querySelector('.gc-limited'),
    };

    let swiperInstance = null;
    const OPEN = 'is-open';

    function fillCaption(fromSlideEl){
        if (!fromSlideEl) return;
        cap.title.textContent = `"${fromSlideEl.getAttribute('data-title') || ''}"`;
        cap.desc.innerHTML = fromSlideEl.getAttribute('data-content') || '';
        cap.loc.innerHTML = fromSlideEl.getAttribute('data-location') || '';
        cap.lim.textContent = fromSlideEl.getAttribute('data-limited') || '';
    }

    function createSwiper() {
        // Якщо хтось ініціалізував раніше (у app.min.js) — зносимо
        if (slider.swiper) {
            slider.swiper.destroy(true, true);
        }
        swiperInstance = new Swiper(slider, {
            slidesPerView: 1,
            spaceBetween: 0,
            centeredSlides: false,
            loop: true,
            speed: 500,
            effect: "slide",
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            on: {
                init(sw){ fillCaption(sw.slides[sw.activeIndex]); },
                slideChange(sw){ fillCaption(sw.slides[sw.activeIndex]); }
            }
        });
    }

    buttons.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const focusedId = parseInt(button.getAttribute("data-id") || '1', 10) - 1;

            body.classList.add("no-scroll");
            gallery.classList.add(OPEN);

            if (!swiperInstance) createSwiper();
            swiperInstance.slideToLoop(Math.max(0, focusedId), 0);
            requestAnimationFrame(() => fillCaption(swiperInstance.slides[swiperInstance.activeIndex]));
        });
    });

    closeButton.addEventListener("click", (e) => {
        e.preventDefault();
        gallery.classList.remove(OPEN);
        body.classList.remove("no-scroll");
    });
}
