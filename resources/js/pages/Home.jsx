import React, { useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import Slider1 from "../components/Slider1";
import Slider2 from "../components/Slider2";
import { HiArrowNarrowDown } from "react-icons/hi";
import { HiArrowNarrowRight } from "react-icons/hi";
import { BiPlay } from "react-icons/bi";
// import { companies, newsHome } from "../components/Data";
import Slider3 from "../components/Slider3";
import VideoPopup from "../components/VideoPopup";
import Layout from "../Layouts/Layout";

const Home = ({seo,sliders, UpcomingEvent, news, slider2, links, page}) => {
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
const sharedData = usePage().props.localizations;
  const [showVideo, setShowVideo] = useState(false);


  const companies = [
    {
      logo: "/assets/images/logo/2.png",
      title: __("client.raycompanies_productrion", sharedData),
      para: __("client.raycompanies_productrion_text", sharedData),
      link: route("client.rayproductrion"),
      color: "green",
      bg: "/assets/images/shapes/Ellipse1.png",
    },
    {
      logo: "/assets/images/logo/3.png",
      title: __("client.raycompanies_animation", sharedData),
      para: __("client.raycompanies_animation_text", sharedData),
      link: route("client.rayanimation"),
      color: "blue",
      bg: "/assets/images/shapes/Ellipse2.png",
    },
    {
      logo: "/assets/images/logo/4.png",
      title: __("client.raycompanies_academy", sharedData),
      para: __("client.raycompanies_academy_text", sharedData),
      link: route("client.rayacademy"),
      color: "red",
      bg: "/assets/images/shapes/Ellipse3.png",
    },
    {
      logo: "/assets/images/logo/5.png",
      title: __("client.raycompanies_shop", sharedData),
      para: __("client.raycompanies_shop_text", sharedData),
      link: "/",
      color: "orange",

      bg: "/assets/images/shapes/Ellipse4.png",
    },
    {
      logo: "/assets/images/logo/6.png",
      title: __("client.raycompanies_crypto", sharedData),
      para: __("client.raycompanies_crypto_text", sharedData),
      link: route("client.raycrypto"),
      color: "purple",
      bg: "/assets/images/shapes/Ellipse5.png",
    },
  ];


  return (
    <>
    <Layout seo={seo}>
      <section className="relative pt-40 pb-12">
        <img className="-z-10 absolute top-0 right-0" src="/assets/images/patterns/1.png" alt="" />
        <div className="wrapper flex items-center justify-between lg:flex-row flex-col">
          <div className="lg:w-2/5 lg:mr-10 lg:mb-0 mb-10">
            <div className="relative 2xl:text-7xl xl:text-6xl sm:text-5xl text-4xl bold w-fit">
              <img
                className="absolute -left-14 top-1/2 -translate-y-1/2 "
                src="/assets/images/shapes/6.png"
                alt=""
              />
              <span className="relative">
                {/* Craft multiple Solutions */}
                {__("client.home_craft_multiple", sharedData)}
                </span>
            </div>
            <div className="highlight-title nun-bold relative sm:px-20 px-14 whitespace-nowrap w-fit text-custom-dark xl:text-5xl text-4xl sm:my-14 my-10">
              <img
                className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                src="/assets/images/shapes/1.png"
                alt=""
              />
              <span className="relative">
                {/* Ray eleven */}
                {__("client.home_rayleven", sharedData)}
                </span>
            </div>
            <p className="opacity-30 ">
              {/* The driving force of all speeches, we believe that creation should
              be the point around which any communication strategy revolves. */}
               {__("client.home_slider1_text", sharedData)}
            </p>
            <div className="flex justify-start items-center mt-10 mb-8">
              <Link href="/about">
                <button className="px-8 h-10 text-custom-dark bg-custom-yellow bold rounded-full transition-all duration-300 hover:bg-white">
                  {/* About us */}
                  {__("client.home_aboutus_btn", sharedData)}
                </button>
              </Link>
              <button
                onClick={() => setShowVideo(true)}
                className="ml-5 flex items-center justify-center text-custom-yellow underline bold group"
              >
                <div
                  className="flex items-center justify-center rounded-full mr-3 bg-custom-dark w-10 h-10 transition-all duration-300 group-hover:text-custom-dark group-hover:bg-custom-yellow"
                  style={{ boxShadow: "0 0  8px -2px #F8D532" }}
                >
                  <BiPlay className="w-5 h-5" />
                </div>
                <span>Watch video</span>
              </button>
            </div>
            <a
              href="#footer"
              className="w-fit flex items-center justify-center border border-solid border-custom-yellow bold text-custom-yellow h-12 px-7 rounded-full transition-all duration-300 hover:text-white hover:border-white"
            >
              <span>
                {/* Get in touch */}
                {__("client.home_getintouch_btn", sharedData)}
                </span>
              <HiArrowNarrowDown className="w-6 h-6 ml-2" />
            </a>
          </div>
          <div className="lg:w-3/5 w-full">
            <Slider1 slidedata={sliders} />
          </div>
        </div>
      </section>
      <section className="py-10 wrapper">
        <div className="flex md:items-center items-start justify-start mb-10 flex-col md:flex-row">
          <div className="bold lg:text-4xl text-2xl">
            {/* Our Companies */}
            {__("client.home_our_companies", sharedData)}
            </div>
          <div className="h-12 w-0.5 bg-white rounded mx-6 md:inline-block hidden"></div>
          <p className="opacity-30 max-w-2xl">
            {/* From banking and insurance to wealth management and on securities
            distribution, we dedicated financial services them the teams serve
            all major sectors. of the industry. */}
             {__("client.home_main_companies_title", sharedData)}
          </p>
        </div>
        <div className="flex items-start 2xl:justify-between justify-center flex-wrap 2xl:flex-nowrap -m-2">
          {companies.map((item, index) => {
            return (
              <div
                key={index}
                className={`group h-80 2xl:w-1/5 max-w-sm border border-solid border-custom-slate-900 py-8 px-6 m-2 transition-all duration-300 companyBox cBox-${item.color}`}
                style={{
                  borderTopLeftRadius: "10px",
                  borderBottomRightRadius: "69px",
                }}
              >
                <img className="h-10" src={item.logo} alt="" />
                <p className="opacity-30 group-hover:opacity-80 h-24 my-5 mb-10 overflow-hidden text-sm !text-white transition-all duration-300">
                  {item.para}
                </p>
                {
                    index != 3?
                    <Link
                    href={item.link}
                    className={`flex items-center justify-center border border-solid border-custom-slate-900 bold text-custom-slate-900 sm:h-12 h-10 w-fit sm:px-7 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap`}
                  >
                    <span className="transition-all duration-300">
                      {/* Learn more */}
                      {__("client.home_learn_more_btn", sharedData)}
                    </span>
                    <HiArrowNarrowRight className="w-6 h-6 " />
                  </Link>
                  :
                  <a
                  href="https://rayshop.ge/ge"
                  className={`flex items-center justify-center border border-solid border-custom-slate-900 bold text-custom-slate-900 sm:h-12 h-10 w-fit sm:px-7 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap`}
                >
                  <span className="transition-all duration-300">
                    {/* Learn more */}
                    {__("client.home_learn_more_btn", sharedData)}
                  </span>
                  <HiArrowNarrowRight className="w-6 h-6 " />
                </a>
                }

              </div>
            );
          })}
        </div>
      </section>
      <section>
        <Slider2 data={UpcomingEvent} />
      </section>
      <section className="py-10 wrapper flex items-center justify-between flex-col lg:flex-row overflow-hidden">
        <div className="lg:max-w-sm  ">
          <div className="xl:text-7xl sm:text-5xl text-3xl bold mb-7 ">
            {/* Don't miss any news */}
            {__("client.home_main_dont_miss_any", sharedData)}
          </div>
          <Link
            href={__("client.navbar_news", sharedData)}
            className={`flex items-center justify-center border border-solid border-custom-yellow bold text-custom-yellow sm:h-10 h-10 w-fit sm:px-6 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap`}
          >
            <span>
                {/* Check all */}
                {__("client.home_checkall", sharedData)}
            </span>
            <HiArrowNarrowRight className="w-6 h-6 m-2" />
          </Link>
        </div>
        <div className="relative w-fit lg:mx-5 my-10 ">
          <img src="/assets/images/home/3.png" alt="" />
          <img
            className="-z-10 absolute -right-10 top-1/2 -translate-y-1/2 max-w-none"
            src="/assets/images/patterns/2.png"
            alt=""
          />
          <div className="w-12 h-12 absolute -left-5 -bottom-5 border-custom-yellow border-l-4 border-b-4 border-solid"></div>
          <div className="w-12 h-12 absolute -right-5 -top-5 border-custom-yellow border-t-4 border-r-4 border-solid"></div>
        </div>
        <div className="lg:max-w-lg">
          {news.map((item, index) => {
             const date = () => {
                let z = item.created_at.split("-");
                z[2] = z[2].split(":");
                z[2] = z[2][0].slice(0, z[2][0].search("T"));
                return z;
            }
            return (
              <Link
                key={index}
                href={route("client.news.show",item.id)}
                className="flex items-center justify-start p-5 border-b border-custom-slate-900 hover:bg-custom-slate-900 rounded transition-all"
              >
                <div className="sm:w-36 sm:h-36 w-28 h-28 rounded mr-3 overflow-hidden shrink-0">
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
                <div>
                  <div className="bold">{item.title}</div>
                  <p className="opacity-30 text-sm my-2">Date: {`${date()[2]}.${date()[1]}.${date()[0]}`}</p>
                  <p className="opacity-30 h-16 overflow-hidden sm:text-base text-sm">
                    {item.short_description}
                  </p>
                </div>
              </Link>
            );
          })}
        </div>
      </section>
      <section className="py-10">
        <Slider3 data={slider2} links={links} />
      </section>
      <section className="py-20 wrapper group relative flex items-center justify-start">
        <img className="w-1/3" src="/assets/images/home/6.png" alt="" />
        <div className="xl:max-w-md max-w-sm bold xl:text-6xl sm:text-4xl text-2xl leading-tight text-right">
          <span className="opacity-10 group-hover:opacity-100 transition-all duration-500">
            {/* Professional Technics for Rent */}
            {__("client.home_main_professional_tec_for_rent", sharedData)}
          </span>
          <Link
            href={route("client.rental.index")}
            className={`flex items-center justify-center border border-solid border-custom-yellow bold text-custom-yellow sm:h-10 h-10 w-fit sm:px-6 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap mx-auto mr-0 mt-5`}
          >
            <span>
                {/* See what we got */}
                {__("client.home_main_see_whatwegot_btn", sharedData)}
            </span>
            <HiArrowNarrowRight className="w-6 h-6 m-2" />
          </Link>
        </div>
        <svg
          className="lg:block hidden absolute top-1/2 -translate-y-1/2 right-0 w-full -z-10"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 1454.159 720.922"
        >
          <defs>
            <linearGradient
              id="linear-gradient"
              x1="1"
              y1="0.486"
              x2="0.21"
              y2="0.5"
              gradientUnits="objectBoundingBox"
            >
              <stop offset="0" stopColor="#fffbd5" />
              <stop offset="0.532" stopColor="#fff2b8" stopOpacity="0.29" />
              <stop offset="1" stopColor="#ffefae" stopOpacity="0" />
            </linearGradient>
            <clipPath id="clip-path">
              <path
                id="Path_1009"
                dataname="Path 1009"
                d="M939.911,6185.617v122.029L-404.032,6608.358V5887.437Z"
                transform="translate(773.873 -2477.535)"
                fill="url(#linear-gradient)"
                style={{ mixBlendMode: "screen", isolation: "isolate" }}
              />
            </clipPath>
          </defs>
          <g
            id="Group_1793"
            dataname="Group 1793"
            transform="translate(-369.841 -3409.902)"
          >
            <image
              id="Untitled-2"
              width="341"
              height="463.308"
              transform="translate(1483 3657.692)"
              xlinkHref="/assets/images/svg/light.png"
            />
            <g
              id="Component_20_2"
              dataname="Component 20 – 2"
              transform="translate(369.841 3409.902)"
            >
              <path
                className="opacity-0 group-hover:opacity-100 transition-all duration-500"
                id="Path_1008"
                dataname="Path 1008"
                d="M938.636,6185.12l1.275,121.653L-404.032,6608.358V5887.437Z"
                transform="translate(404.032 -5887.437)"
                fill="url(#linear-gradient)"
                style={{ mixBlendMode: "screen", isolation: "isolate" }}
              />
            </g>
          </g>
        </svg>
      </section>

      <VideoPopup
        open={showVideo}
        closeVideo={() => setShowVideo(false)}
        src={(page.url?page.url : "https://www.youtube.com/embed/tl6T2nwRers")}
      />
      </Layout>
    </>
  );
};

export default Home;
