
$(document).ready(function() {

    $.fn.watch = function(id, fn) {

        return this.each(function() {

            var self = this;

            var oldVal = self[id];
            $(self).data(
                    'watch_timer',
                    setInterval(function() {
                        if (self[id] !== oldVal) {
                            fn.call(self, id, oldVal, self[id]);
                            oldVal = self[id];
                        }
                    }, 100)
                    );

        });

        return self;
    };

    $.fn.unwatch = function(id) {

        return this.each(function() {
            clearInterval($(this).data('watch_timer'));
        });

    };

    $.fn.valuechange = function(fn) {
        return this.bind('valuechange', fn);
    };

    $.event.special.valuechange = {
        setup: function() {

            $(this).watch('value', function() {
                $.event.handle.call(this, {type: 'valuechange'});
            });

        },
        teardown: function() {
            $(this).unwatch('value');
        }

    };

    $('#id_product_attribute').bind('valuechange', function(e) {
        if ($('#accessoriesforcombinations_form').is(':hidden')) {
            $('#ResetBtn').closest('.panel-footer').before($('#accessoriesforcombinations_form').detach());
            $('#accessoriesforcombinations_form').show();
            $('#accessoriesforcombinations_form .afc-line:not(:last)').remove();
            var id_product_attribute = $('#id_product_attribute').val();
            var obj = $('.afc_id_product_search:last');
            var line = obj.parents('.afc-line:first');
            $.each(afc_accessories[id_product_attribute], function(i, item) {
                var obj2 = line.clone().prependTo(obj.parents('.afc-form:first'));
                obj2.find('.afc_id_product_search').removeClass('ac_input').val(item.name);
                obj2.find('.afc_id_product').val(item.id_product)
                addAfcProduct(item.id_product, item.name, obj2.find('.afc_id_product_search'), item.id_product_attribute)
            });
        }
    });

    $(document).on('click', '.afc_product_remove', function(e) {
        e.preventDefault();
        $(this).parents('.afc-line:first').remove();
    })

    $(document).on('keyup', '.afc_id_product_search', function(e) {
        if ($(this).val() < 1)
            $(this).parents('.afc-line:first').find('.afc_id_product_attribute').hide();
    })

    $(document).on('focus', '.afc_id_product_search:not(.ac_input)', function(e) {
        $(this).autocomplete('ajax_products_list.php?excludeIds=' + id_product, {
            minChars: 2,
            autoFill: true,
            max: 20,
            matchContains: true,
            mustMatch: true,
            scroll: false,
            cacheLength: 0,
            formatItem: function(item) {
                return item[0] + ' - ' + item[1];
            }
        }).result(function(e, i) {
            if (i != undefined)
                addAfcProduct(i[1], i[0], $(this), false);
        });
    });
    // addRelatedProduct(id_product_redirected, product_name_redirected);

    function addAfcProduct(id_product_to_add, product_name, obj, selected)
    {
        var obj = obj;
        if (!id_product_to_add || id_product == id_product_to_add)
            return;
        // ajax search combination
        var line = obj.parents('.afc-line:first');
        $.ajax({
            dataType: "json",
            type: 'GET',
            url: baseDir + 'modules/accessoriesforcombinations/ajax-find-combinations.php',
            data: {ajax: true, token: afc_token, id_product: id_product_to_add}
        }).done(function(data) {
//            $('#search-results').html(template(data.products, data.found));
            if (data.response == 'ok' && Object.keys(data.combinations).length > 0) {
                var select = obj.parents('.afc-line:first').find('.afc_id_product_attribute');
                select.html('');
                $.each(data.combinations, function(i, item) {
                    var x = [];
                    $.each(item.attributes_values, function(i, n) {
                        x.push(n);
                    });
                    select.append('<option value="'
                            + i
                            + '">'
                            + x.join(', ')
                            + '</option>');
                });
                if (selected > 0)
                    select.val(selected);
            }
        });
        if (!obj.prev().val())
            line.clone().appendTo(obj.parents('.afc-form:first')).find('.ac_input').removeClass('ac_input').val('');
        //obj.hide();
        line.find('.afc_product_name').html(product_name).hide();
        line.find('.afc_id_product').val(id_product_to_add);
        line.find('.afc_id_product_attribute,.afc_product_remove').show();
    }


})