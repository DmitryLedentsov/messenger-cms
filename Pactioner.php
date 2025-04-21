<?php class Pactioner extends Actioner {

  private $pluginName = 'p4-second'; // Имя плагина     
  
  
  public function saveBaseOption() {
    USER::AccessOnly('1,4,3','exit()');
    $this->messageSuccess = 'Настройки сохранены';
    $this->messageError = 'Настройки не сохранены';

    $request = $_POST;
    
    // Проверяем наличие данных для опций
    if (!empty($request['data'])) {
      // Валидация первой опции
      if ($request['data']['promocode'] == "") {
        // Изменяем текст ошибки и возвращаем статус "ошибка"
        $this->messageError = 'Укажите промокод';
        return false;
      }



    
      // Устанавливаем новые опции
      P4Second::setOptions($request['data']);

      //обновляем базовый промокод

      $basePromoKey = $request['data']['promocode'];
      $updateData = $request['data'];
      $updateData['title']=$basePromoKey;


      P4Second::logFile('options', P4Second::getOptions());
      foreach (P4Second::getOptions() as $option=>$value) {
        unset($updateData[$option]);
      }

      
      $updateData = array_filter($updateData, function($value) {return !is_null($value) && $value !== '';});
      if($updateData['active_time']) P4Second::setActiveTime($updateData);

      if(!P4Second::getPromo($basePromoKey)) P4Second::createPromo($basePromoKey);// если не нашли создаем новый
      if (!DB::query('
        UPDATE `'.PREFIX.'oik-discount-coupon`
        SET '.DB::buildPartQuery($updateData).'
        WHERE TITLE =  '.DB::quote($basePromoKey))) {
        return false;
      }

    }   
    // Возвращаем статус "успешно"
    return true;
  }
}