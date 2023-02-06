import React, { useState } from "react";
import { Link, usePage } from "@inertiajs/inertia-react";
import logo from "/assets/images/navbar/logo.png";
import phone from "/assets/images/navbar/phone.png";
import './popup.css';

const Navbar = ({data,code, locale,menuforDisplay}) => {
  const {
    locales,
    currentLocale,
    locale_urls,
    homePath,
    gphone,
} = usePage().props;


const current_locale = (url,locale)=>{
    url = url.replace('ge', locale)
    return url
}

function preferredOrder(obj, order) {
    var newObject = {};
    for(var i = 0; i < order.length; i++) {
        if(obj.hasOwnProperty(order[i])) {
            newObject[order[i]] = obj[order[i]];
        }
    }
    return newObject;
}
  const [menu, setMenu] = useState(false);
 let color = 'green';

 function changeOrder(index,order, obj){
    let helpVar
    helpVar = obj[order]
    obj[order] = obj[index]
    obj[index] = helpVar
}
// changeOrder(3,5,data);
 let arrayOfParents = [];
 data.map((e,i)=>{
    if(e.parent_id != null){
        arrayOfParents.push(e.parent_id);
    }
 } )

  const [dropdownPopoverShow, setDropdownPopoverShow] = React.useState(false);
  const [navIndex, setNavIndex] = useState();
  const btnDropdownRef = React.createRef();
  const popoverDropdownRef = React.createRef();


  const popNumber = ()=>{
    let popup = document.querySelector('.backgroundcolorchanger');
    popup.classList.toggle("active");
    document.querySelector('.phonepopup').classList.toggle('phonepopupactive')
  }

  window.addEventListener('click',(e)=>{
    if(e.target == document.querySelector('.backgroundcolorchanger')){
       document.querySelector('.backgroundcolorchanger').classList.toggle("active");
       document.querySelector('.phonepopup').classList.toggle('phonepopupactive');
    }
 })


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
    ? (bgColor = "text-navbar-active")
    : (bgColor = "text-navbar-normal");

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
                <React.Fragment key={index}>
                <li className="block mb-5 md:text-xl">
                  <Link className="bold relative navLink" href={current_locale(route("client.home.menu", nav.name), currentLocale)}>
                  {/* <Link className="bold relative navLink" href={route("client.home.menu", nav.name)}> */}
                    {nav.name}
                  </Link>
                </li>
                </React.Fragment>
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
          <Link href={current_locale(homePath, currentLocale)} style={{width:"100px"}}>
          {/* <Link href={homePath} style={{width:"100px"}}> */}
            <img className="sm:w-48 w-32"
            style={{
                transform: 'scale(1.2, 1.3)'
            }}
            src={logo} alt="" />
          </Link>

          <div className="flex items-center justify-end">
            <ul className="lg:mr-20 sm:mr-0 md:mr-10 xl:mr-10 lg:inline-block md:inline-block hidden">
              {data.map((nav, index) => {
                let conditionOfShowDropdown = arrayOfParents.find((e)=>e==nav.id)
                    return (
                        <ul className="lg:mx-0 md:mx-0 inline-block" key={index}>
                           <li className="inline-block relative group">
                          {
                            nav.parent_id == null &&
                            <div className="container">
                            <Link className={"text-gray-700 font-bold uppercase text-sm px-0 sm:px-0 md:px-1 xl:px-6 hover:text-red-100 outline-none focus:outline-none mr-1 ease-linear transition-all duration-150 " + bgColor}
                          type="button"
                          href={current_locale(route("client.home.menu", nav.name), currentLocale)}
                        //   href={route("client.home.menu", nav.name)}
                          id={index}
                        >
                           <button
                           className={`items-center w-full  text-base font-bold text-left uppercase bg-transparent rounded-lg md:w-auto md:inline md:mt-0 md:text-base sm:text-xs focus:outline-none font-montserrat ${(code == nav.name?'text-navbar-active':'')}
                           hover:text-navbar-active
                           `}
                           >
                              <span>
                                {nav.name}
                              </span>
                           </button>
                        </Link>
                        </div>
                        }

               <div style={{ width: "590px", left:"0px" }} className="absolute z-10 hidden bg-grey-200 group-hover:block">
               <div style={{backgroundColor: conditionOfShowDropdown == null  ? 'transparent' :"#f3f1fd", borderRadius: "0px 0px 20px 20px"}} className={`${conditionOfShowDropdown == null  ? '' :""} navbar_dropdown text-gray-400  text-start break-words mt-2 p-8`}>
               <div className="grid grid-cols-2">
                      {data.map((e,i)=>{
                        let r,g,b,color;
                        r = Math.round(Math.random()*255)
                        g = Math.round(Math.random()*255)
                        b = Math.round(Math.random()*255)
                      if(e.parent_id != null && nav.id == e.parent_id){
                        return(
                          <React.Fragment key={e.id}>
                                <div className="container flex justify-start mb-4">
                                    {/* <Link className="col" href={route("client.home.menu", nav.name)}> */}
                                    <div
                                    className="mr-4"
                                    style={{
                                    backgroundColor: e.menucolor != null ? e.menucolor : `rgb(${r},${g},${b})`,
                                    height: "20px",
                                    width: "20px",
                                    float: 'left',
                                    }}></div> <Link  href={route("client.home.menu", e.name)}> <p>{e.name}</p> </Link>
                                    {/* </Link> */}
                                </div>
                          </React.Fragment>
                        )

                    }
                })}


                </div>
                </div>
                </div>
                        </li>
                        </ul>
                      );


              })}
            </ul>

            <div className="flex justify-between w-10 sm:w-14 md:w-14 xl:w-20">
              {/* <a href={`tel: ${gphone.value != null ? gphone.value : ""}`} className="hidden sm:block md:block xl:block"> <img className="sm:w-6 w-6 h-6" src={phone} alt="err"></img> </a> */}
              <p onClick={popNumber}
              className="hidden sm:block md:block xl:block"> <img className="sm:w-6 w-6 h-6" src={phone} alt="err"></img> </p>
              <div className="backgroundcolorchanger">
              <div className="phonepopup">
                <div className="container">
                <img className="mx-auto" src={phone} alt="" />
                <p>{gphone.value != null ? gphone.value : ""}</p>
                <img className="mx-auto" src={logo} alt="" />
                </div>
              </div>
              </div>

              <div className="bold">
                <span>
                    {
                        Object.keys(locales).map((e, i) => {
                            if(e != currentLocale){
                                return (
                                    <div key={i}>
                                       <Link key={i} href={locale_urls[e]} className='text-navbar-normal'> {e} </Link>
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
