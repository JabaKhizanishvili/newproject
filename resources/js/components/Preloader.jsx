import React from "react";
import { useState } from "react";
import { useEffect } from "react";

const Preloader = ({ loading }) => {
    const [counter, setCounter] = useState(0);

    useEffect(() => {
        setTimeout(() => {
            if (counter < 100) {
                setCounter(counter + 1);
            }
            if (counter === 100) {
                loading = false;
            }
        }, 40);
    }, [counter]);
    // console.log(counter);

    return (
        <div
            className={`fixed w-screen h-screen z-50 left-0 top-0 bg-custom-dark ${
                loading ? "block" : "hidden"
            }`}
        >
            <div className="h-full flex flex-col justify-between items-center p-5 text-center">
                <img className="" src="/assets/images/logo/1.png" alt="" />
                <div className="my-10">
                    <div className="loaderRing bold">{counter}%</div>
                    <div className="bold text-2xl text-custom-yellow mt-10">
                        Content is loading
                    </div>
                </div>
                <div className="opacity-20 bold">
                    <span>Construction works are done by</span>
                    <a href="#">
                        <img
                            className="mx-auto mt-4 mb-2"
                            src="/assets/images/logo/insite.png"
                            alt=""
                        />{" "}
                        <span>www.insite.ge</span>
                    </a>
                </div>
            </div>
        </div>
    );
};

export default Preloader;
