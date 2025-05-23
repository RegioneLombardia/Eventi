/*!
 * FileInput Slovenian Translations
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

    $.fn.fileinputLocales['sl'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'datoteka',
        filePlural: 'datotek',
        browseLabel: 'Prebrskaj &hellip;',
        removeLabel: 'Odstrani',
        removeTitle: 'Počisti izbrane datoteke',
        cancelLabel: 'Prekliči',
        cancelTitle: 'Prekliči nalaganje',
        pauseLabel: 'Pause',
        pauseTitle: 'Pause ongoing upload',
        uploadLabel: 'Naloži',
        uploadTitle: 'Naloži izbrane datoteke',
        msgNo: 'Ne',
        msgNoFilesSelected: 'Nobena datoteka ni izbrana',
        msgPaused: 'Paused',
        msgCancelled: 'Preklicano',
        msgPlaceholder: 'Select {files} ...',
        msgZoomModalHeading: 'Podroben predogled',
        msgSizeTooLarge: 'Datoteka "{name}" (<b>{size}</b>) presega največjo dovoljeno velikost za nalaganje <b>{maxSize}</b>.',
        msgFilesTooLess: 'Za nalaganje morate izbrati vsaj <b>{n}</b> {files}.',
        msgFilesTooMany: 'Število datotek, izbranih za nalaganje <b>({n})</b> je prekoračilo največjo dovoljeno število <b>{m}</b>.',
        msgTotalFilesTooMany: 'You can upload a maximum of <b>{m}</b> files (<b>{n}</b> files detected).',
        msgFileNotFound: 'Datoteka "{name}" ni bila najdena!',
        msgFileSecured: 'Zaradi varnostnih omejitev nisem mogel prebrati datoteko "{name}".',
        msgFileNotReadable: 'Datoteka "{name}" ni berljiva.',
        msgFilePreviewAborted: 'Predogled datoteke "{name}" preklican.',
        msgFilePreviewError: 'Pri branju datoteke "{name}" je prišlo do napake.',
        msgInvalidFileType: 'Napačen tip datoteke "{name}". Samo "{types}" datoteke so podprte.',
        msgInvalidFileExtension: 'Napačna končnica datoteke "{name}". Samo "{extensions}" datoteke so podprte.',
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
        msgUploadAborted: 'Nalaganje datoteke je bilo preklicano',
        msgUploadThreshold: 'Procesiram &hellip;',
        msgUploadBegin: 'Initializing &hellip;',
        msgUploadEnd: 'Done',
        msgUploadResume: 'Resuming upload &hellip;',
        msgUploadEmpty: 'No valid data available for upload.',
        msgUploadError: 'Upload Error',
        msgDeleteError: 'Delete Error',
        msgProgressError: 'Error',
        msgValidationError: 'Napaki pri validiranju',
        msgLoading: 'Nalaganje datoteke {index} od {files} &hellip;',
        msgProgress: 'Nalaganje datoteke {index} od {files} - {name} - {percent}% dokončano.',
        msgSelected: '{n} {files} izbrano',
        msgFoldersNotAllowed: 'Povlecite in spustite samo datoteke! Izpuščenih je bilo {n} map.',
        msgImageWidthSmall: 'Širina slike "{name}" mora biti vsaj <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightSmall: 'Višina slike "{name}" mora biti vsaj <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageWidthLarge: 'Širina slike "{name}" ne sme preseči <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightLarge: 'Višina slike "{name}" ne sme preseči <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageResizeError: 'Nisem mogel pridobiti dimenzij slike za spreminjanje velikosti.',
        msgImageResizeException: 'Napaka pri spreminjanju velikosti slike.<pre>{errors}</pre>',
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
        dropZoneTitle: 'Povlecite in spustite datoteke sem &hellip;',
        dropZoneClickTitle: '<br>(ali kliknite sem za izbiro {files})',
        fileActionSettings: {
            removeTitle: 'Odstrani datoteko',
            uploadTitle: 'Naloži datoteko',
            uploadRetryTitle: 'Retry upload',
            downloadTitle: 'Download file',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'Poglej podrobnosti',
            dragTitle: 'Premaki / Razporedi',
            indicatorNewTitle: 'Še ni naloženo',
            indicatorSuccessTitle: 'Naloženo',
            indicatorErrorTitle: 'Napaka pri nalaganju',
            indicatorPausedTitle: 'Upload Paused',
            indicatorLoadingTitle:  'Nalagam &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'Poglej prejšno datoteko',
            next: 'Poglej naslednjo datoteko',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Preklopi glavo',
            fullscreen: 'Preklopi celozaslonski način',
            borderless: 'Preklopi način brez robov',
            close: 'Zapri predogled podrobnosti'
        }
    };
}));
