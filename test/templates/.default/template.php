<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
?>
<?php if (count($arResult['FROM_JSON']) > 0): ?>
    <table>
    <? foreach ($arResult['FROM_JSON'] as $arItems): ?>
	<tr>
		<? foreach ($arItems as $sRow): ?>
		<td><? echo $sRow; ?></td>
		<? endforeach; ?>
        <td><a href="?userId=<?=$arItems['userId']?> & title=<?=$arItems['title']?> & body=<?=$arItems['body']?>&action=add"  >Добавить</td>
	</tr>
	<? endforeach; ?>
	<? foreach ($arResult['FROM_INFOBLOK'] as $arItems): ?>
	<tr>
		<? foreach ($arItems as $sRow): ?>
		<td><?php echo $sRow; ?></td>
		<? endforeach; ?>
        <td><a href="?ID=<?=$arItems['ID']?>&action=delete"  >Удалить</td>
	</tr>
	<? endforeach; ?>
</table>
<? endif; ?>

