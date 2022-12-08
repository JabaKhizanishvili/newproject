import React from "react";
// import bg from "../assets/images/bgs/3.png";
// import Img1 from "../assets/images/rental/10.png";
// import Img2 from "../assets/images/rental/7.png";
import { HiArrowNarrowRight } from "react-icons/hi";
import { Link } from '@inertiajs/inertia-react'
import { useState } from "react";
import Layout from "../Layouts/Layout";

const SingleRental = ({seo,product,product_images}) => {
    const renderHTML = (rawHTML) => React.createElement("div", { dangerouslySetInnerHTML: { __html: rawHTML } });
//   const imgs = ["/assets/images/rental/10.png", "/assets/images/rental/7.png"];
let imgs = new Array();
// console.log(product_images, 'esa');
product_images.map((e,i)=>{
    imgs.push("/"+e.path+"/"+e.title)
})

  const [imgIndex, setImgIndex] = useState(0);

  return (
    <Layout seo={seo}>
    <div
      className="bg-cover bg-center bg-no-repeat text-custom-dark"
      style={{ backgroundImage: `url('/assets/images/bgs/3.png')` }}
    >
      <div className="wrapper flex items-start justify-between py-40 flex-col lg:flex-row">
        <div className=" lg:w-1/2 mb-20">
          <div className="">
            <img className="w-full " src={imgs[imgIndex]} alt="" />
          </div>
          <div className="mt-10 flex items-center justify-start">
            {imgs.map((item, index) => {
              return (
                <div
                  onClick={() => setImgIndex(index)}
                  key={index}
                  className={`cursor-pointer sm:w-32 w-20 hover:opacity-100 transition-all duration-300 mx-2 ${
                    imgIndex === index ? "opacity-100 " : "opacity-50"
                  }`}
                >
                  <img src={item} alt="" />
                </div>
              );
            })}
          </div>
        </div>
        <div className=" lg:w-1/2  lg:ml-10">
          <div className=" xl:text-6xl sm:text-5xl text-4xl bold ">
            {product.title}
          </div>
          <p className="my-10">
            {product.description}
          </p>
          <div className="bold text-2xl mb-6">Specification</div>
          <div className="sm:columns-2">
            <div className="mb-6">
              {/* processor <br />
              • Processor model: Intel Core i3-12100 <br />
              • Number of cores: 4 <br />
              • Number of streams: 8 <br />
              • Frequency: 3.30 GHz <br />
              • Maximum frequency: 4.40 GHz <br />
              • Cache memory: 12 MB <br /> */}
              {/* {product.specifications} */}
              {renderHTML(product.specifications)}
            </div>




          </div>
          <div className="flex justify-end items-center mt-10">
            <p>For order:</p>
            <Link
              href="/contact"
              className={`flex items-center justify-center border border-solid border-custom-dark bold text-custom-dark sm:h-10 h-10 w-fit sm:px-6 px-4 rounded-full   sm:text-base text-sm  whitespace-nowrap ml-5`}
            >
              <span>Contact us</span>
              <HiArrowNarrowRight className="w-6 h-6 m-2" />
            </Link>
          </div>
        </div>
      </div>
    </div>
    </Layout>
  );
};

export default SingleRental;
