<?php
/**
 * Template for list field type
 * @author Miljko Milosevic
 * @since 13.10.2016
 */
?>

<table>
    <thead>
    <tr>
        <th> <?php echo(implode('</th><th>', $keys)) ?> </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($listArray as $row) { ?>
        <tr>
            <?php foreach ($keys as $column) { ?>
                <td><?php echo(wp_kses_post($row[$column])) ?></td>
            <?php } ?>
        </tr>
    <?php } ?>
    <tbody>
</table>
