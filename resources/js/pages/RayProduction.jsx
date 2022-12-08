import React from "react";
import { SocialMedia } from "../components/SmallComps";
import Slider4 from "../components/Slider4";
import RaySection from "../components/RaySection";
import { videoGrid } from "../components/Data";
import Layout from "../Layouts/Layout";
import { Link, usePage } from "@inertiajs/inertia-react";

const RayProduction = ({seo,project,videogallery, content}) => {
    // console.log(content, 'esaa');
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
    const sharedData = usePage().props.localizations;
  return (
    <Layout seo={seo}>
      <section className="relative pt-40 pb-12">
        <div className="wrapper flex items-center justify-between lg:flex-row flex-col">
          <div className="lg:w-2/5 lg:mr-10 lg:mb-0 mb-10">
            <div className="relative 2xl:text-7xl xl:text-6xl sm:text-5xl text-4xl bold w-fit">
              <img
                className="absolute -left-14 top-1/2 -translate-y-1/2 "
                src="/assets/images/shapes/9.png"
                alt=""
              />
              <span className="relative">
                {/* Trying to make it look great */}
                {__("client.rayproduction_title", sharedData)}
                </span>
            </div>
            <div className="highlight-title nun-bold relative sm:px-20 px-14 whitespace-nowrap w-fit text-custom-dark xl:text-5xl sm:text-4xl text-3xl sm:my-14 my-10">
              <img
                className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                src="/assets/images/shapes/3.png"
                alt=""
              />
              <span className="relative">
                {/* Ray production */}
                {__("client.rayproduction", sharedData)}
                </span>
            </div>
            <p className="opacity-30 ">
              {/* The driving force of all speeches, we believe that creation should
              be the point around which any communication strategy revolves. */}
              {__("client.rayproduction_text", sharedData)}
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
          <div className="lg:w-1/2">
            <img src="/assets/images/raypages/2.png" alt="" />
          </div>
        </div>
      </section>
      <section className="wrapper pb-20">
        <div className="bold text-2xl mb-14">
            {/* Directions */}
            {__("client.rayproduction_directions", sharedData)}
            </div>
        <RaySection color="#A7DE5C" title={__("client.rayproduction_filmsseries", sharedData)}>
          <div className="bold text-xl mb-10">
            {/* Projects */}
            {__("client.rayproduction_projects", sharedData)}
            </div>
          <Slider4 data={project} />
        </RaySection>
        {
            content.map((e,i)=>{
                // console.log(e.translation.title, 'esaa');
                return(
                    <div key={i}>
                         <RaySection color="#A7DE5C" title={e.translation.title}>
                            <div className="flex items-start justify-between flex-col lg:flex-row">
                            <div className="lg:max-w-xl opacity-50 lg:mr-5 mr-0 mb-10">
                                <p className="mb-4">
                                {/* {__("client.rayproduction_commercial_text", sharedData)} */}
                                {e.translation.description}
                                </p>
                            </div>
                            <img className="mx-auto"
                            src={
                                e.files != null && e.files[0]
                                ? "/" +
                                e.files[0].path +
                                "/" +
                                e.files[0].title
                                : null
                            }

                            alt="" />
                            </div>
                          </RaySection>
                    </div>
                )
            })
        }

        <RaySection color="#A7DE5C" title={__("client.rayproduction_musicvideo", sharedData)}>
          <div className="max-w-xl text-lg mb-10">
            {/* From banking and insurance to wealth management and on securities
            distribution. */}
            {__("client.rayproduction_commercial_text", sharedData)}
          </div>
          <div className="flex items-start justify-between flex-col lg:flex-row">
            <div className="grid grid-cols-2 sm:gap-10 gap-5 max-w-xl lg:mr-10 mr-0 mb-14 ">
              {videogallery.map((item, index) => {
                return (
                  <div key={index} className="cursor-pointer group">
                    <div className="w-full h-32 mb-5 relative">
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
                      <img
                        className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 group-hover:animate-pulse"
                        src="/assets/images/svg/play.svg"
                        alt=""
                      />
                    </div>
                    <p>{item.name}</p>
                  </div>
                );
              })}
            </div>
            <div className="max-w-xl opacity-50 lg:mx-auto">
              {/* <p className="mb-5">
                {" "}
                Live streaming is the broadcast of an event over the internet as
                it happens. Awards shows, sports, boxing matches, video games
                and special one-time events are the most popular types of live
                streaming with an ever-growing menu of topics.
              </p>
              <p>
                Social media platforms and others broadcast everything from
                celebrity events, promotions and lifestreaming to streaming
                between users. You can live stream on any compatible smartphone,
                tablet, TV, computer or gaming console with a relatively fast
                internet connection.
              </p> */}
              <p>
              {__("client.rayproduction_music_text", sharedData)}
              </p>
            </div>
          </div>
        </RaySection>

      </section>
    </Layout>
  );
};

export default RayProduction;
