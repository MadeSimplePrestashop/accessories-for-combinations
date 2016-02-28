$(document).ready(function () {
    // **** MAGIC LINE *** 
    // remove comment and move to whenever you want :)
    if (typeof afc_web_site_element != 'undefined')
        $(afc_web_site_element).after($('.afc-case').detach());
    
    
    var temp_updateDisplay = updateDisplay;
    var cache = new Array()
    updateDisplay = function () {
        var combID = $('#idCombination').val();
        //event.preventDefault();
        temp_updateDisplay();
        $('.afc-case,.afc-template').hide();
        if (typeof productHasAttributes != 'undefined' && productHasAttributes == false)
            return;
        if ($('.afc-template-' + combID).length) {
            $('.afc-template-' + combID).show();
            $('.afc-case').show();
        } else {
            var par = 'afc_ajax_' + id_product + '_' + combID;
            if (typeof cache[par] == 'undefined') {
                cache[par] = true;
                $.ajax({
                    dataType: "json",
                    type: 'GET',
                    url: baseDir + 'modules/accessoriesforcombinations/ajax-find-accessories.php',
                    data: {
                        ajax: true, id_product: id_product, id_product_attribute: combID
                    }
                }).done(function (data) {
//            $('#search-results').html(template(data.products, data.found));
                    if (data.response == 'ok') {
                        $('.afc').prepend(data.template);
                        $('.afc-case').show();
                        var $container = $('.height-fix-case').imagesLoaded(function () {
                            fix_height();
                        })
                    }
                });
            }
        }
    }
    updateDisplay();

    function fix_height() {
        var history_offset = -1;
        var history_height = -1;
        var divs_array = [];
        $('.height-fix').css('height', 'auto');
        $('.height-fix').each(function (index) {
            var key = $(this).offset().top;
            if (typeof divs_array[key] == "undefined")
                divs_array[key] = [];
            divs_array[key][index] = $(this);
            if (history_offset != -1 && history_offset != key) {

                for (var i2 in divs_array[history_offset]) {
                    var obj = divs_array[history_offset][i2];

                    if (history_height < obj.outerHeight())
                        history_height = obj.outerHeight();
                }

                for (var i2 in divs_array[history_offset])
                {
                    var obj = divs_array[history_offset][i2];
                    obj.height(history_height);
                }
                history_height = -1;
                history_offset = (obj.next()).offset().top;
                if (typeof divs_array[history_offset] == "undefined")
                    divs_array[history_offset] = [];
                divs_array[history_offset][index] = $(this);
            } else {
                history_offset = key;
            }
        })
        for (var i2 in divs_array[history_offset]) {
            var obj = divs_array[history_offset][i2];
            if (history_height < obj.outerHeight())
                history_height = obj.outerHeight();
        }

        for (var i2 in divs_array[history_offset])
        {
            var obj = divs_array[history_offset][i2];
            obj.height(history_height);
        }
    }
})