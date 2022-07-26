<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
?>
<?php if (count($arResult['FROM_JSON']) > 0): ?>
    <table>
    <?php foreach ($arResult['FROM_JSON'] as $items): ?>
	<tr>
		<?php foreach ($items as $row): ?>
		<td><?php echo $row; ?></td>
		<?php endforeach; ?>
        <td><a href="?userId=<?=$row['userId']?> & title=<?=$row['title']?> & body=<?=$row['body']?>&action=add"  >Добавить</td>
	</tr>
	<?php endforeach; ?>
	<?php foreach ($arResult['TASKS'] as $items): ?>
	<tr>
		<?php foreach ($items as $row): ?>
		<td><?php echo $row; ?></td>
		<?php endforeach; ?>
        <td><a href="?ID=<?=$row['ID']?>&action=delete"  >Удалить</td>
	</tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>

