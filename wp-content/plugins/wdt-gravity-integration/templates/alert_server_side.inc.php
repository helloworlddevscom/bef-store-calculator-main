<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */
?>
<div class="alert alert-warning alert-dismissible alert-server-side hidden" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
    <?php _e('Please be aware that sorting and searching may not work as expected for complex entries when server side is turned on. This problem is on Gravity side and we are working on solution.', 'wpdatatables'); ?>
    <br>
    <?php _e('Disabled features are not allowed for this table type.'); ?>
    <br>
    <?php _e('Thank you for understanding.', 'wpdatatables'); ?>
</div>