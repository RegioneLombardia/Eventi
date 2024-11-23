<?php

use kartik\datecontrol\DateControl;
use open20\amos\core\forms\ActiveForm;
use yii\helpers\Html;
use open20\amos\events\AmosEvents;
use yii\widgets\ListView;
use yii\helpers\Url;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;

\open20\amos\layout\assets\SpinnerWaitAsset::register($this);

$moduleEvents = \Yii::$app->getModule('events');
$googleApikey = \Yii::$app->params['google-maps']['key'];
$yourLocationLabel = AmosEvents::t('amosevents', 'La tua località');
$js = <<<JS

 function displayLocation(latitude,longitude){
        var request = new XMLHttpRequest();
F
        var method = 'GET';
        var url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+latitude+','+longitude+'&result_type=administrative_area_level_2&key=$googleApikey';
        var async = true;

        request.open(method, url, async);
        request.onreadystatechange = function(){
          if(request.readyState == 4 && request.status == 200){
            var data = JSON.parse(request.responseText);
            var address = data.results[0];
            var provincia = '';
            component_address = address.address_components;
            component_address.forEach(function(val){
                var types = val.types;
                if(types.includes('administrative_area_level_2')){
                    provincia = val.short_name;
                }
            });
            
            //console.log(provincia);
           // address.address_components
            
            //console.log(address.formatted_address);
             $.ajax({ 
                      url: '/events/event/get-provincia-from-string',
                      data: {provincia: provincia},
                      type: "GET",
                      success: function(data){
                          // console.log(data);
                          var found = $('#current-comune').find('option[value="'+data.id+'"]');
                          if(found.length == 0 || found === undefined){
                              $('#current-comune').append ('<option value="' + data.id+ '">' + '$yourLocationLabel' + '</option>');
                          }
                          $('#current-comune').val(data.id).trigger("change");
                      },
                      error: function(){
                          alert('Località non trovata');
                      }
             });
          }else{
            //  alert('località non trovata');
          }
        };
        request.send();
}

function geoFindMe() {
      var status = document.querySelector('#status');
    
      function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        var location = displayLocation(latitude, longitude);
    
       console.log('latitude:'+latitude);
       console.log('longitude:'+longitude);
      }
    
      function error() {
        status = 'Unable to retrieve your location';
      }
    
      if(!navigator.geolocation) {
        status = 'Geolocation is not supported by your browser';
      } else {
        status = 'Locating...';
        navigator.geolocation.getCurrentPosition(success, error);
      }

}

function searchAjax(){
    $('.loading').show();
     $.ajax({ 
          url: '/events/event/search-events',
          data: {
              currentComune: $('#current-comune').val(), 
              day: $('#hidden-day-id').val(), 
              tag_id: $('#hidden-tag-id').val()
              },
          type: "GET",
          success: function(data){
              $('.loading').hide();
              $('#event-coming-next-container').html(data);
              enableDisableFilters();
          },
       
    });
}

// disable filter with no results, enable the others
function enableDisableFilters(){
    let filtersString = $('#filter-tags-enabled-id').val();
    const filters = filtersString.split(",");
    //console.log(filters);
   // let activeA = $('filtri-tipologia a.active');
    $('.filtri-tipologia option').each(function(){
        let currentVal = $(this).val();

        if($.inArray($(this).val(), filters ) !== -1){
            $(this).removeAttr('disabled');
            $(this).attr('title', $(this).text());
            // $('#select2-filter-tag-id-results li[id*="'+currentVal+'"]')
            //     .removeAttr('aria-disabled')
            //     .attr('aria-selected', false);
        }else{
            $(this).attr('disabled','disabled');
              // $('#select2-filter-tag-id-results li[id*="'+currentVal+'"]')
              //   .attr('aria-disabled',true)
              //   .removeAttr('aria-selected', false);

            if($(this).attr('id') !== 'filter-all-id'){
                $(this).attr('title', 'Nessun evento per: '+$(this).text());
            }
        }
    });
    if(filters.length > 0){
        $('#filter-all-id').removeAttr('disabled');
    }
    $('#filter-tag-id').select2({minimumResultsForSearch: -1});
}


$(document).on('click','#your-location-id',function(e){
    e.preventDefault();
    geoFindMe();
});

$(document).on('change','#current-comune',function(){
   searchAjax();
});

$(document).on('click', '.filter-date', function(e){
    e.preventDefault();
    var day = $(this).attr('data-key');
    $('.filter-date').removeClass('active');
    $(this).addClass('active');
    $('#hidden-day-id').val(day);
    //console.log(day);
   searchAjax();
});


$(document).on('select2:select', '.filter-tag', function(e){
    e.preventDefault();
      if($(this).hasClass('disabled')){
        return false;
    }
    
    let tag = $(this).val();
    $('#hidden-tag-id').val(tag);
    
  
    //console.log(tag);
   searchAjax();
});

