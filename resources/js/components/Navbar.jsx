import React, { useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import logo from "/assets/images/navbar/logo.png";
import phone from "/assets/images/navbar/phone.png";

const Navbar = ({data,code, locale,menuforDisplay}) => {
  const {
    locales,
    currentLocale,
    locale_urls,
} = usePage().props;
  const [menu, setMenu] = useState(false);
//   const { pathname } = currentLocale;
 let color = 'green';

  const [dropdownPopoverShow, setDropdownPopoverShow] = React.useState(false);
  const [navIndex, setNavIndex] = useState();
  const btnDropdownRef = React.createRef();
  const popoverDropdownRef = React.createRef();
  const openDropdownPopover = () => {
    createPopper(btnDropdownRef.current, popoverDropdownRef.current, {
      placement: "bottom-start"
    });
    setDropdownPopoverShow(true);
  };
  const closeDropdownPopover = () => {
    setDropdownPopoverShow(false);
  };
  // bg colors
  let bgColor;

  color === "white"
    ? (bgColor = "bg-slate-700")
    : (bgColor = "bg-" + color + "-500");

  return (
    <>
      <div
        className={`fixed w-screen h-screen left-0 top-0 z-50 pt-40 pb-12 transisiton-all duration-500 ${
          menu ? "opacity-100 visible" : "opacity-0 invisible"
        } ${true ? "text-custom-dark " : "text-white"} `}
        style={{ background: true ? "#becdf6" : "#1f1f1f" }}
      >
        <div className="wrapper h-full flex flex-col justify-center items-start">
          <ul className="lg:hidden text-center mx-auto md:mb-10 mb-6 ">
            {data.map((nav, index) => {
              return (
                <li className="block mb-5 md:text-xl" key={index}>
                  <Link className="bold relative navLink" href={route("client.home.menu", nav.name)}>
                    {nav.name}
                  </Link>
                </li>
              );
            })}
          </ul>

        </div>
      </div>

   <header
        className={`${true ? "text-custom-dark " : "text-white"} ${
          menu ? "fixed" : "absolute"
        } top-0 left-0 w-full py-8 z-50 px-10 sm:px-20 md:px-40 shadow-md ${menu? 'bg-inherit' : 'bg-white'}`}
      >
        <div className="relative wrapper flex items-center justify-between" id="navbartext">
          <Link href={route("client.home.index")} style={{width:"100px"}}>
            <img className="sm:w-48 w-32" src={logo} alt="" />
          </Link>
          <div className="flex items-center justify-end">
            <ul className="lg:mr-20 sm:mr-0 md:mr-10 xl:mr-10 lg:inline-block md:inline-block hidden">

              {data.map((nav, index) => {
                    return (
                        <ul className="lg:mx-0 md:mx-0 inline-block" key={index}>
                           <li className="inline-block relative group">
                          {
                            nav.parent_id == null &&
                            <Link className={"text-gray-700 font-bold uppercase text-sm px-0 sm:px-0 md:px-1 xl:px-6 hover:text-red-100 outline-none focus:outline-none mr-1 ease-linear transition-all duration-150 " + bgColor}
                          type="button"
                          href={route("client.home.menu", nav.name)}
                          id={index}
                        >
                           <button
                           className={`items-center w-full  text-base font-bold text-left uppercase bg-transparent rounded-lg md:w-auto md:inline md:mt-0 md:text-base sm:text-xs focus:outline-none font-montserrat ${(code == nav.name?'text-red-100':'')}`}
                           >
                              <span>
                                {nav.name}
                              </span>
                           </button>
                        </Link>
                        }


                      {data.map((e,i)=>{
                        let r,g,b,color;
                        r = Math.round(Math.random()*255)
                        g = Math.round(Math.random()*255)
                        b = Math.round(Math.random()*255)

                      if(e.parent_id != null && nav.id == e.parent_id){
                        return(
                          <div id={i} key={e.id}

                      // style={{ minWidth: "12rem" }}
                    >
                     <div className="absolute z-10 hidden bg-grey-200 group-hover:block">

                <div className="bg-white navbar_dropdown text-gray-400 shadow-lg w-40 text-center">
                  <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                      <Link href={route("client.home.menu", e.name)}>
                        <span><div className="w-4 h-4" style={{float:"left",backgroundColor:`rgb(${r},${g},${b})`}}></div> {e.name}</span>
                      </Link>
                  </div>
                </div>
            </div>
                    </div>
                        )

                      }
                      })}
                        </li>
                        </ul>
                      );


              })}
            </ul>

            <div className="flex justify-between w-10 sm:w-10 md:w-12 xl:w-20">
              <a href="tel:+4733378901" className="hidden sm:block md:block xl:block"> <img className="sm:w-4 w-4 h-4" src={phone} alt="err"></img> </a>
              <div className="bold">
                <span>
                    {
                        Object.keys(locales).map((e, i) => {
                            if(e != currentLocale){
                                return (
                                    <div key={i}>
                                    <Link key={i} href={locale_urls[e]}> {e} </Link>
                                    </div>
                                )
                            }
                        })
                    }
                </span>
              </div>
            </div>

            <button
              onClick={() => setMenu(!menu)}
              className={`mx-2 color-red-100 xl:hidden md:hidden sm:block ${menu ? "menuBtn clicked" : "menuBtn"}`}
            >
              <div className="span"></div>
              <div className="span"></div>
              <div className="span"></div>
            </button>
          </div>

        </div>
      </header>
    </>
  );
};

export default Navbar;
