import React ,{ useEffect } from "react";
// import bg from "../assets/images/bgs/2.png";
// import Img1 from "../assets/images/raypages/17.png";
// import Img2 from "../assets/images/raypages/10.png";
// import Img3 from "../assets/images/raypages/11.png";
// import Img4 from "../assets/images/raypages/12.png";
import { BiCheck, BiCalendar } from "react-icons/bi";
// import PlayIcon from "../assets/images/svg/play.svg";
import { HiArrowNarrowRight } from "react-icons/hi";
import { Link, usePage } from '@inertiajs/inertia-react'
import VideoPopup from "../components/VideoPopup";
import { useState } from "react";
import Layout from "../Layouts/Layout";
import { Inertia } from '@inertiajs/inertia'
import Swal from 'sweetalert2'

const SingleCourse = ({seo, courses,othercourses,success}) => {
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
    const sharedData = usePage().props.localizations;
    if (success) {
             Swal.fire({
                 title: __('client.form_success', sharedData) ,
                 text: __('client.form_text', sharedData),
                 icon: 'success',
                 confirmButtonText: __('client.form_btn', sharedData)
             })
            //  Swal.fire({
            //      title: __('client.alert_success', sharedData),
            //      text: __('client.alert_sent_successfuly', sharedData),
            //      icon: __('client.alert_success1', sharedData),
            //      confirmButtonText: 'Cool'
            //  })
         }
    const [values, setValues] = useState({
        name: "",
        surname: "",
        phone: "",
        email: "",
        course: courses.title ? courses.title : "",
    })

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
        let validForm = true;
        for (const key in values) {
            if (forms[key].value == "") {
                validForm = false;
            }
        }
// console.log(values);
        Inertia.post(route('client.documentations.rateservices'), values)
    }


    useEffect(() => {
        // alert('sadsa');
        // document.documentElement.scrollTop = 0
        // console.log(window.pageYOffset);

      },);


  const [showVideo, setShowVideo] = useState(false);
  const checks = [
    "From banking and insurance to wealth management and on securities distribution,",
    "From banking and insurance to wealth management and on securities distribution, we dedicated financial services",
    "From banking and insurance to wealth management.",
    "From banking and insurance to wealth management and on securities distribution, we dedicated financial services them the teams serve all major sectors. of the industry.",
  ];
  const includes = [
    "40 online meetings",
    "90 hours lecture",
    "Home works for every lecture",
    "Full lifetime access to video resources",
    "Certificate on completion",
  ];
  const otherCourses = [
    {
      img: "/assets/images/raypages/10.png",
      name: "Name of the course",
      para: "From banking and insurance to wealth serve all major sectors. of the industry.",
    },
    {
      img: "/assets/images/raypages/11.png",
      name: "Name of the course",
      para: "From banking and insurance to wealth serve all major sectors. of the industry.",
    },
    {
      img: "/assets/images/raypages/12.png",
      name: "Name of the course",
      para: "From banking and insurance to wealth serve all major sectors. of the industry.",
    },
  ];

  return (
    <Layout seo={seo}>
    <div className="bg-white text-custom-dark singleCourse pb-20">
      <section
        className="sm:h-80 h-52 bg-cover bg-no-repeat bg-center mb-10"
        style={{ backgroundImage: `url('/assets/images/bgs/2.png')` }}
      ></section>
      <section className="wrapper flex justify-start items-start lg:flex-row flex-col">
        <div className="lg:w-1/2 lg:mr-10 mb-20">
          <div className=" 2xl:text-7xl xl:text-6xl sm:text-5xl text-4xl bold mb-10">
            {/* Motion designer course */}
            {courses.title}
          </div>
          <div className="sm:text-3xl text-2xl bold mb-7">
            What you'll learn
          </div>
          {/* {checks.map((item, index) => {
            return (
              <p key={index} className="mb-5 flex items-start justify-start">
                <BiCheck className="shrink-0 mr-2 w-5 h-5 " />
                <span> {item}</span>
              </p>
            );
          })} */}
          {renderHTML(courses.whattolearn)}

          <div className="mt-16 ">
            <div className="sm:text-3xl text-2xl bold mb-7">
              Register on a course
            </div>
            <form onSubmit={handleSubmit} name='evaluation' className="max-w-sm ">
              <input type="text" placeholder={__('client.form_name', sharedData)} name='name' id='name' onChange={handleChange} />
              <input type="text" placeholder={__('client.form_surname', sharedData)} name='surname' id='surname' onChange={handleChange} />
              <input type="text" placeholder={__('client.form_phone', sharedData)} name='phone' id='phone' onChange={handleChange} />
              <input type="text" placeholder={__('client.form_email', sharedData)} name='email' id='email' onChange={handleChange} />
              <input type="hidden"  name='course' id='course'
            //    onChange={handleChange}
               value={courses.title}
               />
              <button
                className={`flex items-center justify-center border border-solid border-custom-dark bold text-custom-dark sm:h-10 h-10 w-fit sm:px-6 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap mx-auto mt-5`}
              >
                <span>Register</span>
                <HiArrowNarrowRight className="w-6 h-6 m-2" />
              </button>
            </form>
          </div>
        </div>
        <div className="shrink-0 lg:w-1/3 lg:-translate-y-32">
          <div className="bg-white shadow-lg ">
            <div
              onClick={() => setShowVideo(true)}
              className="relative w-full h-80 grayscale cursor-pointer group"
            >
              <img src={"/assets/images/raypages/17.png"} alt="" className="w-full h-full object-cover" />
              <img
                className="absolute left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2 group-hover:animate-pulse"
                src="/assets/images/svg/play.svg"
                alt=""
              />
            </div>
            <div className="p-6 pb-10 ">
              <div className="flex items-center justify-start mb-6">
                {courses.special_price?
                <div className="text-3xl bold">₾ {courses.special_price}</div>
                : ""
                }
                <div className="text-2xl bold opacity-70 mx-4">₾ {courses.price}</div>
                <div className="text-2xl bold  text-custom-red">
                    {/* 60% */}
                    {
                     courses.special_price? `${((courses.special_price * 100)/ courses.price).toFixed(0) }% ` : ""
                    }
                    { courses.special_price ? "Off" : "" }
                    </div>
              </div>
              <div className="bold mb-3">This course includes:</div>
              {/* {includes.map((item, index) => {
                return (
                  <p className="mb-2" key={index}>
                    {item}
                  </p>
                );
              })} */}
              {renderHTML(courses.course_includes)}
              <div className="bold mb-3 mt-6">Course starting at:</div>
              <p>
                <BiCalendar className="inline-block mr-1 w-5 h-5 mb-1" />
                {/* 15 Octomber */}
                {courses.starts}
              </p>
            </div>
          </div>
          <div className="my-10">
            <div className="bold mb-3 mt-6">Other courses</div>
            {othercourses.map((item, index) => {
              return (
                <Link
                  key={index}
                  href={route("client.rayacademy.show",item.id)}
                  className="p-4 hover:bg-custom-slate-200 flex items-center justify-start transition"
                >
                  <div className="w-20 h-20 rounded-lg overflow-hidden mr-4 shrink-0">
                    <img
                      className="object-cover w-full h-full"
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
                  </div>
                  <div>
                    <div className="bold mb-1">{item.title}</div>
                    <p>{item.short_description}</p>
                  </div>
                </Link>
              );
            })}
          </div>
        </div>
      </section>
      <VideoPopup
        open={showVideo}
        closeVideo={() => setShowVideo(false)}
        src={
            courses.video_url ? courses.video_url : "https://www.youtube.com/embed/xYrPGIJ2qoo"
        }
      />
    </div>
    </Layout>
  );
};

export default SingleCourse;
