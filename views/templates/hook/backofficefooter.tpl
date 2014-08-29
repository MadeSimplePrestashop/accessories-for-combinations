<div id="accessoriesforcombinations_form" style="display:none;">
    <h4>{l s='Accessories:' mod='accessoriesforcombinations'}</h4>
    <div class="afc-form"  >
        <div class="form-group afc-line">
            <div class="col-sm-5">
                <input type="hidden" class="afc_id_product" name="afc_id_product[]" />
                <input type="text" class="afc_id_product_search" name="afc_id_product_search[]" placeholder="{l s="Search product" mod="accessoriesforcombinations"}"  size="20"  />
                <span class="afc_product_name"></span>
            </div>
            <div class="col-sm-6">
                <select style="display:none" class="afc_id_product_attribute" name="afc_id_product_attribute[]"></select>
            </div>
            <div class="col-sm-1">
                <a style="display:none" class="btn btn-default afc_product_remove" href="">
                    <i class="icon-remove text-danger"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    {$addJS|default}
</script>
<script type="text/javascript" src="{$module_dir|escape:htmlall}js/admin_product.js"></script>