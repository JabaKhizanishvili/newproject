import React from "react";
import { VscChromeClose } from "react-icons/vsc";

const VideoPopup = ({ open, closeVideo, src }) => {
  return (
    <div
      className={`fixed left-0 top-0 w-screen h-screen bg-black/[0.9] flex items-center justify-center z-50 transition-all duration-500 ${
        open ? "opacity-100 visible" : "opacity-0 invisible"
      }`}
    >
      <div className="wrapper sm:px-10 px-0 h-4/5 relative">
        <button
          onClick={closeVideo}
          className="absolute -top-6 -right-1 text-white"
        >
          <VscChromeClose className="w-6 h-6" />
        </button>
        <iframe
          className="w-full h-full"
          src={src}
          title="YouTube video player"
        //   frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowFullScreen
        ></iframe>
      </div>
    </div>
  );
};

export default VideoPopup;
