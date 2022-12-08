import React, { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import { projectSlider } from "./Data";
import "swiper/css/scrollbar";
import { Scrollbar } from "swiper";

const Slider4 = ({data}) => {
  return (
    <Swiper
      slidesPerView={4}
      spaceBetween={30}
      scrollbar={true}
      modules={[Scrollbar]}
      className=""
      breakpoints={{
        1200: {
          slidesPerView: 4,
        },
        900: {
          slidesPerView: 3,
        },
        500: {
          slidesPerView: 2,
        },
        0: {
          slidesPerView: 1,
        },
      }}
    >
      {data.map((item, index) => {
        return (
          <SwiperSlide className="pb-10" key={index}>
            <div className="">
              <div className="w-full h-80 mb-5">
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
              <p className="opacity-50">{item.name}</p>
            </div>
          </SwiperSlide>
        );
      })}
    </Swiper>
  );
};

export default Slider4;
