import React,{useState} from 'react';
import { usePage } from "@inertiajs/inertia-react";
import { Pagination }  from "swiper";
import { Inertia } from '@inertiajs/inertia'
import { Swiper, SwiperSlide } from "swiper/react";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";
import "../Layouts/index.css";
import "../../css/blog.css";
import "swiper/css";
import "swiper/css/pagination";

const SingleBlog = ({seo,success,blogs,  menus,menu,code,similarBlogs})=>{
    const [values, setValues] = useState({
        email: "",
    })
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
    const sharedData = usePage().props.localizations;

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

    return(
        <>
        <Navbar data={menus} code={null}/>
          <div className='pt-20'
          style={{
            fontFamily: "geo",
            color: "#b1b1b1",
          }}
          >

            {/* blog start */}
              <div className='mt-20 xl:px:80 px-96 flex align-middle'>
                <div className="px-20">
                <h1 className='text-center mb-10' style={{color:"#5f5f5f",fontSize:"30px"}}>{blogs.title}</h1>
                <div className='mb-8 rounded-xl overflow-hidden'>
                  <img
                  src={
                    blogs.file != null
                    ? "/" +
                    blogs.file.path +
                    "/" +
                    blogs.file.title
                    : null
                  }
                   alt="img" />
                </div>
                <p>
                    {renderHTML(blogs.description)}
                </p>
                </div>

              </div>
              {/* blog end */}




            {/* <div className="slider px-40 p-20 mt-20">
                <h2 className='mb-8 text-xs sm:text-md md:text-xl lg:text-3xl'>{__("client.slider2_title", sharedData)}</h2>
            <Swiper
        slidesPerView={1}
        spaceBetween={30}
        pagination={{
          clickable: true,
        }}
        modules={[Pagination]}
        className="mySwiper"
        breakpoints={{
            // when window width is >= 640px
            640: {
              slidesPerView: 1,
            },

            900:{
                slidesPerView: 3,
            }
          }}
      >
        {
            similarBlogs.map((e,i)=>{
                return(
                    <React.Fragment key={e.id}>
                        <SwiperSlide className='mb-20'>
                            <div className='grid rounded' style={{backgroundColor: "#ffff", overflow:"hidden"}}>
                                <img className='h-44 w-2/4' style={{ objectFit:"fill"}} src={
                                     e.file != null
                                     ? "/" +
                                     e.file.path +
                                     "/" +
                                     e.file.title
                                     : null
                                } alt="" />
                            <div className='p-4'>
                            <h3>{e.title}</h3>
                                <p className="">{e.description}</p>
                            </div>
                            </div>
                        </SwiperSlide>
                    </React.Fragment>
                )
            })
        }

      </Swiper>
            </div> */}

<div className="slider px-4 sm:px-4 md:px-40 xl:px-40 p-20 order-last mt-20">
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
                            true ?
                            similarBlogs.map((e, i) => {
                                    return (
                                        <React.Fragment key={i}>
                                            <SwiperSlide className='mb-20'>
                                                <div className='grid rounded-md' style={{ backgroundColor: "#ffff", overflow: "hidden" }}>
                                                    <div className='md:h-60 xl:h-96'>
                                                    <img className='object-cover'
                                                     style={{
                                                        // maxHeight:'300px'
                                                        height:'100%'
                                                    }}
                                                     src={
                                                        e.file != null
                                                            ? "/" +
                                                            e.file.path +
                                                            "/" +
                                                            e.file.title
                                                            : null
                                                    } alt="" />
                                                    </div>
                                                      <div className='sm:p-1 md:p-4 xl:p-2 '>
                                                        <h3 className='textclamp2'>{e.title}</h3>
                                                        <p className=""
                                                        style={{
                                                            color: "#cbcbcb",
                                                            whiteSpace: 'nowrap',
                                                            overflow: 'hidden',
                                                            width:'40%',
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
          </div>
          <Footer data={menus} code={null}/>
        </>
    )
}

export default SingleBlog;
