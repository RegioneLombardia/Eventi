/*!
 * FileInput Bulgarian Translations
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

    $.fn.fileinputLocales['bg'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'файл',
        filePlural: 'файла',
        browseLabel: 'Избери &hellip;',
        removeLabel: 'Премахни',
        removeTitle: 'Изчисти избраните',
        cancelLabel: 'Откажи',
        cancelTitle: 'Откажи качването',
        pauseLabel: 'Pause',
        pauseTitle: 'Pause ongoing upload',
        uploadLabel: 'Качи',
        uploadTitle: 'Качи избраните файлове',
        msgNo: 'Не',
        msgNoFilesSelected: '',
        msgPaused: 'Paused',
        msgCancelled: 'Отменен',
        msgPlaceholder: 'Select {files} ...',
        msgZoomModalHeading: 'Детайлен преглед',
        msgFileRequired: 'You must select a file to upload.',
        msgSizeTooSmall: 'File "{name}" (<b>{size}</b>) is too small and must be larger than <b>{minSize}</b>.',
        msgSizeTooLarge: 'Файла "{name}" (<b>{size}</b>) надвишава максималните разрешени <b>{maxSize}</b>.',
        msgFilesTooLess: 'Трябва да изберете поне <b>{n}</b> {files} файла.',
        msgFilesTooMany: 'Броя файлове избрани за качване <b>({n})</b> надвишава ограниченито от максимум <b>{m}</b>.',
        msgTotalFilesTooMany: 'You can upload a maximum of <b>{m}</b> files (<b>{n}</b> files detected).',
        msgFileNotFound: 'Файлът "{name}" не може да бъде намерен!',
        msgFileSecured: 'От съображения за сигурност не може да прочетем файла "{name}".',
        msgFileNotReadable: 'Файлът "{name}" не е четим.',
        msgFilePreviewAborted: 'Прегледа на файла е прекратен за "{name}".',
        msgFilePreviewError: 'Грешка при опит за четене на файла "{name}".',
        msgInvalidFileName: 'Invalid or unsupported characters in file name "{name}".',
        msgInvalidFileType: 'Невалиден тип на файла "{name}". Разрешени са само "{types}".',
        msgInvalidFileExtension: 'Невалидно разрешение на "{name}". Разрешени са само "{extensions}".',
        msgFileTypes: {
            'image': 'image',
            'html': 'HTML',
            'text': 'text',
            'video': 'video',
            'audio': 'audio',
            'flash': 'flash',
            'pdf': 'PDF',
            'object': 'object'
        },
        msgUploadAborted: 'Качите файла, бе прекратена',
        msgUploadThreshold: 'Processing &hellip;',
        msgUploadBegin: 'Initializing &hellip;',
        msgUploadEnd: 'Done',
        msgUploadResume: 'Resuming upload &hellip;',
        msgUploadEmpty: 'No valid data available for upload.',
        msgUploadError: 'Upload Error',
        msgDeleteError: 'Delete Error',
        msgProgressError: 'Error',
        msgValidationError: 'утвърждаване грешка',
        msgLoading: 'Зареждане на файл {index} от общо {files} &hellip;',
        msgProgress: 'Зареждане на файл {index} от общо {files} - {name} - {percent}% завършени.',
        msgSelected: '{n} {files} избрани',
        msgFoldersNotAllowed: 'Само пуснати файлове! Пропуснати {n} пуснати папки.',
        msgImageWidthSmall: 'Широчината на изображението "{name}" трябва да е поне <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightSmall: 'Височината на изображението "{name}" трябва да е поне <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageWidthLarge: 'Широчината на изображението "{name}" не може да е по-голяма от <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightLarge: 'Височината на изображението "{name}" нее може да е по-голяма от <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageResizeError: 'Не може да размерите на изображението, за да промените размера.',
        msgImageResizeException: 'Грешка при промяна на размера на изображението.<pre>{errors}</pre>',
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
        dropZoneTitle: 'Пуснете файловете тук &hellip;',
        dropZoneClickTitle: '<br>(or click to select {files})',
        fileActionSettings: {
            removeTitle: 'Махни файл',
            uploadTitle: 'Качване на файл',
            uploadRetryTitle: 'Retry upload',
            downloadTitle: 'Download file',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'Вижте детайли',
            dragTitle: 'Move / Rearrange',
            indicatorNewTitle: 'Все още не е качил',
            indicatorSuccessTitle: 'Качено',
            indicatorErrorTitle: 'Качи Error',
            indicatorPausedTitle: 'Upload Paused',
            indicatorLoadingTitle:  'Качва се &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'View previous file',
            next: 'View next file',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Toggle header',
            fullscreen: 'Toggle full screen',
            borderless: 'Toggle borderless mode',
            close: 'Close detailed preview'
        }
    };
}));
