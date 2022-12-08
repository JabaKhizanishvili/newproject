function setSeoData(seoData) {
    console.log(seoData);
    Object.keys(seoData).map((key) => {
        let value = seoData[key]
        switch (key) {
            case 'title':
                document.title = value;
                break;
            case 'description':
                document.getElementsByTagName('meta')['description'].content = value
                break;
            case 'keywords':
                document.getElementsByTagName('meta')['keywords'].content = value
                break;
            case 'og_title':
                document.querySelector('meta[property="og:title"]').setAttribute("content", value);
                break;
            case 'og_description':
                document.querySelector('meta[property="og:description"]').setAttribute("content", value);
                break;
            case 'image':
                document.querySelector('meta[property="og:image"]').setAttribute("content", value);
                break;
            case 'locale':
                document.documentElement.setAttribute('lang', value)
                break;
        }
    });

    document.querySelector('meta[property="og:url"]').setAttribute("content", window.location.href);
}

export default setSeoData;
