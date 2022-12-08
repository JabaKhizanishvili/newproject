import React, { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";
import "swiper/css/effect-fade";
import { Pagination, Navigation, EffectFade } from "swiper";
// import Img1 from "../assets/images/home/1.png";
// import Img2 from "../assets/images/home/2.png";
import {
  HiArrowNarrowRight,
  HiOutlineArrowCircleRight,
  HiOutlineArrowCircleLeft,
} from "react-icons/hi";
import { Link, usePage } from "@inertiajs/inertia-react";

const Slider1 = ({slidedata}) => {
  const prevRef = useRef(null);
  const nextRef = useRef(null);
  const data = [
    {
      img: "/assets/images/home/1.png",
      title: "Craft multiple Solutions",
      para: "From banking and insurance to wealth management and on securities distribution, we dedicated financial services them the teams serve all major sectors. of the industry.",
      link: "/",
    },
    {
      img: "/assets/images/home/2.png",
      title: "New design Ideas",
      para: "From banking and insurance to wealth management and on seinancial services them the teams serve all major sectors. of the industry.",
      link: "/",
    },
  ];

  return (
    <div className="heroSlider">
      <Swiper
        pagination={{
          type: "fraction",
        }}
        effect={"fade"}
        navigation={true}
        modules={[Pagination, Navigation, EffectFade]}
        onInit={(swiper) => {
          swiper.params.navigation.prevEl = prevRef.current;
          swiper.params.navigation.nextEl = nextRef.current;
          swiper.navigation.init();
          swiper.navigation.update();
        }}
      >
        {slidedata.map((item, index) => {
          return (
            <SwiperSlide className="p-5 " key={index}>
              <div className="flex items-center justify-center ">
                <div className="sliderImg">
                  <div className="img">
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
                </div>
                <div className="contentContainer text-right">
                  <div className="sliderTitle bold">{item.title}</div>
                  <p className="opacity-30 my-6 paragraph">
                    {/* {item.short_description} */}
                    {item.description}
                    </p>
                    <a href={item.reddirect_url?item.reddirect_url: "/"} target="_blank">
                  <button className="flex items-center justify-center border border-solid border-custom-yellow bold text-custom-yellow sm:h-12 h-10 sm:px-7 px-4 rounded-full transition-all duration-300 hover:text-white hover:border-white mx-auto mr-0 sm:text-base text-sm">
                    <span>Learn more</span>
                    <HiArrowNarrowRight className="w-6 h-6 ml-2" />
                  </button>
                  </a>
                </div>
              </div>
            </SwiperSlide>
          );
        })}
        <div className="absolute bottom-4 sm:right-16 right-8 flex items-center justify-center z-20">
          <button ref={prevRef} className="transition-all duration-300">
            <HiOutlineArrowCircleLeft className="w-8 h-8" />
          </button>
          <button
            ref={nextRef}
            className="sm:ml-3 ml-1 transition-all duration-300"
          >
            <HiOutlineArrowCircleRight className="w-8 h-8" />
          </button>
        </div>
      </Swiper>
    </div>
  );
};

export default Slider1;
