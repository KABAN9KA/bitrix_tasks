<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Data\Cache;
class Ctablica extends CBitrixComponent
{
    public $response;//данные подтянутые для таблицы
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
       if (isset($_REQUEST['ID'])){
         $this->delete($_REQUEST['ID']);
       }
       if (isset($_REQUEST['userId'],$_REQUEST['title'],$_REQUEST['body'])){
        $this->addElement($_REQUEST['userId'],$_REQUEST['title'],$_REQUEST['body'],);
      }
   }

   public function table()
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
       $response = curl_exec($curl);
       $this->response = json_decode($response, true); //because of true, it's in an array
     //  array_push($response,"action");
   }

    // public function Check()
    // {
    //     $rsItems = CIBlockElement::GetList(array(),array('IBLOCK_ID' =>'5','=NAME' => 'название'),false,false,array('ID'));
    //     if ($arItem = $rsItems->GetNext())
    //     {
    //         // есть такой элемент
    //     }
    //     else
    //     {
    //     }
    // }
    public function delete ($id)
    {
        CIBlockElement::Delete($id);
    }
    public function Display()
    {
        CModule::IncludeModule('iblock');
        $result = CIBlockElement::GetList([], ['IBLOCK_ID' => 5], false, false, ['ID', 'NAME', 'PROPERTY_ID', 'PROPERTY_TITLE', 'PROPERTY_BODY']);

        $tasks = [];
        while ($element = $result->fetch()) {
            $tasks[$element['ID']] = [
                'NAME' => $element['NAME'],
                'ID' => $element['ID'],
                'TITLE' => $element['PROPERTY_TITLE_VALUE'],
                'BODY' => $element['PROPERTY_BODY_VALUE'],
            ];
        }
        $this->arResult['TASKS'] = $tasks;
    }

    public function executeComponent()
    {
        $this->Display();
        $this->cache=Cache::createInstance();
        if ($this->cache->initCache($this->cacheTtl, $this->cacheKey, $this->cachePath)){//если есть кеш
            $this->response = $this->cache->getVars(); // Получаем переменные
        }
        elseif ($this->cache->startDataCache()){//если кеша нет
            $this->table();
            $this->cache->endDataCache($this->response);
        }

       // $this->arResult['TABLE_RESULT'] = array_merge($this->response,$this->arResult['TASKS']);
        $this->arResult['FROM_JSON']= $this->response;
        $this->IncludeComponentTemplate();
        $this->Request();
    }
}