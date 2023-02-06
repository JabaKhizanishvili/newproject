$(".hero_slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: "#hero_prev",
    nextArrow: "#hero_next",
    dots: true,
    fade: true,
    speed: 900,
    infinite: true,
    cssEase: "cubic-bezier(0.7, 0, 0.3, 1)",
    touchThreshold: 100,
    autoplay: true,
    autoplaySpeed: 4000,
    pauseOnHover: false,
});

$(".home_products_slider").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: "#prev_product",
    nextArrow: "#next_product",
    dots: false,
    speed: 600,
    infinite: true,
    touchThreshold: 100,
    autoplay: false,
    variableWidth: true,
    responsive: [
        {
            breakpoint: 1100,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 800,
            settings: {
                slidesToShow: 1,
            },
        },
    ],
});
$(".home_projects_slider").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: "#prev_project",
    nextArrow: "#next_project",
    dots: false,
    speed: 600,
    infinite: true,
    touchThreshold: 100,
    autoplay: false,
    variableWidth: true,
    responsive: [
        {
            breakpoint: 1100,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 800,
            settings: {
                slidesToShow: 1,
            },
        },
    ],
});
$(".home_about_slider").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: false,
    nextArrow: "#next_about",
    dots: false,
    speed: 500,
    infinite: true,
    touchThreshold: 100,
    autoplay: false,
    variableWidth: true,
});
// $('.ppage_slider').each(function() {
//     var slickInduvidual = $(this);
//     console.log(slickInduvidual.next().find('.pp_prev'));
//     slickInduvidual.slick({
//         slidesToShow: 4,
//         slidesToScroll: 4,
//         draggable: true,
//         arrows: true,
//         prevArrow: slickInduvidual.siblings('.pp_prev'),
//         nextArrow: slickInduvidual.siblings('.pp_next'),
//         dots: true,
//         speed: 500,
//         infinite: true,
//         touchThreshold: 100,
//         autoplay: false,
//         variableWidth: true,
//         responsive: [
//             {
//                 breakpoint: 1300,
//                 settings: {
//                     slidesToShow: 3,
//                 },
//             },
//             {
//                 breakpoint: 1000,
//                 settings: {
//                     slidesToShow: 2,
//                 },
//             },
//             {
//                 breakpoint: 400,
//                 settings: {
//                     slidesToShow: 1,
//                 },
//             },
//         ],
//     });
// });
$(".ppage_slider").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: "#pp_prev",
    nextArrow: "#pp_next",
    dots: false,
    speed: 500,
    infinite: true,
    touchThreshold: 100,
    autoplay: false,
    variableWidth: true,
    responsive: [
        {
            breakpoint: 1300,
            settings: {
                slidesToShow: 3,
            },
        },
        {
            breakpoint: 1000,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 500,
            settings: {
                slidesToShow: 1,
            },
        },
    ],
});
$(".details_slider").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: "#prev_det_img",
    nextArrow: "#next_det_img",
    dots: false,
    speed: 500,
    infinite: false,
    touchThreshold: 100,
    autoplay: false,
    variableWidth: true,
    responsive: [
        {
            breakpoint: 550,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 450,
            settings: {
                slidesToShow: 1,
            },
        },
    ],
});
$(".bottom_about_slide").slick({
    slidesToShow: 6,
    slidesToScroll: 1,
    draggable: true,
    arrows: true,
    prevArrow: "#prev_about",
    nextArrow: "#next_about",
    dots: false,
    speed: 1000,
    infinite: true,
    touchThreshold: 100,
    autoplay: false,
    variableWidth: true,
    responsive: [
        {
            breakpoint: 1500,
            settings: {
                slidesToShow: 5,
            },
        },
        {
            breakpoint: 1200,
            settings: {
                slidesToShow: 4,
            },
        },
        {
            breakpoint: 900,
            settings: {
                slidesToShow: 3,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 400,
            settings: {
                slidesToShow: 1,
            },
        },
    ],
});