JS;
$this->registerJs($js);

$css = <<<CSS

 
.container-favourites .favourite-btn {
    font-size: 24px;
    color: #9B9B9B;
     }
.container-favourites .favourite-btn:hover{
      color: #d97e00;

}

.container-favourites .favourite-btn:focus{
      color: #d97e00;

}

.container-favourites .added-to-favourites {
    color: #d97e00;
  }


  .container-favourites .added-to-favourites {
    color: #d97e00;
  }


CSS;

$this->registerCss($css);
$comingNextId = 'coming-next';
?>
<div class="loading" style="display:none"></div>

<div id="coming-next">
    <?php
    $form = ActiveForm::begin([
        'action' => Url::toRoute(['/backendobjects']),
        'method' => 'get',
        'options' => [
            'id' => 'element_form_' . $model->id,
            'class' => 'form wrap-search',
            'enctype' => 'multipart/form-data',
        ]
    ]);
    ?>

    <div class="heading-filtri-top">
        <div class="uk-container content-filtri">
            <div class="filtri-date">
                <?php echo Html::a(AmosEvents::t('amosevents', "In evidenza"), ['/', 'day' => 'highlights'], [
                    'class' => 'filter-date active',
                    'data-key' => 'highlights',
                    'aria-controls' => 'event-coming-next-container',
                ]) ?>
                <span class="separator"></span>
                <?php echo Html::a(AmosEvents::t('amosevents', "Oggi"), ['/', 'day' => 'today'], [
                    'class' => 'filter-date',
                    'data-key' => 'today',
                    'aria-controls' => 'event-coming-next-container'

                ]) ?>
                <?php echo Html::a(AmosEvents::t('amosevents', "Prossimo weekend"), ['/', 'day' => 'next_weekend'], [
                    'class' => 'filter-date',
                    'data-key' => 'next_weekend',
                    'aria-controls' => 'event-coming-next-container'

                ]) ?>

                <?php echo Html::a(AmosEvents::t('amosevents', "Questa settimana"), ['/', 'day' => 'this_week'], [
                    'class' => 'filter-date',
                    'data-key' => 'this_week',
                    'aria-controls' => 'event-coming-next-container'

                ]) ?>
                <?php echo Html::a(AmosEvents::t('amosevents', "Questo mese"), ['/', 'day' => 'this_month'], [
                    'class' => 'filter-date',
                    'data-key' => 'this_month',
                    'aria-controls' => 'event-coming-next-container'

                ]) ?>
                <?php echo Html::a(AmosEvents::t('amosevents', "Tutti"), ['/', 'day' => 'all'], [
                    'class' => 'filter-date',
                    'data-key' => 'all',
                    'aria-controls' => 'event-coming-next-container'

                ]) ?>
                <span class="separator separator-last"></span>
                <?php echo Html::a(AmosEvents::t('amosevents', "Eventi conclusi"), ['/', 'day' => 'closed'], [
                    'class' => 'filter-date',
                    'data-key' => 'closed',
                    'aria-controls' => 'event-coming-next-container'

                ]) ?>

                <?= Html::hiddenInput('hidden-day', \Yii::$app->request->get('day'), ['id' => 'hidden-day-id']) ?>
            </div>
            <div class="filtri-tipologia">
                <?php
                $tags = \open20\amos\events\utility\EventsUtility::getPreferenceCenterTags();
                $tagsPreference = [];
                $optionsDisabled = [];
                $tagsPreference['all'] = \Yii::t('amosevents', "Tutte le tipologie");
                //                array_push($tagsPreference, ['all' => \Yii::t('amosevents',"Tutte le tipologie")]);
                $optionsDisabled['all'] = [
                    'id' => 'filter-all-id',
                ];
                foreach ($tags as $tag) {
                    $tagsPreference[$tag->id] = $tag->nome;
                    if (!in_array($tag->id, $filtersEnabled)) {
                        $optionsDisabled[$tag->id] = [
                            'disabled' => true,
                            'title' => \Yii::t('amosevents', 'Nessun evento per questa tipologia')
                        ];
                    }
                }

                echo Select2::widget([
                    "name" => "filterType",
                    "data" => $tagsPreference,
                    'hideSearch' => true,
                    'options' => [
                        'id' => 'filter-tag-id',
                        'class' => 'filter-tag',
                        'placeholder' => 'Seleziona tipologia',
                        'options' => $optionsDisabled
                    ],
                ]);
                ?>
                <?= Html::hiddenInput('hidden-tag', \Yii::$app->request->get('tag_id'), ['id' => 'hidden-tag-id']) ?>


            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
    <div class="uk-container">

        <?php
        echo $this->render('coming_next_event_widget_list', [
            'dataProvider' => $dataProvider,
            'filtersEnabled' => $filtersEnabled
        ]);
        ?>
    </div>

</div>