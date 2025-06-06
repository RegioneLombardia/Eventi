/*!
 * FileInput Galician Translations
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

    $.fn.fileinputLocales['gl'] = {
        sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], 
        bitRateUnits: ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s', 'EB/s', 'ZB/s', 'YB/s'],
        fileSingle: 'arquivo',
        filePlural: 'arquivos',
        browseLabel: 'Examinar &hellip;',
        removeLabel: 'Quitar',
        removeTitle: 'Quitar aquivos seleccionados',
        cancelLabel: 'Cancelar',
        cancelTitle: 'Abortar a subida en curso',
        pauseLabel: 'Pause',
        pauseTitle: 'Pause ongoing upload',
        uploadLabel: 'Subir arquivo',
        uploadTitle: 'Subir arquivos seleccionados',
        msgNo: 'Non',
        msgNoFilesSelected: 'Non hay arquivos seleccionados',
        msgPaused: 'Paused',
        msgCancelled: 'Cancelado',
        msgPlaceholder: 'Seleccinar {files} ...',
        msgZoomModalHeading: 'Vista previa detallada',
        msgFileRequired: 'Debes seleccionar un arquivo para subir.',
        msgSizeTooSmall: 'O arquivo "{name}" (<b>{size}</b>) é demasiado pequeno e debe ser maior de <b>{minSize}</b>.',
        msgSizeTooLarge: 'O arquivo "{name}" (<b>{size}</b>) excede o tamaño máximo permitido de <b>{maxSize}</b>.',
        msgFilesTooLess: 'Debe seleccionar ao menos <b>{n}</b> {files} a cargar.',
        msgFilesTooMany: 'O número de arquivos seleccionados a cargar <b>({n})</b> excede do límite máximo permitido de <b>{m}</b>.',
        msgTotalFilesTooMany: 'You can upload a maximum of <b>{m}</b> files (<b>{n}</b> files detected).',
        msgFileNotFound: 'Arquivo "{name}" non encontrado.',
        msgFileSecured: 'Non é posible acceder ao arquivo "{name}" porque estará sendo usado por outra aplicación ou non teñamos permisos de lectura.',
        msgFileNotReadable: 'Non é posible acceder ao arquivo "{name}".',
        msgFilePreviewAborted: 'Previsualización do arquivo "{name}" cancelada.',
        msgFilePreviewError: 'Ocurriu un erro mentras se lía o arquivo "{name}".',
        msgInvalidFileName: 'Caracteres non válidos ou non soportados no nome do arquivo "{name}".',
        msgInvalidFileType: 'Tipo de arquivo non válido para "{name}". Só se permiten arquivos do tipo "{types}".',
        msgInvalidFileExtension: 'Extensión de arquivo non válida para "{name}". Só se permiten arquivos "{extensions}".',
        msgFileTypes: {
            'image': 'imaxe',
            'html': 'HTML',
            'text': 'text',
            'video': 'video',
            'audio': 'audio',
            'flash': 'flash',
            'pdf': 'PDF',
            'object': 'object'
        },
        msgUploadAborted: 'A carga de arquivos cancelouse',
        msgUploadThreshold: 'Procesando &hellip;',
        msgUploadBegin: 'Inicializando &hellip;',
        msgUploadEnd: 'Feito',
        msgUploadResume: 'Resuming upload &hellip;',
        msgUploadEmpty: 'Non existen datos válidos para o envío.',
        msgUploadError: 'Upload Error',
        msgDeleteError: 'Delete Error',
        msgProgressError: 'Erro',
        msgValidationError: 'Erro de validación',
        msgLoading: 'Subindo arquivo {index} de {files} &hellip;',
        msgProgress: 'Subindo arquivo {index} de {files} - {name} - {percent}% completado.',
        msgSelected: '{n} {files} seleccionado(s)',
        msgProcessing: 'Processing ...',
        msgFoldersNotAllowed: 'Arrastra e solta unicamente arquivos. Omitida(s) {n} carpeta(s).',
        msgImageWidthSmall: 'O ancho da imaxe "{name}" debe ser de ao menos <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightSmall: 'A altura da imaxe "{name}" debe ser de ao menos <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageWidthLarge: 'O ancho da imaxe "{name}" non pode exceder de <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageHeightLarge: 'A altura da imaxe "{name}" non pode exceder de <b>{size} px</b> (detected <b>{dimension} px</b>).',
        msgImageResizeError: 'Non se puideron obter as dimensións da imaxe para cambiar o tamaño.',
        msgImageResizeException: 'Erro ao cambiar o tamaño da imaxe. <pre>{errors}</pre>',
        msgAjaxError: 'Algo foi mal ca operación {operation}. Por favor, inténtao de novo máis tarde.',
        msgAjaxProgressError: 'A operación {operation} fallou',
        msgDuplicateFile: 'File "{name}" of same size "{size}" has already been selected earlier. Skipping duplicate selection.',
        msgResumableUploadRetriesExceeded:  'Upload aborted beyond <b>{max}</b> retries for file <b>{file}</b>! Error Details: <pre>{error}</pre>',
        msgPendingTime: '{time} remaining',
        msgCalculatingTime: 'calculating time remaining',
        ajaxOperations: {
            deleteThumb: 'Arquivo borrado',
            uploadThumb: 'Arquivo subido',
            uploadBatch: 'Datos subidos en lote',
            uploadExtra: 'Datos do formulario subidos'
        },
        dropZoneTitle: 'Arrasta e solta aquí os arquivos &hellip;',
        dropZoneClickTitle: '<br>(ou fai clic para seleccionar {files})',
        fileActionSettings: {
            removeTitle: 'Eliminar arquivo',
            uploadTitle: 'Subir arquivo',
            uploadRetryTitle: 'Reintentar a subida',
            downloadTitle: 'Descargar arquivo',
            rotateTitle: 'Rotate 90 deg. clockwise',
            zoomTitle: 'Ver detalles',
            dragTitle: 'Mover / Reordenar',
            indicatorNewTitle: 'Non subido aínda',
            indicatorSuccessTitle: 'Subido',
            indicatorErrorTitle: 'Erro ao subir',
            indicatorPausedTitle: 'Upload Paused',
            indicatorLoadingTitle:  'Subindo &hellip;'
        },
        previewZoomButtonTitles: {
            prev: 'Ver arquivo anterior',
            next: 'Ver arquivo seguinte',
            rotate: 'Rotate 90 deg. clockwise',
            toggleheader: 'Mostrar encabezado',
            fullscreen: 'Mostrar a pantalla completa',
            borderless: 'Activar o modo sen bordes',
            close: 'Cerrar vista detallada'
        }
    };
}));
