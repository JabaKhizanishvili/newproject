import React, { useState, Suspense } from 'react';
const Navbar = React.lazy(() => import('../components/Navbar'))
const Footer = React.lazy(() => import('../components/Footer'));
import BlogsWidget from '../components/BlogsWidget';
import setSeoData from "../Layouts/SetSeoData";
import "../Layouts/index.css";
import { Link, usePage } from "@inertiajs/inertia-react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "../../css/styles.css";
import "../../css/blog.css"
import "swiper/css/pagination";
import arrowRight from "/assets/images/navbar/Icon material-k.png";
import { HomeBlog, BlogPost } from "../components/SmallComps";
import { Inertia } from '@inertiajs/inertia'
import SwiperCore, { FreeMode, Pagination, Autoplay } from "swiper";
SwiperCore.use([Autoplay]);
// import Swal from 'sweetalert2';
// import AOS from 'aos';
// import 'aos/dist/aos.css'; // You can also use <link> for styles
// AOS.init({
//     // Global settings:
//     disable: false, // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
//     startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOS should initialize on
//     initClassName: 'aos-init', // class applied after initialization
//     animatedClassName: 'aos-animate', // class applied on animation
//     useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
//     disableMutationObserver: false, // disables automatic mutations' detections (advanced)
//     debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
//     throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)


//     // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
//     offset: 120, // offset (in px) from the original trigger point
//     delay: 0, // values from 0 to 3000, with step 50ms
//     duration: 900, // values from 0 to 3000, with step 50ms
//     easing: 'ease', // default easing for AOS animations
//     once: false, // whether animation should happen only once - while scrolling down
//     mirror: false, // whether elements should animate out while scrolling past them
//     anchorPlacement: 'top-bottom', // defines which position of the element regarding to window should trigger the animation

//   });


