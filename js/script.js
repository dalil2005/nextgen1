
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

function updateSlides() {
    document.querySelector('.slides').style.transform = `translateX(-${currentSlide * 100}%)`;
 
    dots.forEach(dot => dot.classList.remove('active'));
    dots[currentSlide].classList.add('active');
}

function changeSlide(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateSlides();
}

function goToSlide(slideIndex) {
    currentSlide = slideIndex;
    updateSlides();
}

setInterval(() => {
    changeSlide(1);
}, 5000);

updateSlides();

const menuBar = document.querySelector('.bar');
const menu = document.querySelector('.menu');

menuBar.addEventListener('click', () => {
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});

gsap.registerPlugin(ScrollTrigger);
gsap.utils.toArray('.aboutUs .box').forEach(box => {
    gsap.from(box, {
        y: 50,
        opacity: 0,
        duration: 1,
        scrollTrigger: {
            trigger: box,
            start: "top 80%",
            end: "top 50%",
            toggleActions: "play none none reverse"
        }
    });
});

gsap.utils.toArray('.gallery .box').forEach(box => {
    gsap.from(box, {
        scale: 0.8,
        opacity: 0,
        duration: 0.5,
        scrollTrigger: {
            trigger: box,
            start: "top 80%",
            end: "top 50%",
            toggleActions: "play none none reverse"
        }
    });
});
const inputs = document.querySelectorAll(".input");

function focusFunc() {
  let parent = this.parentNode;
  parent.classList.add("focus");
}

function blurFunc() {
  let parent = this.parentNode;
  if (this.value == "") {
    parent.classList.remove("focus");
  }
}

inputs.forEach((input) => {
  input.addEventListener("focus", focusFunc);
  input.addEventListener("blur", blurFunc);
});