import React from "react";
import AnimatedCursor from "react-animated-cursor";

const CursorFollower = () => {
    return (
        <AnimatedCursor
            color="255,255,255"
            innerSize={10}
            outerSize={45}
            // innerScale={7}
            outerScale={2}
            hasBlendMode={true}
            innerStyle={{
                mixBlendMode: "difference",
            }}
            clickables={[
                "a",
                'input[type="text"]',
                'input[type="email"]',
                'input[type="number"]',
                'input[type="submit"]',
                'input[type="image"]',
                "label[for]",
                "select",
                "textarea",
                "button",
                ".link",
            ]}
        />
    );
};

export default CursorFollower;
