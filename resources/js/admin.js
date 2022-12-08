import axios from "axios";

$(document).ready(function () {
    let oldImages = $('#old_images').val();
    if (oldImages) {
        oldImages = JSON.parse(oldImages);
    }
    let imagedata = [];
    let getUrl = window.location;
    let baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[0];
    if (oldImages && oldImages.length > 0) {
        oldImages.forEach((el, key) => {
            let directory = '';
            if (el.fileable_type === 'App\\Models\\Project') {
                directory = 'project';
            }
            imagedata.push({
                id: el.id,
                src: `${baseUrl}${el.path}/${el.title}`
            })
        })
        $('.input-images').imageUploader({
            preloaded: imagedata,
            imagesInputName: 'images',
            preloadedInputName: 'old_images'
        });
    } else {
        $('.input-images').imageUploader();
    }


    let productCategory = $('#product_category_select');
    let featureContainer = $('#features-container');
    const activeLanguage = $('meta[name="active_language"]').attr('content');
    const defaultLanguage = $('meta[name="default_language"]').attr('content');

    productCategory.on('change', function (event) {
        axios.get(`/api/category/${event.target.value}/feature`)
            .then((response) => {
                featureContainer.html('');
                const category = response.data?.category;
                let content = '';
                if (category.features) {
                    category.features.forEach((feature) => {
                        let featureLanguage = getLanguage(feature.languages);
                        if (feature.answers.length) {
                            content =
                                `
                                ${content}
                                <div class="col">
                                    <label for="feature[${feature.id}][]">
                                        ${featureLanguage.title.substring(0, 25)}
                                    </label>
                                </div>
                                    <div class="input-field col s12">
                                        <select class="select2-customize-result browser-default" multiple="multiple"
                                                id="select2-customize-result-${feature.id}" name="feature[${feature.id}][]">
                                            <optgroup>
                                                ${getOptions(feature.answers)}
                                            </optgroup>
                                        </select>
                                    </div>
                            `
                        }
                    })
                    featureContainer.html(content);
                    $('.select2-customize-result').select2();
                }
            })
    })

    function getLanguage(languages) {
        let data = languages.find(function (language, index) {
            if (language.language_id == activeLanguage && language.title !== null)
                return true;
        });
        if (data !== undefined) {
            return data;
        }

        data = languages.find(function (language, index) {
            if (language.language_id == defaultLanguage && language.title !== null)
                return true;
        });
        return data;
    }

    function getOptions(answers) {
        let options = '';

        answers.forEach((answer) => {
            let answerLanguage = getLanguage(answer.languages)
            options =
                `
                ${options}
                <option value="${answer.id}">
                                    ${answerLanguage.title.substring(0, 25)}
                </option>

                `
        })

        return options;
    }
});

