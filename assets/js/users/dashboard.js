// bannerSlider.js
document.addEventListener('DOMContentLoaded', () => {
    const swiper = new Swiper('.banner-swiper', {
        loop: true,
        speed: 800,
        autoplay: {
        delay: 3500,
        disableOnInteraction: false,
        },
        pagination: {
        el: '.swiper-pagination',
        clickable: true,
        },
        navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
        },
        effect: 'slide', // bisa diganti 'fade' kalau mau halus
    });

    console.log('Banner slider aktif ✅');
});
