import { Inertia } from '@inertiajs/inertia'
import { Link, usePage } from "@inertiajs/inertia-react";
import React, { useState, Suspense } from 'react';
import "../../css/styles.css";
import arrowRight from "/assets/images/navbar/arrow_right.png";


const BlogsWidget = ({data,menu})=>{
    let getblogelements = data;
    const sharedData = usePage().props.localizations;

    return(
        <>
<div
                    className={`blog px-4 sm:px-20 md:px-40 xl:px-0 py-0 sm:py-4 md:py-40 xl:py-40 ${menu != null ? ('order-' + menu) : "order-none"}`}
                    style={{
                         backgroundColor: "#f2f3fd",
                     }}>
                        <h2 className="my-10 text-xs sm:text-md md:text-xl lg:text-3xl"
                        style={{
                            color: "#5e5e5e",
                            textAlign: "center"
                        }}
                        >{__("client.home_blog", sharedData)}</h2>
                        <div className="container-fluid grid grid-cols-1 md:grid-cols-2 xl:px-40 gap-x-10 xl:gap-x-20 place-items-center"
                        >
                            {
                                getblogelements != null && getblogelements[0] != null ?
                                    getblogelements[0].blog.map((e, i) => {
                                        if(i > 1){
                                           return false;
                                        }
                                        if (i != 2) {
                                            return (
                                                <Link className="mb-2 w-full" href={route('client.singleblog', e.id)} key={i}>
                                                    <article style={{
                                                            backgroundColor: "#ffffff",
                                                            overflow: "hidden" ,
                                                            borderRadius: "10px 10px 0px 0px",
                                                            overflow:'hidden',
                                                          }}>
                                                        <div
                                                        // className='h-40 sm:h-40 md:h-60 xl:h-70 w-full'
                                                        className='blogswidget_img'
                                                        >
                                                            <img className='w-full h-full object-cover'
                                                                src={
                                                                    e.file != null
                                                                        ? "/" +
                                                                        e.file.path +
                                                                        "/" +
                                                                        e.file.title
                                                                        : null
                                                                } alt="err" />
                                                        </div>
                                                        <h2 style={{ color: "#5e5e5e", fontWeight: "bolder" }} className="p-4 h-20"> <p className='textclamp2'>
                                                            {e.title}
                                                        </p></h2>
                                                        <p style={{
                                                            color: "#cbcbcb",
                                                            // whiteSpace: 'nowrap',
                                                            // textOverflow: 'ellipsis',
                                                         }} className="px-4 pb-6 textclamp3 h-30">{e.description}</p>
                                                        <Link href={route('client.singleblog', e.id)} key={i}>
                                                            <p className='text-blue-200 text-xs px-4 pb-4' style={{ float: "right" }}>
                                                                {/* გაიგე მეტი */}
                                                                {__("client.blogwidget_btn", sharedData)}
                                                            </p>
                                                        </Link>
                                                    </article>
                                                </Link>
                                            )
                                        }
                                    })
                                    : ""
                            }
                        </div>
                        <div className="flex justify-center pt-20 sm:pt-pt2 md:pt-4 xl:pt-10 pb-2">
                            {
                                getblogelements != null && getblogelements[0] != null ?
                                    <Link href={route("client.home.menu", getblogelements[0].name)}> <p className="flex " style={{color:'#5E5E5E'}}>
                                        {__("client.blogwidget_btn", sharedData)}
                                        <img
                                        className='ml-2'
                                        style={{
                                            transform : 'scale(0.8)'
                                        }}
                                        src={arrowRight} alt='err'></img>
                                    </p> </Link>
                                    : ""
                            }
                        </div>
                    </div>
        </>
    )


}

export default BlogsWidget;
