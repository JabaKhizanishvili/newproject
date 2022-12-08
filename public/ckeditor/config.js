/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    // config.font_names = "tbc-regular";

    config.font_defaultLabel = "tbc-regular";
    config.extraPlugins = 'youtube';
    config.allowedContent = true;
    config.removePlugins = 'iframe';
    config.enterMode = CKEDITOR.ENTER_BR;
};
