/*!
 * FileInput Kazakh Translations
 *
 * This file must be loaded after 'fileinput.js'. Patterns in braces '{}', or
 * any HTML markup tags in the messages must not be converted or translated.
 *
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && typeof module.exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    "use strict";

    $.fn.fileinputLocales['kz'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'файл',
        filePlural: 'файлдар',
        browseLabel: 'Таңдау &hellip;',
        removeLabel: 'Жою',
        removeTitle: 'Таңдалған файлдарды жою',
        cancelLabel: 'Күшін жою',
        cancelTitle: 'Ағымдағы жүктеуді болдырмау',
        pauseLabel: 'Pause',
        pauseTitle: 'Pause ongoing upload',
        uploadLabel: 'Жүктеу',
        uploadTitle: 'Таңдалған файлдарды жүктеу',
        msgNo: 'жоқ',
        msgNoFilesSelected: 'Файл таңдалмады',
        msgPaused: 'Paused',
        msgCancelled: 'Күші жойылған',
        msgPlaceholder: 'Select {files} ...',
        msgZoomModalHeading: 'Алдын ала толық көру',
        msgSizeTooLarge: 'Файл "{name}" (<b>{size}</b>) ең үлкен <b>{maxSize}</b> өлшемінен асады.',
        msgFilesTooLess: 'Жүктеу үшіy кемінде <b>{n}</b> {files} таңдау керек.',
        msgFilesTooMany: 'Таңдалған <b>({n})</b> файлдардың саны берілген <b>{m}</b> саннан асып кетті.',
        msgTotalFilesTooMany: 'You can upload a maximum of <b>{m}</b> files (<b>{n}</b> files detected).',
        msgFileNotFound: 'Файл "{name}" табылмады!',
        msgFileSecured: 'Шектеу қауіпсіздігі "{name}" файлын оқуға тыйым салады.',
        msgFileNotReadable: '"{name}" файлды оқу мүмкін емес.',
        msgFilePreviewAborted: '"{name}" файл үшін алдын ала қарап көру тыйым салынған.',
        msgFilePreviewError: '"{name}" файлды оқығанда қате пайда болды.',
        msgInvalidFileType: '"{name}" тыйым салынған файл түрі. Тек мынаналарға рұқсат етілген: "{types}"',
        msgInvalidFileExtension: '"{name}" тыйым салынған файл кеңейтімі. Тек "{extensions}" рұқсат.',
        msgUploadAborted: 'Файлды жүктеу доғарылды',
        msgUploadThreshold: 'Өңдеу &hellip;',
        msgUploadBegin: 'Initializing &hellip;',
        msgUploadEnd: 'Done',
        msgUploadResume: 'Resuming upload &hellip;',
        msgUploadEmpty: 'No valid data available for upload.',
        msgUploadError: 'Upload Error',
        msgDeleteError: 'Delete Error',
        msgProgressError: 'Error',
        msgValidationError: 'Тексеру қатесі',
        msgLoading: '{index} файлды {files} &hellip; жүктеу',
        msgProgress: '{index} файлды {files} - {name} - {percent}% жүктеу аяқталды.',
        msgSelected: 'Таңдалған файлдар саны: {n}',
        msgFoldersNotAllowed: 'Тек файлдарды сүйреу рұқсат! {n} папка өткізілген.',
        msgImageWidthSmall: '{name} суреттің ені <b>{size} px</b> (detected <b>{dimension} px</b>). аз болмау керек',
        msgImageHeightSmall: '{name} суреттің биіктігі <b>{size} px</b> (detected <b>{dimension} px</b>). аз болмау керек',
        msgImageWidthLarge: '"{name}" суреттің ені <b>{size} px</b> (detected <b>{dimension} px</b>). аспау керек',
        msgImageHeightLarge: '"{name}" суреттің биіктігі <b>{size} px</b> (detected <b>{dimension} px</b>). аспау керек',
        msgImageResizeError: 'Суреттің өлшемін өзгерту үшін, мөлшері алынбады',
        msgImageResizeException: 'Суреттің мөлшерлерін өзгерткен кезде қателік пайда болды.<pre>{errors}</pre>',
        msgAjaxError: 'Something went wrong with the {operation} operation. Please try again later!',
        msgAjaxProgressError: '{operation} failed',
        msgDuplicateFile: 'File "{name}" of same size "{size}" has already been selected earlier. Skipping duplicate selection.',
        msgResumableUploadRetriesExceeded:  'Upload aborted beyond <b>{max}</b> retries for file <b>{file}</b>! Error Details: <pre>{error}</pre>',
        msgPendingTime: '{time} remaining',
        msgCalculatingTime: 'calculating time remaining',
        ajaxOperations: {
            deleteThumb: 'file delete',
            uploadThumb: 'file upload',
            uploadBatch: 'batch file upload',
            uploadExtra: 'form data upload'
        },
        dropZoneTitle: 'Файлдарды осында сүйреу &hellip;',
        dropZoneClickTitle: '<br>(or click to select {files})',
        fileActionSettings: {
            removeTitle: 'Файлды өшіру',
            uploadTitle: 'Файлды жүктеу',
            uploadRetryTitle: 'Retry upload',
            downloadTitle: 'Download file',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'мәліметтерді көру',
            dragTitle: 'Орнын ауыстыру',
            indicatorNewTitle: 'Жүктелген жоқ',
            indicatorSuccessTitle: 'Жүктелген',
            indicatorErrorTitle: 'Жүктелу қатесі ',
            indicatorPausedTitle: 'Upload Paused',
            indicatorLoadingTitle:  'Жүктелу &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'Алдыңғы файлды қарау',
            next: 'Келесі файлды қарау',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Тақырыпты ауыстыру',
            fullscreen: 'Толық экран режимін қосу',
            borderless: 'Жиексіз режиміне ауысу',
            close: 'Толық көрінісін жабу'
        }
    };
}));
