<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

return [

    'email-assistenza' => 'example@example.it Eventi Regione Lombardia',
    'icon-framework' => 'am',
    'googleMapsLibraries'=> null,
    'googleMapsLanguage' =>'en',

    // Enable Amos Exclusive features
    'template-amos' => FALSE,

    // Enable template slideshow
    'slideshow' => TRUE,
    'slideshow-label' => 'Mostra introduzione', // TODO translate and amos-XXX::[t()|tHtml()] ?

    // Enable Localization menu
    'languageSelector' => TRUE,
    'allLanguages' => ['Italiano' => 'it-IT', 'English' => 'en-GB'],

    //enable btn dashboard to frontend
    'toFrontendLink' => true,

    //diable wizard in create content models (News, Documents, Discussions)
    'noWizardNewLayout' => true,
    'dashboardEngine' => 'rows',
    'hideSettings' => true,
    'hideScopeinAction' => ['events/event/view', 'events/event/update'],
    'loginAllowedIPs' => [
    ],
    'showTranslationInlinePersonalized' => [
        'class' => 'open20\amos\events\utility\EventsUtility',
        'method' => 'isMultilanguageEnabled'
    ],
    'disableAfterFindPurify' => true,
];
