<?php
use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use yii\bootstrap4\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use open20\webmeeting\WebMeetingModule;


$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

$event_id = $model->id;
$max_participant = $model->seats_available ? $model->seats_available : 0;

$js = <<<JS

var selected_users = [];
var deleted_users  = [];


//CERCA PER NOME
$(document).on('keyup', '#search-name-id',function(){
    var selected = selected_users;
    var deleted = deleted_users;
    var name = $(this).val();
    if(/*name.length > 3*/ true){
          $.ajax({
                url: '/events/wizard/search-users-webmeeting',
                type: 'GET',
                data: { name: $(this).val() ,event_id: $event_id, selected_users: selected, deleted_users: deleted},
                success: function(data) {
                    $('#container-result-search').html(data);
                 }
        });
    }
});

// CERCA PER LETTERA
$(document).on('click','.char-search', function(e){
    e.preventDefault();
    var selected = selected_users;
    var deleted = deleted_users;

    $('.char-search').each(function(){
            $(this).removeClass('active');
    });
    
    $(this).addClass('active');
        $.ajax({
            url: '/events/wizard/search-users-webmeeting',
            type: 'GET',
            data: { char: $(this).attr('data-key') , event_id: $event_id, selected_users: selected, deleted_users: deleted},
            success: function(data) {
                $('#container-result-search').html(data);
             }
        });
});

// PAGINAZIONE GRID
$(document).on('click', '#container-result-search .pagination li a', function(e){
    e.preventDefault();
    var selected = selected_users;
    var deleted = deleted_users;
    
    var name = $('#search-name-id').val();
    var char = $('#toolbar-search-char .active').attr('data-key');
      $.ajax({
            url: '/events/wizard/search-users-webmeeting',
            type: 'GET',
            data: { 
                name: name , 
                char: char , 
                event_id: $event_id, 
                selected_users: selected, 
                deleted_users: deleted,
                page: parseInt($(this).attr('data-page')) + 1
                },
            success: function(data) {
                $('#container-result-search').html(data);
             }
        });
});

//INSERISCI UTENTI
$(document).on('click','.checkbox-user', function(){
    //select users
    if(this.checked === true){
        var counter = parseInt($('.counter-selected').text());
        // se è tra i cancellati lo rimuovo dalla lista cancellati così riappare l'utente
        //altrimenti lo aggiungo alla lista degli utenti inseriti
        if(counter >= $max_participant){
            $(this).removeAttr('checked');
            $('#modal-alert-max-participanti').modal('show');
            return false;
        }
        //limite per api
        if(counter > 100){
            $(this).removeAttr('checked');
            $('#modal-alert-max-total').modal('show');
            return false;
        }
        
         const index = deleted_users.indexOf($(this).val());
        if (index > -1) {
            deleted_users.splice(index, 1);
        }else{
            selected_users.push($(this).val());
        }
        //incrementa counter
        $('.counter-selected').text(counter + 1);
        $('.counter-selected-hide').text(counter + 1);
    //delete user
    }else{
        const index = selected_users.indexOf($(this).val());
        if (index > -1) {
            selected_users.splice(index, 1);
        } else {
            deleted_users.push($(this).val());
        }
        //decrementa counter
        var counter = parseInt($('.counter-selected').text());
        $('.counter-selected').text(counter - 1);
        $('.counter-selected-hide').text(counter - 1);
    }
    
    //setta campo di input per salvataggio
    $('#selected-users-ids').val(selected_users.join());
    $('#deleted-users-ids').val(deleted_users.join());

    
    console.log(selected_users);
    console.log(deleted_users);
});

// ELIMINA UTENTI
$(document).on('click','.delete-users', function(e){
    e.preventDefault();
    var user_id = $(this).attr('data-key');
    const index = selected_users.indexOf(user_id);
    if (index > -1) {
        selected_users.splice(index, 1);
    }else {
        deleted_users.push(user_id);
    }
    
    //setta campo di input per salvataggio
    $('#selected-users-ids').val(selected_users.join());
    $('#deleted-users-ids').val(deleted_users.join());
    $(this).parents('.item-participant').parent().remove();
    
    //deseleziona checkbox
    $('.checkbox-user[value="'+user_id+'"]').removeAttr('checked');
    
    //decrementa counter
    var counter = parseInt($('.counter-selected').text());
    $('.counter-selected').text(counter - 1);
    $('.counter-selected-hide').text(counter - 1);
    
    console.log(selected_users);
    console.log(deleted_users);
});

