<div class="afc-case">
    <h4>{l s='Accessories'}</h4>
    <ul class="afc">

    </ul>
</div>
<script>
    $(document).ready(function() {
        $('.afc-case').detach().appendTo('.pb-left-column');
        var temp_updateDisplay = updateDisplay;
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
                $.ajax({
                    dataType: "json",
                    type: 'GET',
                    url: '{$base_dir}modules/accessoriesforcombinations/ajax-find-accessories.php',
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
        updateDisplay();
    })

</script>

<style type="text/css">

</style>