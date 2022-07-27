<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Data\Cache;
class Ctablica extends CBitrixComponent
{
    public $arTablefromjson;//данные подтянутые для таблицы
    public $cache;// Служба кеширования

    public $sCachePath = 'mycachepath'; // папка, в которой лежит кеш
    public $iCacheTtl = 3600; // срок годности кеша (в секундах)
    public $sCacheKey = 'mycachekey'; // имя кеша
    public $componentPage="";

   public function addElement($userId, $title , $body)
   {
       $obEl = new CIBlockElement();
       $PROP = array();       // здесь у нас будут храниться свойства
       $PROP['NAME'] = $userId;
       $PROP['TITLE'] = $title;
       $PROP['BODY'] = $body;
       $arLoadProductArray = Array(
            "IBLOCK_CODE" => 'TABLES',
            "NAME" => $userId,
            "TITLE" =>$title,
            "BODY" =>$body,
            "PROPERTY_VALUES"=> $PROP,
       );
       if($PRODUCT_ID = $obEl->Add($arLoadProductArray)) {
           echo 'New ID: '.$PRODUCT_ID;
       } else {
           echo 'Error: '.$obEl->LAST_ERROR;
       }
   }

   function Request ()
   {
       if ( $_REQUEST['action'] == 'delete' && isset($_REQUEST['ID'] )  ){
         $this->delete($_REQUEST['ID']);
       }
       if ($_REQUEST['action'] == 'add' && isset($_REQUEST['userId'],$_REQUEST['title'],$_REQUEST['body'])){
        $this->addElement($_REQUEST['userId'],$_REQUEST['title'],$_REQUEST['body'],);
      }
   }
   public function getCache()
   {
       $this->cache=Cache::createInstance();
       if ($this->cache->initCache($this->iCacheTtl, $this->sCacheKey, $this->sCachePath)){//если есть кеш
           $this->arTablefromjson = $this->cache->getVars(); // Получаем переменные
       }
       elseif ($this->cache->startDataCache()){//если кеша нет
           $this->arTablefromjson = $this->getTable();
           $this->cache->endDataCache($this->arTablefromjson);
       }
   }
   public function getTable()
   {
       $url = curl_init();
       curl_setopt_array($url, array(
           CURLOPT_URL => "https://jsonplaceholder.typicode.com/posts",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_TIMEOUT => 30,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "GET",
           CURLOPT_HTTPHEADER => array(
               "cache-control: no-cache"
           ),
       ));
       $arTablefromjson = curl_exec($url);
       return  json_decode($arTablefromjson, true);
   }
    public function delete ($id)
    {
        CIBlockElement::Delete($id);
    }
    public function getInfoblok()
    {
        CModule::IncludeModule('iblock');
            $obResult = CIBlockElement::GetList([], ['IBLOCK_CODE' => 'TABLES'], false, false, ['ID', 'NAME', 'PROPERTY_ID', 'PROPERTY_TITLE', 'PROPERTY_BODY']);

            $arTable = [];
            while ($arElement = $obResult->fetch()) {
                $arTable[$arElement['ID']] = [
                    'NAME' => $arElement['NAME'],
                    'ID' => $arElement['ID'],
                    'TITLE' => $arElement['PROPERTY_TITLE_VALUE'],
                    'BODY' => $arElement['PROPERTY_BODY_VALUE'],
            ];
        }
        return $this->arResult['FROM_INFOBLOK']= $arTable ;

    }
    public function executeComponent()
    {
        $this->arResult['FROM_INFOBLOK'] = $this->getInfoblok() ;
        $this->getCache();
        $this->arResult['FROM_JSON']= $this->arTablefromjson;
        $this->IncludeComponentTemplate();
        $this->Request();
    }
}