import React from "react";
import { HiArrowNarrowRight } from "react-icons/hi";
import { Link, usePage } from "@inertiajs/inertia-react";

const Form = () => {
    const renderHTML = (rawHTML) =>
    React.createElement("div", {
        dangerouslySetInnerHTML: { __html: rawHTML },
    });
const sharedData = usePage().props.localizations;
  return (
    <div className="text-center form ">
      <div className="xl:text-7xl sm:text-5xl text-3xl bold mb-7 text-custom-yellow title">
        {/* Get in touch */}
        {__("client.contact_getintouch", sharedData)}
      </div>
      <p className="opacity-30 mb-5 ">
        {/* If you have any questions feel free to ask anytime */}
        {__("client.contact_anyquestion", sharedData)}
      </p>
      <form className="max-w-sm mx-auto">
        <input type="text" placeholder={__("client.contact_form_name", sharedData)} />
        <input type="text" placeholder={__("client.contact_form_surname", sharedData)} />
        <input type="text" placeholder={__("client.contact_form_phone", sharedData)} />
        <input type="text" placeholder={__("client.contact_form_address", sharedData)} />
        <textarea placeholder={__("client.contact_form_messega", sharedData)} />
        <button
          className={`flex items-center justify-center border border-solid border-white bold text-white sm:h-10 h-10 w-fit sm:px-6 px-4 rounded-full transition-all duration-300  sm:text-base text-sm  whitespace-nowrap mx-auto`}
        >
            <span>
               {/* Send message */}
               {__("client.contact_form_sendbtn", sharedData)}
            </span>
          <HiArrowNarrowRight className="w-6 h-6 m-2" />
        </button>
      </form>
    </div>
  );
};

export default Form;
