import React, { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";
import { Pagination, Navigation } from "swiper";
import { HiOutlineArrowCircleRight, HiArrowNarrowRight } from "react-icons/hi";
import { MdOutlineLocationOn } from "react-icons/md";
import { eventSlider } from "./Data";
// import Shade from "../assets/images/shapes/7.png";
// import { Link } from "react-router-dom";
import { Link, usePage} from '@inertiajs/inertia-react'

const Slider2 = ({data}) => {
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
const sharedData = usePage().props.localizations;
  const nextRef = useRef(null);
  return (
    <div className="wrapper py-12">
      <div className="bold lg:text-4xl text-2xl text-center mb-12">
        {/* Upcoming Events */}
        {renderHTML(__("client.home_main_events", sharedData))}
      </div>
      <div className="relative bg-custom-slate-300 rounded-2xl text-custom-dark ">
        <img
          className="absolute top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2 -z-10"
          src="/assets/images/shapes/7.png"
          alt=""
        />
        <Swiper
        //   loop
          slidesPerView={3}
          slidesPerGroup={3}
          pagination={{
            clickable: true,
          }}
          modules={[Pagination, Navigation]}
          onInit={(swiper) => {
            swiper.params.navigation.nextEl = nextRef.current;
            swiper.navigation.init();
            swiper.navigation.update();
          }}
          className="overflow-hidden rounded-2xl"
          breakpoints={{
            1300: {
              slidesPerView: 3,
              slidesPerGroup: 3,
            },
            800: {
              slidesPerView: 2,
              slidesPerGroup: 2,
            },
            0: {
              slidesPerView: 1,
              slidesPerGroup: 1,
            },
          }}
        >
            {/* {
                data.map((e,i)=>{
                    return(<h2>asdsda</h2>)
                })
            } */}
          {data.map((item, index) => {
            return (
              <SwiperSlide key={index}>
                <div className="flex sm:items-center justify-center flex-col sm:flex-row 2xl:p-10 p-6 2xl:py-14 py-10 pb-16  border-l-2 border-solid border-zinc-300">
                  <div className="xl:w-36 xl:h-36 w-28 h-28 rounded-full overflow-hidden mr-5 shrink-0 sm:mb-0 mb-5">
                    <img src={
                       item.file != null && item.file
                       ? "/" +
                       item.file.path +
                       "/" +
                       item.file.title
                       : null
                    }
                    alt="" />
                  </div>
                  <div className="">
                    <div className="opacity-50 nun-bold ">
                      <MdOutlineLocationOn className="inline-block mr-1 mb-1" />
                      {item.locations}
                    </div>
                    <div className="nun-bold mb-2 mt-1"># {item.title}</div>
                    <p className="mb-2 ">{item.description}</p>
                    <a href={item.reddirect_url ?item.reddirect_url: "/" } target="_blank" className="bold">
                      Learn more{" "}
                      <HiArrowNarrowRight className="inline-block w-6 h-6 ml-1" />
                    </a>
                  </div>
                </div>
              </SwiperSlide>
            );
          })}
          <button ref={nextRef} className="absolute bottom-3 right-3 z-20">
            <HiOutlineArrowCircleRight className="w-8 h-8" />
          </button>
        </Swiper>
      </div>
    </div>
  );
};

export default Slider2;
