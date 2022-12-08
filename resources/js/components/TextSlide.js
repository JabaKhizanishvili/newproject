import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import { Autoplay } from "swiper";

const TextSlide = () => {
    return (
        <Swiper
            modules={[Autoplay]}
            loop
            autoplay={{
                delay: 1,
                disableOnInteraction: false,
            }}
            slidesPerView="auto"
            speed={10000}
            mousewheelControl={true}
            keyboardControl={true}
            spaceBetween={30}
            varibleWidth
        >
            <SwiperSlide className="opacity-20 text-9xl  text-white whitespace-nowrap w-auto ml-60">
                Brand Platform & Identity Design
            </SwiperSlide>
            <SwiperSlide className="opacity-20 text-9xl  text-white whitespace-nowrap w-auto"></SwiperSlide>
        </Swiper>
    );
};

export default TextSlide;
