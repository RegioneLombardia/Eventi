$(document).ready(function() {
    updateGW();

    $(document).on('click', '.kv-row-checkbox', function() {
        selectCheckbox($(this));
    });

    /**
     * 
     */
    $(document).on('click', '.select-on-check-all', function() {
        $('input:checkbox.kv-row-checkbox').each(function () {
            $(this).click();
        });
    });
    
    /**
     * 
     */
    $(document).on('click', '.selected_remove', function() {
        //recupera il contenitore degli input hidden
        var hiddenContainer = $('#hiddenSelectedContainer');
        var id = $(this).attr('id');
        
        
        if (confirm("Vuoi rimuovere questo utente?")) {
            //cerca l'input hidden
            var hiddenInput = hiddenContainer.find('[value="' + id + '"]');
            if (hiddenInput.length > 0) {
                hiddenInput.remove();
                $('#user_' + id).prop('checked', false);
                updatePreview();
            }
        }
    });

    $(document).on('pjax:send', function () {
        //;
    }).on('pjax:complete', function () {
        //;
    }).on('pjax:end', function () {
        updateGW();
    });
       
    /**
     * 
     */
    function updateGW() {
        //recupera il contenitore degli input hidden
        var hiddenContainer = $('#hiddenSelectedContainer');
        $('table.kv-grid-table tr').each(function() {
            var id = $(this).data('key');
            var hiddenInput = hiddenContainer.find('[value="' + id + '"]');
            if (hiddenInput.length > 0) {
                $('#user_' + id).prop('checked', true);
            }
        });
    }

    /**
     * 
     * @param {type} element
     * @returns {undefined}
     */
    function selectCheckbox(element) {
        //recupera il contenitore degli input hidden
        var hiddenContainer = $('#hiddenSelectedContainer');

        //recupera l'oggetto selezionato
        var obj = $(element);

        //id dell'elemento selezionato
        var val = obj.val();
        var name = obj.data('label');

        //se l'oggetto è selezionato, lo aggiunge
        if (obj.is(":checked")) {
             //crea l'input hidden
            var newHiddenInput = '<input type="hidden" name="selectedUsers[]" class="selectedUsers" data-id="' + val + '" data-label="' + name + '" value="' + val + '" />';

            //lo inserisce nel contenitore
            hiddenContainer.append(newHiddenInput);
        } else {
            //altrimenti lo rimuove
            //cerca l'input hidden
            var hiddenInput = hiddenContainer.find('[value="' + val + '"]');
            if (hiddenInput.length > 0) {
                hiddenInput.remove();
            }
        }
        
        updatePreview();
    };
    
    /**
     * 
     */
    function updatePreview() {
        var selectedUsers = document.getElementById("hiddenSelectedContainer")
            .querySelectorAll(".selectedUsers");

        //identifica il blocco di preview
        var previewBlock = $('#preview_list');

        //lo svuota
        previewBlock.empty();
        
        //se ci sono elementi, li renderizza
        if (selectedUsers.length > 0) {
            $.each(selectedUsers, function(index, data) {              
                var tmp = 
                 '<li class="row">'
                +'    <div class="selected col-xs-12">'
                +'        <div class="selected_part selected_remove_container">'
                +'            <div class="selected_remove" id="' + data.dataset.id + '">'
                +'                  <button type="button" class="btn btn-danger-inverse">'
                +'                      <span class="am am-delete"></span>'
                +'                      <span class="sr-only">Cancella</span>'
                +'                  </button>'
                +'            </div>'
                +'        </div>'
                +'        <div class="selected_part">'
                +'            <div class="selected_label">' + data.dataset.label + '</div>'
                +'        </div>'
                +'    </div>'
                +'</li>';

                //lo inserisce nella preview
                previewBlock.append(tmp);
            });
        } else {
            var no_user = '';
            no_user += "<li>Nessun partecipante</li>";

            previewBlock.append(no_user);
        }
        
        if (selectedUsers.length == 1) {
            var partecipants = 'partecipante';
        }else{
            var partecipants = 'partecipanti';
        }
        
        $("#number-of-partecipants").text(selectedUsers.length + ' ' + partecipants);
    };
        
    /**
     * 
     * @param {type} element
     * @returns {String}
     */
    function getCheckedStatus(element) {
        var hiddenContainer = $('#hiddenSelectedContainer');
        var id = $(element).attr("id");

        //cerca l'input hidden
        var hiddenInput = hiddenContainer.find('[value="' + id + '"]');
        if (hiddenInput.length > 0) {
            return 'checked';
        }

        return '';
    }
    
});
