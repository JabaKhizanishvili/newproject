import React from "react";
// import News1 from "../assets/images/news/1.png";
// import News2 from "../assets/images/news/2.png";
// import News3 from "../assets/images/news/3.png";
// import News4 from "../assets/images/news/4.png";
// import News5 from "../assets/images/news/5.png";
// import News6 from "../assets/images/news/6.png";
// import News7 from "../assets/images/news/7.png";

// ⛔ there should be NO MORE and NO LESS than 8 images in this section

const ImageLayout = () => {
  return (
    <div className="imageLayout wrapper flex items-center justify-center">
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/1.png" alt="" />
        </div>
      </div>
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img short overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/2.png" alt="" />
        </div>
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/3.png" alt="" />
        </div>
      </div>
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img long overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/7.png" alt="" />
        </div>
      </div>
      <div className="column w-1/5 lg:mx-2 mx-1">
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/5.png" alt="" />
        </div>
        <div className="img short overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/6.png" alt="" />
        </div>
      </div>
      <div className="column medium w-1/5 lg:mx-2 mx-1">
        <div className="img medium overflow-hidden lg:my-4 my-2 border  border-solid sm:rounded-xl rounded-md border-custom-yellow">
          <img className="w-full h-full object-cover" src="/assets/images/news/4.png" alt="" />
        </div>
      </div>
    </div>
  );
};

export default ImageLayout;
