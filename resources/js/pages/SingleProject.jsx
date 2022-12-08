import React from "react";
import { useRef, useEffect, useState } from "react";
import { Link } from "@inertiajs/inertia-react";
// import img1 from "../assets/images/projects/1.png";
// import img6 from "../assets/images/projects/6.png";
// import img7 from "../assets/images/projects/7.png";
// import img8 from "../assets/images/projects/8.png";
import {
    MouseParallaxContainer,
    MouseParallaxChild,
} from "react-parallax-mouse";
import { otherProjects } from "../components/Data";
import Layout from "../Layouts/Layout";

const SingleProject = ({ seo, portfolio, category_name, sameproduct }) => {
    // console.log(portfolio[0], 'esaa');
    // console.log(sameproduct, 'esaa');
    const [scrolled, setScrolled] = useState(0);
    const [transform, setTransform] = useState(``);
    const showcase = useRef();
    const video = useRef();
    useEffect(() => {
        video.current.style.marginTop = `-250px`;
    }, [video]);
    // window.addEventListener("scroll", () => {
    //     if (window.scrollY > 150) {
    //         showcase.current.style.transform = `translateY(-100%)`;
    //         video.current.style.marginTop = `-350px`;
    //     } else {
    //         showcase.current.style.transform = `translateY(0)`;
    //         video.current.style.marginTop = `-250px`;
    //     }
    //     setScrolled(window.scrollY);
    //     // console.log(scrolled);
    //     setTransform(`translateY(-${scrolled})`);
    // });
    // console.log(scrolled);

    return (
        <Layout seo={seo}>
            <div className="overflow-hidden ">
                <section
                    ref={showcase}
                    className={`relative z-30 transition-all duration-1000 `}
                >
                    <div className="wrapper pt-44">
                        <div className="flex items-start justify-start flex flex-wrap mb-10">
                            <div>
                                <div className="mr-32 regular text-custom-pink-500 uppercase">
                                    Customer
                                </div>
                                <div className="regular opacity-50 max-w-sm mb-5">
                                    {portfolio[0].name}
                                </div>
                            </div>
                            <div>
                                <div className="mr-32 regular text-custom-pink-500 uppercase">
                                    Duration
                                </div>
                                <div className="regular opacity-50 max-w-sm mb-5">
                                    {portfolio[0].duration}{" "}
                                </div>
                            </div>
                            <div>
                                <div className="mr-32 regular text-custom-pink-500 uppercase">
                                    Category
                                </div>
                                <div className="regular opacity-50 max-w-sm mb-5">
                                    {category_name}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="flex items-start justify-between wrapper flex-col sm:flex-row">
                        <h1
                            className="xl:text-9xl xl:text-8xl lg:text-7xl text-5xl uppercase sm:w-1/2   mb-5"
                            style={{ lineHeight: ".9" }}
                            data-aos="fade-right"
                        >
                            What you need to know for success
                        </h1>
                        <div className="sm:w-2/3 sm:ml-10" data-aos="fade-down">
                            <div className="uppercase mb-5 regular mt-3">
                                About project
                            </div>
                            <p className="opacity-50 text-justify regular leading-relaxed">
                                {portfolio[0].about_project}
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    ref={video}
                    className="w-screen h-screen relative  group transition-all duration-1000"
                >
                    <div className="absolute h-96 w-full left-0 top-0 bg-gradient-to-b from-black to-transparent"></div>
                    <div className="absolute h-96 w-full left-0 bottom-0 bg-gradient-to-t from-black to-transparent group-hover:scale-y-0 transiton-all duration-500 origin-bottom"></div>
                    {portfolio[0].video_url && (
                        <iframe
                            width="100%"
                            height="100%"
                            src={portfolio[0].video_url}
                            title="YouTube video player"
                            frameBorder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowFullScreen
                        ></iframe>
                    )}
                </section>

                <section className="py-20 text-center">
                    <div
                        data-aos="fade-up"
                        className="uppercase text-4xl mb-10"
                    >
                        Credits
                    </div>
                    <div
                        data-aos="fade-up"
                        className="regular text-custom-pink-500 uppercase mb-1 text-2xl"
                    >
                        direction
                    </div>
                    <div
                        data-aos="fade-up"
                        className="opacity-50 text-xl regular mb-5"
                    >
                        {portfolio[0].directions}
                    </div>
                    <div
                        data-aos="fade-up"
                        className="regular text-custom-pink-500 uppercase mb-1 text-2xl"
                    >
                        design
                    </div>
                    <div
                        data-aos="fade-up"
                        className="opacity-50 text-xl regular mb-5"
                    >
                        {portfolio[0].design}
                    </div>
                    <div
                        data-aos="fade-up"
                        className="regular text-custom-pink-500 uppercase mb-1 text-2xl"
                    >
                        animation
                    </div>
                    <div
                        data-aos="fade-up"
                        className="opacity-50 text-xl regular mb-5"
                    >
                        {portfolio[0].animation}
                    </div>
                    <div
                        data-aos="fade-up"
                        className="regular text-custom-pink-500 uppercase mb-1 text-2xl"
                    >
                        music & sound design
                    </div>
                    <div
                        data-aos="fade-up"
                        className="opacity-50 text-xl regular mb-5"
                    >
                        {portfolio[0].music}
                    </div>
                </section>
                {/* <div className="parallax projectWrapper text-center"> */}
                <MouseParallaxContainer
                    enableCSSTransition
                    useWindowMouseEvents
                    className="parallax projectWrapper text-center"
                >
                    {portfolio[0].files.map((e, i) => {
                        const img =
                            e != null ? "/" + e.path + "/" + e.title : null;

                        let bigimg = [0, 3, 6];
                        if (bigimg.some((e) => e == i)) {
                            return (
                                // <h2>asdsad</h2>
                                <MouseParallaxChild
                                    factorX={
                                        Math.random() * (0.1 - 0.01) + 0.01
                                    }
                                    factorY={
                                        Math.random() * (0.1 - 0.01) + 0.01
                                    }
                                    className="inline-block w-full h-auto mb-10"
                                    key={i}
                                >
                                    {/* <div className="inline-block w-full h-auto mb-10" key={i}> */}
                                    <img
                                        data-aos="zoom-in"
                                        className="w-full h-full object-cover"
                                        src={img}
                                        alt="err"
                                    />
                                    {/* </div> */}
                                </MouseParallaxChild>
                            );
                        } else {
                            return (
                                // <div className="lg:w-1/2 projectWrapper w-full inline-block max-w-xl lg:mx-5 lg:my-10">

                                <MouseParallaxChild
                                    factorX={
                                        Math.random() * (0.1 - 0.01) + 0.01
                                    }
                                    factorY={
                                        Math.random() * (0.1 - 0.01) + 0.01
                                    }
                                    className="lg:w-1/2 projectWrapper w-full inline-block max-w-xl lg:mx-5 lg:my-10"
                                    key={i}
                                >
                                    <img
                                        data-aos="zoom-in"
                                        className="w-full h-full object-cover"
                                        src={img}
                                        alt="err"
                                    />
                                </MouseParallaxChild>
                                // </div>
                            );
                        }
                    })}

                    {/* <div className="inline-block w-full h-auto mb-10">
                        <img
                            data-aos="zoom-in"
                            className="w-full h-full object-cover"
                            src={'/assets/images/projects/1.png'}
                            alt="err"
                        />
                    </div>
                    <div className="inline-block sm:w-1/2 w-full sm:h-96 h-60 mb-10 ">
                        <img
                            className="w-full h-full object-cover"
                            src={'/assets/images/projects/1.png'}
                            alt="err"
                        />
                    </div> */}
                </MouseParallaxContainer>
                <section className="projectWrapper py-20">
                    <div className="text-center uppercase text-4xl mb-10">
                        other projects
                    </div>
                    <div className="grid lg:grid-cols-3 md:grid-cols-2 gap-16 mb-20">
                        {sameproduct.map((item, index) => {
                            return (
                                <Link
                                    href={route(
                                        "client.showsingleproject.show",
                                        item.id
                                    )}
                                    key={index}
                                >
                                    <div className="w-full h-60 mb-5 overflow-hidden">
                                        <img
                                            src={
                                                item.files[0] != null
                                                    ? "/" +
                                                      item.files[0].path +
                                                      "/" +
                                                      item.files[0].title
                                                    : null
                                            }
                                            alt=""
                                            className=" w-full h-full object-cover transition-all duration-700 hover:scale-125"
                                        />
                                    </div>
                                    <div className="uppercase text-center text-xl regular">
                                        {item.name}
                                    </div>
                                </Link>
                            );
                        })}
                    </div>
                </section>
            </div>
        </Layout>
    );
};

export default SingleProject;
