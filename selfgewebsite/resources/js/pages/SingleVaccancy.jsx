import React from "react";
import { usePage } from "@inertiajs/inertia-react";
// import { Inertia } from '@inertiajs/inertia'
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";
import fa from "/assets/images/navbar/fa.png";
import ins from "/assets/images/navbar/in.png";
import li from "/assets/images/navbar/li.png";

import "../Layouts/index.css";
import "../../css/styles.css";

const SingleVaccancy= ({seo,vaccancy, menus,menu,code})=>{
    const {
        errors,
        gphone,
        gemail,
        gfacebook,
        glinkedin,
        gtwitter,
        ginstagram,
        gaddress,
        locales,
        currentLocale,
        locale_urls,
    } = usePage().props;

    console.log(vaccancy, 'esaa');
   return(
    <>
     <Navbar data={menus} code={null}/>
        <div className='pt-40 flex justify-center'
          style={{
            fontFamily: "geo",
            color: "#b1b1b1",
          }}
        >
        <div className='w-2/3 pb-20'>
            <h2 className='text-4xl mb-10'
            style={{
                'color': "#5e5e5e"
            }}
            >
            {vaccancy.title}
            </h2>
        <p>
            {vaccancy.description}
        </p>
{/* here hoes icons */}

        <div className="flex mt-4">
        {
                    ( vaccancy.vaccancylink1!= null ?
                        <a href={vaccancy.vaccancylink1} className="mr-2" target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={fa} alt="" />
                    </a>
                    :
                    undefined )
                }
                {
                    ( vaccancy.vaccancylink2!= null ?
                        <a href={vaccancy.vaccancylink2} className="mx-2" target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={ins} alt="inst" />
                    </a>
                    :
                    undefined )
                }
                {
                    ( vaccancy.vaccancylink3!= null ?
                        <a href={vaccancy.vaccancylink3} className="mx-2" target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={li} alt="li" />
                    </a>
                    :
                    undefined )
                }
        </div>

        <div>

        </div>
        </div>

        </div>
        <Footer data={menus} code={null}/>
    </>
   )
}
export default SingleVaccancy;
