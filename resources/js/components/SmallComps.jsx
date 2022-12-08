import React, { useState } from "react";
// import sm1 from "../assets/images/icons/1.png";
// import sm2 from "../assets/images/icons/2.png";
// import sm3 from "../assets/images/icons/3.png";
import { BiChevronDown } from "react-icons/bi";

export const SocialMedia = () => {
    // console.log(data , inst, yout,'jaba');
    // console.log(data , 'esaa');
  return (
    <div className="flex items-center justify-center">
      <a href="#">
        <img src="/assets/images/icons/1.png" alt="" />
      </a>
      <a href="#" className="mx-5">
        <img src="/assets/images/icons/2.png" alt="" />
      </a>
      <a href="#">
        <img src="/assets/images/icons/3.png" alt="" />
      </a>
    </div>
  );
};

export const Question = (props) => {
  const [OpenQ, setOpenQ] = useState(false);

  return (
    <div
      style={{ maxHeight: OpenQ ? "500px" : "52px" }}
      className="border-b border-solid border-custom-slate-900 mb-5 transition-all duration-300 overflow-hidden"
    >
      <div
        onClick={() => setOpenQ(!OpenQ)}
        className="flex items-center justify-between pb-5 cursor-pointer"
      >
        <div className="text-left">{props.q}</div>
        <BiChevronDown className="w-8 h-8" />
      </div>
      <p className="opacity-50 pb-5">{props.a}</p>
    </div>
  );
};
