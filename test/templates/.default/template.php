<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
?>
<?php if (count($arResult['FROM_JSON']) > 0): ?>

    <table>
        <thead>
        <tr>
            <th><?php echo implode('</th><th>', array_keys(current($arResult['TABLE_RESULT']))); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($arResult['FROM_JSON'] as $row): array_map('htmlentities', $row); ?>
            <tr>
                <td><?php echo implode('</td><td>', $row); ?></td>
                <td><a href="?userId=<?=$row['userId']?> & title=<?=$row['title']?> & body=<?=$row['body']?>" >Добавить</td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($arResult['TASKS'] as $row): array_map('htmlentities', $row); ?>
            <tr>
                <td><?php echo implode('</td><td>', $row); ?></td>
                <td><a href="?ID=<?=$row['ID']?>" >Удалить</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

