import React, { useState } from "react";
// import Highlight from "../assets/images/shapes/10.png";
// import Highlight2 from "../assets/images/shapes/5.png";
import { Question, SocialMedia } from "../components/SmallComps";
// import bg from "../assets/images/bgs/1.png";
// import Img1 from "../assets/images/raypages/20.png";
import Slider5 from "../components/Slider5";
import Layout from "../Layouts/Layout";
import { Link, usePage } from "@inertiajs/inertia-react";

const RayAcademy = ({seo,courses}) => {
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
    const sharedData = usePage().props.localizations;

  const questions = [
    {
      q: __("client.rayacademy_q1", sharedData),
      a: __("client.rayacademy_a1", sharedData),
    },
    {
        q: __("client.rayacademy_q2", sharedData),
        a: __("client.rayacademy_a2", sharedData),
    },
      {
        q: __("client.rayacademy_q3", sharedData),
        a: __("client.rayacademy_a3", sharedData),
      },
      {
        q: __("client.rayacademy_q4", sharedData),
        a: __("client.rayacademy_a4", sharedData),
      },
  ];

  return (
    <Layout seo={seo}>
      <section
        className="relative pt-40 pb-12 h-screen bg-cover bg-center"
        style={{ backgroundImage: `url('/assets/images/bgs/1.png')` }}
      >
        <div className="wrapper flex items-center justify-between lg:flex-row flex-col">
          <div className="lg:w-2/5 lg:mr-10 lg:mb-0 mb-10">
            <div className="relative 2xl:text-7xl xl:text-6xl sm:text-5xl text-4xl bold w-fit">
              <img
                className="absolute -left-14 top-1/2 -translate-y-1/2 "
                src="/assets/images/shapes/10.png"
                alt=""
              />
              <span className="relative">
                {/* We share our knowledge */}
                {__("client.rayacademy_title", sharedData)}

              </span>
            </div>
            <div className="highlight-title nun-bold relative sm:px-20 px-14 whitespace-nowrap w-fit text-custom-dark xl:text-5xl sm:text-4xl text-3xl sm:my-14 my-10">
              <img
                className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                src="/assets/images/shapes/5.png"
                alt=""
              />
              <span className="relative">
                {/* Ray academy */}
                {__("client.rayacademy", sharedData)}:
                </span>
            </div>
            <p className="opacity-30 ">
              {/* The driving force of all speeches, we believe that creation should
              be the point around which any communication strategy revolves. */}
              {__("client.rayacademy_text", sharedData)}
            </p>
            <div className="flex justify-start items-center mt-10 mb-8">
              <div className="text-lg whitespace-nowrap">
                {/* Social media */}
                {__("client.social_media", sharedData)}
                </div>
              <div className="w-28 h-0.5 bg-white mx-4"></div>
              <SocialMedia />
            </div>
          </div>
        </div>
      </section>
      <section>
        <Slider5 data={courses}/>
      </section>
      <section className="wrapper flex items-start justify-between lg:py-24 py-20 flex-col lg:flex-row">
        <img className="mt-20 mx-auto " src="/assets/images/raypages/20.png" alt="" />
        <div className="lg:w-2/3 lg:mr-10 mt-10 lg:mt-0">
            <div className="bold text-2xl text-center mb-10">
             {/* Academy FAQ's */}
             {__("client.rayacademy_faqs", sharedData)}

            </div>
          {questions.map((item, index) => {
            return <Question key={index} q={item.q} a={item.a} />;
          })}
        </div>
      </section>
    </Layout>
  );
};

export default RayAcademy;
