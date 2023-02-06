import React, { useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";

export const SocialMedia = () => {
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
                {/* <BiChevronDown className="w-8 h-8" /> */}
            </div>
            <p className="opacity-50 pb-5">{props.a}</p>
        </div>
    );
};


export const HomeBlog = (props) => {
    function fn(text, count, insertDots){
    return text.slice(0, count) + (((text.length > count) && insertDots) ? "..." : "");
}
    let iconsText = [props.data.icon1_text, props.data.icon2_text,props.data.icon3_text];
    const { filepath } = usePage().props;
    const renderHTML = (rawHTML) => React.createElement("div", { dangerouslySetInnerHTML: { __html: rawHTML } });
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
    return (
        <>
            <div className="flex flex-col sm:mt-4 md:mt-20 mx-auto xl:mt-20 px-0 sm:px-10 md:px-10 xl:px-0 my-4 pb-4"
                style={{width:'90%'}}
            >
                <h2 className="text-center text-sm sm:text-md md:text-xl lg:text-3xl" style={{ color: "#c9a7ff" }}>{props.data.title}</h2>
                <div className={`flex-inline sm:flex-inline md:flex xl:flex ${props.left ? "flex-row-reverse" : ""}  mt-4 sm:mt-4 md:mt-20 xl:mt-20 xl:h-96`}
                >
                    <div className="left grid px-2 sm:px-4 md:px-20 xl:px-20 h-full">
                        <span className="text-xs sm:text-xs md:text-xs xl:text-xl" style={{float:"left"}}>{ fn(props.data.description, 50, true)}</span>
                        <div className="mt-4 sm:mt-4 md:mt-10 xl:mt-10 mb-10 grid grid-cols-3"
                        >
                            {
                                props.data.iconimages != [] ?
                                props.data.iconimages.map((e, i) => {
                                    if(e.type != "header_icon"){
                                        return (
                                            <div key={i} className='mr-2 '
                                            >
                                                <img
                                                className="h-8 sm:h-8 md:h-12 xl:h-14"
                                                src={
                                                     props.data.iconimages[i] != null
                                                     ? filepath + '/' + props.data.iconimages[i].name
                                                     : null
                                                } alt="err" />

                                            </div>
                                        )
                                    }
                                    })
                                    : ""
                            }
                              <div className="h-8 sm:h-8 md:h-12 xl:h-14 py-4">
                                         <p className="flex text-xs sm:text-sm md:text-md xl:text-xl ">{iconsText[0]}</p>
                              </div>
                              <div className="h-8 sm:h-8 md:h-12 xl:h-14 py-4">
                                         <p className="flex text-xs sm:text-sm md:text-md xl:text-xl ">{iconsText[1]}</p>
                              </div>
                              <div className="h-8 sm:h-8 md:h-12 xl:h-14 py-4">
                                         <p className="flex text-xs sm:text-sm md:text-md xl:text-xl ">{iconsText[2]}</p>
                              </div>

                        </div>
                        <div className="container">
                        {
                            props.data.vaccancylink != null ?
                                <a href={props.data.vaccancylink} target="_blank">
                                    <button
                                    style={{
                                        border: "1px solid #6f61ea",
                                        color: "#6f61ea",
                                        borderRadius: "5px",
                                    }}
                                    className="mb-10 py-2 px-2 text-sm"> <p>{props.data.button_text}</p> </button>
                                </a>

                              : <button
                              style={{
                                  border: "1px solid #6f61ea",
                                  color: "#6f61ea",
                                  borderRadius: "5px",
                              }}
                              className="mb-10 py-2 px-2 text-sm"> {props.data.button_text}
                       </button>
                           }

                             </div>


                    </div>
                    <div className={`pl-2 pr-2 right w-1/2 pl-2 sm:pl-2 md:pl-20 xl:pl-0 flex ${props.left? 'justify-start': 'justify-end' }`}>
                        <img className=" object-cover h-40 md:h-80 xl:h-80 rounded-lg" src={
                                props.data.file != null
                                ? "/" +
                                props.data.file.path +
                                "/" +
                                props.data.file.title
                                : null
                        } alt="asdasd" />
                    </div>
                </div>
            </div>
        </>
    )
}





// blog


export const BlogPost = ({ data }) => {
    const date = () => {
        let z = data.created_at.split("-");
        z[2] = z[2].split(":");
        z[2] = z[2][0].slice(0, z[2][0].search("T"));
        return z;
    }
    return (
        <Link href={route('client.singleblog', data.id)}>
            <div className='grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-4 sm:gap-40 md:gap-40 xl:gap-40'>
                <div className="h-40 sm:h-40 md:h-60 xl:h-full xl:w-full 2xl:w-3/4 rounded-lg object-fill overflow-hidden" style={{ maxHeight: "350px", overflow: "hidden" }}>
                    <img className="w-full h-full"
                        style={{ objectFit: "fill" }}
                        src={
                            data.file != null
                                ? "/" +
                                data.file.path +
                                "/" +
                                data.file.title
                                : null
                        }
                        alt="img" />
                </div>
                <div className='flex items-center'>
                    <div className=''>
                        <h2 style={{ color: "#686868" }} className="text-xs sm:text-md md:text-xl lg:text-3xl textclamp2">{data.title}</h2>
                        <p className='mt-4' style={{ color: "#7668eb" }}>{`${date()[2]}.${date()[1]}.${date()[0]}`}</p>
                    </div>
                </div>
            </div>
        </Link>
    )
}
