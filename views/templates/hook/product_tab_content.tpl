<section class="page-product-box">
    <div class="afc-case block products_block accessories-block clearfix">
        <h3 class="page-product-heading">{l s='Accessories' mod='accessoriesforcombinations'}</h3>
        <ul class="afc">
            
        </ul>
    </div>
</section>
<script>
    $(document).ready(function() {
        // remove comment and move to whenever you want :)
        //$('.afc-case').detach().appendTo('.pb-left-column');
        var temp_updateDisplay = updateDisplay;
        var cache = new Array()
        updateDisplay = function() {
            //event.preventDefault();
            temp_updateDisplay();
            $('.afc-case,.afc-template').hide();
            if (typeof productHasAttributes != 'undefined' && productHasAttributes == false)
                return;
            if ($('.afc-template-' + combID).length) {
                $('.afc-template-' + combID).show();
                $('.afc-case').show();
            } else {
                var par  = 'afc_ajax_' + id_product + '_' + combID;
                if (typeof cache[par] == 'undefined' ) {
                    cache[par] = true;
                    $.ajax({
                        dataType: "json",
                        type: 'GET',
                        url: '{$base_dir|escape:htmlall}modules/accessoriesforcombinations/ajax-find-accessories.php',
                        data: {
                            ajax: true, id_product: id_product, id_product_attribute: combID
                        }
                    }).done(function(data) {
//            $('#search-results').html(template(data.products, data.found));
                        if (data.response == 'ok') {
                            $('.afc').prepend(data.template);
                            $('.afc-case').show();

                        }
                    });
                }
            }
        }
        updateDisplay();
    })

</script>