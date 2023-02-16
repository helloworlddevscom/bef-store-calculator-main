<!-- /.col Gravity form selection -->
<div class="col-sm-6 hidden" id="wdt-gf-form-container">
    <h4 class="c-title-color m-b-4">
        <?php _e('Choose a Gravity Form','wpdatatables');?>
        <i class="wpdt-icon-info-circle-thin" data-toggle="tooltip" data-placement="right" title="" data-original-title="Please choose a Gravity Form that will be used as data source for wpDataTable"></i>
    </h4>

    <div class="form-group">
        <div class="fg-line">
            <select class="selectpicker" data-live-search="true" id="wdt-gravity-form-picker">
                <option value=""><?php _e( 'Pick a Gravity form...', 'wpdatatables' );?></option>
                <?php foreach( WDTGravityIntegration\Plugin::getGFNamesIds() as $form ){ ?>
                <option value="<?php echo $form->id;?>"><?php echo $form->title; ?> </option>
                <?php } ?>
            </select>
        </div>
    </div>

</div>
<!-- /.col Gravity form selection -->

<!-- .col Fields selection -->
<div class="col-sm-6 hidden" id="wdt-gf-column-container">
    <h4 class="c-title-color m-b-4">
        <?php _e('Choose fields to show as columns','wpdatatables');?>
        <i class="wpdt-icon-info-circle-thin" data-toggle="tooltip" data-placement="left" title="" data-original-title="Please choose form fields that will be used as wpDataTable columns"></i>
    </h4>

    <div class="form-group">
        <div class="fg-line">
            <select class="selectpicker" multiple="true" id="wdt-gravity-form-column-picker" data-actions-box="true">
                <optgroup id="wdt-gf-form-fields" label="Form fields">

                </optgroup>
                <optgroup id="wdt-gf-common-fields" label="Common fields">
                    <option value="date_created">Entry Date</option>
                    <option value="id">Entry ID</option>
                    <option value="created_by">User</option>
                    <option value="ip">User IP</option>
                </optgroup>
            </select>
        </div>
    </div>

</div>
<!-- /.col Fields selection -->