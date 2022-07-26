<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Data\Cache;
class Ctablica extends CBitrixComponent
{
    public $arResponse;//данные подтянутые для таблицы
    public $cache;// Служба кеширования

    public $cachePath = 'mycachepath'; // папка, в которой лежит кеш
    public $cacheTtl = 3600; // срок годности кеша (в секундах)
    public $cacheKey = 'mycachekey'; // имя кеша
    public $componentPage="";

   public function addElement($userId, $title , $body)
   {
       $el = new CIBlockElement();
       $PROP = array();       // здесь у нас будут храниться свойства
       $PROP[1] = $userId;
       $PROP['TITLE'] = $title;
       $PROP['BODY'] = $body;
       $arLoadProductArray = Array(
            'IBLOCK_ID' => 5,
            "NAME" => $userId,
            "TITLE" =>$title,
            "BODY" =>$body,
            "PROPERTY_VALUES"=> $PROP,
       );
       if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
           echo 'New ID: '.$PRODUCT_ID;
       } else {
           echo 'Error: '.$el->LAST_ERROR;
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
       if ($this->cache->initCache($this->cacheTtl, $this->cacheKey, $this->cachePath)){//если есть кеш
           $this->arResponse = $this->cache->getVars(); // Получаем переменные
       }
       elseif ($this->cache->startDataCache()){//если кеша нет
           $this->getTable();
           $this->cache->endDataCache($this->arResponse);
       }
   }
   public function getTable()
   {
       $curl = curl_init();
       curl_setopt_array($curl, array(
           CURLOPT_URL => "https://jsonplaceholder.typicode.com/posts",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_TIMEOUT => 30,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "GET",
           CURLOPT_HTTPHEADER => array(
               "cache-control: no-cache"
           ),
       ));
       $arResponse = curl_exec($curl);
       $this->arResponse = json_decode($arResponse, true); //because of true, it's in an array
     //  array_push($response,"action");
   }
    public function delete ($id)
    {
        CIBlockElement::Delete($id);
    }
    public function getInfoblok()
    {
        CModule::IncludeModule('iblock');
        $result = CIBlockElement::GetList([], ['IBLOCK_ID' => 5], false, false, ['ID', 'NAME', 'PROPERTY_ID', 'PROPERTY_TITLE', 'PROPERTY_BODY']);

        $arTasks = [];
        while ($element = $result->fetch()) {
            $arTasks[$element['ID']] = [
                'NAME' => $element['NAME'],
                'ID' => $element['ID'],
                'TITLE' => $element['PROPERTY_TITLE_VALUE'],
                'BODY' => $element['PROPERTY_BODY_VALUE'],
            ];
        }
        $this->arResult['TASKS'] = $arTasks;
    }

    public function executeComponent()
    {
        $this->getInfoblok();
        $this->getCache();
       // $this->arResult['TABLE_RESULT'] = array_merge($this->response,$this->arResult['TASKS']);
        $this->arResult['FROM_JSON']= $this->arResponse;
        $this->IncludeComponentTemplate();
        $this->Request();
    }
}