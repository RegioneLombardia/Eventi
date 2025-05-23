/*!
 * FileInput Slovakian Translations
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

    $.fn.fileinputLocales['sk'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'súbor',
        filePlural: 'súbory',
        browseLabel: 'Vybrať &hellip;',
        removeLabel: 'Odstrániť',
        removeTitle: 'Vyčistiť vybraté súbory',
        cancelLabel: 'Storno',
        cancelTitle: 'Prerušiť  nahrávanie',
        pauseLabel: 'Pozastaviť',
        pauseTitle: 'Pozastaviť prebiehajúce nahrávanie',
        uploadLabel: 'Nahrať',
        uploadTitle: 'Nahrať vybraté súbory',
        msgNo: 'Nie',
        msgNoFilesSelected: '',
        msgPaused: 'Pozastavené',
        msgCancelled: 'Zrušené',
        msgPlaceholder: 'Vybrať {files} ...',
        msgZoomModalHeading: 'Detailný náhľad',
        msgFileRequired: 'Musíte vybrať súbor, ktorý chcete nahrať.',
        msgSizeTooSmall: 'Súbor "{name}" (<b>{size}</b>) je príliš malý, musí mať veľkosť najmenej <b>{minSize}</b>.',
        msgSizeTooLarge: 'Súbor "{name}" (<b>{size}</b>) je príliš veľký, maximálna povolená veľkosť <b>{maxSize}</b>.',
        msgFilesTooLess: 'Musíte vybrať najmenej <b>{n}</b> {files} pre nahranie.',
        msgFilesTooMany: 'Počet vybratých súborov <b>({n})</b> prekročil maximálny povolený limit <b>{m}</b>.',
        msgTotalFilesTooMany: 'Môžete nahrať maximálne <b>{m}</b> súborov (zistených <b>{n}</b> súborov).',
        msgFileNotFound: 'Súbor "{name}" nebol nájdený!',
        msgFileSecured: 'Zabezpečenie súboru znemožnilo čítať súbor "{name}".',
        msgFileNotReadable: 'Súbor "{name}" nie je čitateľný.',
        msgFilePreviewAborted: 'Náhľad súboru bol prerušený pre "{name}".',
        msgFilePreviewError: 'Nastala chyba pri načítaní súboru "{name}".',
        msgInvalidFileName: 'Invalid or unsupported characters in file name "{name}".',
        msgInvalidFileType: 'Neplatný typ súboru "{name}". Iba "{types}" súborov sú podporované.',
        msgInvalidFileExtension: 'Neplatná extenzia súboru "{name}". Iba "{extensions}" súborov sú podporované.',
        msgFileTypes: {
            'image': 'obrázok',
            'html': 'HTML',
            'text': 'text',
            'video': 'video',
            'audio': 'audio',
            'flash': 'flash',
            'pdf': 'PDF',
            'object': 'object'
        },
        msgUploadAborted: 'Nahrávanie súboru bolo prerušené',
        msgUploadThreshold: 'Spracovávam &hellip;',
        msgUploadBegin: 'Inicializujem &hellip;',
        msgUploadEnd: 'Hotovo',
        msgUploadResume: 'Obnovuje sa nahrávanie &hellip;',
        msgUploadEmpty: 'Na nahrávanie nie sú k dispozícii žiadne platné údaje.',
        msgUploadError: 'Chyba pri nahrávaní',
        msgDeleteError: 'Chyba pri odstraňovaní',
        msgProgressError: 'Chyba',
        msgValidationError: 'Chyba overenia',
        msgLoading: 'Nahrávanie súboru {index} z {files} &hellip;',
        msgProgress: 'Nahrávanie súboru {index} z {files} - {name} - {percent}% dokončené.',
        msgSelected: '{n} {files} vybraté',
        msgProcessing: 'Spracovávam ...',
        msgFoldersNotAllowed: 'Tiahni a pusť iba súbory! Vynechané {n} pustené prečinok(y).',
        msgImageWidthSmall: 'Šírka obrázku "{name}", musí byť minimálne <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightSmall: 'Výška obrázku "{name}", musí byť minimálne <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageWidthLarge: 'Šírka obrázku "{name}" nemôže presiahnuť <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightLarge: 'Výška obrázku "{name}" nesmie presiahnuť <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageResizeError: 'Nepodarilo sa získať veľkosť obrázka pre zmenu veľkosti.',
        msgImageResizeException: 'Chyba pri zmene veľkosti obrázka.<pre>{errors}</pre>',
        msgAjaxError: 'Pri operácii {operation} sa vyskytla chyba. Skúste to prosím neskôr!',
        msgAjaxProgressError: '{operation} - neúspešné',
        msgDuplicateFile: 'Súbor "{name}" rovnakej veľkosti "{size}" už bol vybratý skôr. Preskočenie duplicitného výberu.',
        msgResumableUploadRetriesExceeded:  'Nahrávanie bolo prerušené po <b>{max}</b> opakovaniach súboru <b>{file}</b>! Detaily chyby: <pre>{error}</pre>',
        msgPendingTime: '{time} zostáva',
        msgCalculatingTime: 'výpočet zostávajúceho času',
        ajaxOperations: {
            deleteThumb: 'odstrániť súbor',
            uploadThumb: 'nahrať súbor',
            uploadBatch: 'nahrať várku súborov',
            uploadExtra: 'odosielanie údajov z formulára'
        },
        dropZoneTitle: 'Tiahni a pusť súbory tu &hellip;',
        dropZoneClickTitle: '<br>(alebo kliknite sem a vyberte {files})',
        fileActionSettings: {
            removeTitle: 'Odstrániť súbor',
            uploadTitle: 'Nahrať súbor',
            uploadRetryTitle: 'Znova nahrať',
            downloadTitle: 'Stiahnuť súbor',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'Zobraziť podrobnosti',
            dragTitle: 'Posunúť / Preskládať',
            indicatorNewTitle: 'Ešte nenahral',
            indicatorSuccessTitle: 'Nahraný',
            indicatorErrorTitle: 'Chyba pri nahrávaní',
            indicatorPausedTitle: 'Nahrávanie bolo pozastavené',
            indicatorLoadingTitle:  'Nahrávanie &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'Zobraziť predchádzajúci súbor',
            next: 'Zobraziť následujúci súbor',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Prepnúť záhlavie',
            fullscreen: 'Prepnúť zobrazenie na celú obrazovku',
            borderless: 'Prepnúť na bezrámikové zobrazenie',
            close: 'Zatvoriť detailný náhľad'
        }
    };
}));