const Test = ({ seo, menus, success, menu, code, partners, mainSlider,
    blogs, getblogelements, slider2,slider3, locale, sliderLike,
    partnerslink, menuforDisplay, footermeny }) => {
    const [screenWidth, setScreenWidth] = useState(0)
    window.addEventListener("resize", () => {
        setScreenWidth(screen.size);
    })
    // seo settings
    if (seo) {
        setSeoData(seo);
    }
    const getIconAndText = (data) => {
        let arr = [];
        for (let i = 0; i < data.split(",").length; i++) {
            if (i % 2 == 0) {
                arr.push(
                    {
                        icon: data.split(",")[i],
                        text: data.split(",")[++i],
                    }
                )
            }
        }
        return arr;
    }


    const [values, setValues] = useState({
        email: "",
    })

    const renderHTML = (rawHTML) =>
        React.createElement("div", {
            dangerouslySetInnerHTML: { __html: rawHTML },
        });

    const { errors, filepath } = usePage().props;
    const sharedData = usePage().props.localizations;

    // form success
    if (success) {
        // Swal.fire({
        //     title: __('client.form_success', sharedData) ,
        //     text: __('client.form_text', sharedData),
        //     icon: 'success',
        //     confirmButtonText: __('client.form_btn', sharedData)
        // })
        // alert('success')
    }

    const [values1, setValues1] = useState({
        name: "",
        company: "",
        position: "",
        phone: "",
        mail: "",
        quantity: "",
        contact: "",
    })

    function handleChange1(e) {
        const key = e.target.id;
        const value = e.target.value
        setValues1(values1 => ({
            ...values1,
            [key]: value,
        }))
    }

    function handleSubmit1(e) {
        e.preventDefault()
        Inertia.post(route("client.bookvisitform"), values1)
    }

    function handleChange(e) {
        const key = e.target.id;
        const value = e.target.value
        setValues(values => ({
            ...values,
            [key]: value,
        }))
    }

    function handleSubmit(e) {
        e.preventDefault()
        var forms = document.evaluation;
        Inertia.post(route('client.documentations.rateservices'), values)
    }

    return (
        <>
            <Suspense fallback={<div>Loading...</div>}>
                <Navbar data={menus} code={code} menuforDisplay={menuforDisplay} locale={locale} />
            </Suspense>
            <div
                className="pt-20 flex flex-col" style={{
                    fontFamily: "geo",
                    color: "#b1b1b1",
                }}
            >

                {
                    menu.slider != null &&
                    //   const [sreenWidth, sreenHeight] = useState(0)

                    <div
                    // data-aos="slide-down"
                     className="order-first mt-4 sm:mt-4 md:mt-2 xl:mt-2 py-10" style={{ backgroundColor: "#f2f3fd", }}>
                        <div
                            className="xl:px-40 md:px-40 sm:px-10 px-10"
                            style={{
                                width: "100%",
                                maxHeight: "30rem",
                            }}
                        >

                            <Swiper
                                style={{
                                    "--swiper-pagination-bullet-inactive-opacity": "1",
                                    "--swiper-pagination-bullet-size": "10px",
                                    "--swiper-pagination-bullet-horizontal-gap": "6px",
                                }}
                                autoplay={{
                                    delay: 5000,
                                    disableOnInteraction: false,
                                  }}
                                pagination={{ clickable: true }} modules={[Autoplay,Pagination]} className="mySwiper">

                                {
                                    mainSlider.map((e, i) => {
                                        return (
                                            <SwiperSlide
                                                className="flex md:flex-nowrap xl:flex-nowrap"
                                                key={i}
                                            >
                                                <div className="left w-1/2 content-end align-middle">
                                                    <div className='h-2/3 mt-0 sm:mt-0 md:mt-8 xl:mt-10 pt-10'>
                                                        <h3 style={{ color: "#6f61ea" }} className="mb-4 text-xs sm:text-md md:text-xl lg:text-2xl" >{e.title}</h3>
                                                        <h4 style={{ color: "#c49fff" }} className="mb-10 text-xs sm:text-md md:text-xl lg:text-4xl textclamp2">{e.description}</h4>
                                                        {
                                                            e.reddirect_url != null ?
                                                                <a target='_blank' href={(e.reddirect_url ? e.reddirect_url : "")}>
                                                                    <button
                                                                        style={{
                                                                            backgroundColor: e.btncolor == null ? "#6f61ea" : e.btncolor,
                                                                            color: "#ffff",
                                                                            padding: "10px 4px 10px 10px",
                                                                            borderRadius: "5px",
                                                                            minWidth: "100px",
                                                                        }}
                                                                        className="sliderbtn mb-10 flex">
                                                                            {e.button_text}
                                                                             <img className='ml-2'
                                                                             style={{transform:'scale(0.8)'}}
                                                                             src={arrowRight} alt="" />
                                                                    </button>
                                                                </a> :
                                                                <button
                                                                    style={{
                                                                        backgroundColor: e.btncolor == null ? "#6f61ea" : e.btncolor,
                                                                        color: "#ffff",
                                                                        padding: "10px 20px 10px",
                                                                        borderRadius: "5px",
                                                                        minWidth: "100px",
                                                                    }}
                                                                    className="mb-10 flex">{e.button_text}
                                                                </button>
                                                        }
                                                    </div>


                                                </div>
                                                <div
                                                    className="px-1 pl-4 sm:pl-10 md:pl-10 xl:pr-20" style={{maxWidth:'70%'}}>
                                                    <img
                                                        src={
                                                            e.file != null
                                                                ? "/" +
                                                                e.file.path +
                                                                "/" +
                                                                e.file.title
                                                                : null
                                                        } alt="asd" />
                                                </div>

                                            </SwiperSlide>
                                        )
                                    })
                                }



                            </Swiper>
                        </div>
                    </div>
                }


                {/* ragacnairi meore slaiderivit ragac */}

                {
                    menu.sliderlike != null &&
                        sliderLike != null ?
                        <div className="text-white grid grid-cols-1 sm-grid-cols-1 md:grid-cols-3 xl:grid-cols-3 w-100 px-4 sm:px-4 md:px-40 xl:px-40 py-20 gap-x-10"
                            style={{
                                backgroundColor: sliderLike.color
                            }}
                        // data-aos="slide-up"
                        >

                            <div className='col-span-2'>
                                <h2 className='w-1/2 text:md sm:text-sm md:text-md xl:text-2xl mb-2 sm:mb-2 md:mb-8 xl:mb-8'>
                                    {sliderLike.title}
                                </h2>
                                <p className='text:sm sm:text-sm md:text-md xl:text-xl'>{sliderLike.description}</p>
                            </div>

                            <div className='w-80 flex justify-center mt-2 sm:mt-2 md:mt-0 xl:mt-0'>
                                <img className='w-full h-full' src={
                                    sliderLike.file != null
                                        ? "/" +
                                        sliderLike.file.path +
                                        "/" +
                                        sliderLike.file.title
                                        : null
                                } alt="err" />
                            </div>
                        </div>
                        : ""
                }




                {
                    menu.layout == 3 ?
                        <div className="order-2">
                            {
                                blogs.map((e, i) => {
                                    if (e.translation.visible == '1') {
                                        return (
                                            <div key={i}>
                                                <HomeBlog data={e} left={i % 2 != 0 ? true : false} />
                                            </div>
                                        )
                                    }
                                })
                            }
                        </div>
                        : ""
                }

                {/* singlepost layout */}
                {
                    menu.layout == 8 ?
                        <div className="order-2">
                            {
                                blogs.map((e, i) => {
                                    if (e.translation.visible == '1') {
                                        return (
                                            <div key={e.id}>
                                                {/* <HomeBlog data={e} left={i % 2 != 0 ?true: false}/> */}
                                                <div className='px-40 mt-20'>
                                                    <h1 className='text-center mb-10' style={{ color: "#5f5f5f", fontSize: "30px" }}>{e.title}</h1>
                                                    <div className='mb-8'>
                                                        <img
                                                            src={
                                                                e.file != null
                                                                    ? "/" +
                                                                    e.file.path +
                                                                    "/" +
                                                                    e.file.title
                                                                    : null
                                                            }
                                                            alt="img" />
                                                    </div>
                                                    <p className='mb-4'>
                                                        {renderHTML(e.description)}
                                                    </p>
                                                </div>
                                            </div>
                                        )
                                    }
                                })
                            }
                        </div>
                        : ""
                }

                {/* application layout */}

                {
                    menu.layout == 7 ?
                        <div
                            style={{
                                fontFamily: "geo",
                                color: "#b1b1b1",
                            }}
                        >
                            <div className='justify-center w-full text-center px-4 sm:px-4 md:px-40 xl:px-40 pt-20'>
                                <div className='w-full'>
                                    <h2 className='text-xs sm:text-md md:text-xl lg:text-xl w-1/2 mx-auto'> {__("client.application_title", sharedData)}</h2>
                                    <div className='mt-10 sm:mt-10 md:mt-20 xl:mt-20 grid grid-flow-row-dense grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-2 justify-center xl:gap-x-60'>
                                        {
                                            blogs.map((e, i) => {
                                                let r, g, b;
                                                r = Math.round(Math.random() * 255);
                                                g = Math.round(Math.random() * 255);
                                                b = Math.round(Math.random() * 255);
                                                if (e.translation.visible == 1) {
                                                    return (
                                                        <div key={i} className='text-start py-4 '
                                                            style={{
                                                                padding: "20px 10px 20px",
                                                                alignItems: "center"
                                                            }}
                                                        >
                                                            <div className='flex align-middle content-between mb-2'>
                                                                <div className='w-4 h-4 rouded my-auto' style={{ backgroundColor: e.btncolor == null ? `rgb(${r},${g},${b})` : e.btncolor }}></div>
                                                                <span className='text-xs sm:text-md md:text-xl lg:text-xl mx-2' style={{ color: e.btncolor == null ? `rgb(${r},${g},${b})` : e.btncolor }}>{e.title}</span>
                                                            </div>
                                                            <img className='h-40 md:h-80 xl:h-80 w-80 md:w-96 xl:w-full rounded-xl object-cover mb-2' src={
                                                                e.file != null
                                                                    ? "/" +
                                                                    e.file.path +
                                                                    "/" +
                                                                    e.file.title
                                                                    : null
                                                            } />
                                                            <p className='text-xs sm:text-md md:text-xl lg:text-xl'>{e.description}</p>
                                                        </div>
                                                    )
                                                }
                                            })
                                        }

                                    </div>
                                </div>
                            </div>


                        </div>
                        : ""
                }

                {/* blog layout */}
                {
                    menu.layout == 2 ?
                        <div className="order-2">
                            <div
                                className="mt-10 sm:mt-10 md:mt-20 xl:mt-10 grid grid-flow-row-dense grid-cols-1 mb-4 place-items-center">
                                {
                                    blogs.map((e, i) => {
                                        let r, g, b;
                                        r = Math.floor(Math.random() * 255)
                                        g = Math.floor(Math.random() * 255)
                                        b = Math.floor(Math.random() * 255)
                                        let color = `rgb(${r}, ${g}, ${b})`
                                        if (e.translation.visible == 1) {
                                            return (
                                                <React.Fragment key={e.id}>
                                                    <div className='px-4 sm:px-4 md-px-40 xl:px-40 grid'>
                                                        {
                                                            e.iconimages.map((e, i) => {
                                                                if (e.type == "header_icon") {
                                                                    return (
                                                                        <img key={i} className='w-24 h-24 mx-auto mb-4' src={
                                                                            e != null
                                                                                ? filepath + '/' + e.name
                                                                                : null
                                                                        } alt="img" />
                                                                    )
                                                                }
                                                            })
                                                        }
                                                        <h2 className="text-center mb-4 sm:mb-4 md:mb-20 xl:mb-20 text-xl">{e.title}</h2>
                                                        <div className="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-x-4 sm:gap-x-5 md:gap-x-20 xl:gap-x-20">
                                                        <div className='col'>
                                                            <p className='break-words overflow-hidden order-2 sm:order-2 md:order-1 xl:order-1'>{e.description}</p>
                                                            </div>
                                                            <div className='col'>
                                                            <div>
                                                                <img
                                                                    style={{maxHeight: '300px'}}
                                                                    className="xl:w-full object-cover mb-2 order-2 sm:order-1 md:order-2 xl:order-2"
                                                                    src={
                                                                        e.file != null
                                                                            ? "/" +
                                                                            e.file.path +
                                                                            "/" +
                                                                            e.file.title
                                                                            : null
                                                                    } alt="err" />
                                                            </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </React.Fragment>

                                            )
                                        }
                                    })
                                }
                            </div>
                        </div>
                        : ""
                }

                {/* contact layout */}
                {
                    menu.layout == 6 ?
                        <div className="px-8 sm:px-8 md:px-40 xl:px-40 pb-4">
                            <div className="pt-8 w-full sm:w-full md:w-1/2 xl:w-1/2"
                                style={{
                                    fontFamily: "geo",
                                    color: "#b1b1b1",
                                }}>

                                <h1 className='mb-8 text-lg sm:text-md md:text-xl xl:text-2xl'
                                // style={{
                                //     fontSize:"30px"
                                // }}
                                >{__("client.form_bookvisit", sharedData)}</h1>
                                <form onSubmit={handleSubmit1} className='mb-20'>
                                    <p>{__("client.form_name_surname", sharedData)}</p>
                                    <input type="text" name="name" id="name" value={values1.name} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.name && <div className="text-red-100">{errors.name}</div>}
                                    <p>{__("client.form_company", sharedData)}</p>
                                    <input type="text" name="company" id="company" value={values1.company} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.company && <div className="text-red-100">{errors.company}</div>}
                                    <p>{__("client.form_position", sharedData)}</p>
                                    <input type="text" name="position" id="position" value={values1.position} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.position && <div className="text-red-100">{errors.position}</div>}
                                    <p>{__("client.form_phone", sharedData)}</p>
                                    <input type="number" name="phone" id="phone" value={values1.phone} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.phone && <div className="text-red-100">{errors.phone}</div>}
                                    <p>{__("client.form_mail", sharedData)}</p>
                                    <input type="email" name="phone" id="mail" value={values1.mail} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.mail && <div className="text-red-100">{errors.mail}</div>}
                                    <p>{__("client.form_workers-quantity", sharedData)}</p>
                                    <input type="text" name="quantity" id="quantity" value={values1.quantity} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.quantity && <div className="text-red-100">{errors.quantity}</div>}
                                    <p>{__("client.form_contact_info", sharedData)}</p>
                                    <input type="text" name="contact" id="contact" value={values1.contact} onChange={handleChange1} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    {errors.contact && <div className="text-red-100">{errors.contact}</div>}
                                    <button type="submit" className='my-2'
                                        style={{
                                            backgroundColor: "#6f61ea",
                                            color: "#ffff",
                                            padding: "10px 20px 10px",
                                            borderRadius: "5px",
                                            minWidth: "100px",
                                        }}
                                    >
                                        {__("client.form_submit_btn", sharedData)}
                                        {/* {__("client.form_contact_info", sharedData)} */}
                                    </button>

                                    {success ?
                                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                                            <span class="font-medium">Success alert!</span>
                                        </div>
                                        : ""
                                    }
                                </form>
                            </div>
                        </div>

                        : ""
                }

                {/* post layout */}
                {
                    menu.layout == 5 ?
                        <div className="order-2">
                            <div className='mt-20 grid grid-cols-1 px-4 sm:px-20 md:px-20 xl:px-40 gap-20'>
                                {
                                    blogs.map((e, i) => {
                                        if (e.translation.visible == 1) {
                                            return (
                                                <div key={i} className="pb-8 h-96">
                                                    <BlogPost data={e} />
                                                </div>
                                            )
                                        }
                                    })
                                }
                            </div>
                        </div>
                        : ""
                }


                {/* vaccancy layout */}
                {
                    menu.layout == 4 ?
                        <div
                            style={{
                                fontFamily: "geo",
                                color: "#b1b1b1",
                            }}
                        >

                            <div className='justify-center w-full text-center px-4 sm:px-20 md:px-40 xl:px-40 pt-20'>
                                <div className='w-full'>
                                    <h2 className='text-md sm:text-xl md:text-3xl xl:text-3xl'>{__("client.home_jointeam", sharedData)}</h2>

                                    <div className='mt-10 sm:mt-10 md:mt-20 xl:mt-40 grid grid-flow-row-dense grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-3 justify-center gap-4 mb-4 place-items-center'>
                                        {
                                            blogs.map((e, i) => {
                                                if (e.translation.visible == 1) {
                                                    if (e.vaccancylink == null) {
                                                        return (
                                                            <div className='container'>
                                                            <Link key={e.id} href={route("client.SingleVaccancy", e.title)} className='bg-custom-color-blue hover:bg-custom-color-darkblue hover:text-white rounded-md'
                                                                style={{
                                                                    position: 'relative',
                                                                    padding: "20px 10px 20px",
                                                                    width: "300px",
                                                                    height: "250px",
                                                                    float: "left",
                                                                    alignItems: "center",
                                                                }}
                                                            >
                                                                <p
                                                                style={{
                                                                    position: 'absolute',
                                                                    top:'50%',
                                                                    left:'50%',
                                                                    transform: 'translate(-50%,-50%)'
                                                                }}
                                                                className=''>{e.title}</p>
                                                            </Link>
                                                            </div>
                                                        )

                                                    } else {
                                                        return (
                                                            <div className='container'>
                                                            <a key={e.id} target="_blank" href={e.vaccancylink} className='flex bg-custom-color-blue hover:bg-custom-color-darkblue hover:text-white'
                                                                style={{
                                                                    padding: "20px 10px 20px",
                                                                    width: "200px",
                                                                    height: "150px",
                                                                    alignItems: "center"
                                                                }}
                                                            >
                                                                <p className='h-1/3'>{e.title}</p>
                                                            </a>
                                                            </div>
                                                        )
                                                    }
                                                }
                                            })
                                        }

                                    </div>
                                </div>
                            </div>


                        </div>
                        : ""
                }
                {/* blogs widget goes here */}
                {
                    menu.blog != null &&
                    <BlogsWidget data={getblogelements} menu={menu.blog_order}/>
                }
                {/* partners goes here */}
                <Suspense fallback={<div>Loading...</div>}>
                    {
                        menu.partners &&
                        <div
                            className={`partners md:mt-20 mt-10 sm:mt-10 text-xs sm:text-md md:text-xl lg:text-3xl text-3xl px-8 sm:px-10 md:px-40 xl:px-50 pb-4 sm:pb-20 md:pb-20 xl:pb-20 text-center
  ${menu.partners_order != null ? ('order-' + menu.partners_order) : "order-none"}`}>
                            <h1 style={{ color: "#a3a3a3", textAlign: "center" }}>
                                {/* პარტნიორები */}
                                {__("client.partners", sharedData)}
                            </h1>
                            <Swiper
                                loop={true}
                                autoplay={{
                                    delay: 2500,
                                    disableOnInteraction: false,
                                }}
                                slidesPerView={3}
                                breakpoints={{
                                    // when window width is >= 640px
                                    640: {
                                        slidesPerView: 3,
                                    },

                                    900: {
                                        slidesPerView: 6,
                                    }
                                }}
                                spaceBetween={30}
                                freeMode={true}
                                pagination={{
                                    clickable: true,
                                }}
                                modules={[FreeMode]}
                                className="mySwiper mt-10 sm:mt-10 md:mt-20 xl:mt-20"
                            >
                                {
                                    (partners != null ?
                                        partners.map((e, i) => {
                                            return (
                                                <SwiperSlide key={i}> <img style={{ maxWidth: "100px" }}
                                                    src={
                                                        e.file != null
                                                            ? "/" +
                                                            e.file.path +
                                                            "/" +
                                                            e.file.title
                                                            : null
                                                    }
                                                    alt="asd" /> </SwiperSlide>
                                            )
                                        })
                                        : "")
                                }
                            </Swiper>
                            {
                                partnerslink != null ?
                                    <a className='text-sm' href={partnerslink} target="_blank">გაიგე მეტი</a>
                                    : ""
                            }
                        </div>
                    }
                </Suspense>


                {
                    menu.subscribers != null &&
                    <div className={`px-4 sm:px-4 md:px-40 xl:px-40 py-20 mt-2 ${menu.subscribers_order != null ? ('order-' + menu.subscribers_order) : "order-none"}`} style={{ backgroundColor: "#f3f2fd" }}>
                        <p className='mb-4' style={{ color: "#5e5e5e" }}> {__('client.subscribe_form', sharedData)}</p>
                        <form onSubmit={handleSubmit}>
                            {/* <input type="text" name="email" id="email" /> */}
                            <input type="email" id="email"
                                className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-1/2 md:screen-1/2 xl:screen-1/2 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="name@flowbite.com" required name='email' onChange={handleChange} ></input>
                            <button type='submit'
                                style={{
                                    backgroundColor: "#6f61ea",
                                    color: "#ffff",
                                    padding: "10px 20px 10px",
                                    borderRadius: "5px",
                                    minWidth: "100px",
                                }}
                                className="mb-10 flex mt-4">გამოიწერე

                            </button>
                            {success ?
                                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                                    <span class="font-medium">Success alert!</span>
                                </div>
                                : ""
                            }
                        </form>
                    </div>
                }

            </div>

            {/* //შეიძლება დაგაინტერესოს
 სლადიერი1
*/}
            {
                menu.slider1 != null &&
                <div
                // data-aos="slide-down"
                 className="slider px-4 sm:px-4 md:px-40 xl:px-40 p-20 order-last">
                    <h2 className='mb-8 text-xs sm:text-md md:text-xl lg:text-3xl'>{__("client.slider2_title", sharedData)}</h2>
                    <Swiper
                        slidesPerView={1}
                        spaceBetween={30}
                        breakpoints={{
                            // when window width is >= 640px
                            640: {
                                slidesPerView: 1,
                            },

                            900: {
                                slidesPerView: 3,
                            }
                        }}
                        className="mySwiper text-gray-900"
                    >
                        {
                            null != slider2[0] ?
                                slider2[0].blog.map((e, i) => {
                                    return (
                                        <React.Fragment key={i}>
                                            <SwiperSlide className='mb-20'>
                                                <div className='grid rounded-xl' style={{ backgroundColor: "#ffff", overflow: "hidden" }}>
                                                    <img className='' style={{maxHeight:'250px'}} src={
                                                        e.file != null
                                                            ? "/" +
                                                            e.file.path +
                                                            "/" +
                                                            e.file.title
                                                            : null
                                                    } alt="" />
                                                      <div className='sm:p-1 md:p-4 xl:p-2 '>
                                                        <h3 className='textclamp2'
                                                        >{e.title}</h3>
                                                        <p className=""
                                                        style={{
                                                            color: "#cbcbcb",
                                                            whiteSpace: 'nowrap',
                                                            overflow: 'hidden',
                                                            width:'200px',
                                                            textOverflow: 'ellipsis',
                                                         }}>{e.description}</p>
                                                    </div>

                                                </div>
                                            </SwiperSlide>
                                        </React.Fragment>
                                    )
                                }) : ""
                        }


                    </Swiper>
                </div>
            }
            {/* შეიძლება დაგაინტერესოს end */}

            {/* მსგავსი პოსტების სსლაიდერივით როა  */}
            <div className="order-first" style={{ backgroundColor: "#f2f3fd", }}>


                {
                    menu.slider3 != null &&
                        <div
                            className="xl:px-40 md:px-40 sm:px-10 px-10"
                            style={{
                                width: "100%",
                                backgroundColor:'#6e61ea',
                            }}
                        >
                            <Swiper className="mySwiper"
                                style={{
                                    "--swiper-pagination-bullet-inactive-opacity": "1",
                                    "--swiper-pagination-bullet-size": "10px",
                                    "--swiper-pagination-bullet-horizontal-gap": "6px",
                                }}
                                loop={true} >

                                {
                                    slider3.map((e, i) => {
                                        return (
                                            <SwiperSlide
                                                className="flex md:flex-nowrap xl:flex-nowrap py-20"
                                                key={i}
                                            >
                                                <div
                                                className="px-1 sm:pr-10 md:pr-40 xl:pr-40">
                                                       <div style={{position:'relative'}}>
                                                        <img className='w-3/4 rounded-md'
                                                        style={{zIndex:'9999'}}
                                                        src={
                                                            e.file != null
                                                                ? "/" +
                                                                e.file.path +
                                                                "/" +
                                                                e.file.title
                                                                : null
                                                        } alt="asd" />
                                                        <div className='h-full rounded-md'
                                                        style={{
                                                            backgroundColor: '#C49FFF',
                                                            zIndex:'-10',
                                                            position: 'absolute',
                                                            height: '100%',
                                                            width: '80%',
                                                            left: '10px',
                                                            top: '30px',
                                                        }}
                                                        ></div>
                                                       </div>
                                                </div>
                                                <div className="left w-1/3 pt-20 text-white">
                                                    <h3 className="mb-4 text-xs sm:text-md md:text-xl lg:text-3xl">{e.title}</h3>
                                                    <h1 className="mb-4">{e.description}</h1>
                                                    <h4 className='text-xs text-slider3color'>{e.short_description}</h4>
                                                </div>

                                            </SwiperSlide>
                                        )
                                    })
                                }



                            </Swiper>
                        </div>
                }
            </div>


            {/* footer here */}
            <Suspense fallback={<div>Loading...</div>}>
                <Footer data={footermeny} code={code} />
            </Suspense>
        </>
    )

}

export default Test;
