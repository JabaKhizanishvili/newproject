import React from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import logo from "/assets/images/navbar/logo.png";
import phone from "/assets/images/navbar/phone_icon.png";
import location from "/assets/images/navbar/location.png";
import fa from "/assets/images/navbar/fa.png";
import ins from "/assets/images/navbar/in.png";
import tw from "/assets/images/navbar/tw.png";
import li from "/assets/images/navbar/li.png";
import mail from "/assets/images/navbar/mail.png";
import { SocialMedia } from "./SmallComps";


const Footer = (props) => {
    const {
    gphone,
    gemail,
    gfacebook,
    glinkedin,
    gtwitter,
    ginstagram,
    gaddress,
    currentLocale,
} = usePage().props;

  const { pathname } = currentLocale;

  return (
    <footer
    style={{
      color: "#b1b1b1"
    }}
      id="footer"
      className={`pt-0 relative block`}
    >
      <div className="wrapper">
      </div>
      <div className="text-center">

        <div
        style={{
          backgroundColor:"#f3f2fd"
        }}
        className="py-14 border-t border-solid sm:text-base text-sm flex justify-around items-center px-4 sm:px-4 md:px-0 xl:px-0">
          <div className="flex flex-col">
              <Link className=" w-fit mb-8" to="/">
                <img className="mx-auto w-50" src={logo} alt="" />
              </Link>

                {
                    gphone.value != null &&
                    <div className="">
                    {/* <FaPhone/> */}
                    <img src={phone} style={{float: "left"}} className="w-4 h-4" alt="" />
                    <span style={{flaot:"left"}}>{gphone.value}</span>
                    </div>
                }
                {
                    gemail.value != null &&
                    <div className="">
                    {/* <FaPhone/> */}
                    <img src={mail} style={{float: "left"}} className="w-4 h-4" alt="" />
                    <span style={{flaot:"right"}}>{gemail.value}</span>
                    </div>
                }
                {
                    gaddress.value != null &&
                    <div className="">
                    {/* <FaPhone/> */}
                    <img src={location} style={{float: "left"}} className="w-4 h-4" alt="" />
                    <span style={{flaot:"right"}}>{gaddress.value}</span>
                    </div>
                }


          </div>

          <div div className="flex flex-col items-end h-40 justify-between"
          style={{
            color: "#8c80ee",
          }}
          >
              <ul className="">
                {
                    props.data.map(
                        (e,i)=>{
                            if(e.parent_id == null){
                                return(
                                    <li
                                    key={i}
                                    className={`inline-block bold sm:mx-4 mx-2 ${props.code == e.name?'text-blue-900':undefined}`}
                                    >
                                       <Link href={route("client.home.menu", e.name)}>{e.name}</Link>
                                    </li>
                                )
                            }
                        }
                    )
                }
              </ul>

              <div className="flex justify-between w-1/2 "
              style={{
                color:"#8c80ee",
              }}
              >


                {
                    ( gfacebook.value!= null ?
                        <a href={gfacebook.value} target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={fa} alt="" />
                    </a>
                    :
                    undefined )
                }
                {
                    ( ginstagram.value!= null ?
                        <a href={ginstagram.value} target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={ins} alt="inst" />
                    </a>
                    :
                    undefined )
                }
                {
                    ( glinkedin.value!= null ?
                        <a href={glinkedin.value} target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={li} alt="li" />
                    </a>
                    :
                    undefined )
                }
                {
                    ( gtwitter.value!= null ?
                        <a href={gtwitter.value} target="_blank">
                    {/* <FaTwitter/> */}
                    <img src={tw} alt="tw" />
                    </a>
                    :
                    undefined )
                }
              </div>
              <p
              style={{
                color: "#d0cfd8",
                fontSize: "15px",
              }}
              >@copyright by Jaba Khizanishvili</p>
          </div>

        </div>
      </div>
    </footer>
  );
};

export default Footer;
