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
            <div className="flex flex-col sm:mt-4 md:mt-20 xl:mt-20 px-0 sm:px-10 md:px-40 xl:px-40 my-4">
                <h2 className="text-center text-sm sm:text-md md:text-xl lg:text-3xl" style={{ color: "#c9a7ff" }}>{props.data.title}</h2>
                <div className={`flex ${props.left ? "flex-row-reverse" : ""}  mt-20`}>
                    <div className="left w-1/2 px-4 sm:px-4 md:px-20 xl:px-20">
                        <p className="text-sm sm:text-sm md:text-md xl:text-lg" style={{float:"left"}}>{props.data.description}</p>
                        <div className="grid grid-cols-3 gap-x-0 sm:gap-x-0 md:gap-x-10 xl:gap-x-10  justify-around mt-10">
                            {
                                props.data.iconimages != [] ?
                                props.data.iconimages.map((e, i) => {
                                    if(e.type != "header_icon"){
                                        return (
                                            <div key={i}>
                                                <img src={
                                                     props.data.iconimages[i] != null
                                                     ? filepath + '/' + props.data.iconimages[i].name
                                                     : null
                                                } alt="err" />
                                                <div className="flex flex-col py-10">
                                                    <p className="flex  text-sm sm:text-sm md:text-md xl:text-lg ">{e.text}</p>
                                                </div>
                                            </div>
                                        )
                                    }
                                    })
                                    : ""
                            }

                        </div>
                           {
                            props.data.vaccancylink != null ?
                                <a href={props.data.vaccancylink} target="_blank">
                                    <button
                                    style={{
                                        border: "1px solid #6f61ea",
                                        color: "#6f61ea",
                                        // padding: "10px 20px 10px",
                                        borderRadius: "5px",
                                        // minWidth: "100px",
                                    }}
                                    className="mb-10 flex py-2 px-5 text-sm"> <p>{props.data.button_text}</p> </button>
                                </a>

                              : ""
                           }

                    </div>
                    <div className="right w-1/2">
                        <img className="w-full object-cover h-40 md:h-80 xl:h-80" src={
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
                <div className="h-40 sm:h-40 md:h-60 xl:h-72 w-full object-fill" style={{ maxHeight: "350px", overflow: "hidden" }}>
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
                        <h2 style={{ color: "#686868" }} className="text-xs sm:text-md md:text-xl lg:text-3xl">{data.title}</h2>
                        <p className='mt-4' style={{ color: "#7668eb" }}>{`${date()[2]}.${date()[1]}.${date()[0]}`}</p>
                    </div>
                </div>
            </div>
        </Link>
    )
}
