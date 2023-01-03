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
              <div className='px-40 mt-20'>
                <h1 className='text-center mb-10' style={{color:"#5f5f5f",fontSize:"30px"}}>{blogs.title}</h1>
                <div className='mb-8'>
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
              {/* blog end */}


              <div className='px-40 py-20 mt-20' style={{backgroundColor: "#f3f2fd"}}>
                <p className='mb-4' style={{color: "#5e5e5e"}}> აფსასდა ასდასდ ასდას</p>
                <form onSubmit={handleSubmit}>
                {/* <input type="text" name="email" id="email" /> */}
                <input type="email" id="email" className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@flowbite.com" required  name='email' onChange={handleChange} ></input>
                <button type='submit'
              style={{
                backgroundColor : "#6f61ea",
                color: "#ffff",
                padding: "10px 20px 10px",
                borderRadius: "5px",
                minWidth: "100px",
              }}
              className="mb-10 flex">გამოიწერე
              </button>
                </form>
            </div>

            <div className="slider px-40 p-20 mt-20">
                <h2 className='mb-2' style={{fontSize: "28px"}}>შეიძლება დაგაინტერესოს</h2>
            <Swiper
        slidesPerView={3}
        spaceBetween={30}
        pagination={{
          clickable: true,
        }}
        modules={[Pagination]}
        className="mySwiper"
      >
        {
            similarBlogs.map((e,i)=>{
                return(
                    <React.Fragment key={e.id}>
                        <SwiperSlide className='mb-20'>
                            <div className='grid rounded' style={{backgroundColor: "#ffff", overflow:"hidden"}}>
                                <img src={
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
            </div>
          </div>
          <Footer data={menus} code={null}/>
        </>
    )
}

export default SingleBlog;
