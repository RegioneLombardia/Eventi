/*!
 * FileInput German Translations
 *
 * This file must be loaded after 'fileinput.js'. Patterns in braces '{}', or
 * any HTML markup tags in the messages must not be converted or translated.
 *
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

    $.fn.fileinputLocales['de'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'Datei',
        filePlural: 'Dateien',
        browseLabel: 'Auswählen &hellip;',
        removeLabel: 'Löschen',
        removeTitle: 'Ausgewählte löschen',
        cancelLabel: 'Abbrechen',
        cancelTitle: 'Hochladen abbrechen',
        pauseLabel: 'Pause',
        pauseTitle: 'Pause ongoing upload',
        uploadLabel: 'Hochladen',
        uploadTitle: 'Hochladen der ausgewählten Dateien',
        msgNo: 'Keine',
        msgNoFilesSelected: 'Keine Dateien ausgewählt',
        msgPaused: 'Paused',
        msgCancelled: 'Abgebrochen',
        msgPlaceholder: '{files} auswählen ...',
        msgZoomModalHeading: 'ausführliche Vorschau',
        msgFileRequired: 'Sie müssen eine Datei zum Hochladen auswählen.',
        msgSizeTooSmall: 'Datei "{name}" (<b>{size}</b>) unterschreitet mindestens notwendige Upload-Größe von <b>{minSize}</b>.',
        msgSizeTooLarge: 'Datei "{name}" (<b>{size}</b>) überschreitet maximal zulässige Upload-Größe von <b>{maxSize}</b>.',
        msgFilesTooLess: 'Sie müssen mindestens <b>{n}</b> {files} zum Hochladen auswählen.',
        msgFilesTooMany: 'Anzahl der zum Hochladen ausgewählten Dateien <b>({n})</b>, überschreitet maximal zulässige Grenze von <b>{m}</b> Stück.',
        msgTotalFilesTooMany: 'You can upload a maximum of <b>{m}</b> files (<b>{n}</b> files detected).',
        msgFileNotFound: 'Datei "{name}" wurde nicht gefunden!',
        msgFileSecured: 'Sicherheitseinstellungen verhindern das Lesen der Datei "{name}".',
        msgFileNotReadable: 'Die Datei "{name}" ist nicht lesbar.',
        msgFilePreviewAborted: 'Dateivorschau abgebrochen für "{name}".',
        msgFilePreviewError: 'Beim Lesen der Datei "{name}" ein Fehler aufgetreten.',
        msgInvalidFileName: 'Ungültige oder nicht unterstützte Zeichen im Dateinamen "{name}".',
        msgInvalidFileType: 'Ungültiger Typ für Datei "{name}". Nur Dateien der Typen "{types}" werden unterstützt.',
        msgInvalidFileExtension: 'Ungültige Erweiterung für Datei "{name}". Nur Dateien mit der Endung "{extensions}" werden unterstützt.',
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
        msgUploadAborted: 'Der Datei-Upload wurde abgebrochen',
        msgUploadThreshold: 'Wird bearbeitet &hellip;',
        msgUploadBegin: 'Wird initialisiert &hellip;',
        msgUploadEnd: 'Erledigt',
        msgUploadResume: 'Resuming upload &hellip;',
        msgUploadEmpty: 'Keine gültigen Daten zum Hochladen verfügbar.',
        msgUploadError: 'Upload Error',
        msgDeleteError: 'Delete Error',
        msgProgressError: 'Fehler',
        msgValidationError: 'Validierungsfehler',
        msgLoading: 'Lade Datei {index} von {files} hoch &hellip;',
        msgProgress: 'Datei {index} von {files} - {name} - zu {percent}% fertiggestellt.',
        msgSelected: '{n} {files} ausgewählt',
        msgProcessing: 'Processing ...',
        msgFoldersNotAllowed: 'Drag & Drop funktioniert nur bei Dateien! {n} Ordner übersprungen.',
        msgImageWidthSmall: 'Breite der Bilddatei "{name}" muss mindestens <b>{size} px</b> (detected <b>{dimension} px</b>) betragen.',
        msgImageHeightSmall: 'Höhe der Bilddatei "{name}" muss mindestens <b>{size} px</b> (detected <b>{dimension} px</b>) betragen.',
        msgImageWidthLarge: 'Breite der Bilddatei "{name}" nicht überschreiten <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightLarge: 'Höhe der Bilddatei "{name}" nicht überschreiten <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageResizeError: 'Konnte nicht die Bildabmessungen zu ändern.',
        msgImageResizeException: 'Fehler beim Ändern der Größe des Bildes.<pre>{errors}</pre>',
        msgAjaxError: 'Bei der Aktion {operation} ist ein Fehler aufgetreten. Bitte versuche es später noch einmal!',
        msgAjaxProgressError: '{operation} fehlgeschlagen',
        msgDuplicateFile: 'File "{name}" of same size "{size}" has already been selected earlier. Skipping duplicate selection.',
        msgResumableUploadRetriesExceeded:  'Upload aborted beyond <b>{max}</b> retries for file <b>{file}</b>! Error Details: <pre>{error}</pre>',
        msgPendingTime: '{time} remaining',
        msgCalculatingTime: 'calculating time remaining',
        ajaxOperations: {
            deleteThumb: 'Datei löschen',
            uploadThumb: 'Datei hochladen',
            uploadBatch: 'Batch-Datei-Upload',
            uploadExtra: 'Formular-Datei-Upload'
        },
        dropZoneTitle: 'Dateien hierher ziehen &hellip;',
        dropZoneClickTitle: '<br>(oder klicken um {files} auszuwählen)',
        fileActionSettings: {
            removeTitle: 'Datei entfernen',
            uploadTitle: 'Datei hochladen',
            uploadRetryTitle: 'Upload erneut versuchen',
            downloadTitle: 'Datei herunterladen',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'Details anzeigen',
            dragTitle: 'Verschieben / Neuordnen',
            indicatorNewTitle: 'Noch nicht hochgeladen',
            indicatorSuccessTitle: 'Hochgeladen',
            indicatorErrorTitle: 'Upload Fehler',
            indicatorPausedTitle: 'Upload Paused',
            indicatorLoadingTitle:  'Hochladen &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'Vorherige Datei anzeigen',
            next: 'Nächste Datei anzeigen',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Header umschalten',
            fullscreen: 'Vollbildmodus umschalten',
            borderless: 'Randlosen Modus umschalten',
            close: 'Detailansicht schließen'
        }
    };
}));
