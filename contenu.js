window.addEventListener("scroll", function () {
    const nav = document.getElementById("menubar");
    if (window.scrollY > 99) {
        nav.classList.add("active");
    } else {
        nav.classList.remove("active");
    }
});
window.addEventListener("scroll", function () {
    const btn = document.getElementById("dark");
    if (window.scrollY > 99) {
        btn.classList.add("active");
    } else {
        btn.classList.remove("active");
    }
});

let slideIndex = 1;
showSlide(slideIndex);

function moveSlide(n) {
    showSlide(slideIndex += n);
}

function currentSlide(n) {
    showSlide(slideIndex = n);
}

function showSlide(n) {
    const slides = document.querySelectorAll('.item');

    if (n > slides.length) { slideIndex = 1; }
    if (n < 1) { slideIndex = slides.length; }

    slides.forEach(function (slide) {
        slide.style.display = "none";
    });

    slides[slideIndex - 1].style.display = "block";
}

document.getElementById("dark").addEventListener("click", function () {
    document.body.classList.toggle("dark");
});
