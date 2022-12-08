// import {usePage} from "@inertiajs/inertia-react";
//
// const sharedData = usePage().props.localizations;

export function __(key, replace = {}, sharedData=[]) {
    let translation = sharedData[key] || key;

    Object.keys(replace).forEach(function (key) {
        translation = translation.replace(':' + key, replace[key])
    });

    return translation;
}
