<?php
// $floorMap = [
//   'it' => [
//     'palazzo-lombardia' => [
//       'gli-uffici-del-presidente' => 35,
//       'spazio-photo' => 1,
//       'sala-solesin' => 1,
//       'spazio-fiume' => 1,
//       'sala-marco-biagi' => 1,
//       'auditorium-testori' => 0
//     ],
//     'grattacielo-pirelli' => [
//       'sala-gio-ponti' => 0
//     ]
//   ]
// ];

$floorMap = [
    'palazzo-lombardia' => [
        '39' => [
            [
                'url' => 'belvedere',
                'label' => 'Il belvedere'
            ]
        ],
        '38' => [
            [
                'url' => 'terrazza',
                'label' => 'La terrazza'
            ],
            [
                'url' => 'vetrofanie',
                'label' => 'Vetrofanie'
            ]
        ],
        '35' => [
            [
                'url' => 'gli-uffici-del-presidente',
                'label' => 'Gli Uffici del Presidente'
            ]
        ],
        '13' => [
            [
                'url' => 'sala-opportunità',
                'label' => 'Sala opportunità'
            ]
        ],
        '11' => [
            [
                'url' => 'sala-falcone',
                'label' => 'Sala Falcone'
            ]
        ],
        '1' => [
            [
                'url' => 'sala-marco-biagi',
                'label' => 'Sala Marco Biagi'
            ],
            [
                'url' => 'spazio-fiume',
                'label' => 'Spazio Fiume'
            ],
            [
                'url' => 'sala-solesin',
                'label' => 'Sala Solesin'
            ],
            [
                'url' => 'spazio-photo',
                'label' => 'Spazio photo'
            ]

        ],
        '0' => [
            [
                'url' => 'piazza-città-di-lombardia',
                'label' => 'Piazza Città di Lombardia'
            ],
            [
                'url' => 'auditorium-testori',
                'label' => 'Auditorium Testori'
            ],
            [
                'url' => 'foyer-testori',
                'label' => 'Foyer Testori'
            ],
            [
                'url' => 'ingressi',
                'label' => 'Ingressi'
            ],
            [
                'url' => 'isola-set-spazio-esposizioni-temporanee',
                'label' => 'Set - Spazio esposizioni temporanee'
            ]
        ],
    ],
    'grattacielo-pirelli' => [
        'ROOFTOP' => [
            [
                'url' => 'falchi-pellegrini',
                'label' => 'I Falchi Pellegrini'
            ],
            [
                'url' => 'madonnina-pirellone',
                'label' => 'La Madonnina'
            ]
        ],
        '31' => [
            [
                'url' => 'belvedere-jannacci',
                'label' => 'Belvedere Jannacci'
            ]
        ],
        '26' => [
            [
                'url' => 'piano-della-memoria',
                'label' => 'Piano della memoria'
            ]
        ],
        '1' => [
            [
                'url' => 'spazio-eventi',
                'label' => 'Spazio eventi'
            ],
            [
                'url' => "foyer-piazza-duca-d'aosta",
                'label' => "Foyer Piazza Duca d'Aosta"
            ],
            [
                'url' => "sala-gonfalone",
                'label' => "Sala Gonfalone"
            ],
            [
                'url' => "sala-pirelli",
                'label' => "Sala Pirelli"
            ]
        ],
        '0' => [
            [
                'url' => 'sala-gio-ponti',
                'label' => 'Sala Gio Ponti'
            ],
            [
                'url' => 'foyer-gio-ponti',
                'label' => 'Foyer Gio Ponti'
            ]
        ],
        '-1' => [
            [
                'url' => 'auditorium-gaber',
                'label' => 'Auditorium Gaber'
            ],
            [
                'url' => 'aula-consiliare',
                'label' => 'Aula consiliare'
            ]
        ]

    ]
];


$address = explode('/', Yii::$app->request->getUrl());
$current = $floorMap[$address[1]][$address[2]][$address[3]];

$currentAction = Yii::$app->menu->current->getAlias();
$urlPalazzo = '';
$currentPiano = 0;
$showMenuSale = false;
foreach ($floorMap as $palazzo => $piani) {
    foreach ($piani as $n_piano => $sale) {
        foreach ($sale as $sala) {
            if ($currentAction == $palazzo . '/' . $sala['url']) {
                $currentPiano = $n_piano;
                $urlPalazzo = $palazzo;
                $showMenuSale = true;
            }
        }
    }
}
//pr('-----------------------------');
//pr($urlPalazzo, 'palazzo');
//pr($currentPiano, 'piano');
if ($showMenuSale) {
    ?>
    <div class="content-menu-sale">


        <div class="menu-sale">
            <span class="current-floor"><?= 'Piano ' . $currentPiano ?></span>
            <?php

            foreach ($floorMap[$urlPalazzo][$currentPiano] as $sala) {
                $classActive = '';
                if ($currentAction == $urlPalazzo . '/' . $sala['url']) {
                    $classActive = 'active ';
                }
                //    pr($sala,'sala');
                ?>
                <?= \yii\helpers\Html::a($sala['label'], '/' . $urlPalazzo . '/' . $sala['url'], [
                    'class' => $classActive . 'link-menu'
                ]); ?>
            <?php }


            //$rawMap = [
            //    '/it/palazzo-lombardia/gli-uffici-del-presidente' => 35,
            //    '/it/palazzo-lombardia/auditorium-testori' => 0
            //];
            //$rawCurrent = $rawMap[Yii::$app->request->getUrl()];

            ?>

            <a href="<?= '/' . $urlPalazzo . '/#floor-' . $currentPiano ?>" class="return-floor"><span
                        class="mdi mdi-chevron-left"></span>Esci</a>


        </div>
    </div>
<?php } ?>