//MOSTRA TUTTI GLI UTENTI SELEZIONATI E NASCONDI LA RICERCA
$('#show-all-users').click(function(e){
    e.preventDefault();
    $('#container-result-search').hide();
    $('#container-all-users').show();
    
    var users = selected_users.join();
    var users_deleted = deleted_users.join();
    
     $('#show-all-users').hide();
     $('#hide-all-users').show();
     $('#search-fields').hide();
    $.ajax({
        url: '/events/wizard/users-invited-webmeeting',
        type: 'GET',
        data: { id: $event_id, selected_users: users , deleted_users: users_deleted},
        success: function(data) {
            $('#container-all-users').html(data);
         }
    });
});

//NASCONDI TUTTI GLI UTENTI SELEZIONATI E TORNA ALLA RICERCA
$('#hide-all-users').click(function(e){
    e.preventDefault();
    $('#container-result-search').show();
    $('#container-all-users').hide();
    
    $('#show-all-users').show();
    $('#hide-all-users').hide();
    $('#search-fields').show();
    
})

JS;
$this->registerJs($js);


?>



<div id="search-fields">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($modelSearch, 'name')->textInput([
                    'id' => 'search-name-id',
                    'aria-controls' => 'container-result-search',
                    'placeholder' => AmosEvents::t('amosevents','Cerca')
                    // 'type' => 'search'
            ])
            ->label(AmosEvents::t('amosevents','Digita qui sotto il nome, cognome, email o il codice fiscale per filtrare i risultati, selzionali per inserirli nella lista degli invitati')); ?>
        </div>
    </div>

    <div class="row mb-3">
        <div id="toolbar-search-char" class="col-md-12">
            <?= Html::a(AmosEvents::t('amosevents','Tutti'), ['/events/wizard/search-user-events', 'char' => ''], [
                'class' => 'char-search',
                'data-key' => ''
            ]); ?>
            <?php foreach (range('A', 'Z') as $char) {
                echo Html::a($char, ['/events/wizard/search-user-events', 'char' => $char], [
                    'class' => 'char-search',
                    'data-key' => $char,
                    'aria-controls' => 'container-result-search'
                ]);
            } ?>
        </div>
    </div>
</div>
<div class="row mb-5">
    <div class="col-md-12">
        <?= Html::a(AmosEvents::t('amosevents', "Visualizza tutti gli invitati") . " (<span class='counter-selected'>$countInvitationUsers</span>) ", '#', [
            'id' => 'show-all-users',
            'class' => 'btn btn-primary float-right',
            'aria-controls' => 'container-all-users'

        ]) ?>
        <?= Html::a(AmosEvents::t('amosevents', "Torna alla ricerca"), '#', [
            'id' => 'hide-all-users',
            'class' => 'btn btn-outline-primary float-right',
            'style' => 'display:none',
            'aria-controls' => 'container-result-search'
        ]) ?>
    </div>
</div>
<?php $dataProviderInvitationUsers->pagination = false;?>
<div style="display:none" id="container-all-users">
    <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_result_search', [
        'dataProvider' => $dataProviderInvitationUsers,
        'participants' => true,
        'readOnly' => false,

    ])
    ?>
</div>

<?php $dataProvider->pagination->pageSize = 30;?>

<div id="container-result-search" class="container-partecipants">
    <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_result_search', [
        'dataProvider' => $dataProvider,
        'participants_user_ids' => $participants_user_ids,
        'participants' => false,
        'readOnly' => false,
    ])
    ?>
</div>

<?= Html::hiddenInput('selected_users_ids', null, ['id' => 'selected-users-ids']) ?>
<?= Html::hiddenInput('deleted_users_ids', null, ['id' => 'deleted-users-ids']) ?>

<?php
\yii\bootstrap4\Modal::begin([
    'id' => 'modal-alert-max-participanti',
    'title' => "<h3>" . AmosEvents::t('amosevents', '') . "</h3>",
    'size' => 'modal-lg',

]); ?>
<h5><?=  AmosEvents::t('amosevents', 'Attenzione! Puoi inserire un massimo di {x} partecipanti',[
        'x'=> $max_participant
    ]) ?>
</h5>
<?php
\yii\bootstrap4\Modal::end();
?>


<?php
\yii\bootstrap4\Modal::begin([
    'id' => 'modal-alert-max-total',
    'title' => "<h3>" . AmosEvents::t('amosevents', '') . "</h3>",
    'size' => 'modal-lg',

]); ?>
<h5><?=  AmosEvents::t('amosevents', 'Attenzione! Puoi selezionare un massimo di 100 utenti alla volta, effettua il salvataggio e continua ad invitare altri utenti.') ?>
</h5>
<?php
\yii\bootstrap4\Modal::end();
?>
