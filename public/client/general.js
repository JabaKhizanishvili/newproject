const search = document.querySelector(".search");
const searchButton = document.querySelector(".search_icon");
const closeSearch = document.querySelector(".close_search");
// const projectFilterHome = document.querySelectorAll(".project_filter_home");
// const projectContentHome = document.querySelectorAll(".project_content_home");
const viewVideo = document.querySelectorAll(".view_video");
const videoPopup = document.querySelectorAll(".the_video_popup");
const closeVideo = document.querySelectorAll(".close_vid_popup");
// const productFilterPage = document.querySelectorAll(".product_filter_page");
// const productPcontent = document.querySelectorAll(".product_p_content");
const detImgFilter = document.querySelectorAll(".detail_img_filter");
const largeImage = document.querySelectorAll(".large_image");
// const projectPFilter = document.querySelectorAll(".project_p_filter");
// const projectMainGrid = document.querySelectorAll(".project_main_grid");

// search input
searchButton.addEventListener("click", () => {
    search.classList.add("open");
});
closeSearch.addEventListener("click", () => {
    search.classList.remove("open");
});

// project filter on home page
// projectFilterHome.forEach((el, i) => {
//     el.addEventListener("click", () => {
//         projectFilterHome.forEach((el) => {
//             el.classList.remove("active");
//         });
//         projectContentHome.forEach((el) => {
//             el.classList.remove("active");
//         });
//         projectFilterHome[i].classList.add("active");
//         projectContentHome[i].classList.add("active");
//     });
// });

// video popup
if ((viewVideo && closeVideo)) {
    viewVideo.forEach((el) => {
        el.addEventListener("click", () => {
            videoPopup.forEach((video) => {
                if (video.id===el.id){
                    video.classList.add("open");
                }
            });
        });
    });
    closeVideo.forEach((el) =>{
        el.addEventListener("click", () => {
            videoPopup.forEach((video) => {
                    video.classList.remove("open");
            });
        });
    });

}

// product filter on p page
// productFilterPage.forEach((el, i) => {
//     el.addEventListener("click", () => {
//
//         productFilterPage.forEach((el) => {
//             el.classList.remove("active");
//         });
//         productPcontent.forEach((el) => {
//             el.classList.remove("active");
//         });
//         productFilterPage[i].classList.add("active");
//         productPcontent[i].classList.add("active");
//
//     });
// });

// detail images filter slider
detImgFilter.forEach((el, i) => {
    el.addEventListener("mouseenter", () => {
        detImgFilter.forEach((el) => {
            el.classList.remove("active");
        });
        largeImage.forEach((el) => {
            el.classList.remove("active");
        });
        detImgFilter[i].classList.add("active");
        largeImage[i].classList.add("active");
    });
});

// project main page filter
// projectPFilter.forEach((el, i) => {
//     el.addEventListener("click", () => {
//         projectPFilter.forEach((el) => {
//             el.classList.remove("active");
//         });
//         projectMainGrid.forEach((el) => {
//             el.classList.remove("active");
//         });
//         projectPFilter[i].classList.add("active");
//         projectMainGrid[i].classList.add("active");
//     });
// });

// responsive changes
const menuButton = document.querySelector(".menu_button");
const headerRight = document.querySelector(".header_right");

menuButton.addEventListener("click", () => {
    menuButton.classList.toggle("clicked");
    headerRight.classList.toggle("open");
});
