/*!
 * FileInput Polish Translations
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

    $.fn.fileinputLocales['pl'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'plik',
        filePlural: 'pliki',
        browseLabel: 'Przeglądaj &hellip;',
        removeLabel: 'Usuń',
        removeTitle: 'Usuń zaznaczone pliki',
        cancelLabel: 'Przerwij',
        cancelTitle: 'Anuluj wysyłanie',
        pauseLabel: 'Wstrzymaj',
        pauseTitle: 'Wstrzymaj trwające przesyłanie',
        uploadLabel: 'Wgraj',
        uploadTitle: 'Wgraj zaznaczone pliki',
        msgNo: 'Nie',
        msgNoFilesSelected: 'Brak zaznaczonych plików',
        msgPaused: 'Wstrzymano',
        msgCancelled: 'Odwołany',
        msgPlaceholder: 'Wybierz {files} ...',
        msgZoomModalHeading: 'Szczegółowy podgląd',
        msgFileRequired: 'Musisz wybrać plik do wgrania.',
        msgSizeTooSmall: 'Plik "{name}" (<b>{size}</b>) jest zbyt mały i musi być większy niż <b>{minSize}</b>.',
        msgSizeTooLarge: 'Plik o nazwie "{name}" (<b>{size}</b>) przekroczył maksymalną dopuszczalną wielkość pliku wynoszącą <b>{maxSize}</b>.',
        msgFilesTooLess: 'Minimalna liczba plików do wgrania: <b>{n}</b>.',
        msgFilesTooMany: 'Liczba plików wybranych do wgrania w liczbie <b>({n})</b>, przekracza maksymalny dozwolony limit wynoszący <b>{m}</b>.',
        msgTotalFilesTooMany: 'Możesz wgrać maksymalnie <b>{m}</b> plików (wykryto <b>{n}</b>).',
        msgFileNotFound: 'Plik "{name}" nie istnieje!',
        msgFileSecured: 'Ustawienia zabezpieczeń uniemożliwiają odczyt pliku "{name}".',
        msgFileNotReadable: 'Plik "{name}" nie jest plikiem do odczytu.',
        msgFilePreviewAborted: 'Podgląd pliku "{name}" został przerwany.',
        msgFilePreviewError: 'Wystąpił błąd w czasie odczytu pliku "{name}".',
        msgInvalidFileName: 'Nieprawidłowe lub nieobsługiwane znaki w nazwie pliku "{name}".',
        msgInvalidFileType: 'Nieznany typ pliku "{name}". Tylko następujące rodzaje plików są dozwolone: "{types}".',
        msgInvalidFileExtension: 'Złe rozszerzenie dla pliku "{name}". Tylko następujące rozszerzenia plików są dozwolone: "{extensions}".',
        msgUploadAborted: 'Przesyłanie pliku zostało przerwane',
        msgUploadThreshold: 'Przetwarzanie &hellip;',
        msgUploadBegin: 'Rozpoczynanie &hellip;',
        msgUploadEnd: 'Gotowe!',
        msgUploadResume: 'Wznawianie przesyłania &hellip;',
        msgUploadEmpty: 'Brak poprawnych danych do przesłania.',
        msgUploadError: 'Błąd przesyłania',
        msgDeleteError: 'Błąd usuwania',
        msgProgressError: 'Błąd',
        msgValidationError: 'Błąd walidacji',
        msgLoading: 'Wczytywanie pliku {index} z {files} &hellip;',
        msgProgress: 'Wczytywanie pliku {index} z {files} - {name} - {percent}% zakończone.',
        msgSelected: '{n} Plików zaznaczonych',
        msgProcessing: 'Processing ...',
        msgFoldersNotAllowed: 'Metodą przeciągnij i upuść, można przenosić tylko pliki. Pominięto {n} katalogów.',
        msgImageWidthSmall: 'Szerokość pliku obrazu "{name}" musi być co najmniej <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightSmall: 'Wysokość pliku obrazu "{name}" musi być co najmniej <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageWidthLarge: 'Szerokość pliku obrazu "{name}" nie może przekraczać <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightLarge: 'Wysokość pliku obrazu "{name}" nie może przekraczać <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageResizeError: 'Nie udało się uzyskać wymiaru obrazu, aby zmienić rozmiar.',
        msgImageResizeException: 'Błąd podczas zmiany rozmiaru obrazu.<pre>{errors}</pre>',
        msgAjaxError: 'Coś poczło nie tak podczas {operation}. Spróbuj ponownie!',
        msgAjaxProgressError: '{operation} nie powiodło się',
        msgDuplicateFile: 'Plik "{name}" o identycznym rozmiarze "{size}" został wgrany wcześniej. Pomijanie zduplikowanego pliku.',
        msgResumableUploadRetriesExceeded:  'Przekroczono limit <b>{max}</b> prób wgrania pliku <b>{file}</b>! Szczegóły błędu: <pre>{error}</pre>',
        msgPendingTime: 'Pozostało {time}',
        msgCalculatingTime: 'obliczanie pozostałego czasu',
        ajaxOperations: {
            deleteThumb: 'usuwanie pliku',
            uploadThumb: 'przesyłanie pliku',
            uploadBatch: 'masowe przesyłanie plików',
            uploadExtra: 'przesyłanie danych formularza'
        },
        dropZoneTitle: 'Przeciągnij i upuść pliki tutaj &hellip;',
        dropZoneClickTitle: '<br>(lub kliknij tutaj i wybierz {files} z komputera)',
        fileActionSettings: {
            removeTitle: 'Usuń plik',
            uploadTitle: 'Przesyłanie pliku',
            uploadRetryTitle: 'Ponów',
            downloadTitle: 'Pobierz plik',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'Pokaż szczegóły',
            dragTitle: 'Przenies / Ponownie zaaranżuj',
            indicatorNewTitle: 'Jeszcze nie przesłany',
            indicatorSuccessTitle: 'Dodane',
            indicatorErrorTitle: 'Błąd',
            indicatorPausedTitle: 'Przesyłanie zatrzymane',
            indicatorLoadingTitle:  'Przesyłanie &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'Pokaż poprzedni plik',
            next: 'Pokaż następny plik',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Włącz / wyłącz nagłówek',
            fullscreen: 'Włącz / wyłącz pełny ekran',
            borderless: 'Włącz / wyłącz tryb bez ramek',
            close: 'Zamknij szczegółowy widok'
        }
    };
}));
