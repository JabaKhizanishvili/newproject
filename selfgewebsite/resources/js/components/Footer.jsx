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

  const leftContactArr = [
    {
        title: gphone.value,
        image: phone
    },
    {
        title: gemail.value,
        image: mail
    },
    {
        title: gaddress.value,
        image: location
    },
  ]

  const rightContactArr = [
    {
        title: gfacebook.value,
        image: fa,
    },
    {
        title: ginstagram.value,
        image: ins,
    },
    {
        title: glinkedin.value,
        image: li,
    },
    {
        title: gtwitter.value,
        image: tw,
    },
  ]


  return (
    <footer
    style={{
      color: "#b1b1b1"
    }}
      id="footer"
      className='relative block'
    >
      <div className="text-center">
        <div
        style={{
          backgroundColor:"#f3f2fd",
        }}

        className="px-4 sm-px-4 md:px-20 xl:px-40 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-2 py-14 border-t border-solid sm:text-base text-sm flex">
          <div className="flex flex-col justify-end">
              <Link className="w-fit mb-8 mx-auto sm:mx-0 md:mx-0 xl:mx-0" to="/">
                <img className="mx-auto w-50" src={logo} alt="" />
              </Link>
              <div className="container sm:w">
              {
                leftContactArr.map((e,i)=>{
                    return(
                        <React.Fragment key={i}>
                            {
                                e.title != null &&
                                <div className="flex mx-auto sm:mx-auto md:mx-0 xl:mx-0 justify-start w-1/2 sm:w-full md:w-96 xl:w-full">
                                <img src={e.image} style={{float: "left"}} className="w-4 h-4 mr-4" alt="" />
                                <span className="text-footer-default-color text-sm">{e.title}</span>
                                </div>
                            }
                        </React.Fragment>
                    )
                })
              }
              </div>

          </div>

          <div div className="flex flex-col items-center sm:items-center md:items-end xl:items-end h-40 justify-between align-baseline pt-4"
          style={{
            color: "#8c80ee",
          }}
          >
              {/* <ul className="grid-inline grid-cols-2 sm:grid-cols-2 md:grid-cols-1 xl:grid-cols-1 text-start justify-around"> */}
              <ul className="">
                {
                    props.data.map(
                        (e,i)=>{
                            if((e.parent_id == null).toString()){
                                return(
                                    <li
                                    key={i}
                                    className={`inline-block bold sm:mx-4 mx-2 ${props.code == e.name?'text-navbar-active':undefined}`}
                                    >
                                       <Link className='text-navbar-active' href={route("client.home.menu", e.name)}>{e.name}</Link>
                                    </li>
                                )
                            }
                        }
                    )
                }
              </ul>

              <div className="flex justify-between w-1/2"
              style={{
                color:"#8c80ee",
              }}
              >
                {
                    rightContactArr.map((e,i)=>{
                        return(
                            <React.Fragment key={i}>
                                {
                                ( (e.title!= null).toString  ?
                                    <a href={gfacebook.value} target="_blank">
                                    {/* <FaTwitter/> */}
                                    <img src={e.image} alt="" />
                                    </a>
                                    :
                                undefined )
                                }
                            </React.Fragment>
                        )
                    })
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
