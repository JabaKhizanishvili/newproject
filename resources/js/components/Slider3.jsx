import React, { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation } from "swiper";
import {
  HiArrowNarrowRight,
  HiOutlineArrowCircleRight,
  HiOutlineArrowCircleLeft,
} from "react-icons/hi";
import { companySlide } from "./Data";
// import { Link } from "react-router-dom";
import { Link } from '@inertiajs/inertia-react'

const Slider3 = ({data, links}) => {
    console.log(data);
  const prevRef = useRef(null);
  const nextRef = useRef(null);

  return (
    <div className="companySlider">
      <Swiper
        // loop
        slidesPerView="auto"
        spaceBetween={30}
        grabCursor
        navigation={true}
        modules={[Navigation]}
        onInit={(swiper) => {
          swiper.params.navigation.prevEl = prevRef.current;
          swiper.params.navigation.nextEl = nextRef.current;
          swiper.navigation.init();
          swiper.navigation.update();
        }}
      >
        {data.map((item, index) => {
          return (
            <SwiperSlide className="slide-item" key={index}>
              <div className="h-full flex items-start justify-start flex-col md:flex-row">
                <div className="img h-full mr-4">
                  <img
                    className="w-full h-full object-cover"
                    src={
                        item.file != null && item.file
                        ? "/" +
                        item.file.path +
                        "/" +
                        item.file.title
                        : null
                    }
                    alt=""
                  />
                </div>
                <div className="max-w-sm pt-5">
                  <img className="h-12" src={links + "/" +item.logo} alt="" />
                  <div className="xl:text-7xl md:text-5xl text-3xl bold xl:my-8 my-5 md:-ml-20 !leading-tight">
                    {item.title}
                  </div>
                  <p className="opacity-30 mb-5">{item.short_description}</p>
                  <div className="companyBox">
                    <Link
                      href="/news"
                      className={`flex items-center justify-center border border-solid bold  sm:h-12 h-10 w-fit sm:px-7 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap`}
                      style={{ color: item.color, borderColor: item.color }}
                    >
                      <span className="transition-all duration-300">
                        Learn more
                      </span>
                      <HiArrowNarrowRight className="w-6 h-6 " />
                    </Link>
                  </div>
                </div>
              </div>
            </SwiperSlide>
          );
        })}

        <div className="absolute bottom-4 sm:right-52 right-8 flex items-center justify-center z-20 opacity-50">
          <button
            ref={prevRef}
            className="transition-all duration-300 active:scale-90"
          >
            <HiOutlineArrowCircleLeft className="w-8 h-8" />
          </button>
          <button
            ref={nextRef}
            className="sm:ml-2 ml-1 transition-all duration-300 active:scale-90"
          >
            <HiOutlineArrowCircleRight className="w-8 h-8" />
          </button>
        </div>
      </Swiper>
    </div>
  );
};

export default Slider3;
