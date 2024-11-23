<?php

use yii\grid\GridView;
use open20\amos\events\AmosEvents;
use \yii\bootstrap4\Modal;


$this->title = AmosEvents::t('amosevents', "Analisi dati - Errori mailup");
$this->params['breadcrumbs'][] = $this->title;

$valuesGlossario = [
    ['code' => 'GB', 'error' => 'General Bounce (GB) - Errore generico grave', 'type' => 'Errori temporanei', 'message' => 'Il messaggio del server contiene parti di testo che non sono interpretabili o sono equivoche. Si tratta quindi di un errore generico non categorizzabile ma definito come errore permanente e grave di consegna.'],
    ['code' => 'HB', 'error' => 'Hard Bounce (HB) - Casella non esistente', 'type' => 'Indirizzo errato', 'message' => 'Il contatto viene disiscritto in maniera permanente al primo bounce, senza possibilità di cambiare di stato. Avviene quando la local part di un indirizzo email è errata (es. amministrazioneeeeeee@nomeazienda.com).Per utilizzare il contatto corretto l’utente NON deve modificare quello esistente perchè resta disiscritto senza possibilità di reiscrizione'],
    ['code' => 'SB', 'error' => 'Soft Bounce - General (SB) - Errore generico non grave', 'type' => 'Errori temporanei', 'message' => 'Si tratta di un messaggio di errore generico dovuto al fatto che il sistema non è in grado di interpretare la risposta del server, poiché non standard o equivoca. Il cliente di posta non può interpretare i messaggi formattati MIME o DNS.'],
    ['code' => 'SBDF', 'error' => 'Soft Bounce - Dns Failure (SBDF) -  Problema risoluzione dominio', 'type' => 'Errore generico', 'message' => 'Problemi con il DNS. Il messaggio ha avuto errori permanenti di consegna. Si tratta di un errore dovuto al fatto che il dominio del destinatario (host) non è stato risolto in un equivalente IP valido. Questo può essere dovuto a problemi temporanei del server di destinazione sulla rete internet, oppure al fatto che il dominio di destinazione non esiste o è scritto in modo errato. Lo strumento riprova per 3 giorni consecutivi l\'invio del messaggio.'],
    ['code' => 'SBMF', 'error' => 'Soft Bounce - Mailbox Full (SBMF) - Casella piena', 'type' => 'Casella piena', 'message' => 'Casella di posta elettronica piena. Questo messaggio non è stato recapitato perché l’utente destinatario è temporaneamente fuori quota massima del proprio account di posta.'],
    ['code' => 'MB', 'error' => 'Mail Block - General (MB) - Blocco generico', 'type' => 'Email bloccate', 'message' => 'Il messaggio contiene parti di testo che non sono interpretabili, ma comunque riferibili ad un blocco da parte del server di destinazione. Il messaggio ha avuto errori permanenti di consegna. Il server di posta del destinatario ha rifiutato il messaggio per un motivo non precisato. Bisogna vedere nel dettaglio il codice dell’errore per eventuali informazioni aggiuntive.'],
    ['code' => 'MBKS', 'error' => 'Mail Block - Know Spammer (MBKS) - Blocco per spammer riconosciuto', 'type' => 'Email bloccate', 'message' => 'Il mittente è riconosciuto come spammer dal sistema di posta del ricevente. In questo caso è importante monitorare questi ritorni e, nel momento in cui si verificano, bisogna risolvere il problema con il destinatario. In caso negativo, è necessario spostare nei disiscritti gli indirizzi relativi.'],
    ['code' => 'MBSD', 'error' => 'Mail Block - Spam Detected (MBSD) - Blocco per spam', 'type' => 'Email bloccate', 'message' => 'Il messaggio è stato classificato come spam dal sistema di posta del ricevente. Sono ritorni meno gravi dei precedenti, ma vanno comunque monitorati perché non sono un segnale positivo, generalmente sono regole automatiche impostate per rifiutare le comunicazioni. Consigliamo di spostare in disiscritti gli indirizzi che hanno almeno 3 ritorni di questo tipo, oppure di contattare direttamente il destinatario per verificare con lui i motivi del blocco.'],
    ['code' => 'MBAD', 'error' => 'Mail Block - Attachment Detected (MBAD) - Blocco per allegato', 'type' => 'Email bloccate', 'message' => 'Il messaggio è stato rigettato dal sistema di posta del ricevente, a causa di un allegato non ammesso.'],
    ['code' => 'MBRD', 'error' => 'Mail Block - Relay Denied (MBRD) - Blocco accesso negato', 'type' => 'Email bloccate', 'message' => 'Il messaggio ha avuto errori permanenti di consegna ed è stato bloccato dal sistema di posta del ricevente poiché rifiuta la connessione da parte del server del mittente (in genere questo messaggio avviene per SMTP mal configurati o account che non possono ricevere la posta dall\'esterno).'],
    ['code' => 'TB', 'error' => 'Transient Bounce (TB) - Errore temporaneo', 'type' => 'Errori temporanei', 'message' => 'È un errore transitorio, il sistema di invio ha riscontrato un errore da parte del ricevente, ma ritenterà nelle ore successive. Nel caso l’errore dovesse persistere, il sistema ritornerà un errore Hard Bounce. Questi errori vanno ignorati.'],
];

