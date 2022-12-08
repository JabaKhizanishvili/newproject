import React, { useState } from "react";
import { VscChromeClose } from "react-icons/vsc";
import { AiFillCloseSquare } from "react-icons/ai";

const RaySection = ({ title, color, children }) => {
  const [open, setOpen] = useState(false);

  return (
    <div className="border-b border-white/[0.3] mb-10">
      <div
        onClick={() => setOpen(!open)}
        className="flex items-center justify-between "
      >
        <div
          style={{ color: open ? color : "white" }}
          className={`xl:text-7xl sm:text-5xl text-3xl transition-all duration-300  ${
            open ? "opacity-100 " : "opacity-50"
          }`}
        >
          {title}
        </div>
        <button>
          <VscChromeClose
            className={`cursor-pointer w-9 h-9 rotate-45 transition-all duration-300 ${
              open ? "rotate-0" : "rotate-45"
            }`}
          />
        </button>
      </div>
      <div
        className={`${
          open
            ? "h-fit opacity-100 visible py-10"
            : "h-0 opacity-0 invisible py-5"
        } transition-all duration-300`}
      >
        {children}
      </div>
    </div>
  );
};

export default RaySection;
