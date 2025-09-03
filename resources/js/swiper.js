import Swiper from 'swiper';
import { Navigation, Pagination, Scrollbar } from 'swiper/modules';

// import Swiper and modules styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

const swiper = new Swiper('.swiper', {
    loop: true, // 無限ループ
    slidesPerView: 1, // 1枚ずつ表示
    spaceBetween: 10, // スライド間の余白(px)

    // モジュールを明示的に指定
    modules: [Navigation, Pagination, Scrollbar],

    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },

    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    scrollbar: {
        el: '.swiper-scrollbar',
        draggable: true,
    },
});