$tmpDir = \Yii::getAlias('@backend').'/web';
$dateFile = $tmpDir . '/last_date_error_mailup_cron.txt';
if (file_exists($dateFile)) {
    $dateExecutionCron = file_get_contents($dateFile);
    $dateMax =  new \DateTime($dateExecutionCron);
}else {
    if(!empty($maxError)) {
        $dateMax = new \DateTime($maxError->created_at);
    }
}

if (!empty($dateMax)) {?>
    <div class="row">
        <div class="col-md-8">
            <p><?= AmosEvents::t('amosevents', "Dato aggiornato alla data {date}", ['date' => $dateMax->format('d/m/Y H:i')]) ?></p>
        </div>

        <div class="col-md-4">
            <?php echo \yii\helpers\Html::a('Glossario', "#", [
                    'class' => 'btn btn-secondary float-right mr-2',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalGlossario'
                ]
            );
            ?>
        </div>
    </div>
<?php } ?>
<?php
echo \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'striped' => true,
    'hover' => true,
    'panel' => [
        'type' => 'info',
        'heading' => false, // 'Suddivisione di genere'
    ],
    'toggleDataOptions' => [
        'all' => [
            'label' => 'Tutto'
        ],
    ],
    'exportConfig' => [
        \kartik\grid\GridView::HTML => [],
        \kartik\grid\GridView::CSV => [],
        \kartik\grid\GridView::TEXT => [],
        \kartik\grid\GridView::EXCEL => [],
        \kartik\grid\GridView::PDF => [],
    ],
    'columns' => [
        [
            'value' => function ($model) {
                $user = \open20\amos\core\user\User::find()->andWhere(['email' => $model->email])->one();
                if ($user) {
                    return $user->userProfile->nome;
                }
                return '-';
            },
            'label' => AmosEvents::t('amosevents', "Nome")
        ],
        [
            'value' => function ($model) {
                $user = \open20\amos\core\user\User::find()->andWhere(['email' => $model->email])->one();
                if ($user) {
                    return $user->userProfile->cognome;
                }
                return '-';
            },
            'label' => AmosEvents::t('amosevents', "Cognome")
        ],
        'email',

        [
            'attribute' => 'type',
            'label' => AmosEvents::t('amosevents', "Tipo di errore"),
        ],
//        [
//            'attribute' => 'created_at',
//            'label' => AmosEvents::t('amosevents', "Data"),
//            'value' => function($model){
//                 $date =  new \DateTime($model->created_at);
//                return $date->format('d/m/Y H:i');
//            }
//        ]
    ]
])
?>

<?php
Modal::begin([
    'title' => '<h2>' . Yii::t('amosevents', 'Glossario errori') . '</h2>',
    'size' => 'modal-lg',
    'id' => 'modalGlossario',
]);
?>
<?php
$dataproviderGlossario = new \yii\data\ArrayDataProvider([
    'allModels' => $valuesGlossario
]);
echo GridView::widget([
    'dataProvider' => $dataproviderGlossario,
    'columns' => [
        [
            'label' => AmosEvents::t('amosevents', "Codice"),
            'attribute' => 'code',
        ],
        [
            'label' => AmosEvents::t('amosevents', "Errore"),
            'attribute' => 'error',
        ],
        [
            'label' => AmosEvents::t('amosevents', "Tipo"),
            'attribute' => 'type',
        ],
        [
            'label' => AmosEvents::t('amosevents', "Descrizione"),
            'attribute' => 'message',
        ],

    ]
])

?>

<?php Modal::end(); ?>
