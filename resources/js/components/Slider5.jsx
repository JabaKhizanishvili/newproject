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
import { academySlider } from "./Data";
import { Link } from '@inertiajs/inertia-react'
import { Route } from "react-router-dom";

const Slider5 = ({data}) => {
    // console.log(data, 'esaa');
  const prevRef = useRef(null);
  const nextRef = useRef(null);

  return (
    <div className="courseSlider bg-custom-slate-300 text-custom-dark">
      <div className="wrapper">
        <Swiper
          loop
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
              <SwiperSlide key={index}>
                <div className=" flex items-start justify-between flex-col lg:flex-row  py-20">
                  <div className="lg:w-1/2">
                    <div className="xl:text-7xl sm:text-5xl text-2xl bold mb-7 ">
                      {item.title}
                    </div>
                    <div className="flex items-stretch">
                      <div className="sm:w-64 w-40 sm:mr-8 mr-4 shrink-0 grayscale">
                        <img
                          className="w-full h-full object-cover"
                          src={
                            item.files != null && item.files[0]
                            ? "/" +
                            item.files[0].path +
                            "/" +
                            item.files[0].title
                            : null

                           }
                          alt=""
                        />
                      </div>
                      <div className="max-w-sm">
                        <div className="xl:text-7xl sm:text-5xl text-xl bold opacity-50">
                          {item.short_description}
                        </div>
                        <p className="opacity-30 sm:my-10 my-2">{item.description}</p>
                        <Link
                          href={route("client.rayacademy.show",item.id)}
                          className={`flex items-center justify-center border border-solid border-custom-slate-900 bold text-custom-slate-900 sm:h-12 h-10 w-fit sm:px-7 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap`}
                        >
                          <span className="transition-all duration-300">
                            See details
                          </span>
                          <HiArrowNarrowRight className="w-6 h-6 ml-2" />
                        </Link>
                      </div>
                    </div>
                  </div>
                  <div className="pt-10 lg:w-1/2">
                    <img src={
                           item.files != null && item.files[1]
                           ? "/" +
                           item.files[1].path +
                           "/" +
                           item.files[1].title
                           : null
                        } alt="" />
                  </div>
                </div>
              </SwiperSlide>
            );
          })}

          <div className="absolute md:top-20 top-10 right-0 flex items-center justify-center z-20 ">
            <button
              ref={prevRef}
              className=" hidden transition-all duration-300"
            >
              <HiOutlineArrowCircleLeft className="inline-block w-7 h-7" />
              Previous course
            </button>
            <button
              ref={nextRef}
              className="sm:ml-2 ml-1 transition-all duration-300"
            >
              Next course
              <HiOutlineArrowCircleRight className="inline-block w-7 h-7" />
            </button>
          </div>
        </Swiper>
      </div>
    </div>
  );
};

export default Slider5;
