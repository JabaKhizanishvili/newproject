import React, { useState } from "react";
// import Img1 from "../assets/images/other/5.png";
// import Img2 from "../assets/images/other/6.png";
// import Img3 from "../assets/images/other/7.png";
// import Img4 from "../assets/images/other/8.png";
import ImageLayout from "../components/ImageLayout";
// import PlayIcon from "../assets/images/svg/play.svg";
import VideoPopup from "../components/VideoPopup";
// import { Link } from "react-router-dom";
import { Link, usePage } from '@inertiajs/inertia-react'
import { HiArrowNarrowRight } from "react-icons/hi";
// import { companies } from "../components/Data";
import Layout from "../Layouts/Layout";

const About = ({seo,images,page}) => {

    // console.log( images ,'esaa');
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
const sharedData = usePage().props.localizations;

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

  const [showVideo, setShowVideo] = useState(false);
  return (
    <Layout seo={seo}>
      <section className="relative pt-60">
        <img className="absolute left-0 top-10 -z-10" src="/assets/images/other/5.png" alt="" />
        <img
          className="absolute right-0 top-0 -z-10 md:w-1/2 w-2/3"
          src="/assets/images/other/7.png"
          alt=""
        />
        <div className="wrapper relative">
          {" "}
          <img className="absolute left-32 -top-20 z-10" src="/assets/images/other/6.png" alt="" />
          <div className="bold opacity-50 text-2xl relative w-fit">
            {/* About us */}
            {__("client.about_about_us", sharedData)}
          </div>
          <div className=" 2xl:text-7xl xl:text-6xl sm:text-5xl text-4xl mb-10 max-w-2xl underline italic mt-10">
            {/* We are the best at creating things that's why so many people trusts
            us. */}
             {__("client.about_text", sharedData)}
          </div>
        </div>
      </section>
      <section className="lg:mt-40 mt-20">
        {/* <ImageLayout /> */}
        <div className="imageLayout wrapper flex items-center justify-center">
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[0]?images[0]: "/assets/images/news/1.png"} alt="" />
        </div>
      </div>
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img short overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[1]?images[1] : "/assets/images/news/2.png"} alt="" />
        </div>
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[2]? images[2]: "/assets/images/news/3.png"} alt="" />
        </div>
      </div>
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img long overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[3]? images[3]: "/assets/images/news/7.png"} alt="" />
        </div>
      </div>
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[4]? images[4]: "/assets/images/news/5.png"} alt="" />
        </div>
        <div className="img short overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[5]? images[5]: "/assets/images/news/6.png"} alt="" />
        </div>
      </div>
      <div className="column medium w-1/5 lg:mx-2 mx-1">
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src={images[6]? images[6]: "/assets/images/news/4.png"} alt="" />
        </div>
      </div>
    </div>
      </section>
      <section className="wrapper text-center py-20">
        <div className="bold text-2xl opacity-30 mb-10">
            {/* History */}
            {__("client.about_history", sharedData)}
            </div>
        <div className="max-w-5xl mx-auto relative">
          <div className="absolute w-8 h-8 -top-5 -left-5 border-l-4 border-t-4"></div>
          <div className="absolute w-8 h-8 -top-5 -right-5 border-r-4 border-t-4"></div>
          <div className="absolute w-8 h-8 -bottom-5 -right-5 border-r-4 border-b-4"></div>
          <div className="absolute w-8 h-8 -bottom-5 -left-5 border-l-4 border-b-4"></div>
          <p className="opacity-50">
            {" "}
            {/* A business goal is an endpoint, accomplishment or target an
            organization wants to achieve in the short term or long term.
            Business goals can take many different forms and be aspirational or
            motivational, such as driving an organization toward a certain
            objective like improved customer service. A business goal is an
            endpoint, accomplishment or target an organization wants to achieve
            in the short term or long term. Business goals can take many
            different forms and be aspirational or motivational, such as driving
            an organization toward a certain objective like improved customer
            service. */}
            {__("client.about_history_text", sharedData)}
          </p>
        </div>
      </section>
      <section
        onClick={() => setShowVideo(true)}
        className="relative h-fit cursor-pointer group"
      >
        <img className="max-h-screen" src="/assets/images/other/8.png" alt="" />
        <img
          className="absolute left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2 group-hover:animate-pulse"
          src="/assets/images/svg/play.svg"
          alt=""
        />
      </section>
      <section className="py-20 wrapper">
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
                <Link
                  href={item.link}
                  className={`flex items-center justify-center border border-solid border-custom-slate-900 bold text-custom-slate-900 sm:h-12 h-10 w-fit sm:px-7 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap`}
                >
                  <span className="transition-all duration-300">
                    Learn more
                  </span>
                  <HiArrowNarrowRight className="w-6 h-6 " />
                </Link>
              </div>
            );
          })}
        </div>
      </section>
      <VideoPopup
        open={showVideo}
        closeVideo={() => setShowVideo(false)}
        // src="https://www.youtube.com/embed/xYrPGIJ2qoo"
        src={(page.url?page.url : "https://www.youtube.com/embed/tl6T2nwRers")}
        // src="https://www.youtube.com/embed/tl6T2nwRers"
      />
    </Layout>
  );
};

export default About;
