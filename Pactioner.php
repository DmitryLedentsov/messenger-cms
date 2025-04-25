<?php class Pactioner extends Actioner {

  private $pluginName = 'p4-messenger'; // Имя плагина     
  
  
  public function saveBaseOption() {
    USER::AccessOnly('1,4,3','exit()');
    $this->messageSuccess = 'Настройки сохранены';
    $this->messageError = 'Настройки не сохранены';

    $request = $_POST;
    
    // Проверяем наличие данных для опций
    if (!empty($request['data'])) {
      // Валидация первой опции
   

    
      // Устанавливаем новые опции
      P4Messenger::setOptions($request['data']);

      //обновляем базовый промокод

      return true;

    }   
   
    // Возвращаем статус "успешно"
    return false;
  }
}